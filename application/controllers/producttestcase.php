<?php
if (!defined('BASEPATH'))
	exit('no direct script access allowed');
class Producttestcase extends CW_Controller
{
	public function __construct()
	{
		//
		parent::__construct();
		$userrole = $this->session->userdata("team");
		if($userrole == '测试员及其他人员')
		{
			redirect(base_url().'index.php/login/toIndex/error');
		}
		//获得所有测试项
		$testitemObj = $this->db->query("SELECT tm.id,tm.name FROM testitem tm
											JOIN status ss ON tm.status = ss.id
											AND ss.statusname = 'active'");
		$testitemArr = $testitemObj->result_array();
		$testitem = $this->array_switch($testitemArr, 'name', "");
		$this->smarty->assign("testitem",$testitem);
		//Type数组
		$type = array("MAX"=>"MAX",
					  "MIN"=>"MIN",
					  "OFF"=>"OFF");
		$this->smarty->assign("type",$type);
		//单位数组
		$unit = array(""=>"",
					  "n"=>"n",
					  "u"=>"u",
					  "m"=>"m",
					  "k"=>"k",
					  "M"=>"M",
					  "G"=>"G"
					  );
		$this->smarty->assign("unit",$unit);
		//取得1~10供用户所选
		$one_tenArr = array();
		for($i=1;$i<=10;$i++)
		{
			$arr = array($i => $i);
			$one_tenArr += $arr;
		}
		$one_tenArr_withNull = array(""=>"NULL");

		for($i=1;$i<=4;$i++)
		{
			$arr = array($i => $i);
			$one_tenArr_withNull += $arr;
		}
		$this->smarty->assign("one_tenArr_withNull",$one_tenArr_withNull);
		$this->smarty->assign("one_tenArr",$one_tenArr);
	}
	
	public function index($offset = 0, $limit = 30,$search_export = "")
	{
		//获得所有产品型号
		$producttypeObj = $this->db->query("SELECT pe.id,pe.name FROM producttype pe
											JOIN status ss ON pe.status = ss.id
											AND ss.statusname = 'active'
											ORDER BY pe.name");
		$producttypeArr = $producttypeObj->result_array();
		$producttype = $this->array_switch($producttypeArr, 'name', "");
		$producttypeSearch = $this->array_switch1($producttypeArr, 'name', "(ALL)");
		$this->smarty->assign("producttype",$producttype);
		$this->smarty->assign("producttypeSearch",$producttypeSearch);
		
		$producttype = $this->input->post("producttypesearch");
		$producttypeSql = "";
		//判断查询还是第一次进入页面
		if(isset($_POST['producttypesearch']))
		{
			if($producttype != "")
			{
				$producttypeSql = " AND tn.producttype = '".$producttype."'";
			}
			else
			{
				$this->smarty->assign("curProduct","");
			}
		}
		else
		{
			if(count($producttypeArr) > 0)
			{
				$producttypeSql = " AND tn.producttype = '".$producttypeArr[0]['id']."'";
				$curProduct = $producttypeArr[0]['id'];
				$this->smarty->assign("curProduct",$curProduct);
			}
			else
			{
				$this->smarty->assign("curProduct",'');
			}
		}
		
		if($search_export == "")
		{
			$producttestcaseSql = "SELECT tn.* FROM test_configuration tn
								   JOIN producttype pe ON tn.producttype = pe.id
								   JOIN testitem tm ON tn.testitem = tm.id
								   JOIN status ss ON pe.status = ss.id
								   AND tm.status = ss.id
								   AND ss.statusname = 'active'
								   ".$producttypeSql." 
								   GROUP BY tn.producttype,tn.testitem,tn.statefile,tn.ports,tn.channel,tn.trace,
								   tn.type,tn.beginstim,tn.endstim,tn.beginresp,tn.endresp ";
			$testcaseObj = $this->db->query($producttestcaseSql);
			$testcaseArr = $testcaseObj->result_array();
			
			$this->load->library('pagination');
			$config['full_tag_open'] = '<div class="locPage">';
			$config['full_tag_close'] = '</div>';
			$config['base_url'] = '';
			$config['uri_segment'] = 3;
			$config['total_rows'] = count($testcaseArr);
			$config['per_page'] = $limit;
			$this->pagination->initialize($config);
			
			$producttestcaseSql = $producttestcaseSql." LIMIT ".$offset.",".$limit;
			$testcaseObj = $this->db->query($producttestcaseSql);
			$testcaseArr = $testcaseObj->result_array();
			$count = count($testcaseArr);
			//取出当前所有记录的id
			$idStr = "";
			if($count != 0)
			{
				foreach($testcaseArr as $value)
				{
					$idStr .= $value['id'].",";
				}
			}
			$this->smarty->assign("idStr",$idStr);
			
			$this->smarty->assign("testcaseArr",$testcaseArr);
			$this->smarty->assign("count",$count);
			$this->smarty->assign("item","测试方案");
			$this->smarty->assign("title","测试方案");
			$this->smarty->display("producttestcase.tpl");
		}
		else
		{
			$producttestcaseSql = "SELECT pe.name AS producttypeName,tm.name AS testitemName,tn.statefile,tn.ports,tn.channel,tn.trace,tn.type,tn.beginstim,tn.endstim,tn.beginresp,tn.endresp 
								   FROM test_configuration tn
								   JOIN producttype pe ON tn.producttype = pe.id
								   JOIN testitem tm ON tn.testitem = tm.id
								   JOIN status ss ON pe.status = ss.id
								   AND tm.status = ss.id
								   AND ss.statusname = 'active'
								   ".$producttypeSql." GROUP BY tn.producttype,tn.testitem,tn.statefile,tn.ports,tn.channel,tn.trace,tn.type,tn.beginstim,tn.endstim";
			$testcaseObj = $this->db->query($producttestcaseSql);
			$testcaseArr = $testcaseObj->result_array();
			$slash = "\\";
			$downloadRoot = getcwd();
			$filename = $downloadRoot.$slash."producttestcase.csv";
			if(file_exists($filename))
			{
				unlink($filename);
			}
			$handle = fopen($filename, 'w');
			if(count($testcaseArr) != 0)
			{
				foreach ($testcaseArr as $value)
		   		{
		   			//fputcsv($handle, $value);
		   			$str = $value["producttypeName"].",".$value["testitemName"].",".$value["statefile"].",".
		   				   $value["ports"].",".$value["channel"].",".$value["trace"].",".$value["type"].",".
		   				   str_replace("#", "", $value["beginstim"]).",".str_replace("#", "", $value["endstim"]).",".
		   				   $value["beginresp"].",".$value["endresp"]."\r\n";
		   			fwrite($handle, iconv('UTF-8','GB2312',$str));
		   		}
			}
		  	fclose($handle);
		  	$fileName = "producttestcase.csv";
		  	header("Pragma: public");
   			header("Expires: 0");
    		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    		header("Cache-Control: public");
    		header("Content-Description: File Transfer");
    		header("Content-type: application/octet-stream");
    		header("Content-Disposition: attachment; filename=\"" . $fileName . "\"");
    		header("Content-Transfer-Encoding: binary");
    		header("Content-Length: " . filesize($filename));
    		ob_end_flush();
			@readfile($filename);
			unlink($filename);
		}
	}
	
	//导入csv方法
	public function importCsvFile()
	{
		if(count($_FILES) == 0)
		{
			echo "Error";
			return;
		}
		if ($_FILES["file"]["error"] > 0)
  		{
 	 		echo "Error";
  		}
		else
  		{
  			//判断是不是.csv格式文件
  			if($_FILES["file"]["type"] == "application/csv" || $_FILES["file"]["type"] == "application/vnd.ms-excel")
  			{
  				//暂存目录
  				$root = getcwd();
				$slash = "\\";
				$file_temp = $_FILES['file']['tmp_name'];
				$file_name = $root.$slash.iconv("utf-8","gbk",$_FILES['file']['name']);
				//文件是否存在。如存在，删除
				if(file_exists($file_name))
				{
					unlink($file_name);
				}
				//保存文件
				$filestatus = move_uploaded_file($file_temp, $file_name);
				if(!$filestatus)
				{
					echo "Error:upload file fail.";
				}
				else
				{
					//打开文件
					if($handle = fopen($file_name, "r"))
					{
						$producttypeObj = $this->db->query("SELECT id,name FROM producttype");
						$producttypeArr = $producttypeObj->result_array();
						$testitemObj = $this->db->query("SELECT id,name FROM testitem");
						$testitemArr = $testitemObj->result_array();
						if(count($producttypeArr) == 0 || count($testitemArr) == 0)
						{
							echo "Error:producttype&&testitem Table can not null.";
						}
						else
						{
							//转换产品型号，测试项数组
							$producttype = array();
							foreach ($producttypeArr as $value) 
							{
								if($value["name"] != "")
								{
									$producttype[$value['id']] = $value['name'];
								}
							}
							$testitem = array();
							foreach ($testitemArr as $value) 
							{
								if($value["name"] != "")
								{
									$testitem[$value['id']] = $value['name'];
								}
							}
							$row = 0;
							$insertSql = "INSERT INTO `test_configuration`(`producttype`, `testitem`, `statefile`, `ports`, `channel`, `trace`, `type`, `beginstim`, `endstim`, `beginresp`, `endresp`) VALUES ";
							$insertValue = "";
							$errorLine = "";
							while(!feof($handle))
							{
						    	$line = str_replace("\t",",",fgets($handle));
		   						$lineArr = explode(",", $line);
								if($row < 1)
								{
									//do noting
								}
								else
								{
									if($lineArr[0] == "" || $lineArr[1] == "")
									{
										$errorLine .= $row.",";
									}
									else
									{
										$producttypeId = array_search($lineArr[0], $producttype);
										$testitemId = array_search($lineArr[1], $testitem);
										$status = $lineArr[2];
										$ports = $lineArr[3];
										if($producttypeId == NULL || $testitemId== NULL)
										{
											$errorLine .= $row.",";
										}
										else
										{
											$insertValue .= "('$producttypeId','$testitemId','$status','$ports','','1','MAX','','','',''),";
										}
									}
								}
								$row++;
					    	}
							$insertValue = substr($insertValue, 0,-1);
							if(strlen($insertValue) != 0)
							{
								$insertSql .= $insertValue;
								$this->db->query($insertSql);
							}
							else
							{
								//do nothing
							}
							
							if(strlen($errorLine) == 0)
							{
								echo "Success";
							}
							else
							{
								echo "Error Line:".substr($errorLine,0,-1);
							}
						}
						fclose($handle);
					}
					else
					{
						echo "Error";
					}
					
				}
				unlink($file_name);
  			}
  			else
  			{
  				echo "Error";
  			}
  		}
	}
	
	//保存页面内容
	public function del_ins()
	{
		$this->db->trans_start();
		//取当前页面的记录的id,并删除
		$ids = $this->input->post("ids");
		if($ids != "")
		{
			//去除最后的","
			$ids = substr($ids, 0,-1);
			$this->db->query("DELETE FROM test_configuration WHERE id IN (".$ids.")");
		}
		//循环取到当前页面内容,并插入数据库
		//先取到有多少记录
		
		$addcount = $this->input->post("addcount");
		//遍历取得所有记录的值
		$value = "";
		//保存实际有多少条记录
		$actualRecord = 0;
		for($i=1;$i<=$addcount;$i++)
		{
			//取得当前记录的各个值
			$producttype = $this->input->post("producttype".$i);
			$testitem = $this->input->post("testitem".$i);
			$statusfile = $this->input->post("statusfile".$i);
			$ports = $this->input->post("ports".$i);
			$channel = $this->input->post("channel".$i);
			//判断$_POST是否传过来Trace
			if(isset($_POST["trace".$i]))
			{
				$trace = $this->input->post("trace".$i);
				$type = $this->input->post("type".$i);
				$beginstimVal = $this->input->post("beginstim".$i);
				$beginstimUnit = $this->input->post("beginstimunit".$i);
				if($beginstimVal == "")
				{
					$beginstim = "";
				}
				else
				{
					$beginstim = $beginstimVal."#".$beginstimUnit;
				}
				$endstimVal = $this->input->post("endstim".$i);
				$endstimUnit = $this->input->post("endstimunit".$i);
				if($endstimVal == "")
				{
					$endstim = "";
				}
				else
				{
					$endstim = $endstimVal."#".$endstimUnit;
				}
				$beginresp = $this->input->post("beginresp".$i);
				$endresp = $this->input->post("endresp".$i);
			}
			else
			{
				$trace = '1';
				$type = 'MAX';
				$beginstim = "";
				$endstim = "";
				$beginresp = "";
				$endresp = "";
			}
			if($producttype != "")
			{
				$actualRecord++;
				$value .= "('$producttype','$testitem','$statusfile','$ports','$channel','$trace','$type','$beginstim','$endstim','$beginresp','$endresp'),";
			}
		}
		if(strlen($value) > 0)
		{
			$value = substr($value, 0, -1);
			$insertSql = "INSERT INTO `test_configuration`(`producttype`, `testitem`, `statefile`, `ports`, `channel`, `trace`, `type`, `beginstim`, `endstim`, `beginresp`, `endresp`) VALUES ".$value;
			$this->db->query($insertSql);
			//取得插入记录的开始ID
			$insertId = $this->db->insert_id();
		}
		else
		{
			//echo "helloworld";
		}
		if ($this->db->trans_status() === FALSE)
		{
			$this->db->trans_rollback();
			echo "保存失败，请重试";
		}
		else
		{
			$this->db->trans_commit();
			//成功信息
			$successRecord = "";
			//取得刚才插入的记录的所有ID
			for($k = 0;$k < $actualRecord;$k++)
			{
				$successRecord.= ($insertId+$k).",";
			}
			//将记录ID传回前台页面
			echo $successRecord;
		}
		$this->db->trans_complete();
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
	
	protected function array_switch1($var1,$var2,$var3)
	{
		$arr = array();
		foreach($var1 as $value)
		{
			$arr = $arr + array($value['id']=>$value[$var2]);
		}
		$arr = $arr + array(""=>$var3);
		return $arr;
	}
}
