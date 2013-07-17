<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');
class QualitypassTongzhou extends CW_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->_init();
		$this->load->library('PHPExcel');
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
		//放行状态
		$passStatusArr = array("" => "(ALL)",
							   "3" => "已放行",
							   "1" => "未放行");
		$this->smarty->assign("passStatusArr",$passStatusArr);
	}
	
	public function index($offset = 0, $limit = 30)
	{
		$searchDateSql = '';
		$searchTeststationSql = '';
		$searchProducttypeSql = '';
		$searchPassStatusSql = '';
		$searchDate = $this->input->post("date");
		//日期搜索条件
		if(!$this->_checkDateFormat($searchDate))
		{
			$searchDate = date("Y-m-d");
		}
		$searchDateSql = " AND a.testTime > '".$searchDate." 00:00:00' AND a.testTime < '".$searchDate." 23:59:59'";
		
		//取得工序
		$searchProcess = $this->input->post("process");
		//根据工序取得测试站搜索条件
		$testStationArr = array();
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
			$searchProducttypeSql = " AND b.id = ".$searchProducttype;
		}
		
		$searchPassStatus = $this->input->post("passstatus");
		if($searchPassStatus == '')
		{
			$searchPassStatusSql = " AND ((a.result = 0 AND a.tag1 = 1) OR (a.result = 1 AND a.tag1 = 3))";
		}
		else
		{
			if($searchPassStatus == 3)
			{
				$searchPassStatusSql = " AND (a.result = 1 AND a.tag1 = 3)";
			}
			else
			{
				$searchPassStatusSql = " AND (a.result = 0 AND a.tag1 = 1)";
			}
		}
		
		$infoResultSql = "SELECT 
						  	a.id,a.testTime,a.lathe,a.platenum,a.innermeter,a.outmeter,a.sn,a.facadeorother,
						  	a.client,a.qualityengineersuggestion,a.responsibledepartment,a.qualitymanagerreview,
						  	a.headengineerreview,a.tag1,
						  	b.name AS producttypename
						  FROM producttestinfo a
						  JOIN producttype b ON a.productType = b.id
						  ".$searchDateSql.$searchTeststationSql.$searchProducttypeSql.$searchPassStatusSql.
						  " ORDER BY a.testTime DESC";
		$infoObj = $this->db->query($infoResultSql);
		$infoArr = $infoObj->result_array();

		$this->load->library('pagination');
		$config['full_tag_open'] = '<div class="locPage">';
		$config['full_tag_close'] = '</div>';
		$config['base_url'] = '';
		$config['uri_segment'] = 3;
		$config['total_rows'] = count($infoArr);
		$config['per_page'] = $limit;
		$this->pagination->initialize($config);
		
		$infoResultSql = $infoResultSql." LIMIT ".$offset.",".$limit;
		$infoObj = $this->db->query($infoResultSql);
		$infoArr = $infoObj->result_array();

		foreach ($infoArr as $key => $value) 
		{
			$prodducttestinfoId = $value['id'];
			//取得"驻波1","驻波2","回波损耗1","回波损耗2","时域阻抗","TDR电长度"测试项的详细值	
			$testitems = array("驻波1","驻波2","回波损耗1","回波损耗2","时域阻抗","TDR电长度");
			foreach ($testitems as $k => $val) 
			{
				$itemObj = $this->db->query("SELECT a.mark,a.value
										   FROM 
										   testitemmarkvalue a
										   JOIN testitemresult b ON a.testItemResult = b.id
										   JOIN producttestinfo c ON b.productTestInfo = c.id
										   JOIN testitem d ON b.testItem = d.id
										   AND c.id = ".$prodducttestinfoId.
										   " AND d.name = '".$val."'");
				$itemArr = $itemObj->result_array();
				$infoArr[$key][$val] = $itemArr;
			}
			//取得"衰减"测试项的结果
			$shuaijianResultObj = $this->db->query("SELECT a.testResult
										   			FROM 
										   			testitemresult a
										   			JOIN producttestinfo b ON a.productTestInfo = b.id
										   			JOIN testitem c ON a.testItem = c.id
										   			AND b.id = ".$prodducttestinfoId.
										   			" AND c.name = '衰减'");
			$shuaijianResultArr = $shuaijianResultObj->result_array();
			$infoArr[$key]['衰减'] = $shuaijianResultArr;
		}
		$this->smarty->assign('infoArr',$infoArr);
		
		$this->smarty->assign('searchDate',$searchDate);
		$this->smarty->assign('item', '质量放行-同轴');
		$this->smarty->assign('title', '质量放行-同轴');
		$this->smarty->display("qualitypassTongzhou.tpl");
	}
	
	public function saveQualityPass()
	{
		$record = $_POST;
		$totoalnum = $record['totoalnum'];
		if($totoalnum != 0)
		{
			for($i = 1; $i <= $totoalnum; $i++)
			{
				$id = $record['id'.$i];
				$facadeorother = $record['facadeorother'.$i];
				$client = $record['client'.$i];
				$qualityengineersuggestion = $record['qualityengineersuggestion'.$i];
				$responsibledepartment = $record['responsibledepartment'.$i];
				$qualitymanagerreview = $record['qualitymanagerreview'.$i];
				$headengineerreview = $record['headengineerreview'.$i];
				if(isset($record['qualitypass'.$i]))
				{
					//放行
					$qualitypass = $record['qualitypass'.$i];
					$qualitypassSql = "a.result = 1, a.tag1 = 3 ";
				}
				else
				{
					//不放行
					$qualitypassSql = "a.result = 0, a.tag1 = 1 ";
				}
				/*
				echo "UPDATE producttestinfo a
								 SET 
								 a.facadeorother = '".$facadeorother."',
								 a.client = '".$client."',
								 a.qualityengineersuggestion = '".$qualityengineersuggestion."',
								 a.responsibledepartment = '".$responsibledepartment."',
								 a.qualitymanagerreview = '".$qualitymanagerreview."',
								 a.headengineerreview = '".$headengineerreview."',
								 ".$qualitypassSql."
								 WHERE a.id = ".$id."
								 ";
				*/
				$this->db->query("UPDATE producttestinfo a
								  SET 
								  a.facadeorother = '".$facadeorother."',
								  a.client = '".$client."',
								  a.qualityengineersuggestion = '".$qualityengineersuggestion."',
								  a.responsibledepartment = '".$responsibledepartment."',
								  a.qualitymanagerreview = '".$qualitymanagerreview."',
								  a.headengineerreview = '".$headengineerreview."',
								  ".$qualitypassSql."
								  WHERE a.id = ".$id."
								 ");
			}
		}
		
		$this->index();
	}
	
	public function exportResult()
	{
		$searchDateSql = '';
		$searchTeststationSql = '';
		$searchProducttypeSql = '';
		$searchPassStatusSql = '';
		$searchDate = $this->input->post("date");
		//日期搜索条件
		if(!$this->_checkDateFormat($searchDate))
		{
			$searchDate = date("Y-m-d");
		}
		$searchDateSql = " AND a.testTime > '".$searchDate." 00:00:00' AND a.testTime < '".$searchDate." 23:59:59'";
		
		//取得工序
		$searchProcess = $this->input->post("process");
		//根据工序取得测试站搜索条件
		$testStationArr = array();
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
			$searchProducttypeSql = " AND b.id = ".$searchProducttype;
		}
		
		$searchPassStatus = $this->input->post("passstatus");
		if($searchPassStatus == '')
		{
			$searchPassStatusSql = " AND ((a.result = 0 AND a.tag1 = 1) OR (a.result = 1 AND a.tag1 = 3))";
		}
		else
		{
			if($searchPassStatus == 3)
			{
				$searchPassStatusSql = " AND (a.result = 1 AND a.tag1 = 3)";
			}
			else
			{
				$searchPassStatusSql = " AND (a.result = 0 AND a.tag1 = 1)";
			}
		}
		
		$infoResultSql = "SELECT 
						  	a.id,a.testTime,a.lathe,a.platenum,a.innermeter,a.outmeter,a.sn,a.facadeorother,
						  	a.client,a.qualityengineersuggestion,a.responsibledepartment,a.qualitymanagerreview,
						  	a.headengineerreview,a.tag1,
						  	b.name AS producttypename
						  FROM producttestinfo a
						  JOIN producttype b ON a.productType = b.id
						  ".$searchDateSql.$searchTeststationSql.$searchProducttypeSql.$searchPassStatusSql.
						  " ORDER BY a.testTime DESC";
		$infoObj = $this->db->query($infoResultSql);
		$infoArr = $infoObj->result_array();
		foreach ($infoArr as $key => $value) 
		{
			$prodducttestinfoId = $value['id'];
			//取得"驻波1","驻波2","回波损耗1","回波损耗2","时域阻抗","TDR电长度"测试项的详细值	
			$testitems = array("驻波1","驻波2","回波损耗1","回波损耗2","时域阻抗","TDR电长度");
			foreach ($testitems as $k => $val) 
			{
				$itemObj = $this->db->query("SELECT a.mark,a.value
										   FROM 
										   testitemmarkvalue a
										   JOIN testitemresult b ON a.testItemResult = b.id
										   JOIN producttestinfo c ON b.productTestInfo = c.id
										   JOIN testitem d ON b.testItem = d.id
										   AND c.id = ".$prodducttestinfoId.
										   " AND d.name = '".$val."'");
				$itemArr = $itemObj->result_array();
				$infoArr[$key][$val] = $itemArr;
			}
			//取得"衰减"测试项的结果
			$shuaijianResultObj = $this->db->query("SELECT a.testResult
										   			FROM 
										   			testitemresult a
										   			JOIN producttestinfo b ON a.productTestInfo = b.id
										   			JOIN testitem c ON a.testItem = c.id
										   			AND b.id = ".$prodducttestinfoId.
										   			" AND c.name = '衰减'");
			$shuaijianResultArr = $shuaijianResultObj->result_array();
			$infoArr[$key]['衰减'] = $shuaijianResultArr;
		}
		
		error_reporting(E_ALL);
		ini_set('display_errors', TRUE);
		ini_set('display_startup_errors', TRUE);
		date_default_timezone_set('Asia/Shanghai');
		
		// Create new PHPExcel object
		$objPHPExcel = new PHPExcel();
		// Add some data
		$objPHPExcel->setActiveSheetIndex(0)
		            ->setCellValue('A1', '序号')
		            ->setCellValue('B1', '车台')
		            ->setCellValue('C1', '盘号')
		            ->setCellValue('D1', '长度(km)')
					->setCellValue('E1', '序列号')
					->setCellValue('F1', '内外端')
					->setCellValue('G1', '驻波/回波损耗')
					->setCellValue('J1', '衰减')
					->setCellValue('K1', '时域阻抗')
					->setCellValue('L1', 'TDR电长度')
					->setCellValue('M1', '外观及其他')
					->setCellValue('N1', '客户')
					->setCellValue('O1', '质量工程师/技术部意见')
					->setCellValue('P1', '责任部门')
					->setCellValue('Q1', '质量经理审核')
					->setCellValue('R1', '总工审核');
		$objPHPExcel->getActiveSheet()
					->mergeCells('G1:H1')
					->mergeCells('G1:I1');
		if(count($infoArr) != 0)
		{
			$i = 2;
			foreach ($infoArr as $key => $value) 
			{
				$num = substr($value['testTime'], -8, -6);
				if( 0 <= $num && $num <= 7)
				{
					$num = ($key+1)."C";
				}
				elseif (15 <= $num && $num <= 23) 
				{
					$num = ($key+1)."B";
				}
				else
				{
					$num = ($key+1)."A";
				}
				
				$zhuboOrSunhao11 = '';
				$zhuboOrSunhao12 = '';
				$zhuboOrSunhao13 = '';
				$zhuboOrSunhao21 = '';
				$zhuboOrSunhao22 = '';
				$zhuboOrSunhao23 = '';
				if(count($value['驻波1']) != 0)
				{
					$zhuboOrSunhao11 .= $value['驻波1'][0]['mark'].'/'.$value['驻波1'][0]['value'];
					$zhuboOrSunhao12 .= $value['驻波1'][1]['mark'].'/'.$value['驻波1'][1]['value'];
					$zhuboOrSunhao13 .= $value['驻波1'][2]['mark'].'/'.$value['驻波1'][2]['value'];
				}
				if(count($value['回波损耗1']) != 0)
				{
					$zhuboOrSunhao11 .= $value['回波损耗1'][0]['mark'].'/'.$value['回波损耗1'][0]['value'];
					$zhuboOrSunhao12 .= $value['回波损耗1'][1]['mark'].'/'.$value['回波损耗1'][1]['value'];
					$zhuboOrSunhao13 .= $value['回波损耗1'][2]['mark'].'/'.$value['回波损耗1'][2]['value'];
				}
				if(count($value['驻波2']) != 0)
				{
					$zhuboOrSunhao21 .= $value['驻波2'][0]['mark'].'/'.$value['驻波2'][0]['value'];
					$zhuboOrSunhao22 .= $value['驻波2'][1]['mark'].'/'.$value['驻波2'][1]['value'];
					$zhuboOrSunhao23 .= $value['驻波2'][2]['mark'].'/'.$value['驻波2'][2]['value'];
				}
				if(count($value['回波损耗2']) != 0)
				{
					$zhuboOrSunhao21 .= $value['回波损耗2'][0]['mark'].'/'.$value['回波损耗2'][0]['value'];
					$zhuboOrSunhao22 .= $value['回波损耗2'][1]['mark'].'/'.$value['回波损耗2'][1]['value'];
					$zhuboOrSunhao23 .= $value['回波损耗2'][2]['mark'].'/'.$value['回波损耗2'][2]['value'];
				}
				
				$shuaijian = '';
				if(count($value['衰减']) != 0)
				{
					if($value['衰减'][0]['testResult'] == 0)
					{
						$shuaijian = '不合格';
					}
					else
					{
						$shuaijian = '合格';
					}
				}
				
				$zukang = '';
				if(count($value['时域阻抗']) != 0)
				{
					foreach ($value['时域阻抗'] as $k => $val) 
					{
						$zukang .= $val['mark'].'/'.$val['value'];
					}
				}
				
				$tdrLength = '';
				if(count($value['TDR电长度']) != 0)
				{
					foreach ($value['TDR电长度'] as $k => $val) 
					{
						$tdrLength .= $val['mark'].'/'.$val['value'];
					}
				}
				
				$objPHPExcel->setActiveSheetIndex(0)
		            		->setCellValue('A'.$i, $num)
		            		->setCellValue('B'.$i, $value['lathe'])
		            		->setCellValue('C'.$i, $value['platenum'])
		            		->setCellValue('D'.$i, abs($value['innermeter']-$value['outmeter']))
							->setCellValue('E'.$i, $value['sn'])
							->setCellValue('F'.$i, '内端')
							->setCellValue('F'.($i+1), '外端')
							->setCellValue('G'.$i, $zhuboOrSunhao11)
							->setCellValue('H'.$i, $zhuboOrSunhao12)
							->setCellValue('I'.$i, $zhuboOrSunhao13)
							->setCellValue('G'.($i+1), $zhuboOrSunhao21)
							->setCellValue('H'.($i+1), $zhuboOrSunhao22)
							->setCellValue('I'.($i+1), $zhuboOrSunhao23)
							->setCellValue('J'.$i, $shuaijian)
							->setCellValue('K'.$i, $zukang)
							->setCellValue('L'.$i, $tdrLength)
							->setCellValue('M'.$i, $value['facadeorother'])
							->setCellValue('N'.$i, $value['client'])
							->setCellValue('O'.$i, $value['qualityengineersuggestion'])
							->setCellValue('P'.$i, $value['responsibledepartment'])
							->setCellValue('Q'.$i, $value['qualitymanagerreview'])
							->setCellValue('R'.$i, $value['headengineerreview']);
					
				$objPHPExcel->getActiveSheet()
						    ->mergeCells('A'.$i.':A'.($i+1))
							->mergeCells('B'.$i.':B'.($i+1))
							->mergeCells('C'.$i.':C'.($i+1))
							->mergeCells('D'.$i.':D'.($i+1))
							->mergeCells('E'.$i.':E'.($i+1))
							->mergeCells('J'.$i.':J'.($i+1))
							->mergeCells('K'.$i.':K'.($i+1))
							->mergeCells('L'.$i.':L'.($i+1))
							->mergeCells('M'.$i.':M'.($i+1))
							->mergeCells('N'.$i.':N'.($i+1))
							->mergeCells('O'.$i.':O'.($i+1))
							->mergeCells('P'.$i.':P'.($i+1))
							->mergeCells('Q'.$i.':Q'.($i+1))
							->mergeCells('R'.$i.':R'.($i+1));
				$i = $i+2;
			}
		}
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="result.xls"');
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