<?php
namespace controller;
use \app\controller;

class admin extends controller {
	/**
	 *  账号列表
	 * @return [type] [description]
	 */
	function users() {
		include \view('admin__users');
	}

	/**
	 * 查找用户列表
	 * @param  $search_txt 账号或角色
	 * @return [type] [description]
	 */
	function aj_users_list() {
		$data = $_GET;
		$sql_add = "";

		$factory_id = $_SESSION['user']['factory_id'];
		if (!empty($factory_id)) {
			$sql_add .= " and factory_id=".$factory_id;
		}
		
		if (!empty($data['search_txt'])) {
			$sql_add = " and (role like '%".$data['search_txt']."%' or name like '%".$data['search_txt']."%') ";
		}
		$page_param = [
			'length' => 10,
			'page' => $data['page'],
		];
		$total = 0;
		$res = \model\admin::finds("where type = ".\model\admin::$TYPE['ADMIN'].$sql_add." order by deleted asc, id desc", "id,name,role,create_at,deleted,factory_id", $total, $page_param);

		$permissions = \model\permission::finds('');
		$permissions = \indexBy($permissions);
		$roles = \model\role::finds('');
		$roles = \indexBy($roles);

		foreach ($res as &$item) {
			$r_p_list = $roles[$item['role']]['permissions'];
			$r_p_list = explode(',', $r_p_list);
			\vd($r_p_list);
			$p_list = [];
			foreach ($r_p_list as $p_id) {
				if (!empty($p_id)) {
					$p_list[] = $permissions[$p_id]['name'];
				}
			}
			$item['role'] = $roles[$item['role']]['name'];
			$item['permission'] = $p_list;
			$item['create_at'] = date('Y-m-d H:i:s', $item['create_at']);
			$item['factory_id'] = \DataConfig::$FACTORY[$item['factory_id']];
		}

		$this->data([
			'ls' => $res,
			'total' => $total
		]);
	}

	/**
	 * 用户详细页
	 * @param  $id 用户ID
	 * @param  $type 类型，区分是账号界面还是代理人界面
	 * @return [type] [description]
	 */
	function user_detail() {
		$data = $_GET;
		$__id = empty($data['id']) ? '' : $data['id'];
		$__type = $data['type'];
		$roles = \model\role::finds('');
		if (count($roles) == 0) {
			$__roles = '[]';
		} else {
			$__roles = json_encode($roles);	
		}

		$__factory_ids = json_encode(\DataConfig::$FACTORY);

		if (!empty($data['id'])) {
			$user = \model\admin::loadObj($data['id']);
			$__user = $user->data;
		}
		
		include \view('admin__user_detail');
	}

	/**
	 * 保存用户信息
	 * @param $id ID存在时为保存，不存在时为新增
	 * @return [type] [description]
	 */
	function aj_user_detail() {
		$data = $_GET;
		// 新建
		if (empty($data['id'])) {
			$exists = \model\admin::loadObj($data['formItem']['name'], 'name');
			if ($exists) {
				$this->error('-1', '用户名已存在');
			}

			$user = new \model\admin;
			$user->data['create_at'] = time();
			$user->data['type'] = $data['type'];
			
			// 初始化代理人余额
			if ($data['type'] == \model\admin::$TYPE['MANAGER']) {
				$manager = new \model\client_manager;
				$manager->data['manager_name'] = $data['formItem']['name'];
				$manager->save();
			}
		} else {
			$user = \model\admin::loadObj($data['id']);
		}

		$user->data['name'] = $data['formItem']['name'];
		$user->data['password'] = $data['formItem']['password'];
		if (!empty($data['formItem']['role'])) {
			$user->data['role'] = $data['formItem']['role'];
		}
		$user->data['update_at'] = time();
		$user->data['factory_id'] = empty($data['formItem']['factory_id']) ? $_SESSION['user']['factory_id'] : $data['formItem']['factory_id'];
		$user->save();


		$this->data('');
	}

	/**
	 * 删除用户
	 * @return [type] [description]
	 */
	function aj_user_delete() {
		$data = $_GET;
		$id = $data['id'];
		$user = \model\admin::loadObj($id);
		$user->data['deleted'] = 1;
		$user->save();
		$this->data('');
	}

	/**
	 * 角色界面
	 * @return [type] [description]
	 */
	function role() {
		$factory_id = $_SESSION['user']['factory_id'];
		$sql_add = "";
		if (!empty($factory_id)) {
			$sql_add .= 'where (factory_id = '.$factory_id.' or factory_id = 0)';
		}
		$roles = \model\role::finds($sql_add.' order by id desc');
		if (count($roles) > 0) {
			$__roles = json_encode($roles);
		} else {
			$__roles = '[]';
		}

		$permissions = \model\permission::finds('');
		$__permissions = json_encode($permissions);
		
		include \view('admin__role');
	}

	/**
	 * 添加角色
	 * @return [type] [description]
	 */
	function aj_role_add() {
		$data = $_GET;
		$exists = \model\role::loadObj($data['name'], 'name');
		if ($exists) {
			$this->error('-1', '角色名已存在');
		}

		$role = new \model\role;
		$role->data['name'] = $data['name'];
		$role->data['factory_id'] = $_SESSION['user']['factory_id'];
		$role->save();

		$roles = \model\role::finds('order by id desc');
		$this->data($roles);
	}

	/**
	 * 根据角色ID 加载权限
	 * @return [type] [description]
	 */
	function aj_load_permission() {
		$data = $_GET;
		$role = \model\role::loadObj($data['role']);
		$role = $role->data;
		$res = [];
		if (!empty($role['permissions'])) {
			$roles = explode(',', $role['permissions']);
			foreach ($roles as $item) {
				if (!empty($item)) {
					$res[] = $item;
				}
			}
		}
		
		$this->data($res);
	}

	/**
	 * 删除角色
	 * @return [type] [description]
	 */
	function aj_delete_role() {
		$data = $_GET;
		$role = $data['role'];
		\model\role::sqlQuery("delete from njzs_role where id=".$role);
		\model\role::sqlQuery("update njzs_admin set role=0 where role=".$role);
		$roles = \model\role::finds('order by id desc');
		$this->data($roles);
	}

	/**
	 * 变更权限
	 * @return [type] [description]
	 */
	function aj_set_permission() {
		$data = $_GET;
		$role = \model\role::loadObj($data['role']);
		$role->data['permissions'] = $data['permissions'];
		$role->save();
		$this->data('');
	}

	/**
   * 代理人列表界面
   * @return [type] [description]
   */
  function manager_list() {
    include \view('admin__manager_list');
  }

	/**
	 * 代理人列表
	 * @return [type] [description]
	 */
	function aj_manager_list() {
		$data = $_GET;
		$page_param = [
			'length' => 10,
			'page' => $data['page'],
		];
		$total = 0;
		$factory_id = $_SESSION['user']['factory_id'];
		$sql_add = "";
		if (!empty($data['search_txt'])) {
      		$sql_add .= " and c.manager_name like '%".$data['search_txt']."%' or c.storename like '%".$data['search_txt']."%'";
		}

		if (!empty($factory_id)) {
			$sql_add .= " and (a.factory_id=".$factory_id." or a.factory_id=0)";
		}

		$m_list = \model\admin::sqlQuery("SELECT a.id, a.name, c.storename, c.deposit,count(c.storename) AS clients_count, a.deleted, a.factory_id FROM njzs_admin a LEFT JOIN njzs_client c ON a.name = c.manager_name WHERE a.type=".\model\admin::$TYPE['MANAGER'].$sql_add." GROUP BY a.name ORDER BY c.`order` DESC, a.id ASC LIMIT ".($data['page'] -1) * $page_param['length'].", ".$page_param['length']);
		
		$t_res = \model\admin::sqlQuery("SELECT a.id FROM njzs_admin a LEFT JOIN njzs_client c ON a.name = c.manager_name WHERE a.type=".\model\admin::$TYPE['MANAGER'].$sql_add." GROUP BY a.name ORDER BY c.`order` DESC ");
		$total = count($t_res);

		foreach ($m_list as &$item) {
			if ($item['clients_count'] == 0) {
				$item['clients'] = [];
			} else if ($item['clients_count'] >= 1) {
				$clients = [
					[
						'storename' => $item['storename']
					]
				];

				$manager = \model\client_manager::find("where manager_name='".$item['name']."'");
				$item['can_allot'] = $manager['balance'];
				if ($item['clients_count'] > 1) {
					$clients = \model\client::finds("where manager_name='".$item['name']."'", 'storename, deposit');
					$deposit = $manager['balance'];
					foreach ($clients as $c) {
						$deposit += $c['deposit'];
					}
					$item['deposit'] = $deposit;
				}
				$item['clients'] = $clients;
				$item['factory_id'] = \DataConfig::$FACTORY[$item['factory_id']];
			}
		}
		$this->data(['ls' => $m_list, 'total' => $total]);
	}

	/**
	 * 导出代理人分配记录
	 * @return [type] [description]
	 */
	function export_manager_allot_his() {

		include \view("admin__export_manager_allot_his");
	}

	/**
	 * 登录页面
	 * @return [type] [description]
	 */
	function login() {
		include \view("admin__login");
	}

	/**
	 * 登录
	 * @return [type] [description]
	 */
	function aj_login(){
		$data = $_POST;
		$name = $data['name'];
		$password = $data['password'];
		if( empty($name) || empty($password)){
			$this->error(-1,'帐号密码不能为空!');
		}else{
			$oAdmin = \model\admin::login($name, $password);
			// 整理权限相关
			$user = $_SESSION['user'];
			$oRole = \model\role::loadObj($user['role']);
			$p_res = \model\permission::finds('');
			$p_res = \indexBy($p_res, 'id');
			if (!empty($oRole->data['permissions'])) {
				$permissions = explode(',', $oRole->data['permissions']);
				$p_list = [];
				foreach ($permissions as $item) {
					if (!empty($item)) {
						$tmp = $p_res[$item];
						$p_list[$tmp['url']] = $tmp['url'];
					}
				}
				$_SESSION['user']['permissions'] = $p_list;
			} else {
				$_SESSION['user']['permissions'] = [];
			}

			// 目前系统中所有需要验证权限url
			$urls = [];
			foreach ($p_res as $item) {
				if (!empty($item)) {
					$urls[$item['url']] = $item['url'];
				}
			}
			$_SESSION['user']['urls'] = $urls;


			\vd($_SESSION['user']);
			
			$this->data(['type'=>$_SESSION['user']['type']]);
			
		}
		\vd($_SESSION['user'],'ffffff');
	}

	/**
	 * 登出
	 * @return [type] [description]
	 */
	function logout(){
		unset($this->data['password']);
		unset($_SESSION['user']);
		header("Location:/index");
	}

	/**
	 * 修改密码页面
	 * @return [type] [description]
	 */
	function change_pwd() {
		include \view('admin__change_pwd');
	}

	/**
	 * 修改密码请求
	 * @return [type] [description]
	 */
	function aj_update_pwd() {
		$data = $_POST;
		$oUser = \model\admin::loadObj($_SESSION['user']['id']);
		$oUser->data['password'] = $data['password'];
		$oUser->save();
		$this->data(true);
	}
}
