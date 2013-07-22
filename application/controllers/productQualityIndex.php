<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');
class ProductQualityIndex extends CW_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->_init();
	}
	
	private function _init()
	{
		//获得工序
		$processObj = $this->db->query("SELECT id,name FROM process WHERE name != '跳线'");
		$processArr = $processObj->result_array();
		$processArr = $this->array_switch($processArr, 'name', '(ALL)');
		$this->smarty->assign('processArr',$processArr);
		//取得产品型号
		$producttypeObject = $this->db->query("SELECT pe.id,pe.name FROM producttype pe
											   JOIN status ss ON pe.status = ss.id
											   AND ss.statusname = 'active'
											   ORDER BY pe.name");
		$producttypeArr = $producttypeObject->result_array();
		$producttypeArr = $this->array_switch($producttypeArr, "name", "(ALL)");
		$this->smarty->assign("producttypeArr",$producttypeArr);
		//取得盘号
		$platenumObj = $this->db->query("SELECT DISTINCT a.platenum
										 FROM
										 producttestinfo a
										");
		$platenumArr = $platenumObj->result_array();
		$platenumArr = $this->array_switch1($platenumArr, "platenum", "(ALL)");
		$this->smarty->assign("platenumArr",$platenumArr);
	}
	
	public function index($offset = 0, $limit = 30)
	{
		$startTimeSql = '';
		$endTimeSql = '';
		$searchTeststationSql = '';
		$searchProducttypeSql = '';
		$plateNumSql = '';
		//日期搜索条件
		$startTime = $this->input->post("startdate");
		if(!$this->_checkDateFormat($startTime))
		{
			$startTime = date("Y-m-01");
		}
		$startTimeSql = " WHERE a.testTime >= '".$startTime." 00:00:00'";
		$endTime = $this->input->post("enddate");
		if(!$this->_checkDateFormat($endTime))
		{
			$endTime = date("Y-m-d");
		}
		$endTimeSql = " AND a.testTime <= '".$endTime." 23:59:59'";
		//根据工序取得测试站搜索条件
		$testStationArr = array();
		$searchProcess = $this->input->post("process");
		if($searchProcess == '')
		{
			$testStationObj = $this->db->query("SELECT a.id AS teststationId
												FROM teststation a
											   	JOIN process b ON a.process = b.id
											   	JOIN status c ON a.status = c.id
											   	AND b.name IN ('成品','半成品')
											   	AND c.statusname = 'active'
											   ");
			$testStationArr = $testStationObj->result_array();
		}
		else
		{
			$testStationObj = $this->db->query("SELECT a.id AS teststationId
												FROM teststation a
												WHERE a.process = ".$searchProcess);
			$testStationArr = $testStationObj->result_array();
		}
		if(count($testStationArr) != 0)
		{
			$searchTeststationSql = " AND a.testStation IN (";
			foreach ($testStationArr as $value) 
			{
				$searchTeststationSql .= $value['teststationId'].",";
			}
			$searchTeststationSql = substr($searchTeststationSql, 0, -1).")";
		}
		else
		{
			$searchTeststationSql = " AND a.testStation = null";
		}
		//产品型号搜索条件
		$searchProducttype = $this->input->post("produdttype");
		if($searchProducttype != '')
		{
			$searchProducttypeSql = " AND a.productType = ".$searchProducttype;
		}
		//盘号搜索条件
		$searchPlatenum = $this->input->post("platenum");
		if($searchPlatenum != '')
		{
			$plateNumSql = " AND a.platenum = '".$searchPlatenum."'";
		}
		
		$resultSql = "SELECT a.id,a.sn,a.testTime
					  FROM producttestinfo a
					  ".$startTimeSql.$endTimeSql.$searchTeststationSql.$searchProducttypeSql.$plateNumSql.
					  " ORDER BY a.testTime DESC";
		$resultObj = $this->db->query($resultSql);
		$resultArr = $resultObj->result_array();
		
		
		$this->load->library('pagination');
		$config['full_tag_open'] = '<div class="locPage">';
		$config['full_tag_close'] = '</div>';
		$config['base_url'] = '';
		$config['uri_segment'] = 3;
		$config['total_rows'] = count($resultArr);
		$config['per_page'] = $limit;
		$this->pagination->initialize($config);
		
		$resultSql = $resultSql." LIMIT ".$offset.",".$limit;
		$resultObj = $this->db->query($resultSql);
		$resultArr = $resultObj->result_array();
		
		
		$zhubo11 = array();
		$zhubo12 = array();
		$zhubo13 = array();
		$zhubo21 = array();
		$zhubo22 = array();
		$zhubo23 = array();
		$shuaijian = array('shuaijian100' => array(),
						   'shuaijian150' => array(),
						   'shuaijian200' => array(),
						   'shuaijian280' => array(),
						   'shuaijian450' => array(),
						   'shuaijian800' => array(),
						   'shuaijian900' => array(),
						   'shuaijian1000' => array(),
						   'shuaijian1500' => array(),
						   'shuaijian1800' => array(),
						   'shuaijian2000' => array(),
						   'shuaijian2200' => array(),
						   'shuaijian2400' => array(),
						   'shuaijian2500' => array(),
						   'shuaijian3000' => array()
						   );
		$zukang = array();
		foreach ($resultArr as $key => $value) 
		{
			$prodducttestinfoId = $value['id'];
			//取得"驻波1"
			$zhubo1Obj = $this->db->query("SELECT a.mark,a.value
									   	 FROM 
									   	 testitemmarkvalue a
									   	 JOIN testitemresult b ON a.testItemResult = b.id
									   	 JOIN producttestinfo c ON b.productTestInfo = c.id
									   	 JOIN testitem d ON b.testItem = d.id
									   	 AND c.id = ".$prodducttestinfoId.
									   	 " AND d.name = '驻波1'");
			$zhubo1Arr = $zhubo1Obj->result_array();
			if(count($zhubo1Arr) != 0)
			{
				$resultArr[$key]['zhubo11'] =  array('value' => $zhubo1Arr[0]['value'],'mark' => $zhubo1Arr[0]['mark']);
				$resultArr[$key]['zhubo12'] =  array('value' => $zhubo1Arr[1]['value'],'mark' => $zhubo1Arr[1]['mark']);
				$resultArr[$key]['zhubo13'] =  array('value' => $zhubo1Arr[2]['value'],'mark' => $zhubo1Arr[2]['mark']);
				array_push($zhubo11,$zhubo1Arr[0]['value']);
				array_push($zhubo12,$zhubo1Arr[1]['value']);
				array_push($zhubo13,$zhubo1Arr[2]['value']);
			}
			else
			{
				$resultArr[$key]['zhubo11'] =  '';
				$resultArr[$key]['zhubo12'] =  '';
				$resultArr[$key]['zhubo13'] =  '';
			}
			//取得"驻波2"测试项的详细值
			$zhubo2Obj = $this->db->query("SELECT a.mark,a.value
									   	 FROM 
									   	 testitemmarkvalue a
									   	 JOIN testitemresult b ON a.testItemResult = b.id
									   	 JOIN producttestinfo c ON b.productTestInfo = c.id
									   	 JOIN testitem d ON b.testItem = d.id
									   	 AND c.id = ".$prodducttestinfoId.
									   	 " AND d.name = '驻波2'");
			$zhubo2Arr = $zhubo2Obj->result_array();
			if(count($zhubo2Arr) != 0)
			{
				$resultArr[$key]['zhubo21'] =  array('value' => $zhubo2Arr[0]['value'], 'mark' => $zhubo2Arr[0]['mark']);
				$resultArr[$key]['zhubo22'] =  array('value' => $zhubo2Arr[1]['value'], 'mark' => $zhubo2Arr[1]['mark']);
				$resultArr[$key]['zhubo23'] =  array('value' => $zhubo2Arr[2]['value'], 'mark' => $zhubo2Arr[2]['mark']);
				array_push($zhubo21,$zhubo2Arr[0]['value']);
				array_push($zhubo22,$zhubo2Arr[1]['value']);
				array_push($zhubo23,$zhubo2Arr[2]['value']);
			}
			else
			{
				$resultArr[$key]['zhubo21'] =  '';
				$resultArr[$key]['zhubo22'] =  '';
				$resultArr[$key]['zhubo23'] =  '';
			}
			//取得"衰减"测试项的详细值
			$shuaijianObj = $this->db->query("SELECT a.mark,a.value
									   	 FROM 
									   	 testitemmarkvalue a
									   	 JOIN testitemresult b ON a.testItemResult = b.id
									   	 JOIN producttestinfo c ON b.productTestInfo = c.id
									   	 JOIN testitem d ON b.testItem = d.id
									   	 AND c.id = ".$prodducttestinfoId.
									   	 " AND d.name = '衰减'");
			$shuaijianArr = $shuaijianObj->result_array();
			if(count($shuaijianArr) != 0)
			{
				$resultArr[$key]['shuaijian100'] =  '';
				$resultArr[$key]['shuaijian150'] =  '';
				$resultArr[$key]['shuaijian200'] =  '';
				$resultArr[$key]['shuaijian280'] =  '';
				$resultArr[$key]['shuaijian450'] =  '';
				$resultArr[$key]['shuaijian800'] =  '';
				$resultArr[$key]['shuaijian900'] =  '';
				$resultArr[$key]['shuaijian1000'] =  '';
				$resultArr[$key]['shuaijian1500'] =  '';
				$resultArr[$key]['shuaijian1800'] =  '';
				$resultArr[$key]['shuaijian2000'] =  '';
				$resultArr[$key]['shuaijian2200'] =  '';
				$resultArr[$key]['shuaijian2400'] =  '';
				$resultArr[$key]['shuaijian2500'] =  '';
				$resultArr[$key]['shuaijian3000'] =  '';
				foreach ($shuaijianArr as $k => $val) 
				{
					$unit = substr($val['mark'], -1);
					$markValue = substr($val['mark'], 0,-1);
					$testValue = $val['value'];
					if(strtolower($unit) == 'm')
					{
						$markValue = $markValue;
					}
					else
					{
						$markValue = $markValue*1000;
					}
					$resultArr[$key]['shuaijian'.$markValue] =  $testValue;
					array_push($shuaijian['shuaijian'.$markValue],$testValue);
				}
			}
			else
			{
				$resultArr[$key]['shuaijian100'] =  '';
				$resultArr[$key]['shuaijian150'] =  '';
				$resultArr[$key]['shuaijian200'] =  '';
				$resultArr[$key]['shuaijian280'] =  '';
				$resultArr[$key]['shuaijian450'] =  '';
				$resultArr[$key]['shuaijian800'] =  '';
				$resultArr[$key]['shuaijian900'] =  '';
				$resultArr[$key]['shuaijian1000'] =  '';
				$resultArr[$key]['shuaijian1500'] =  '';
				$resultArr[$key]['shuaijian1800'] =  '';
				$resultArr[$key]['shuaijian2000'] =  '';
				$resultArr[$key]['shuaijian2200'] =  '';
				$resultArr[$key]['shuaijian2400'] =  '';
				$resultArr[$key]['shuaijian2500'] =  '';
				$resultArr[$key]['shuaijian3000'] =  '';
			}
			//取得"时域阻抗"测试项的详细值
			$zukangObj = $this->db->query("SELECT a.mark,a.value
									   	 FROM 
									   	 testitemmarkvalue a
									   	 JOIN testitemresult b ON a.testItemResult = b.id
									   	 JOIN producttestinfo c ON b.productTestInfo = c.id
									   	 JOIN testitem d ON b.testItem = d.id
									   	 AND c.id = ".$prodducttestinfoId.
									   	 " AND d.name = '时域阻抗'");
			$zukangArr = $zukangObj->result_array();
			if(count($zukangArr) != 0)
			{
				$resultArr[$key]['zukang'] =  $zukangArr[0]['value'];
				array_push($zukang,$zukangArr[0]['value']);
			}
			else
			{
				$resultArr[$key]['zukang'] =  '';
			}
		}
		//处理最大值、最小值、平均值
		if(count($zhubo11) != 0)
		{
			$zhubo11Max = max($zhubo11);
			$zhubo11Min = min($zhubo11);
			$zhubo11Avg = round(array_sum($zhubo11)/count($zhubo11),2);
		}
		else
		{
			$zhubo11Max = '';
			$zhubo11Min = '';
			$zhubo11Avg = '';
		}
		if(count($zhubo12) != 0)
		{
			$zhubo12Max = max($zhubo12);
			$zhubo12Min = min($zhubo12);
			$zhubo12Avg = round(array_sum($zhubo12)/count($zhubo12),2);
		}
		else
		{
			$zhubo12Max = '';
			$zhubo12Min = '';
			$zhubo12Avg = '';
		}
		if(count($zhubo13) != 0)
		{
			$zhubo13Max = max($zhubo13);
			$zhubo13Min = min($zhubo13);
			$zhubo13Avg = round(array_sum($zhubo13)/count($zhubo13),2);
		}
		else
		{
			$zhubo13Max = '';
			$zhubo13Min = '';
			$zhubo13Avg = '';
		}
		if(count($zhubo21) != 0)
		{
			$zhubo21Max = max($zhubo21);
			$zhubo21Min = min($zhubo21);
			$zhubo21Avg = round(array_sum($zhubo21)/count($zhubo21),2);
		}
		else
		{
			$zhubo21Max = '';
			$zhubo21Min = '';
			$zhubo21Avg = '';
		}
		if(count($zhubo22) != 0)
		{
			$zhubo22Max = max($zhubo22);
			$zhubo22Min = min($zhubo22);
			$zhubo22Avg = round(array_sum($zhubo22)/count($zhubo22),2);
		}
		else
		{
			$zhubo22Max = '';
			$zhubo22Min = '';
			$zhubo22Avg = '';
		}
		if(count($zhubo23) != 0)
		{
			$zhubo23Max = max($zhubo23);
			$zhubo23Min = min($zhubo23);
			$zhubo23Avg = round(array_sum($zhubo23)/count($zhubo23),2);
		}
		else
		{
			$zhubo23Max = '';
			$zhubo23Min = '';
			$zhubo23Avg = '';
		}
		if(count($zukang) != 0)
		{
			$zukangMax = max($zukang);
			$zukangMin = min($zukang);
			$zukangAvg = round(array_sum($zukang)/count($zukang),2);
		}
		else
		{
			$zukangMax = '';
			$zukangMin = '';
			$zukangAvg = '';
		}
		
		$shuajianMax = array();
		$shuajianMin = array();
		$shuajianAvg = array();
		foreach ($shuaijian as $k1 => $val1) 
		{
			if(count($val1) != 0)
			{
				$shuajianMax[$k1] = max($val1);
				$shuajianMin[$k1] = min($val1);
				$shuajianAvg[$k1] = round(array_sum($val1)/count($val1),2);
			}
			else
			{
				$shuajianMax[$k1] = '';
				$shuajianMin[$k1] = '';
				$shuajianAvg[$k1] = '';
			}
		}
		$this->smarty->assign('resultArr',$resultArr);
		
		$this->smarty->assign('zhubo11Max',$zhubo11Max);
		$this->smarty->assign('zhubo11Min',$zhubo11Min);
		$this->smarty->assign('zhubo11Avg',$zhubo11Avg);
		
		$this->smarty->assign('zhubo12Max',$zhubo12Max);
		$this->smarty->assign('zhubo12Min',$zhubo12Min);
		$this->smarty->assign('zhubo12Avg',$zhubo12Avg);
		
		$this->smarty->assign('zhubo13Max',$zhubo13Max);
		$this->smarty->assign('zhubo13Min',$zhubo13Min);
		$this->smarty->assign('zhubo13Avg',$zhubo13Avg);
		
		$this->smarty->assign('zhubo21Max',$zhubo21Max);
		$this->smarty->assign('zhubo21Min',$zhubo21Min);
		$this->smarty->assign('zhubo21Avg',$zhubo21Avg);
		
		$this->smarty->assign('zhubo22Max',$zhubo22Max);
		$this->smarty->assign('zhubo22Min',$zhubo22Min);
		$this->smarty->assign('zhubo22Avg',$zhubo22Avg);
		
		$this->smarty->assign('zhubo23Max',$zhubo23Max);
		$this->smarty->assign('zhubo23Min',$zhubo23Min);
		$this->smarty->assign('zhubo23Avg',$zhubo23Avg);
		
		$this->smarty->assign('zukangMax',$zukangMax);
		$this->smarty->assign('zukangMin',$zukangMin);
		$this->smarty->assign('zukangAvg',$zukangAvg);
		
		$this->smarty->assign('shuajianMax',$shuajianMax);
		$this->smarty->assign('shuajianMin',$shuajianMin);
		$this->smarty->assign('shuajianAvg',$shuajianAvg);
		
		$this->smarty->assign('startTime', $startTime);
		$this->smarty->assign('endTime', $endTime);
		$this->smarty->assign('item', '同轴产品指标统计表');
		$this->smarty->assign('title', '同轴产品指标统计表');
		$this->smarty->display("productQualityIndex.tpl");
	}
	
	protected function array_switch($var1,$var2,$var3)
	{
		$arr = array(""=>$var3);
		foreach($var1 as $value)
		{
			$arr = $arr+array($value['id']=>$value[$var2]);
		}
		return $arr;
	}
	
	protected function array_switch1($var1,$var2,$var3)
	{
		$arr = array(""=>$var3);
		foreach($var1 as $value)
		{
			$arr = $arr + array($value[$var2]=>$value[$var2]);
		}
		return $arr;
	}
	
	//验证日期格式
	private function _checkDateFormat($date)
	{
		if(preg_match('/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/', $date, $parts))
		{
			if(checkdate($parts[2], $parts[3], $parts[1]))
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
	}
}