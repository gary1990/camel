<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');
class Qualitylosspercent extends CW_Controller
{
	function __construct()
	{
		parent::__construct();
		//判断当前登录用户
		$userrole = $this->session->userdata("team");
		if($userrole != '管理员')
		{
			redirect(base_url().'index.php/login/toIndex/error');
		}
		$this->load->library('grocery_CRUD');
	}
		
	public function index($var = '')
	{
		$errorMsgPost = $var;
		$errorMsg = '';
		//保存所有质量损失费用比例数组
		$qualityLossArry = array();
		//取得“驻波1”，“回波损耗1”，“驻波2”，“回波损耗2”质量损失比
		$generalQualityLossObj = $this->db->query("SELECT a.frquencelimits,qualitylosspercentval FROM qualitylosspercent a");
		$generalQualityLossArr = $generalQualityLossObj->result_array();
		foreach ($generalQualityLossArr as $key => $value) 
		{
			$qualityLossArry['general'.($key+1)] = substr($value['qualitylosspercentval'], 0, -1);
		}
		
		//取得除去“驻波1”，“驻波2”，“回波损耗1”，“回波损耗2”测试项的其他测试项
		$totalTestitemObj = $this->db->query("SELECT DISTINCT b.id,b.name,b.qualitylosspercent
		    								  FROM testitem b
		    								  JOIN testitemsection d ON b.testitemsection = d.id
		    								  AND b.name NOT IN ('驻波1','驻波2','回波损耗1','回波损耗2')
		    								  AND d.sectionname = '同轴'
		    								 ");
		$totalTestitemArray = $totalTestitemObj->result_array();
		foreach ($totalTestitemArray as $key => $value) 
		{
			if($value['qualitylosspercent'] != '')
			{
				$qualityLossArry[$value['id']] = substr($value['qualitylosspercent'], 0, -1);
			}
			else
			{
				$errorMsg .= "测试项:'".$value['name']."'的质量损失费用比例不能为空！";
				$this->smarty->assign('totalTestitemArray', $totalTestitemArray);
				$this->smarty->assign('errorMsg', $errorMsg);
				$this->smarty->assign('errorMsgPost', $errorMsgPost);
				$this->smarty->assign('item', '质量损失费用比例');
				$this->smarty->assign('title', '质量损失费用比例');
				$this->smarty->display("qualitylosspercent.tpl");
				return;
			}
		}
		$this->smarty->assign('totalTestitemArray', $totalTestitemArray);
		//出错信息
		$this->smarty->assign('errorMsg', $errorMsg);
		$this->smarty->assign('errorMsgPost', $errorMsgPost);
		//质量损失费用比例
		$this->smarty->assign('qualityLossArry', $qualityLossArry);
		$this->smarty->assign('item', '质量损失费用比例');
		$this->smarty->assign('title', '质量损失费用比例');
		$this->smarty->display('qualitylosspercent.tpl');
	}
	
	
	public function qualitylossPost()
	{
		$qualitylossPost = $_POST;
		$unique_qualitylossPost = array_unique($qualitylossPost);
		if(count($qualitylossPost) != count($unique_qualitylossPost))
		{
			$error = '质量损失比存在重复项，保存失败。';
			$this->index($error);
		}
		else
		{
			for($i = 1;$i <= 13;$i++)
			{
				$this->db->query("UPDATE qualitylosspercent a 
					  			  SET a.qualitylosspercentval = '".$qualitylossPost['general'.$i]."%'
					              WHERE id = ".$i);
			}
			for($i = 1;$i <= 13;$i++)
			{
				unset($qualitylossPost['general'.$i]);
			}
			foreach ($qualitylossPost as $key => $value) 
			{
				$this->db->query("UPDATE testitem a 
					  			  SET a.qualitylosspercent = '".$value."%'
					              WHERE id = ".$key);
			}
			$this->index();
		}
	}
	//质量损失比例计算
	public function qualitylosspercent($str)
	{
		if(strlen(trim($str)) != 0)
		{
			if(preg_match("/^((([0-9]+)([\.]([0-9]+))([%]))?|(([0-9]+)([%]))?)$/", $str))
			{
				if(substr(trim($str), 0, -1) > 100 || substr(trim($str), 0, -1) < 0)
				{
					$this->form_validation->set_message('qualitylosspercent', '质量损失费用比为0~100之间的数字！');
					return FALSE;
				}
				else
				{
					return TRUE;
				}
			}
			else
			{
				$this->form_validation->set_message('qualitylosspercent', '质量损失费用比格式不正确！格式为：数值%%');
				return FALSE;
			}
		}
		else
		{
			$this->form_validation->set_message('qualitylosspercent', '质量损失费用比不能为空！');
			return FALSE;
		}
		
	}

}
