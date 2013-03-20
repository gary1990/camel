<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');
class firstPage extends CW_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->library('grocery_CRUD');
	}

	public function department()
	{
		$crud = new grocery_CRUD();
		$crud->set_theme('datatables');
		$crud->required_fields('name');
		$crud->display_as('name', '名称');
		$output = $crud->render();
		foreach ($output as $key=>$value)
		{
			$this->smarty->assign($key, $value);
		}
		$this->smarty->assign('title', '部门');
		$this->smarty->display('firstPage.tpl');
	}

	public function producttype()
	{
		$crud = new grocery_CRUD();
		$crud->set_theme('datatables');
		$crud->required_fields('name', 'img');
		$crud->display_as('name', '名称')->display_as('img', '工艺文档');
		$crud->set_field_upload('img', 'assets/uploads/files');
		$output = $crud->render();
		foreach ($output as $key=>$value)
		{
			$this->smarty->assign($key, $value);
		}
		$this->smarty->assign('title', '产品管理');
		$this->smarty->display('firstPage.tpl');
	}

	public function skilllevel()
	{
		$crud = new grocery_CRUD();
		$crud->set_theme('datatables');
		$crud->required_fields('name');
		$crud->display_as('name', '名称');
		$output = $crud->render();
		foreach ($output as $key=>$value)
		{
			$this->smarty->assign($key, $value);
		}
		$this->smarty->assign('title', '技能水平');
		$this->smarty->display('firstPage.tpl');
	}

	public function team()
	{
		$crud = new grocery_CRUD();
		$crud->set_theme('datatables');
		$crud->required_fields('name');
		$crud->display_as('name', '名称');
		$output = $crud->render();
		foreach ($output as $key=>$value)
		{
			$this->smarty->assign($key, $value);
		}
		$this->smarty->assign('title', '用户组');
		$this->smarty->display('firstPage.tpl');
	}

	public function tester()
	{
		$crud = new grocery_CRUD();
		$crud->set_theme('datatables');
		$crud->required_fields('name', 'employeeId', 'testRight', 'skillLevel', 'department');
		$crud->display_as('name', '姓名')->display_as('password', '密码')->display_as('employeeId', '工号')->display_as('testRight', '权限')->display_as('skillLevel', '技能')->display_as('department', '部门');
		$crud->set_relation('testRight', 'testRight', 'name');
		$crud->set_relation('skillLevel', 'skillLevel', 'name');
		$crud->set_relation('department', 'department', 'name');
		$crud->add_action('查看绩效', '', '', 'performance');
		$crud->add_action('查看工作时间', '', '', 'workload');
		$crud->unset_delete();
		$output = $crud->render();
		foreach ($output as $key=>$value)
		{
			$this->smarty->assign($key, $value);
		}
		$this->smarty->assign('title', '测试员');
		$this->smarty->assign('diagram', TRUE);
		$this->smarty->display('firstPage.tpl');
	}

	public function testitem()
	{
		$crud = new grocery_CRUD();
		$crud->set_theme('datatables');
		$crud->required_fields('name');
		$crud->display_as('name', '名称');
		$output = $crud->render();
		foreach ($output as $key=>$value)
		{
			$this->smarty->assign($key, $value);
		}
		$this->smarty->assign('title', '测试项');
		$this->smarty->display('firstPage.tpl');
	}

	public function testright()
	{
		$crud = new grocery_CRUD();
		$crud->set_theme('datatables');
		$crud->required_fields('name');
		$crud->display_as('name', '名称');
		$output = $crud->render();
		foreach ($output as $key=>$value)
		{
			$this->smarty->assign($key, $value);
		}
		$this->smarty->assign('title', '测试员权限');
		$this->smarty->display('firstPage.tpl');
	}

	public function teststation()
	{
		$crud = new grocery_CRUD();
		$crud->set_theme('datatables');
		$crud->required_fields('name');
		$crud->required_fields('equipmentSn');
		$crud->display_as('name', '名称')->display_as('equipmentSn', '设备序列号');
		$crud->unset_add();
		$crud->unset_delete();
		$output = $crud->render();
		foreach ($output as $key=>$value)
		{
			$this->smarty->assign($key, $value);
		}
		$this->smarty->assign('title', '测试站点');
		$this->smarty->display('firstPage.tpl');
	}

	public function producttestinfo()
	{
		$crud = new grocery_CRUD();
		$crud->set_theme('datatables');
		$crud->required_fields('sn', 'equipmentSn', 'testTime', 'testStation', 'tester', 'productType', 'result');
		$crud->display_as('sn', '序列号')->display_as('equipmentSn', '设备序列号')->display_as('testTime', '测试时间')->display_as('testStation', '测试站点')->display_as('tester', '测试员')->display_as('productType', '产品类型')->display_as('result', '测试结果');
		$crud->set_relation('testStation', 'testStation', 'name');
		$crud->set_relation('tester', 'tester', 'employeeId');
		$crud->set_relation('productType', 'productType', 'name');
		$output = $crud->render();
		foreach ($output as $key=>$value)
		{
			$this->smarty->assign($key, $value);
		}
		$this->smarty->assign('title', '产品测试信息');
		$this->smarty->display('firstPage.tpl');
	}

	public function producttypetestcase()
	{
		$crud = new grocery_CRUD();
		$crud->set_theme('datatables');
		$crud->required_fields('productType', 'testItem', 'stateFile', 'portNum');
		$crud->display_as('productType', '产品型号')->display_as('testItem', '测试项')->display_as('stateFile', '状态文件')->display_as('portNum', '端口数');
		$crud->set_relation('productType', 'productType', 'name');
		$crud->set_relation('testItem', 'testItem', 'name');
		$output = $crud->render();
		foreach ($output as $key=>$value)
		{
			$this->smarty->assign($key, $value);
		}
		$this->smarty->assign('title', '产品测试方案');
		$this->smarty->display('firstPage.tpl');
	}

	public function testitemmarkvalue()
	{
		$crud = new grocery_CRUD();
		$crud->set_theme('datatables');
		$crud->required_fields('testItemResult', 'value');
		$crud->display_as('testItemResult', '测试项目')->display_as('value', '结果值')->display_as('markF', '频率标记')->display_as('markT', '时间标记');
		$crud->set_relation('testItemResult', 'testItemResult', 'id');
		$output = $crud->render();
		foreach ($output as $key=>$value)
		{
			$this->smarty->assign($key, $value);
		}
		$this->smarty->assign('title', '测试项目配置');
		$this->smarty->display('firstPage.tpl');
	}

	public function testitemresult()
	{
		$crud = new grocery_CRUD();
		$crud->set_theme('datatables');
		$crud->required_fields('productTestInfo', 'testItem', 'img');
		$crud->display_as('productTestInfo', '产品测试信息')->display_as('testItem', '测试项目')->display_as('img', '测试图路径');
		$crud->set_relation('productTestInfo', 'productTestInfo', 'id');
		$crud->set_relation('testItem', 'testItem', 'name');
		$output = $crud->render();
		foreach ($output as $key=>$value)
		{
			$this->smarty->assign($key, $value);
		}
		$this->smarty->assign('title', '测试项目结果');
		$this->smarty->display('firstPage.tpl');
	}

	public function user()
	{
		$crud = new grocery_CRUD();
		$crud->set_theme('datatables');
		$crud->required_fields('username', 'password', 'team');
		$crud->display_as('username', '用户名')->display_as('password', '密码')->display_as('team', '组');
		$crud->set_relation('team', 'team', 'name');
		$output = $crud->render();
		foreach ($output as $key=>$value)
		{
			$this->smarty->assign($key, $value);
		}
		$this->smarty->assign('title', '系统用户');
		$this->smarty->display('firstPage.tpl');
	}

	public function firstpagenotice()
	{
		$crud = new grocery_CRUD();
		$crud->set_theme('datatables');
		$crud->required_fields('content');
		$crud->display_as('content', '通知内容');
		$crud->unset_add();
		$crud->unset_delete();
		$output = $crud->render();
		foreach ($output as $key=>$value)
		{
			$this->smarty->assign($key, $value);
		}
		$this->smarty->assign('title', '首页通知');
		$this->smarty->display('firstPage.tpl');
	}

	function index()
	{
		$this->smarty->assign('title', '首页');
		$this->smarty->assign('css_files', array());
		$this->smarty->assign('js_files', array());
		$this->smarty->assign('output', '');
		$this->smarty->display('firstPage.tpl');
	}
	
	/* add by gary */
	public function packingemployees()
	{
		$crud = new grocery_CRUD();
		$crud->set_theme('datatables');
		$crud->required_fields('employeeId', 'name', 'password');
		$crud->display_as('employeeId', '工号')->display_as('name', '姓名')->display_as('password', '密码');
		$output = $crud->render();
		foreach ($output as $key=>$value)
		{
			$this->smarty->assign($key, $value);
		}
		$this->smarty->assign('title', '包装用户');
		$this->smarty->display('firstPage.tpl');		
	}
	
}
