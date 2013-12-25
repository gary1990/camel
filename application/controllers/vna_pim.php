<?php
if (!defined('BASEPATH'))
	exit('no direct script access allowed');
class Vna_pim extends CW_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->_init();
		$this->load->library('PHPExcel');
		$this->load->library("Pagefenye");
		$this->load->library("zip");
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
			$timeFrom1 = date("Y-m-d");
		}
		//$current_time = date("H:i:s");
		//echo $current_time;
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
		$timeFrom = $timeFrom1." ".$timeFrom2.":".$timeFrom3.":0";
		$timeTo1 = emptyToNull($this->input->post("timeTo1"));
		if ($timeTo1 == null)
		{
			$timeTo1 = date("Y-m-d");
		}
		$timeTo2 = emptyToNull($this->input->post("timeTo2"));
		if ($timeTo2 == null)
		{
			$timeTo2 = 23;
		}
		$timeTo3 = emptyToNull($this->input->post("timeTo3"));
		if ($timeTo3 == null)
		{
			$timeTo3 = 59;
		}
		$timeTo = $timeTo1." ".$timeTo2.":".$timeTo3.":59";
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
		$pim_limitSql = "";
		if($testResult != null)
		{
			if($testResult == 0 || $testResult == 1)
			{
				$testResultSql = " AND po.result = ".$testResult;
				$pim_testResultSql = " AND pm.result = ".$testResult;
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
		
		$pimResultSql = "SELECT pm.result,pm.id,pm.col12,MAX(pp.test_time) AS test_time,pp.upload_date,pm.model,pm.ser_num,pm.work_num,pl.name 
						  FROM pim_ser_num pm
						  JOIN pim_label pl ON pm.pim_label = pl.id 
						  JOIN pim_ser_num_group pp ON pp.pim_ser_num = pm.id
						  ".$pim_timeFromSql.$pim_timeToSql.$pim_snSql.$pim_labelnumSql.$pim_testResultSql."
						  GROUP BY pm.id
						  ORDER BY pp.test_time DESC
						";
		$pimResultObject = $this->db->query($pimResultSql);
		$pimResultArray = $pimResultObject->result_array();

		$this->smarty->assign("pimCount",count($pimResultArray));
		$pimFenye = $this->pagefenye->getFenye(1, count($pimResultArray), $limit, 3);
		$pimResultSql = $pimResultSql." LIMIT 0,".$limit;
		$pimResultObject = $this->db->query($pimResultSql);
		$pimResultArray = $pimResultObject->result_array();
		
		$this->smarty->assign("timeFrom1",$timeFrom1);
		$this->smarty->assign("timeFrom2", $timeFrom2);
		$this->smarty->assign("timeFrom3", $timeFrom3);
		$this->smarty->assign("timeTo1",$timeTo1);
		$this->smarty->assign("timeTo2", $timeTo2);
		$this->smarty->assign("timeTo3", $timeTo3);
		
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
			$timeFrom1 = date("Y-m-d");
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
		$timeFrom = $timeFrom1." ".$timeFrom2.":".$timeFrom3.":0";
		$timeTo1 = emptyToNull($this->input->post("timeTo1"));
		if ($timeTo1 == null)
		{
			$timeTo1 = date("Y-m-d");
		}
		$timeTo2 = emptyToNull($this->input->post("timeTo2"));
		if ($timeTo2 == null)
		{
			$timeTo2 = 23;
		}
		$timeTo3 = emptyToNull($this->input->post("timeTo3"));
		if ($timeTo3 == null)
		{
			$timeTo3 = 59;
		}
		$timeTo = $timeTo1." ".$timeTo2.":".$timeTo3.":59";
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
		$pim_limitSql = "";
		
		if($testResult != null)
		{
			if($testResult == 0 || $testResult == 1)
			{
				$testResultSql = " AND po.result = ".$testResult;
				$pim_testResultSql = " AND pm.result = ".$testResult;
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
		
		$pimResultSql = "SELECT pm.result,pm.id,pm.col12,MAX(pp.test_time) AS test_time,pp.upload_date,pm.model,pm.ser_num,pm.work_num,pl.name 
						  FROM pim_ser_num pm 
						  JOIN pim_label pl ON pm.pim_label = pl.id 
						  JOIN pim_ser_num_group pp ON pp.pim_ser_num = pm.id
						  ".$pim_timeFromSql.$pim_timeToSql.$pim_snSql.$pim_labelnumSql.$pim_testResultSql."
						  GROUP BY pm.id
						  ORDER BY pp.test_time DESC
						  ";
		$pimResultObject = $this->db->query($pimResultSql);
		$pimResultArray = $pimResultObject->result_array();
		
		$this->smarty->assign("pimCount",count($pimResultArray)-($offset-1)*$limit);
		$pimFenye = $this->pagefenye->getFenye($offset, count($pimResultArray), $limit, 3);
		$pimResultSql = $pimResultSql." LIMIT ".($offset-1)*$limit.",".$limit;
		$pimResultObject = $this->db->query($pimResultSql);
		$pimResultArray = $pimResultObject->result_array();
		
		$this->smarty->assign("timeFrom1",$timeFrom1);
		$this->smarty->assign("timeFrom2", $timeFrom2);
		$this->smarty->assign("timeFrom3", $timeFrom3);
		$this->smarty->assign("timeTo1",$timeTo1);
		$this->smarty->assign("timeTo2", $timeTo2);
		$this->smarty->assign("timeTo3", $timeTo3);
		
		$this->smarty->assign("vnaResultArray",$vnaResultArray);
		$this->smarty->assign("vnaFenye", $vnaFenye);
		$this->smarty->assign("pimResultArray", $pimResultArray);
		$this->smarty->assign("pimFenye", $pimFenye);
		$this->smarty->assign("item","PIM测试记录");
		$this->smarty->assign("title","PIM测试记录");
		$this->smarty->display("vna_pim.tpl");
	}

	public function pim_result()
	{
		
		set_time_limit(0);
		$pim_nullObj = $this->db->query("SELECT a.id,a.result FROM pim_ser_num a WHERE a.result IS NULL");
		$pim_nullArr = $pim_nullObj->result_array();
		if(count($pim_nullArr) != 0)
		{
			foreach ($pim_nullArr as $key => $value) 
			{
				$pim_ser_num = $value['id'];
				$pim_failcountSql = "
							SELECT t.id,COUNT(CASE WHEN t.value=1 THEN 0 ELSE NULL END) AS failcount FROM
							(
								SELECT a.id,MAX(c.value) > SUBSTRING(a.col12,13) AS value
								FROM
								pim_ser_num a
								JOIN pim_label pl ON a.pim_label = pl.id 
								JOIN pim_ser_num_group b ON b.pim_ser_num = a.id
								JOIN pim_ser_num_group_data c ON c.pim_ser_num_group = b.id
								AND a.id = ".$pim_ser_num."
								GROUP BY b.test_time
							) t
							GROUP BY t.id
							";
				$pim_failcountObj = $this->db->query($pim_failcountSql);
				$pim_failcountArr = $pim_failcountObj->result_array();
				$result = NULL;
				if(count($pim_failcountArr) != 0)
				{
					$failCount = $pim_failcountArr[0]['failcount'];
					if($failCount > 1)
					{
						$result = 0;
					}
					elseif($failCount == 1 || $failCount == 0)
					{
						$result = 1;
					}
					else
					{
						
					}
					$this->db->query("UPDATE pim_ser_num a SET a.result = ".$result." WHERE a.id = ".$pim_ser_num);
				}
			}
			$pim_nullLastObj = $this->db->query("SELECT a.id,a.result FROM pim_ser_num a WHERE a.result IS NULL");
			$pim_nullLastArr = $pim_nullLastObj->result_array();
			echo count($pim_nullLastArr)." record lasted";
		}
		else
		{
			echo "No record need calculate";
		}
	}

	public function export_vna()
	{
		set_time_limit(0);
		
		//获得选中产品测试项的id,name
		$testItemSql = "SELECT a.id,a.name FROM testitem a 
						JOIN status b ON a.status = b.id
						AND b.statusname = 'active'";
		$testitemObject = $this->db->query($testItemSql);
		$testitemArray = $testitemObject->result_array();
		
		//根据当前用户填选状况查到满足情况的SN
		$timeFrom1 = $this->input->post("timeFrom1");
		if($timeFrom1 == "")
		{
			$timeFrom1 = date("Y-m-d");
		}
		$timeFrom2 = $this->input->post("timeFrom2");
		if($timeFrom2 == "")
		{
			$timeFrom2 = "00";
		}
		$timeFrom3 = $this->input->post("timeFrom3");
		if($timeFrom3 == "")
		{
			$timeFrom3 = "00";
		}
		$timeTo1 = $this->input->post("timeTo1");
		if($timeTo1 == "")
		{
			$timeTo1 = date("Y-m-d");
		}
		$timeTo2 = $this->input->post("timeTo2");
		if($timeTo2 == "")
		{
			$timeTo2 = "23";
		}
		$timeTo3 = $this->input->post("timeTo3");
		if($timeTo3 == "")
		{
			$timeTo3 = "59";
		}
		$timeFrom = $timeFrom1." ".$timeFrom2.":".$timeFrom3;
		$timeTo = $timeTo1." ".$timeTo2.":".$timeTo3;
		
		$producttype = $this->input->post("producttype");

		$timeConditionSql = " AND (a.testTime >= '".$timeFrom."' AND a.testTime <= '".$timeTo."')";
		if($timeFrom != "1900-01-01 00:00" || $timeTo != "2999-01-01 00:00")
		{
			$timeConditionSql = " AND a.testTime >= '".$timeFrom."' AND a.testTime <= '".$timeTo."'";
		}

		$producttypeSql = "";

		if($producttype != null)
		{
			$producttypeSql = " AND b.id = '".$producttype."'";
		}
		
		$vnaTotalSnSql = "SELECT a.id,a.sn AS productsn,a.result,b.name AS producttypename,a.tag
						  FROM producttestinfo a
						  JOIN producttype b ON a.productType = b.id
						   ".$timeConditionSql.$producttypeSql."
						  ORDER BY a.testTime DESC
						  ";
//echo $vnaTotalSnSql;
		$packingTotalSnObject = $this->db->query($vnaTotalSnSql);
		$packingTotalSnArray= $packingTotalSnObject->result_array();
//$packingTotalSnArray = array();
		/*
		$packingTotalSnSql = "SELECT DISTINCT pt.id,pt.productsn,pt.boxsn,pt.result,pt.tag
		                          FROM packingresult pt 
		                          JOIN tester tr ON pt.packer=tr.employeeid 
								  LEFT JOIN producttestinfo po ON pt.productsn = po.sn
								  LEFT JOIN producttype pe ON po.productType = pe.id
							 	  ".$timeConditionSql.$packBoxSql.$producttypeSql.$productSnSql.$orderNumSql.$packerSql.$testResultSql." 
							      ORDER BY pt.packingtime DESC";
		 * 
		 */
		//$packingTotalSnObject = $this->db->query($packingTotalSnSql);
		//$packingTotalSnArray= $packingTotalSnObject->result_array();
		
		//遍历得到的序列号数组
		if(count($packingTotalSnArray) == 0)
		{
			$this->_returnUploadFailed("查询数据为空");
			return;
		}
		else
		{
			date_default_timezone_set('Asia/Shanghai');
			$dateStamp = date("YmdHis");
			$dateInReport = date("Y年m月d日");
			
			if(PHP_OS == 'WINNT')
			{
				$slash = "\\";
				$downloadRoot = getcwd().$slash."assets".$slash."downloadedSource";
			}
			else
			{
				$this->_returnUploadFailed("错误的服务器操作系统");
				return;
			}
			
			//创建文件下载的根目录downloadedSource
			if(file_exists($downloadRoot) && is_dir($downloadRoot))
			{
				//do nothing
			}
			else
			{
				if(mkdir($downloadRoot))
				{
				}
				else
				{
					$this->_returnUploadFailed("文件下载目录创建失败");
					return;
				}
			}
			//创建当前下载的文件夹
			$currdownloadRoot = $downloadRoot.$slash.$dateStamp;
			if(file_exists($currdownloadRoot) && is_dir($currdownloadRoot))
			{
				//do noting
			}
			else
			{
				if(mkdir($currdownloadRoot))
				{
					//拷贝公司logo
					$logoRoot = getcwd().$slash."resource".$slash."img".$slash."logo.png";
					if(file_exists($logoRoot))
					{
						copy($logoRoot,$currdownloadRoot.$slash."logo.png");
					}
					else
					{
						$this->_returnUploadFailed($logoRoot."公司logo不存在");
						return;
					}
				}
				else
				{
					$this->_returnUploadFailed("创建下载根目录时出错");
					return;
				}
			}
			//获取生产厂家名称
			$producterRoot = getcwd().$slash."resource".$slash."producter.txt";
			
			if(file_exists($producterRoot))
			{
				$producterName = file_get_contents($producterRoot);
			}
			else
			{
				$this->_returnUploadFailed($producterRoot."未找到");
				return;
			}
			
			//创建html文件，先写index.html
			$indexHandle = fopen($currdownloadRoot.$slash."index.html", "a");
			fwrite($indexHandle, '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
								  <html xmlns="http://www.w3.org/1999/xhtml">
									<head>
										<meta http-equiv="content-type" content="text/html;charset=utf-8">
										<style type="text/css">
											body{border:0px;margin:0px}
											a{text-decoration:none;}
											.container{width:1024px;margin:0px auto;border:1px solid black;padding:15px;}
											img{width:60px;height:30px;}
											table{border-collapse:collapse;}
											table, td, th{border:1px solid black;}
										</style>
									</head>
										<body>
											<div class="container">
												<div class="top">
													<div style="float:left;width:45%;"><img src="./logo.png"/></div>
													<div style="font-weight:bold;font-size:28px;text-align:left">质量报告</div>
												</div>
												<div style="margin-top:30px;margin-bottom:10px;">
													<div style="text-align:left;padding-left:70%;">
														<span>生产厂家：'.iconv("gbk", "utf-8", $producterName).'</span>
													</div>
													<div style="text-align:left;padding-left:70%;">
														<span>报告日期：'.$dateInReport.'</span>
													</div>
												</div>'
												);
			fwrite($indexHandle,'<div class="content" style="padding-left:10px;padding-right:10px;font-size:13px;">
								');
			fwrite($indexHandle, '<table style="width:100%;"><tr><th>序号</th><th>产品型号</th><th>产品序列号</th><th>检测结果</th>');
			//index.html中写入表头<th>部分的测试项--用户所选
			if(count($testitemArray) == 0)
			{
				//do noting
			}
			else
			{
				//循环写入vna测试项--用户所选
				foreach($testitemArray as $value)
				{
					fwrite($indexHandle, '<th>'.$value['name'].'</th>');
				}
			}
			fwrite($indexHandle, "</tr>");
			//循环得到的序列号数组sn数组
			foreach($packingTotalSnArray as $key=>$value)
			{
				fwrite($indexHandle, '<tr><td>'.($key+1).'</td>');
				//取得产品序列号
				$producttestinfoId = $value['id'];
				$sn = $value['productsn'];
				//取得测试结果
				$result = $value['result'];
				//取得标志位
				$packTag = $value['tag'];
				//取得产品类型
				$producttype = $value['producttypename'];
				/*
				//取得产品类型
				$producttypeObject = $this->db->query("SELECT pe.name FROM producttestinfo po 
								  					   JOIN producttype pe ON po.productType = pe.id
								                       AND po.sn = '".$sn."'
								                       AND po.tag = '".$packTag."'");

				$producttypeArray = $producttypeObject->result_array();
				if(count($producttypeArray) == 0)
				{
					$producttype = "";
				}
				else
				{
					$producttype = $producttypeArray[0]["name"];
				}
				*/
				//index.html中写入产品类型，产品型号，序列号
				fwrite($indexHandle, '<td>'.$producttype.'</td><td>'.$sn.'</td>');
				//index.html中写入检测结果
				if($result == "1")
				{
					fwrite($indexHandle, '<td><span style="color:green;"><b>合格</b></span></td>');
				}
				else if($result == "0")
				{
					fwrite($indexHandle, '<td style="color:red"><b>不合格</b></td>');
				}
				
				
				//写入各vna测试项最大值--用户所选
				if(count($testitemArray) == 0)
				{
					//do noting
				}
				else
				{
					//循环	用户所选的测试项
					foreach($testitemArray as $value)
					{
						$testitemId = $value['id'];
						
						//判断当前测试项，是否包含在当前产品实际测试项中
						$maxvalueObject = $this->db->query("SELECT MAX(te.value) AS value FROM testitemmarkvalue te
						 				  					JOIN testitemresult tt ON te.testItemResult = tt.id
						 				  					JOIN producttestinfo po ON tt.productTestInfo = po.id
						 				  					AND po.id = '".$producttestinfoId."'
						 				  					AND tt.testItem = '".$testitemId."'
						 				 					");
						$maxvalueArray = $maxvalueObject->result_array();
						
						if(count($maxvalueArray) == 0)
						{
							fwrite($indexHandle, '<td>&nbsp;</td>');
						}
						else
						{
							$maxvalue = $maxvalueArray[0]['value'];
							fwrite($indexHandle, '<td>'.$maxvalue.'</td>');
						}
					}
				}
				
				fwrite($indexHandle, "</tr>");
			}

			fwrite($indexHandle, "</table>");
			fwrite($indexHandle, '</div></div></body></html>');
			fclose($indexHandle);
			
			exec('C:\Progra~1\7-Zip\7z.exe a -tzip '.$currdownloadRoot.'.zip '.$currdownloadRoot);
			$this->delDirAndFile($currdownloadRoot);
			
			$fileRoot = $currdownloadRoot.".zip";
			$fileName = $dateStamp.".zip";

			if(!file_exists($fileRoot))
			{
				die("Error:File not found.");
			}
			else
			{
				header("Pragma: public");
       			header("Expires: 0");
        		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        		header("Cache-Control: public");
        		header("Content-Description: File Transfer");
        		header("Content-type: application/octet-stream");
        		header("Content-Disposition: attachment; filename=\"" . $fileName . "\"");
        		header("Content-Transfer-Encoding: binary");
        		header("Content-Length: " . filesize($fileRoot));
        		ob_end_flush();
				@readfile($fileRoot);
			}	
		}
	}

	public function exportExcel_vna()
	{
		set_time_limit(0);
		
		//获得选中产品测试项的id,name
		$testItemSql = "SELECT a.id,a.name FROM testitem a 
						JOIN status b ON a.status = b.id
						AND b.statusname = 'active'";
		$testitemObject = $this->db->query($testItemSql);
		$testitemArray = $testitemObject->result_array();
		
		//根据当前用户填选状况查到满足情况的SN
		$timeFrom1 = $this->input->post("timeFrom1");
		if($timeFrom1 == "")
		{
			$timeFrom1 = date("Y-m-d");
		}
		$timeFrom2 = $this->input->post("timeFrom2");
		if($timeFrom2 == "")
		{
			$timeFrom2 = "00";
		}
		$timeFrom3 = $this->input->post("timeFrom3");
		if($timeFrom3 == "")
		{
			$timeFrom3 = "00";
		}
		$timeTo1 = $this->input->post("timeTo1");
		if($timeTo1 == "")
		{
			$timeTo1 = date("Y-m-d");
		}
		$timeTo2 = $this->input->post("timeTo2");
		if($timeTo2 == "")
		{
			$timeTo2 = "23";
		}
		$timeTo3 = $this->input->post("timeTo3");
		if($timeTo3 == "")
		{
			$timeTo3 = "59";
		}
		$timeFrom = $timeFrom1." ".$timeFrom2.":".$timeFrom3;
		$timeTo = $timeTo1." ".$timeTo2.":".$timeTo3;
		
		$producttype = $this->input->post("producttype");

		$timeConditionSql = " AND (a.testTime >= '".$timeFrom."' AND a.testTime <= '".$timeTo."')";
		if($timeFrom != "1900-01-01 00:00" || $timeTo != "2999-01-01 00:00")
		{
			$timeConditionSql = " AND a.testTime >= '".$timeFrom."' AND a.testTime <= '".$timeTo."'";
		}

		$producttypeSql = "";

		if($producttype != null)
		{
			$producttypeSql = " AND b.id = '".$producttype."'";
		}
		
		$vnaTotalSnSql = "SELECT a.id,a.sn AS productsn,a.result,b.name AS producttypename,a.tag
						  FROM producttestinfo a
						  JOIN producttype b ON a.productType = b.id
						   ".$timeConditionSql.$producttypeSql."
						  ORDER BY a.testTime DESC
						  ";
		//echo $vnaTotalSnSql;
		$packingTotalSnObject = $this->db->query($vnaTotalSnSql);
		$packingTotalSnArray= $packingTotalSnObject->result_array();
		
		//遍历得到的序列号数组
		if(count($packingTotalSnArray) == 0)
		{
			$this->_returnUploadFailed("查询数据为空");
			return;
		}
		else
		{
			error_reporting(E_ALL);
			ini_set('display_errors', TRUE);
			ini_set('display_startup_errors', TRUE);
			date_default_timezone_set('Asia/Shanghai');
			
			$objPHPExcel = new PHPExcel();
			
			$objPHPExcel->setActiveSheetIndex(0)
			            ->setCellValueByColumnAndRow(0, 1, "序号")
						->setCellValueByColumnAndRow(1, 1, "产品型号")
						->setCellValueByColumnAndRow(2, 1, "产品序列号")
						->setCellValueByColumnAndRow(3, 1, "检测结果");
			//写入测试项
			if(count($testitemArray) == 0)
			{
				//do noting
			}
			else
			{
				//Excel第一行标题中循环写入vna测试项，从第五列开始
				$i = 4;
				foreach($testitemArray as $value)
				{
					$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($i, 1, $value['name']);
					$i++;
				}
			}
			
			$objPHPExcel->getActiveSheet()->setTitle('Sheet1');
			
			//循环得到的序列号数组sn数组
			$rowStart = 2;//从第二行开始
			foreach($packingTotalSnArray as $key=>$value)
			{
				//取得产品序列号
				$producttestinfoId = $value['id'];
				$sn = $value['productsn'];
				//取得测试结果
				$result = $value['result'];
				//取得标志位
				$packTag = $value['tag'];
				//取得产品类型
				$producttype = $value['producttypename'];
				
				//Excel中写入序号，产品型号，序列号
				$objPHPExcel->setActiveSheetIndex(0)
			            	->setCellValueByColumnAndRow(0, $rowStart, $rowStart-1)
							->setCellValueByColumnAndRow(1, $rowStart, $producttype)
							->setCellValueByColumnAndRow(2, $rowStart, $sn." ");
				//Excel中写入检测结果
				if($result == "1")
				{
					$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(3, $rowStart, "合格");
				}
				else if($result == "0")
				{
					$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(3, $rowStart, "不合格");
				}
				
				//Excel写入各vna测试项最大值--用户所选
				if(count($testitemArray) == 0)
				{
					//do noting
				}
				else
				{				
					//循环	用户所选的测试项
					$colStart = 4;//从第五列开始写入个测试项的值
					foreach($testitemArray as $value)
					{
						$testitemId = $value['id'];
						
						//判断当前测试项，是否包含在当前产品实际测试项中
						$maxvalueObject = $this->db->query("SELECT MAX(te.value) AS value FROM testitemmarkvalue te
						 				  					JOIN testitemresult tt ON te.testItemResult = tt.id
						 				  					JOIN producttestinfo po ON tt.productTestInfo = po.id
						 				  					AND po.id = '".$producttestinfoId."'
						 				  					AND tt.testItem = '".$testitemId."'
						 				  					
						 				 					");
						$maxvalueArray = $maxvalueObject->result_array();
						
						if(count($maxvalueArray) == 0)
						{
							$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($colStart, $rowStart, "");
						}
						else
						{
							$maxvalue = $maxvalueArray[0]['value'];
							$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($colStart, $rowStart, $maxvalue);
						}
						
						$colStart++;//下一个测试项
					}
				}
				$rowStart++;//下一行
			}

			// Redirect output to a client’s web browser (Excel5)
			
			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename="qualityreport2.xls"');
			header('Cache-Control: max-age=0');
			// If you're serving to IE 9, then the following may be needed
			header('Cache-Control: max-age=1');
			
			// If you're serving to IE over SSL, then the following may be needed
			header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
			header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
			header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
			header ('Pragma: public'); // HTTP/1.0
			
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
			$objWriter->save('php://output');
			exit;
		}
	}

	private function _returnUploadFailed($err)
	{
		$this->load->helper('xml');
		$dom = xml_dom();
		$uploadResult = xml_add_child($dom, 'uploadResult');
		xml_add_child($uploadResult, 'result', 'false');
		xml_add_child($uploadResult, 'info', $err);
		xml_print($dom);
	}
	
	//循环删除目录和文件函数
	private function delDirAndFile($dirName)
	{
		if (PHP_OS == 'WINNT')
		{
			$slash = "\\";
		}
		else if (PHP_OS == 'Darwin')
		{
			$slash = "/";
		}
		if (file_exists($dirName))
		{
			if ($handle = opendir($dirName))
			{
				while (false !== ($item = readdir($handle)))
				{
					if ($item != "." && $item != "..")
					{
						if (is_dir($dirName.$slash.$item))
						{
							$this->delDirAndFile($dirName.$slash.$item);
						}
						else
						{
							unlink($dirName.$slash.$item);
						}
					}
				}
				closedir($handle);
				rmdir($dirName);
			}
		}
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
