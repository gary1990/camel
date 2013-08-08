<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');
class TestStandard extends CW_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->library('grocery_CRUD');
	}
		
	public function index()
	{
		$crud = new grocery_CRUD();
		$crud->set_theme('datatables');
		$crud->set_table('producttype_damping_standard');
		$crud->required_fields('producttype');
		$crud->display_as('producttype', '产品型号')->display_as('frequence', '频点（MHz）')->display_as('standard', '标准衰减值');
		$crud->set_rules('frequence','频点（MHz）','required|numeric');
		$crud->set_rules('standard','标准衰减值','required|numeric');
		$crud->set_relation('producttype','producttype','name',array('status' => '1'));
		$crud->unset_export();
		$crud->unset_print();
		$output = $crud->render();
		foreach ($output as $key=>$value)
		{
			$this->smarty->assign($key, $value);
		}
		
		$this->smarty->assign('item', '测试项判断标准');
		$this->smarty->assign('title', '测试项判断标准');
		$this->smarty->assign('currentitem', 'shuaijian');
		$this->smarty->display('teststandard.tpl');
	}
	
	public function tdrEleLength()
	{
		$crud = new grocery_CRUD();
		$crud->set_theme('datatables');
		$crud->set_table('producttype_tdrelelength_standard');
		//$crud->required_fields('standard');
		$crud->display_as('standard', 'TDR电长度（千米）');
		$crud->set_rules('standard','TDR电长度（千米）','required|numeric');
		$crud->unset_export();
		$crud->unset_print();
		$crud->unset_add();
		$crud->unset_delete();
		$output = $crud->render();
		//新增时对产品型号的判断
		$postUrl = $this->uri->uri_string();
		
		foreach ($output as $key=>$value)
		{
			$this->smarty->assign($key, $value);
		}
		
		$this->smarty->assign('item', '测试项判断标准');
		$this->smarty->assign('title', '测试项判断标准');
		$this->smarty->assign('currentitem', 'tdr');
		$this->smarty->display('teststandard.tpl');
	}
	
	public function timedomainImpedance()
	{
		$crud = new grocery_CRUD();
		$crud->set_theme('datatables');
		$crud->set_table('producttype_timedomainimpedance_standard');
		$crud->display_as('producttype', '产品型号')->display_as('min', 'Min')->display_as('max', 'Max');
		$crud->set_rules('min','Min','required|numeric');
		$crud->set_rules('max','Max','required|numeric');
		$crud->set_relation('producttype','producttype','name',array('status' => '1'));
		$crud->unset_export();
		$crud->unset_print();
		//新增时对产品型号的判断
		$postUrl = $this->uri->uri_string();
		if(strpos($postUrl, "insert_validation") != FALSE)
		{
			$crud->set_rules('producttype','producttype','callback_add_timedomainimpedance');
		}
		else if(strpos($postUrl, "update_validation") != FALSE)
		{
			$crud->set_rules('producttype','producttype','callback_edit_timedomainimpedance');
		}
		else
		{
			//
		}
		$output = $crud->render();
		foreach ($output as $key=>$value)
		{
			$this->smarty->assign($key, $value);
		}
		
		$this->smarty->assign('item', '测试项判断标准');
		$this->smarty->assign('title', '测试项判断标准');
		$this->smarty->assign('currentitem', 'timedomainimpedance');
		$this->smarty->display('teststandard.tpl');
	}

	public function add_timedomainimpedance($str)
	{
		if(strlen($str) == 0)
		{
			$this->form_validation->set_message('add_timedomainimpedance', '产品型号不能为空');
			return FALSE;
		}
		else
		{
			$recordObj = $this->db->query("SELECT a.id FROM producttype_timedomainimpedance_standard a WHERE a.producttype = '$str'");
			if($recordObj->num_rows() != 0)
			{
				$this->form_validation->set_message('add_timedomainimpedance', '同一产品型号只能添加一组Min/Max值！');
				return FALSE;
			}
			else
			{
				return TRUE;
			}
		}
	}
	
	public function edit_timedomainimpedance($str)
	{
		if(strlen($str) == 0)
		{
			$this->form_validation->set_message('edit_timedomainimpedance', '产品型号不能为空');
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}
}
