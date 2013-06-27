<?php
if (!defined('BASEPATH'))
	exit('no direct script access allowed');
class Vna_pim extends CW_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->_init();
		$this->load->library("Pagefenye");
	}

	private function _init()
	{
		$hourList = array(''=>'');
		for ($i = 0; $i <= 23; $i++)
		{
			$arr = array($i=>$i);
			$hourList = array_merge_recursive($hourList, $arr);
		}
		$this->smarty->assign("hourList", $hourList);
		$minuteList = array(''=>'');
		for ($i = 0; $i <= 59; $i++)
		{
			$arr = array($i=>$i);
			$minuteList = array_merge_recursive($minuteList, $arr);
		}
		$this->smarty->assign("minuteList", $minuteList);
		$testResultList = array(
			''=>'(ALL)',
			'0'=>'FAIL',
			'1'=>'PASS'
		);
		$this->smarty->assign('testResultList', $testResultList);
		//取得测试站
		$teststationObject = $this->db->query("SELECT tn.id,tn.name FROM teststation tn 
											   JOIN status ss ON tn.status = ss.id
											   AND ss.statusname = 'active'
											   ORDER BY tn.name
											   ");
		$teststationArray = $teststationObject->result_array();
		$teststation = $this->array_switch($teststationArray, "name", "(ALL)");
		$this->smarty->assign("teststation",$teststation);
		//取得测试设备
		$equipmentObject = $this->db->query("SELECT et.id,et.sn FROM equipment et 
											   JOIN status ss ON et.status = ss.id
											   AND ss.statusname = 'active'");
		$equipmentArray = $equipmentObject->result_array();
		$equipment = $this->array_switch($equipmentArray, "sn", "(ALL)");
		$this->smarty->assign("equipment",$equipment);
		//取得测试者
		$vnatesterObject = $this->db->query("SELECT tr.id,tr.employeeid FROM tester tr 
											   JOIN status ss ON tr.status = ss.id
											   JOIN tester_section tn ON tr.tester_section = tn.id
											   AND tn.name = 'VNA'
											   AND ss.statusname = 'active'");
		$vnatesterArray = $vnatesterObject->result_array();
		$vnatester = $this->array_switch($vnatesterArray, "employeeid", "(ALL)");
		$this->smarty->assign("vnatester",$vnatester);
		/*
		$pimtesterObject = $this->db->query("SELECT tr.id,tr.employeeid FROM tester tr 
											   JOIN status ss ON tr.status = ss.id
											   JOIN tester_section tn ON tr.tester_section = tn.id
											   AND tn.name = 'PIM'
											   AND ss.statusname = 'active'");
		$pimtesterArray = $pimtesterObject->result_array();
		$pimtester = $this->array_switch($pimtesterArray, "employeeid", "(ALL)");
		$this->smarty->assign("pimtester",$pimtester);
		 * 
		 */
		//取得产品型号
		$producttypeObject = $this->db->query("SELECT pe.id,pe.name FROM producttype pe
											   JOIN status ss ON pe.status = ss.id
											   AND ss.statusname = 'active'
											   ORDER BY pe.name");
		$producttypeArray = $producttypeObject->result_array();
		$producttype = $this->array_switch($producttypeArray, "name", "(ALL)");
		$this->smarty->assign("producttype",$producttype);
		
	}
	
	public function vna($offset = 1, $limit = 30)
	{
		$current_item = $this->input->post("current_item");
		$current_page = $this->input->post("current_page");
		$current_action = $this->input->post("current_action");
		$timeFrom1 = emptyToNull($this->input->post("timeFrom1"));
		if ($timeFrom1 == null)
		{
			$timeFrom1 = 1900;
		}
		$timeFrom2 = emptyToNull($this->input->post("timeFrom2"));
		if ($timeFrom2 == null)
		{
			$timeFrom2 = 0;
		}
		$timeFrom3 = emptyToNull($this->input->post("timeFrom3"));
		if ($timeFrom3 == null)
		{
			$timeFrom3 = 0;
		}
		$timeFrom = $timeFrom1." ".$timeFrom2.":".$timeFrom3;
		$timeTo1 = emptyToNull($this->input->post("timeTo1"));
		if ($timeTo1 == null)
		{
			$timeTo1 = 2999;
		}
		$timeTo2 = emptyToNull($this->input->post("timeTo2"));
		if ($timeTo2 == null)
		{
			$timeTo2 = 00;
		}
		$timeTo3 = emptyToNull($this->input->post("timeTo3"));
		if ($timeTo3 == null)
		{
			$timeTo3 = 00;
		}
		$timeTo = $timeTo1." ".$timeTo2.":".$timeTo3;
		$testResult = emptyToNull($this->input->post('testResult'));
		$sn = emptyToNull($this->input->post('sn'));
		$teststation = emptyToNull($this->input->post('teststation'));
		$equipment = emptyToNull($this->input->post('equipment'));
		$labelnum = emptyToNull($this->input->post('labelnum'));
		$producttype = emptyToNull($this->input->post('producttype'));
		$ordernum = emptyToNull($this->input->post('ordernum'));
		$tester = emptyToNull($this->input->post('tester'));
		$platenum = emptyToNull($this->input->post('platenum'));
		
		$timeFromSql=" AND po.testTime >= '".$timeFrom."'";
		$timeToSql = " AND po.testTime <= '".$timeTo."'";
		$testResultSql = "";
		$snSql = "";
		$teststationSql = "";
		$equipmentSql = "";
		$labelnumSql = "";
		$producttypeSql = "";
		$testerSql = "";
		$platenumSql = "";
		
		$pim_timeFromSql=" AND pp.test_time >= '".$timeFrom."'";
		$pim_timeToSql = " AND pp.test_time <= '".$timeTo."'";
		$pim_testResultSql = "";
		$pim_snSql = "";
		$pim_teststationSql = "";
		$pim_labelnumSql = "";
		$pim_producttypeSql = "";
		$pim_ordernumSql = "";
		$pim_testerSql = ""; 
		if($testResult != null)
		{
			if($testResult == 0 || $testResult == 1)
			{
				$testResultSql = " AND po.result = ".$testResult;
			}
			else
			{
				$testResultSql = " AND 0 ";
			}
		}
		if($sn != null)
		{
			$start = strpos($sn, "+");
			$end = strripos($sn, "+");
			
			if(strlen($sn) == 1)
			{
				$snSql = " AND po.sn LIKE '%".$sn."%' ";
				$pim_snSql = " AND pm.ser_num LIKE '%".$sn."%' ";
			}
			else
			{
				if($start == 0 && $end == (strlen($sn)-1))
				{
					$sn = substr($sn, 1,strlen($sn)-2);
					$snSql = " AND po.sn = '".$sn."' ";
					$pim_snSql = " AND pm.ser_num = '".$sn."' ";
				}
				else
				{
					$snSql = " AND po.sn LIKE '%".$sn."%' ";
					$pim_snSql = " AND pm.ser_num LIKE '%".$sn."%' ";
				}
			}
			
		}
		if($equipment != null)
		{
			$equipmentSnObj = $this->db->query("SELECT sn FROM equipment WHERE id = '".$equipment."'");
			$equipmentSn = $equipmentSnObj->first_row()->sn;
			$equipmentSql = " AND po.equipmentSn = '".$equipmentSn."' ";
		}
		if($teststation != null)
		{
			$teststationSql = " AND po.testStation = '".$teststation."' ";
		}
		if($producttype != null)
		{
			$producttypeSql = " AND po.productType = '".$producttype."' ";
		}
		if($tester != null)
		{
			$testerSql = " AND po.tester = '".$tester."' ";
		}
		if($platenum != null)
		{
			$platenumSql = " AND po.platenum LIKE '%".$platenum."%' ";
		}
		if($labelnum != null)
		{
			$labelnumSql = " AND po.workorder LIKE '%".$labelnum."%' ";
			$pim_labelnumSql = " AND pl.name like '%".$labelnum."%' ";
		}
		
		$vnaResultSql = "SELECT po.result,po.id,po.testTime,po.equipmentSn,po.workorder,po.tag,po.tag1,tn.name AS testStation,tr.employeeid AS tester,pe.name AS productType,po.sn 
						 FROM producttestinfo po
						 JOIN teststation tn ON po.testStation = tn.id
						 JOIN tester tr ON po.tester = tr.id
						 JOIN producttype pe ON po.productType = pe.id
						 ".$timeFromSql.$timeToSql.$testResultSql.$snSql.$teststationSql.$equipmentSql.$producttypeSql.$testerSql.$platenumSql.$labelnumSql."
						 ORDER BY po.testTime DESC";
		
		$vnaResultObject = $this->db->query($vnaResultSql);
		$vnaResultArray = $vnaResultObject->result_array();
		$this->smarty->assign("vnaCount",count($vnaResultArray)-($offset-1)*$limit);
		$vnaFenye = $this->pagefenye->getFenye($offset, count($vnaResultArray), $limit, 3);
		$vnaResultSql = $vnaResultSql." LIMIT ".($offset-1)*$limit.",".$limit;
		$vnaResultObject = $this->db->query($vnaResultSql);
		$vnaResultArray = $vnaResultObject->result_array();
		
		$pimResultSql = "SELECT t.id,MAX(t.test_time) AS test_time,t.col12,t.upload_date,t.model,t.ser_num,t.work_num,t.name
							FROM (SELECT pm.id,pm.col12,pp.test_time,pp.upload_date,pm.model,pm.ser_num,pm.work_num,pl.name 
								  FROM pim_ser_num pm
								  JOIN pim_label pl ON pm.pim_label = pl.id 
								  JOIN pim_ser_num_group pp ON pp.pim_ser_num = pm.id
								  ".$pim_timeFromSql.$pim_timeToSql.$pim_snSql.$pim_labelnumSql."
								  ) t
							GROUP BY t.id
							ORDER BY t.test_time DESC";
		$pimResultObject = $this->db->query($pimResultSql);
		$pimResultArray = $pimResultObject->result_array();
		
		//处理pim结果
		if(count($pimResultArray) != 0)
		{
			foreach($pimResultArray as $key=>$value)
			{
				//取得极限值
				$limitLine = substr($value["col12"], strrpos($value["col12"], ":")+1);
				//取得pim_ser_num的id
				$pim_ser_num_id = $value['id'];
				//取得所有值
				$pimdataObject = $this->db->query("SELECT pp.test_time,pa.value
										  FROM pim_label pl
										  JOIN pim_ser_num pm ON pm.pim_label = pl.id
										  JOIN pim_ser_num_group pp ON pp.pim_ser_num = pm.id
										  JOIN pim_ser_num_group_data pa ON pa.pim_ser_num_group = pp.id
										  WHERE pm.id = '".$pim_ser_num_id."'");
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
				//pim判断有几组数据大于极限值
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
				//pim判断是否合格，0代表不合格，1代表合格
				if(count($pimdataFormart) == 1)
				{
					if($i > 0)
					{
						$pimtestResult = "0";
					}
					else
					{
						$pimtestResult = "1";
					}
				}
				else
				{
					if($i >= 2)
					{
						$pimtestResult = "0";
					}
					else
					{
						$pimtestResult = "1";
					}
				}
				//将结果放入已查询出的数组最后
				$pimResultArray[$key]["result"] = $pimtestResult;
			}
			//根据用户所选pim结果过滤条件，对pim结果数组处理
			if($testResult == "0")
			{
				foreach ($pimResultArray as $key => $value) 
				{
					if($value["result"] == "1")
					{
						unset($pimResultArray[$key]);
					}
				}
			}
			else if($testResult == "1")
			{
				foreach ($pimResultArray as $key => $value) 
				{
					if($value["result"] == "0")
					{
						unset($pimResultArray[$key]);
					}
				}
			}
			else
			{
				
			}
		}
		
		//总数，供tpl页面中序列号计数用
		$this->smarty->assign("pimCount",count($pimResultArray));
		$pimFenye = $this->pagefenye->getFenye(1,count($pimResultArray),$limit,3);
		$pimResultArray = array_slice($pimResultArray, 0 , $limit);
		
		$this->smarty->assign("vnaResultArray",$vnaResultArray);
		$this->smarty->assign("vnaFenye", $vnaFenye);
		$this->smarty->assign("pimResultArray", $pimResultArray);
		$this->smarty->assign("pimFenye", $pimFenye);
		$this->smarty->assign("item","VNA测试记录");
		$this->smarty->assign("title","VNA测试记录");
		$this->smarty->display("vna_pim.tpl");
	}

	public function pim($offset = 1, $limit = 30)
	{
		$current_item = $this->input->post("current_item");
		$current_page = $this->input->post("current_page");
		$current_action = $this->input->post("current_action");
		$timeFrom1 = emptyToNull($this->input->post("timeFrom1"));
		if ($timeFrom1 == null)
		{
			$timeFrom1 = 1900;
		}
		$timeFrom2 = emptyToNull($this->input->post("timeFrom2"));
		if ($timeFrom2 == null)
		{
			$timeFrom2 = 0;
		}
		$timeFrom3 = emptyToNull($this->input->post("timeFrom3"));
		if ($timeFrom3 == null)
		{
			$timeFrom3 = 0;
		}
		$timeFrom = $timeFrom1." ".$timeFrom2.":".$timeFrom3;
		$timeTo1 = emptyToNull($this->input->post("timeTo1"));
		if ($timeTo1 == null)
		{
			$timeTo1 = 2999;
		}
		$timeTo2 = emptyToNull($this->input->post("timeTo2"));
		if ($timeTo2 == null)
		{
			$timeTo2 = 00;
		}
		$timeTo3 = emptyToNull($this->input->post("timeTo3"));
		if ($timeTo3 == null)
		{
			$timeTo3 = 00;
		}
		$timeTo = $timeTo1." ".$timeTo2.":".$timeTo3;
		$testResult = emptyToNull($this->input->post('testResult'));
		$sn = emptyToNull($this->input->post('sn'));
		$teststation = emptyToNull($this->input->post('teststation'));
		$equipment = emptyToNull($this->input->post('equipment'));
		$labelnum = emptyToNull($this->input->post('labelnum'));
		$producttype = emptyToNull($this->input->post('producttype'));
		$ordernum = emptyToNull($this->input->post('ordernum'));
		$tester = emptyToNull($this->input->post('tester'));
		$platenum = emptyToNull($this->input->post('platenum'));
		
		$timeFromSql=" AND po.testTime >= '".$timeFrom."'";
		$timeToSql = " AND po.testTime <= '".$timeTo."'";
		$testResultSql = "";
		$snSql = "";
		$teststationSql = "";
		$equipmentSql = "";
		$labelnumSql = "";
		$producttypeSql = "";
		$testerSql = "";
		$platenumSql = "";
		
		$pim_timeFromSql=" AND pp.test_time >= '".$timeFrom."'";
		$pim_timeToSql = " AND pp.test_time <= '".$timeTo."'";
		$pim_testResultSql = "";
		$pim_snSql = "";
		$pim_teststationSql = "";
		$pim_labelnumSql = "";
		$pim_producttypeSql = "";
		$pim_ordernumSql = "";
		$pim_testerSql = "";
		
		if($testResult != null)
		{
			if($testResult == 0 || $testResult == 1)
			{
				$testResultSql = " AND po.result = ".$testResult;
			}
			else
			{
				$testResultSql = " AND 0 ";
			}
		}
		if($sn != null)
		{
			$start = strpos($sn, "+");
			$end = strripos($sn, "+");
			if(strlen($sn) == 1)
			{
				$snSql = " AND po.sn LIKE '%".$sn."%' ";
				$pim_snSql = " AND pm.ser_num LIKE '%".$sn."%' ";
			}
			else
			{
				if($start == 0 && $end == (strlen($sn)-1))
				{
					$sn = substr($sn, 1,strlen($sn)-2);
					$snSql = " AND po.sn = '".$sn."' ";
					$pim_snSql = " AND pm.ser_num = '".$sn."' ";
				}
				else
				{
					$snSql = " AND po.sn LIKE '%".$sn."%' ";
					$pim_snSql = " AND pm.ser_num LIKE '%".$sn."%' ";
				}
			}
			
		}
		if($teststation != null)
		{
			$teststationSql = " AND po.testStation = '".$teststation."' ";
		}
		if($equipment != null)
		{
			$equipmentSnObj = $this->db->query("SELECT sn FROM equipment WHERE id = '".$equipment."'");
			$equipmentSn = $equipmentSnObj->first_row()->sn;
			$equipmentSql = " AND po.equipmentSn = '".$equipmentSn."' ";
		}
		if($producttype != null)
		{
			$producttypeSql = " AND po.productType = '".$producttype."' ";
		}
		if($tester != null)
		{
			$testerSql = " AND po.tester = '".$tester."' ";
		}
		if($platenum != null)
		{
			$platenumSql = " AND po.platenum LIKE '%".$platenum."%' ";
		}
		if($labelnum != null)
		{
			$labelnumSql = " AND po.workorder LIKE '%".$labelnum."%' ";
			$pim_labelnumSql = " AND pl.name like '%".$labelnum."%' ";
		}
		
		$vnaResultSql = "SELECT po.result,po.id,po.testTime,po.equipmentSn,po.workorder,po.tag,po.tag1,tn.name AS testStation,tr.employeeid AS tester,pe.name AS productType,po.sn 
						 FROM producttestinfo po
						 JOIN teststation tn ON po.testStation = tn.id
						 JOIN tester tr ON po.tester = tr.id
						 JOIN producttype pe ON po.productType = pe.id
						 ".$timeFromSql.$timeToSql.$testResultSql.$snSql.$teststationSql.$equipmentSql.$producttypeSql.$testerSql.$platenumSql.$labelnumSql."
						 ORDER BY po.testTime DESC";
		$vnaResultObject = $this->db->query($vnaResultSql);
		$vnaResultArray = $vnaResultObject->result_array();
		$this->smarty->assign("vnaCount",count($vnaResultArray));
		$vnaFenye = $this->pagefenye->getFenye(1, count($vnaResultArray), $limit, 3);
		$vnaResultSql = $vnaResultSql." LIMIT 0,".$limit;
		$vnaResultObject = $this->db->query($vnaResultSql);
		$vnaResultArray = $vnaResultObject->result_array();
		
		$pimResultSql = "SELECT t.id,MAX(t.test_time) AS test_time,t.col12,t.upload_date,t.model,t.ser_num,t.work_num,t.name
							FROM (SELECT pm.id,pm.col12,pp.test_time,pp.upload_date,pm.model,pm.ser_num,pm.work_num,pl.name 
								  FROM pim_ser_num pm 
								  JOIN pim_label pl ON pm.pim_label = pl.id 
								  JOIN pim_ser_num_group pp ON pp.pim_ser_num = pm.id
								  ".$pim_timeFromSql.$pim_timeToSql.$pim_snSql.$pim_labelnumSql."
								  ) t
							GROUP BY t.id
							ORDER BY t.test_time DESC";
		$pimResultObject = $this->db->query($pimResultSql);
		$pimResultArray = $pimResultObject->result_array();
		
		//处理pim结果
		if(count($pimResultArray) != 0)
		{
			foreach($pimResultArray as $key=>$value)
			{
				//取得极限值
				$limitLine = substr($value["col12"], strrpos($value["col12"], ":")+1);
				//取得pim_ser_num的id
				$pim_ser_num_id = $value['id'];
				//取得所有值
				$pimdataObject = $this->db->query("SELECT pp.test_time,pa.value
										  FROM pim_label pl
										  JOIN pim_ser_num pm ON pm.pim_label = pl.id
										  JOIN pim_ser_num_group pp ON pp.pim_ser_num = pm.id
										  JOIN pim_ser_num_group_data pa ON pa.pim_ser_num_group = pp.id
										  WHERE pm.id = '".$pim_ser_num_id."'");
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
						$pimtestResult = "0";
					}
					else
					{
						$pimtestResult = "1";
					}
				}
				else
				{
					if($i >= 2)
					{
						$pimtestResult = "0";
					}
					else
					{
						$pimtestResult = "1";
					}
				}
				//将结果放入已查询出的数组最后
				$pimResultArray[$key]["result"] = $pimtestResult;
			}
			//根据用户所选pim结果过滤条件，对pim结果数组处理
			if($testResult == "0")
			{
				foreach ($pimResultArray as $key => $value) 
				{
					if($value["result"] == "1")
					{
						unset($pimResultArray[$key]);
					}
				}
			}
			else if($testResult == "1")
			{
				foreach ($pimResultArray as $key => $value) 
				{
					if($value["result"] == "0")
					{
						unset($pimResultArray[$key]);
					}
				}
			}
			else
			{
				//do noting
			}
		}
		
		$this->smarty->assign("pimCount",count($pimResultArray)-($offset-1)*$limit);
		$pimFenye = $this->pagefenye->getFenye($offset,count($pimResultArray),$limit,3);
		$pimResultArray = array_slice($pimResultArray, ($offset-1)*$limit,$limit);
		
		$this->smarty->assign("vnaResultArray",$vnaResultArray);
		$this->smarty->assign("vnaFenye", $vnaFenye);
		$this->smarty->assign("pimResultArray", $pimResultArray);
		$this->smarty->assign("pimFenye", $pimFenye);
		$this->smarty->assign("item","PIM测试记录");
		$this->smarty->assign("title","PIM测试记录");
		$this->smarty->display("vna_pim.tpl");
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
