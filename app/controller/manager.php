<?php
namespace controller;

use common\ErrorCode;

class manager extends \app\controller
{
	function index(){
		include \view('manager__index');
	}

	function update_manager(){
		include \view('manager__update_manager');
	}

	function manager_detail(){
		include \view("manager__manager_detail");
	}

	function add_manager(){
		$data = $_GET;
		$factory_id = $_SESSION['user']['factory_id'];
		$name = $data['name'];
		$password = $data['password'];
		$comfirm_password = $data['comfirm_password'];
		if( empty($name) || empty($password)){
			\except(-1,"帐号密码不能为空");
		}
		$manager = \model\admin::finds("where name='".$name."'");
		if( !empty($manager) ){
			\except(-1,"代理人帐号已存在");
		}

		if($password != $comfirm_password){
			\except(-1,"输入的密码不一致");
		}

		$oManager = new \model\admin;
		$oManager->data=[
			'name' => $name,
			'password' => $password,
			'type' => 1,
			'role' => 0,
			'factory_id' => $factory_id,
		];
		$oManager->save();

		$oClient_manager = new\model\client_manager;
		$oClient_manager->data=[
			'manager_name' => $name,
			'balance' => 0,
		];
		$oClient_manager->save();
		$this->data(["ok"]);
	}

	function update_password(){
		$data = $_GET;
		$id = $_SESSION['user']['id'];
		$new_password = $data['new_password'];
		$comfirm_password = $data['comfirm_password'];
		if($new_password != $comfirm_password){
			\except(-1,'输入的密码不一致');
		}
		if(empty($new_password) || empty($comfirm_password)){
			\except(-1,'输入密码不能为空');
		}

		$oManager = \model\admin::loadObj($id);
		$oManager->data['password'] = $new_password;
		$oManager->save();
		$this->data(["ok"]);


	}

	function manager_ls(){
		$factory_id = $_SESSION['user']['factory_id'];
		$type = \model\admin::$TYPE['MANAGER'];
		// $manager_names = [];
		$managers = \model\admin::finds("where type='".$type."' and factory_id='".$factory_id."'","name");
		$ls = [];
		foreach ($managers as $key => $name) {
			$res = \model\client::finds("where manager_name='".$name['name']."'", "id,storename,deposit,manager_name");
			\vd($res);
			if (count($res) > 1) {
				$client_manager = \model\client_manager::find("where manager_name='".$name['name']."'");
				foreach ($res as $item) {
					$client_manager['storename'] .= $item['storename'].",";
				}
				$client_manager['deposit'] = $client_manager['balance'];
				$ls[] = $client_manager;
				
			} else {
				$ls[] = $res[0];
			}
			// $manager_names[] = $name['name'];
		}

		// $manager_list = \model\client_manager::finds('where manager_name in ("'.implode('","', $manager_names).'")');
		// \vd($ls,'1111');
		$this->data(['ls' => $ls]); 
	}




}