<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');
class TeamPermission extends CW_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->library('grocery_CRUD');
	}
		
	public function index()
	{
		$crud = new grocery_CRUD();
		$crud->set_table('team_permission');
		$crud->set_theme('datatables');
		$crud->display_as('team', '用户组')
			 ->display_as('permission', '权限名');
		$crud->set_relation('team','team','name');
		$crud->set_relation('permission','permission','name');
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
