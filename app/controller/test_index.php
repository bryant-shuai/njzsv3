<?php

namespace controller;

use \app\controller;
use common\ErrorCode;

class test_index extends controller
{
	function index(){
		include \view('test_index');
	}

	function search_client(){
		$data = $_GET;
		$clientIds = array($data['client_id']);
		vd($clientIds);
		if(!empty($clientIds)){
			$clients = \model\client::findByIds($clientIds);
			\vd($clients,'____');
		}
		
		$this->data(['ls'=>$clients]);
	}

	function ajax(){
		include \view('test_ajax');
	}

		
	function fetch(){
    $factory = \model\factory::$CONF_val_2_name['1'];
    \vd($factory,'###');
	}

	// function search_obj(){
	// 	$data = $_GET;
	// 	$clientIds = $data['client_id'];
	// 	$ls = [];
	// 	if(!empty($clientIds)){
	// 		foreach ($clientIds as $key => $id) {
	// 			$clients = \model\client::finds("where id='".$id."'");
	// 			$ls[] = $clients;
	// 		}
	// 		\vd($clients,'____');
	// 	}
		
	// 	$this->data(['ls'=>$ls]);
	// }
}


	