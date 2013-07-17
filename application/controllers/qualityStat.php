<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');
class QualityStat extends CW_Controller
{
	public function __construct()
	{
		parent::__construct();
		//判断当前登录用户
		$userrole = $this->session->userdata("userrole");
		if($userrole == 'user')
		{
			redirect(base_url().'index.php/login/toIndex');
		}
		$this->_init();
	}
	
	private function _init()
	{
		//获得工序
		$processObj = $this->db->query("SELECT id,name FROM process WHERE name != '跳线'");
		$processArr = $processObj->result_array();
		$processArr = $this->array_switch($processArr, 'name', '(ALL)');
		$this->smarty->assign('processArr',$processArr);
		//取得车台
		$latheObj = $this->db->query("SELECT DISTINCT lathe FROM producttestinfo");
		$latheArr = $latheObj->result_array();
		$latheArr = $this->array_switch1($latheArr, 'lathe', '(ALL)');
		$this->smarty->assign('latheArr',$latheArr);
		//取得产品型号
		$producttypeObject = $this->db->query("SELECT pe.id,pe.name FROM producttype pe
											   JOIN status ss ON pe.status = ss.id
											   AND ss.statusname = 'active'
											   ORDER BY pe.name");
		$producttypeArr = $producttypeObject->result_array();
		$producttypeArr = $this->array_switch($producttypeArr, "name", "(ALL)");
		$this->smarty->assign("producttypeArr",$producttypeArr);
	}
	
	public function index($offset = 0, $limit = 30)
	{
		$errorMsg = '';
		$startTimeSql = '';
		$endTimeSql = '';
		$searchTeststationSql = '';
		$searchProducttypeSql = '';
		$latheSql = '';
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
		//车台搜索条件
		$searchLathe = $this->input->post("lathe");
		if($searchLathe != '')
		{
			$latheSql = " AND a.lathe = '".$searchLathe."'";
		}
		
		//保存所有质量损失费用比例数组
		$qualityLossArry = array();
		//取得“驻波1”，“回波损耗1”，“驻波2”，“回波损耗2”质量损失比
		$generalQualityLossObj = $this->db->query("SELECT qualitylosspercentval FROM qualitylosspercent");
		$generalQualityLossArr = $generalQualityLossObj->result_array();
		foreach ($generalQualityLossArr as $key => $value) 
		{
			$qualityLossArry['general'.($key+1)] = substr($value['qualitylosspercentval'], 0, -1);
		}
		//取得除去“驻波1”，“驻波2”，“回波损耗1”，“回波损耗2”测试项的其他测试项
		$totalTestitemObj = $this->db->query("SELECT DISTINCT b.id,b.name,b.qualitylosspercent
		    								  FROM testitem b
		    								  JOIN testitemresult c ON c.testItem = b.id
		    								  JOIN producttestinfo a ON c.productTestInfo = a.id".
		    								  $startTimeSql.$endTimeSql.$searchTeststationSql.$latheSql."
		    								  AND b.name NOT IN ('驻波1','驻波2','回波损耗1','回波损耗2')
		    								  AND a.result = 0
		    								 ");
		$totalTestitemArray = $totalTestitemObj->result_array();
		foreach ($totalTestitemArray as $key => $value) 
		{
			if($value['qualitylosspercent'] != '')
			{
				$qualityLossArry[$value['name']] = substr($value['qualitylosspercent'], 0, -1);
			}
			else
			{
				$errorMsg .= "测试项:'".$value['name']."'的质量损失费用比例不能为空！";
				$this->smarty->assign('errorMsg', $errorMsg);
				$this->smarty->assign('startTime', $startTime);
				$this->smarty->assign('endTime', $endTime);
				$this->smarty->assign('item', '质量统计');
				$this->smarty->assign('title', '质量统计');
				$this->smarty->display("qualityStat.tpl");
				return;
			}
		}
//print_r($qualityLossArry);
		//取得所有车台、产品型号的组合
		$lathe_producttypeSql = "SELECT 
								 DISTINCT a.lathe, a.productType, b.name AS producttypename
								 FROM producttestinfo a
								 JOIN producttype b ON a.productType = b.id"
								 .$startTimeSql.$endTimeSql.$searchTeststationSql.$latheSql."
								 AND a.result = 0 AND a.tag1 = 1
								 ORDER BY a.lathe ASC
								";
		$lathe_producttypeObj = $this->db->query($lathe_producttypeSql);
		$lathe_producttypeArr = $lathe_producttypeObj->result_array();
		
		$this->load->library('pagination');
		$config['full_tag_open'] = '<div class="locPage">';
		$config['full_tag_close'] = '</div>';
		$config['base_url'] = '';
		$config['uri_segment'] = 3;
		$config['total_rows'] = count($lathe_producttypeArr);
		$config['per_page'] = $limit;
		$this->pagination->initialize($config);
		
		$lathe_producttypeSql = $lathe_producttypeSql." LIMIT ".$offset.",".$limit;
		$lathe_producttypeObj = $this->db->query($lathe_producttypeSql);
		$lathe_producttypeArr = $lathe_producttypeObj->result_array();
		
//echo $lathe_producttypeSql;
//print_r($lathe_producttypeArr);
		//结果数组
		$resultArr = array();
		//统计数组
		$totalStat = array('totallength' => 0,'faillength' => 0,'passpercent' => 0);
		for($i = 1; $i <= count($generalQualityLossArr); $i++)
		{
			$totalStat['general'.$i] = 0;
		}
		foreach ($totalTestitemArray as $kstat => $valstat) 
		{
			$totalStat[$valstat['name']] = 0;
		}

		if(count($lathe_producttypeArr) != 0)
		{
			foreach ($lathe_producttypeArr as $key => $value) 
			{
				$resultArr[$key] = array('lathe' => $value['lathe'],
										 'producttypename' => $value['producttypename'],
										 'totallength' => 0,
										 'faillength' => 0,
										 'passpercent' => 0
										 );
				//各测试项不合格量
				foreach ($qualityLossArry as $k1 => $val1) 
				{
					$resultArr[$key][$k1] = 0;
				}
				//当前车台、产品型号下的总记录
				$totalResultSql = "SELECT a.id,a.result,a.innermeter,a.outmeter
								   FROM 
								   producttestinfo a ".$startTimeSql.$endTimeSql.$searchTeststationSql.$latheSql."
								   AND a.lathe = '".$value['lathe']."' AND a.productType = ".$value['productType']."
								  ";
				$totalResultObj = $this->db->query($totalResultSql);
				$totalResultArr = $totalResultObj->result_array();
//print_r($totalResultArr);
				//遍历各条记录
				foreach ($totalResultArr as $k2 => $val2)
				{
					//计入总产量
					$resultArr[$key]['totallength'] += abs($val2['innermeter']-$val2['outmeter']);
					//不合格
					if($val2['result'] == 0)
					{
						//计入不合格产量
						$resultArr[$key]['faillength'] += abs($val2['innermeter']-$val2['outmeter']);
						//不合格记录id
						$failid = $val2['id'];
						//取得不合格记录详细测试项
						$failTestitemDetailObj = $this->db->query("SELECT
																   a.id AS testitemresultid,b.name AS testitemname,a.testResult
																   FROM testitemresult a
																   JOIN testitem b ON a.testItem = b.id
																   AND a.productTestInfo = ".$failid."
						  										   AND a.testResult = 0
																   ");
						$failTestitemDetailArr = $failTestitemDetailObj->result_array();
//print_r($failTestitemDetailArr);
						//临时保存不合格项的数组
						$tempFailArr = array();
						foreach ($failTestitemDetailArr as $k3 => $val3) 
						{
							$failTestitemname = $val3['testitemname'];
							if($failTestitemname == '驻波1' || $failTestitemname == '驻波2')
							{
								//取得不合格测试项的详细值
								$failTestitemValueObj = $this->db->query("SELECT a.id,a.mark,a.value
																		   FROM testitemmarkvalue a
																		   WHERE a.testItemResult = ".$val3['testitemresultid']."
																		   AND a.result = 0
																		  ");
								$failTestitemValueArr = $failTestitemValueObj->result_array();
								//不合格详细值为一组
								if(count($failTestitemValueArr) == 1)
								{
									$detailmark = $failTestitemValueArr[0]['mark'];
									$detailval = $failTestitemValueArr[0]['value'];
									if(substr($detailmark, -1) == 'M')
									{
										$detailmark = $detailmark/1000;
									}
									if($detailmark >= 0.8 && $detailmark <=1 )
									{
										if($detailval < 1.15)
										{
											$tempFailArr['general1'] = $qualityLossArry['general1'];
										}
										elseif($detailval >= 1.15 && $detailval <= 1.2)
										{
											$tempFailArr['general2'] = $qualityLossArry['general2'];
										}
										elseif($detailval >= 1.2 && $detailval <= 1.3)
										{
											$tempFailArr['general3'] = $qualityLossArry['general3'];
										}
										else
										{
											$tempFailArr['general4'] = $qualityLossArry['general4'];
										}
									}
									elseif($detailmark >= 1.7 && $detailmark <= 2.5)
									{
										if($detailval < 1.15)
										{
											$tempFailArr['general5'] = $qualityLossArry['general5'];
										}
										elseif($detailval >= 1.15 && $detailval <= 1.2)
										{
											$tempFailArr['general6'] = $qualityLossArry['general6'];
										}
										elseif($detailval >= 1.2 && $detailval <= 1.3)
										{
											$tempFailArr['general7'] = $qualityLossArry['general7'];
										}
										else
										{
											$tempFailArr['general8'] = $qualityLossArry['general8'];
										}
									}
									else
									{
										$tempFailArr['general13'] = $qualityLossArry['general13'];
									}
								}
								elseif(count($failTestitemValueArr) == 2)
								{
									$detailmark1 = $failTestitemValueArr[0]['mark'];
									$detailmark2 = $failTestitemValueArr[1]['mark'];
									$detailval1 = $failTestitemValueArr[0]['value'];
									$detailval2 = $failTestitemValueArr[1]['value'];
									if(substr($detailmark1, -1) == 'M')
									{
										$detailmark1 = $detailmark1/1000;
									}
									if(substr($detailmark2, -1) == 'M')
									{
										$detailmark2 = $detailmark2/1000;
									}
									if(($detailmark1 >= 0.8 && $detailmark1 <=1) && ($detailmark2 >= 0.8 && $detailmark2 <=1))
									{
										if($detailval1 < 1.15)
										{
											$tempFailArr['general1'] = $qualityLossArry['general1'];
										}
										elseif($detailval1 >= 1.15 && $detailval1 <= 1.2)
										{
											$tempFailArr['general2'] = $qualityLossArry['general2'];
										}
										elseif($detailval1 >= 1.2 && $detailval1 <= 1.3)
										{
											$tempFailArr['general3'] = $qualityLossArry['general3'];
										}
										else
										{
											$tempFailArr['general4'] = $qualityLossArry['general4'];
										}
										
										if($detailval2 < 1.15)
										{
											$tempFailArr['general1'] = $qualityLossArry['general1'];
										}
										elseif($detailval2 >= 1.15 && $detailval2 <= 1.2)
										{
											$tempFailArr['general2'] = $qualityLossArry['general2'];
										}
										elseif($detailval2 >= 1.2 && $detailval2 <= 1.3)
										{
											$tempFailArr['general3'] = $qualityLossArry['general3'];
										}
										else
										{
											$tempFailArr['general4'] = $qualityLossArry['general4'];
										}
									}
									elseif(($detailmark1 >= 1.7 && $detailmark1 <= 2.5) && ($detailmark2 >= 1.7 && $detailmark2 <= 2.5))
									{
										if($detailval1 < 1.15)
										{
											$tempFailArr['general5'] = $qualityLossArry['general5'];
										}
										elseif($detailval1 >= 1.15 && $detailval1 <= 1.2)
										{
											$tempFailArr['general6'] = $qualityLossArry['general6'];
										}
										elseif($detailval1 >= 1.2 && $detailval1 <= 1.3)
										{
											$tempFailArr['general7'] = $qualityLossArry['general7'];
										}
										else
										{
											$tempFailArr['general8'] = $qualityLossArry['general8'];
										}
										
										if($detailval2 < 1.15)
										{
											$tempFailArr['general5'] = $qualityLossArry['general5'];
										}
										elseif($detailval2 >= 1.15 && $detailval2 <= 1.2)
										{
											$tempFailArr['general6'] = $qualityLossArry['general6'];
										}
										elseif($detailval2 >= 1.2 && $detailval2 <= 1.3)
										{
											$tempFailArr['general7'] = $qualityLossArry['general7'];
										}
										else
										{
											$tempFailArr['general8'] = $qualityLossArry['general8'];
										}
									}
									elseif((($detailmark1 >= 0.8 && $detailmark1 <= 1) && ($detailmark2 >= 1.7 && $detailmark2 <= 2.5)) || (($detailmark2 >= 0.8 && $detailmark2 <= 1) && ($detailmark1 >= 1.7 && $detailmark1 <= 2.5)))
									{
										if($detailval1 < 1.15)
										{
											$tempFailArr['general9'] = $qualityLossArry['general9'];
										}
										elseif($detailval1 >= 1.15 && $detailval1 <= 1.2)
										{
											$tempFailArr['general10'] = $qualityLossArry['general10'];
										}
										elseif($detailval1 >= 1.2 && $detailval1 <= 1.3)
										{
											$tempFailArr['general11'] = $qualityLossArry['general11'];
										}
										else
										{
											$tempFailArr['general12'] = $qualityLossArry['general12'];
										}
										
										if($detailval2 < 1.15)
										{
											$tempFailArr['general9'] = $qualityLossArry['general9'];
										}
										elseif($detailval2 >= 1.15 && $detailval2 <= 1.2)
										{
											$tempFailArr['general10'] = $qualityLossArry['general10'];
										}
										elseif($detailval2 >= 1.2 && $detailval2 <= 1.3)
										{
											$tempFailArr['general11'] = $qualityLossArry['general11'];
										}
										else
										{
											$tempFailArr['general12'] = $qualityLossArry['general12'];
										}
									}
									else
									{
										$tempFailArr['general13'] = $qualityLossArry['general13'];
									}
								}
							}
							elseif($failTestitemname == '回波损耗1' || $failTestitemname == '回波损耗2')
							{
								//取得不合格测试项的详细值
								$failTestitemValueObj = $this->db->query("SELECT a.id,a.mark,a.value
																		   FROM testitemmarkvalue a
																		   WHERE a.testItemResult = ".$val3['testitemresultid']."
																		   AND a.result = 0
																		  ");
								$failTestitemValueArr = $failTestitemValueObj->result_array();
								//不合格详细值为一组
								if(count($failTestitemValueArr) == 1)
								{
									$detailmark = $failTestitemValueArr[0]['mark'];
									$detailval = $failTestitemValueArr[0]['value'];
									if(substr($detailmark, -1) == 'M')
									{
										$detailmark = $detailmark/1000;
									}
									if($detailmark >= 0.8 && $detailmark <=1 )
									{
										if($detailval > 23)
										{
											$tempFailArr['general1'] = $qualityLossArry['general1'];
										}
										elseif($detailval >= 21 && $detailval <= 23)
										{
											$tempFailArr['general2'] = $qualityLossArry['general2'];
										}
										elseif($detailval >= 18 && $detailval < 21)
										{
											$tempFailArr['general3'] = $qualityLossArry['general3'];
										}
										else
										{
											$tempFailArr['general4'] = $qualityLossArry['general4'];
										}
									}
									elseif($detailmark >= 1.7 && $detailmark <= 2.5)
									{
										if($detailval > 23)
										{
											$tempFailArr['general5'] = $qualityLossArry['general5'];
										}
										elseif($detailval >= 21 && $detailval <= 23)
										{
											$tempFailArr['general6'] = $qualityLossArry['general6'];
										}
										elseif($detailval >= 18 && $detailval < 21)
										{
											$tempFailArr['general7'] = $qualityLossArry['general7'];
										}
										else
										{
											$tempFailArr['general8'] = $qualityLossArry['general8'];
										}
									}
									else
									{
										$tempFailArr['general13'] = $qualityLossArry['general13'];
									}
								}
								elseif(count($failTestitemValueArr) == 2)
								{
									$detailmark1 = $failTestitemValueArr[0]['mark'];
									$detailmark2 = $failTestitemValueArr[1]['mark'];
									$detailval1 = $failTestitemValueArr[0]['value'];
									$detailval2 = $failTestitemValueArr[1]['value'];
									if(substr($detailmark1, -1) == 'M')
									{
										$detailmark1 = $detailmark1/1000;
									}
									if(substr($detailmark2, -1) == 'M')
									{
										$detailmark2 = $detailmark2/1000;
									}
									if(($detailmark1 >= 0.8 && $detailmark1 <=1) && ($detailmark2 >= 0.8 && $detailmark2 <=1))
									{
										if($detailval1 > 23)
										{
											$tempFailArr['general1'] = $qualityLossArry['general1'];
										}
										elseif($detailval1 >= 21 && $detailval1 <= 23)
										{
											$tempFailArr['general2'] = $qualityLossArry['general2'];
										}
										elseif($detailval1 >= 18 && $detailval1 < 21)
										{
											$tempFailArr['general3'] = $qualityLossArry['general3'];
										}
										else
										{
											$tempFailArr['general4'] = $qualityLossArry['general4'];
										}
										
										if($detailval2 > 23)
										{
											$tempFailArr['general1'] = $qualityLossArry['general1'];
										}
										elseif($detailval2 >= 21 && $detailval2 <= 23)
										{
											$tempFailArr['general2'] = $qualityLossArry['general2'];
										}
										elseif($detailval2 >= 18 && $detailval2 < 21)
										{
											$tempFailArr['general3'] = $qualityLossArry['general3'];
										}
										else
										{
											$tempFailArr['general4'] = $qualityLossArry['general4'];
										}
									}
									elseif(($detailmark1 >= 1.7 && $detailmark1 <= 2.5) && ($detailmark2 >= 1.7 && $detailmark2 <= 2.5))
									{
										if($detailval1 > 23)
										{
											$tempFailArr['general5'] = $qualityLossArry['general5'];
										}
										elseif($detailval1 >= 21 && $detailval1 <= 23)
										{
											$tempFailArr['general6'] = $qualityLossArry['general6'];
										}
										elseif($detailval1 >= 18 && $detailval1 < 21)
										{
											$tempFailArr['general7'] = $qualityLossArry['general7'];
										}
										else
										{
											$tempFailArr['general8'] = $qualityLossArry['general8'];
										}
										
										if($detailval2 > 23)
										{
											$tempFailArr['general5'] = $qualityLossArry['general5'];
										}
										elseif($detailval2 >= 21 && $detailval2 <= 23)
										{
											$tempFailArr['general6'] = $qualityLossArry['general6'];
										}
										elseif($detailval2 >= 18 && $detailval2 < 21)
										{
											$tempFailArr['general7'] = $qualityLossArry['general7'];
										}
										else
										{
											$tempFailArr['general8'] = $qualityLossArry['general8'];
										}
									}
									elseif((($detailmark1 >= 0.8 && $detailmark1 <= 1) && ($detailmark2 >= 1.7 && $detailmark2 <= 2.5)) || (($detailmark2 >= 0.8 && $detailmark2 <= 1) && ($detailmark1 >= 1.7 && $detailmark1 <= 2.5)))
									{
										if($detailval1 > 23)
										{
											$tempFailArr['general9'] = $qualityLossArry['general9'];
										}
										elseif($detailval1 >= 21 && $detailval1 <= 23)
										{
											$tempFailArr['general10'] = $qualityLossArry['general10'];
										}
										elseif($detailval1 >= 18 && $detailval1 < 21)
										{
											$tempFailArr['general11'] = $qualityLossArry['general11'];
										}
										else
										{
											$tempFailArr['general12'] = $qualityLossArry['general12'];
										}
										
										if($detailval2 > 23)
										{
											$tempFailArr['general9'] = $qualityLossArry['general9'];
										}
										elseif($detailval2 >= 21 && $detailval2 <= 23)
										{
											$tempFailArr['general10'] = $qualityLossArry['general10'];
										}
										elseif($detailval2 >= 18 && $detailval2 < 21)
										{
											$tempFailArr['general11'] = $qualityLossArry['general11'];
										}
										else
										{
											$tempFailArr['general12'] = $qualityLossArry['general12'];
										}
									}
									else
									{
										$tempFailArr['general13'] = $qualityLossArry['general13'];
									}
								}
							}
							else
							{
								$tempFailArr[$failTestitemname] = $qualityLossArry[$failTestitemname];
							}
						}
						//print_r($tempFailArr);
						$qualityLossMax = max($tempFailArr);
						$qualityLossKey = array_search($qualityLossMax, $qualityLossArry);
						//计入质量损失比例最高的对应的
						$resultArr[$key][$qualityLossKey] +=  abs($val2['innermeter']-$val2['outmeter']);
					}
					else//合格
					{
						continue;
					}
				}
				//合格率
				$resultArr[$key]['passpercent'] = round(100 - $resultArr[$key]['faillength']/$resultArr[$key]['totallength']*100,2);
			}
			//遍历结果数组，统计到总计数组
			foreach ($resultArr as $per_k => $per_val) 
			{
				foreach ($per_val as $per_val_k => $per_val_val) 
				{
					if(isset($totalStat[$per_val_k]))
					{
						$totalStat[$per_val_k] += $per_val_val;
					}
				}
			}
			//总计数组合格率
			$totalStat['passpercent'] = round(100 - $totalStat['faillength']/$totalStat['totallength']*100,2);
		}
		//所有结果数组
		$this->smarty->assign('resultArr', $resultArr);
		//统计结果数组
		$this->smarty->assign('totalStat', $totalStat);
		//除“驻波1”，“驻波2”，“回波损耗1”，“回波损耗2”外的其他测试项
		$this->smarty->assign('totalTestitemArray', $totalTestitemArray);
		//出错信息
		$this->smarty->assign('errorMsg', $errorMsg);

		$this->smarty->assign('startTime', $startTime);
		$this->smarty->assign('endTime', $endTime);
		$this->smarty->assign('item', '质量统计');
		$this->smarty->assign('title', '质量统计');
		$this->smarty->display("qualityStat.tpl");
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