<?php
if (!defined('BASEPATH'))
	exit('no direct script access allowed');
class CustomerLogin extends CW_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->_init();
		$this->load->library("zip");
	}

	private function _init()
	{
			
	}
	
	public function index()
	{
		//取得生产厂家名称
		$producterUrl = base_url()."resource/producter.txt";
		$producter = @file_get_contents($producterUrl);
		if($producter == FALSE)
		{
			$producter = "未找到配置文件producter.txt";
		}
		else
		{
			$producter = iconv("gbk", "utf-8", $producter);
		}
		$this->smarty->assign("producter",$producter);
		
		$reportdate = date("Y年m月d日");
		$this->smarty->assign("reportdate",$reportdate);
		
		//取得序列号
		$sn = "";
		if(isset($_POST['sn']))
		{
			$sn = $this->input->post("sn");
		}
		else
		{
			$sn = "NULL";
		}
		
		$this->smarty->assign("productsn",$sn);
		
		//获取vna基本信息
		$basicInfoObject = $this->db->query("SELECT DISTINCT po.testTime,tn.name as teststationname,po.equipmentSn,pe.name,tr.fullname AS tester,po.result,po.tag1
											FROM producttestinfo po 
											JOIN testitemresult tt ON tt.productTestInfo = po.id 
											JOIN testitemmarkvalue te ON te.testItemResult = tt.id
											JOIN producttype pe ON po.productType = pe.id
											JOIN tester tr ON po.tester = tr.id
											JOIN teststation tn ON po.testStation = tn.id
											WHERE po.sn = '".$sn."'");
		$basicInfoArray = $basicInfoObject->result_array();
		if(count($basicInfoArray) != 0)
		{
			$basicInfoArray = $basicInfoArray[0];
		}
		else
		{
			$basicInfoArray = array();
		}
		$this->smarty->assign("basicInfoArray",$basicInfoArray);
		
		
		//获取vna测试详情
		$testDetailObject = $this->db->query("SELECT tm.name,tt.testResult,tt.img,te.value,te.mark
										FROM producttestinfo po 
										JOIN testitemresult tt ON tt.productTestInfo = po.id
										JOIN testitemmarkvalue te ON te.testItemResult = tt.id
										JOIN testitem tm ON tt.testItem = tm.id
										WHERE po.sn = '".$sn."'");
		$testDetailArray = $testDetailObject->result_array();
		//结果数组
		$result = array();
		//测试项数组
		$testitem = array();
		if(count($testDetailArray) != 0)
		{
			foreach($testDetailArray as $value)
			{
				if(!in_array($value['name'], $testitem))
				{
					$arr = array($value['img'],array(array($value['mark'],$value['value'],$value['testResult'])));
					$result[$value['name']] = $arr;
					array_push($testitem,$value['name']);
				}
				else
				{
					$arr = array($value['mark'],$value['value'],$value['testResult']);
					array_push($result[$value['name']][1],$arr);
				}
			}
		}
		$this->smarty->assign("result",$result);
		
		//获取PIM基本信息
		$pimbasicInfoObject = $this->db->query("SELECT pl.name,pm.col12,pm.col13,MAX(pp.test_time) AS testtime,pp.upload_date
												FROM pim_label pl
												JOIN pim_ser_num pm ON pm.pim_label = pl.id
												JOIN pim_ser_num_group pp ON pp.pim_ser_num = pm.id
												JOIN pim_ser_num_group_data pa ON pa.pim_ser_num_group = pp.id
												WHERE pm.ser_num = '".$sn."'");
		$pimbasicInfoArray = $pimbasicInfoObject->result_array();

		$pimbasicInfo = array();
		$pimtestResult = "";
		$pimmaxdataArray = array();
		
		//加$pimbasicInfoArray[0]["testtime"] != ""条件，因为上面的sql语句执行结果总不为空
		if(count($pimbasicInfoArray) != 0 && $pimbasicInfoArray[0]["testtime"] != "")
		{
			$pimbasicInfo = $pimbasicInfoArray[0];
			//取得极限值
			$limitLine = substr($pimbasicInfo["col12"], strrpos($pimbasicInfo["col12"], ":")+1);
			//取得所有值
			$pimdataObject = $this->db->query("SELECT pp.test_time,pa.value
									  FROM pim_label pl
									  JOIN pim_ser_num pm ON pm.pim_label = pl.id
									  JOIN pim_ser_num_group pp ON pp.pim_ser_num = pm.id
									  JOIN pim_ser_num_group_data pa ON pa.pim_ser_num_group = pp.id
									  WHERE pm.ser_num = '".$sn."'");
			$pimdataArray = $pimdataObject->result_array();
			//对数据处理，将同一测试时间的数据放到一组
			$pim_testtime = array();
			$pimdataFormart = array();
			foreach($pimdataArray as $value)
			{
				if(!in_array($value["test_time"], $pim_testtime))
				{
					$arr = array($value["value"]);
					$pimdataFormart[$value["test_time"]] = $arr;
					array_push($pim_testtime,$value["test_time"]);
				}
				else
				{
					array_push($pimdataFormart[$value["test_time"]],$value["value"]);
				}
			}
			//判断有几组数据大于极限值
			$i = 0;
			foreach($pimdataFormart as $value)
			{
				foreach($value as $val)
				{
					if($val >= $limitLine)
					{
						$i++;
						break;
					}
				}
			}
			//判断是否合格，0代表不合格，1代表合格
			if(count($pimdataFormart) == 1)
			{
				if($i > 0)
				{
					$pimtestResult = "不合格";
				}
				else
				{
					$pimtestResult = "合格";
				}
			}
			else
			{
				if($i >= 2)
				{
					$pimtestResult = "不合格";
				}
				else
				{
					$pimtestResult = "合格";
				}
			}
			//取得各组的最大值
			$pimmaxdataObject = $this->db->query("SELECT pp.test_time,pp.upload_date,MAX(pa.value) AS value FROM pim_ser_num pm
											JOIN pim_label pl ON pm.pim_label=pl.id
											JOIN pim_ser_num_group pp ON pp.pim_ser_num = pm.id
											JOIN pim_ser_num_group_data pa ON pa.pim_ser_num_group = pp.id
											AND pm.ser_num = '".$sn."'
											GROUP BY pp.test_time");
			$pimmaxdataArray = $pimmaxdataObject->result_array();
		}
		$this->smarty->assign("pimbasicInfo",$pimbasicInfo);
		$this->smarty->assign("pimtestResult",$pimtestResult);
		$this->smarty->assign("pimmaxdataArray",$pimmaxdataArray);

		$this->smarty->display("customerView.tpl");
	}
	
	//转换从数据库根据id,另一项项取出的数组，赋给页面下拉列表
	protected function array_switch($var1,$var2,$var3)
	{
		$arr = array(""=>$var3);
		foreach($var1 as $value)
		{
			$arr = $arr+array($value['id']=>$value[$var2]);
		}
		return $arr;
	}
}
