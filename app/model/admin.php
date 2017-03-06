<?php
namespace model;

class admin extends \app\model
{
	public static $table = "admin";

	public static $TYPE = [
		'ADMIN' => 0,
		'MANAGER' => 1
	];

	static function login($name,$password){
		$oAdmin = self::loadObj($name,'name');
		if(!$oAdmin){
			\except(-1,'找不到用户!');
		}
		
		if($oAdmin->data['password'] != $password){
			\except(-1,'密码错误!');
		}

		if ($oAdmin->data['deleted'] == 1) {
			\except(-1,'无效用户');
		}

		$oAdmin->updateCache();
		return $oAdmin;
	}
	
	function updateCache(){
		unset($this->data['password']);
		$_SESSION['user'] = $this->data;
	}
} 