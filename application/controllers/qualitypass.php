<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');
class Qualitypass extends CW_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->_init();
	}
	
	private function _init()
	{
		//取得测试站
		$teststation = array(""=>"");
		$teststationObj = $this->db->query("SELECT id,name FROM teststation ORDER BY name");
		if($teststationObj->num_rows != 0)
		{
			foreach ($teststationObj->result() as $value) 
			{
				$teststation[$value->id] = $value->name;
			}
		}
		$this->smarty->assign("teststation",$teststation);
		//取得产品型号
		$producttype = array(""=>"");
		$producttypeObj = $this->db->query("SELECT id,name FROM producttype ORDER BY name");
		if($producttypeObj->num_rows !=0)
		{
			foreach ($producttypeObj->result() as $value) 
			{
				$producttype[$value->id] = $value->name;
			}
		}
		$this->smarty->assign("producttype",$producttype);
	}
	
	public function index($offset = 0, $limit = 30)
	{
		//取得用户选择日期，默认当前日期
		$date = $this->input->post("date");
		if(!$this->_checkDateFormat($date))
		{
			$date = date("Y-m-d");
		}
		$teststation = $this->input->post("teststation");
		$producttype = $this->input->post("producttype");
		$dateSql = " AND po.testTime >= '".$date." 00:00:00' AND po.testTime <= '".$date." 23:59:59' ";
		$teststationSql = "";
		$producttypeSql = "";
		if($teststation != "")
		{
			$teststationSql = " AND po.testStation = '".$teststation."' ";
		}
		if($producttype != "")
		{
			$producttypeSql = " AND po.productType = '".$producttype."' ";
		}
		$qualitypassSql = "SELECT po.id,po.testTime,tn.name AS teststaion,pe.name AS producttype,po.sn,po.result
						   FROM producttestinfo po
						   JOIN teststation tn ON po.testStation = tn.id
						   JOIN producttype pe ON po.productType = pe.id
						   AND po.tag1 = '1'
						   AND po.result = '0'
						   ".$dateSql.$teststationSql.$producttypeSql." ORDER BY po.testTime DESC";
		$qualitypassObj = $this->db->query($qualitypassSql);
		$qualitypassArr = $qualitypassObj->result_array();
		$totalcount = count($qualitypassArr);
		//分页
		$this->load->library('pagination');
		$config['full_tag_open'] = '<div class="locPage">';
		$config['full_tag_close'] = '</div>';
		$config['base_url'] = '';
		$config['uri_segment'] = 3;
		$config['total_rows'] = count($qualitypassArr);
		$config['per_page'] = $limit;
		$this->pagination->initialize($config);
		$qualitypassLimitSql = $qualitypassSql." LIMIT ".$offset.",".$limit;
		$qualitypassObj = $this->db->query($qualitypassLimitSql);
		$qualitypassArr = $qualitypassObj->result_array();
		//记录序号开始值
		$this->smarty->assign('totalcount', $totalcount-$offset);
		
		$this->smarty->assign('qualitypassArr', $qualitypassArr);
		$this->smarty->assign('item', '质量放行');
		$this->smarty->assign('title', '质量放行');
		$this->smarty->display("qualitypass.tpl");
	}
	
	//保存放行记录
	public function savequalitypass()
	{
		//放行时间
		$currDate = date("Y-m-d H:i:s");
		//放行记录
		$record = $_POST;
		//总放行记录数
		$totalNum = $record["totalrecord"];
		//当前登录用户
		$user = $this->session->userdata["username"];
		//判断总记录条数
		if($totalNum != 0)
		{
			$idSql = "";
			$insertValueSql = "";
			for($i=1;$i<=$totalNum;$i++)
			{
				if(isset($record["id".$i]))
				{
					$id = $record["id".$i];
					$remark = $record["remark".$id];
					$idSql .= $id.",";
					$insertValueSql .= " ('".$id."','".$user."','".$remark."','".$currDate."'),";
				}
			}
			$idSql = substr($idSql, 0,-1);
			$insertValueSql = substr($insertValueSql,0,-1);
			//放行记录SQL语句
			if(strlen($idSql) != 0)
			{
				$updateSql = "UPDATE producttestinfo po SET po.result = '1',po.tag1 = '3'
							  WHERE po.id IN ( ".$idSql.")";
				$insertSql = "INSERT INTO qualitypass_record (producttestinfo,responsible_person,remark,modify_time)
							  VALUE ".$insertValueSql;
				$this->db->query($updateSql);
				$this->db->query($insertSql);
			}
		}
		else
		{
			//do nothing
		}
		$this->index();
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