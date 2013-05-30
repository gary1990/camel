<?php
if (!defined('BASEPATH'))
	exit('no direct script access allowed');
class Producttestcase extends CW_Controller
{
	public function __construct()
	{
		//
		parent::__construct();
		//判断当前登录用户
		$userrole = $this->session->userdata("userrole");
		if($userrole == 'user')
		{
			redirect(base_url().'index.php/login/toIndex');
		}
		//获得所有产品型号
		$producttypeObj = $this->db->query("SELECT pe.id,pe.name FROM producttype pe
											JOIN status ss ON pe.status = ss.id
											AND ss.statusname = 'active'
											ORDER BY pe.name");
		$producttypeArr = $producttypeObj->result_array();
		$producttype = $this->array_switch($producttypeArr, 'name', "");
		$producttypeSearch = $this->array_switch($producttypeArr, 'name', "(ALL)");
		$this->smarty->assign("producttype",$producttype);
		$this->smarty->assign("producttypeSearch",$producttypeSearch);
		//获得所有测试项
		$testitemObj = $this->db->query("SELECT tm.id,tm.name FROM testitem tm
											JOIN status ss ON tm.status = ss.id
											AND ss.statusname = 'active'");
		$testitemArr = $testitemObj->result_array();
		$testitem = $this->array_switch($testitemArr, 'name', "");
		$this->smarty->assign("testitem",$testitem);
		//Type数组
		$type = array(""=>"",
					  "MAX"=>"MAX",
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
		$this->smarty->assign("one_tenArr",$one_tenArr);
	}
	
	public function index($offset = 0, $limit = 30,$search_export = "")
	{
		$producttype = $this->input->post("producttypesearch");
		$producttypeSql = "";
		if($producttype != "")
		{
			$producttypeSql = " AND tn.producttype = '".$producttype."'";
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
			/*
			$producttestcaseSql = "SELECT pe.name AS producttypeName,tm.name AS testitemName,tn.statefile,tn.ports,tn.channel,tn.trace,tn.startf,tn.stopf,tn.mark,tn.min,tn.max 
								   FROM test_configuration tn
								   JOIN producttype pe ON tn.producttype = pe.id
								   JOIN testitem tm ON tn.testitem = tm.id
								   JOIN status ss ON pe.status = ss.id
								   AND tm.status = ss.id
								   AND ss.statusname = 'active'
								   ".$producttypeSql." GROUP BY tn.producttype,tn.testitem,tn.statefile,tn.ports";
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
		   				   $value["ports"].",".$value["channel"].",".$value["trace"].",".$value["startf"].",".
		   				   $value["stopf"].",".$value["mark"].",".$value["min"].",".$value["max"]."\r\n";
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
			 * 
			 */
		}
	}
	
	//保存页面内容
	public function del_ins()
	{
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
		for($i=1;$i<=$addcount;$i++)
		{
			//取得当前记录的各个值
			$producttype = $this->input->post("producttype".$i);
			$testitem = $this->input->post("testitem".$i);
			$statusfile = $this->input->post("statusfile".$i);
			$ports = $this->input->post("ports".$i);
			$channel = $this->input->post("channel".$i);
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
			if($producttype != "")
			{
				$value .= "('$producttype','$testitem','$statusfile','$ports','$channel','$trace','$type','$beginstim','$endstim','$beginresp','$endresp'),";
			}
		}
		if(strlen($value) > 0)
		{
			$value = substr($value, 0, -1);
			$insertSql = "INSERT INTO `test_configuration`(`producttype`, `testitem`, `statefile`, `ports`, `channel`, `trace`, `type`, `beginstim`, `endstim`, `beginresp`, `endresp`) VALUES ".$value;
			$this->db->query($insertSql);
		}
		else
		{
			//echo "helloworld";
		}
		echo "保存成功！";
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