<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');
class Permission extends CW_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->library('grocery_CRUD');
	}
		
	public function index()
	{
		$crud = new grocery_CRUD();
		$crud->set_table('permission');
		$crud->set_theme('datatables');
		$crud->display_as('name', '权限名称')
			 ->display_as('controller', '控制器名')
			 ->display_as('function', '方法名');
		$output = $crud->render();
		foreach ($output as $key=>$value)
		{
			$this->smarty->assign($key, $value);
		}
		$this->smarty->assign('item', '');
		$this->smarty->assign('title', '权限');
		$this->smarty->display('firstPage.tpl');
	}
}
