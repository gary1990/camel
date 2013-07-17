<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');
class Qualitylosspercent extends CW_Controller
{
	function __construct()
	{
		parent::__construct();
		//判断当前登录用户
		$userrole = $this->session->userdata("userrole");
		if($userrole == 'user')
		{
			redirect(base_url().'index.php/login/toIndex');
		}
		$this->load->library('grocery_CRUD');
	}
		
	public function index()
	{
		$crud = new grocery_CRUD();
		$crud->set_table('qualitylosspercent');
		$crud->set_theme('datatables');
		$crud->display_as('testitem', '测试项')->display_as('frquencelimits', '频段')
			 ->display_as('valuelimits', '值')->display_as('qualitylosspercentval', '质量损失费用比例');
		$crud->set_relation('testitem','testitem','name');
		$crud->set_rules('qualitylosspercentval','qualitylosspercent','callback_qualitylosspercent');
		$crud->edit_fields('qualitylosspercentval');
		$crud->unset_delete();
		$crud->unset_add();
		$crud->unset_export();
		$crud->unset_print();
		$output = $crud->render();
		foreach ($output as $key=>$value)
		{
			$this->smarty->assign($key, $value);
		}
		$this->smarty->assign('item', '质量损失费用比例');
		$this->smarty->assign('title', '质量损失费用比例');
		$this->smarty->display('qualitylosspercent.tpl');
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
