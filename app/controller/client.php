<?php
namespace controller;

use common\ErrorCode;

class client extends \app\controller {
  //店铺所有分区
  function client_area_list(){
    $areas = $this->di['ClientService']->getArea();
    \vd($areas,'___');
    $this->data(['ls'=>$areas]);
  }

  function allot() {
    $__nav = 'home';
    // TODO-临时，name将来从登录中取
    $type = \model\admin::$TYPE['MANAGER'];
    if($_SESSION['user']['type'] == $type){
      $manager_name = $_SESSION['user']['name'];
      $manager_id = $_SESSION['user']['id'];
    }

    $oManager = \model\client_manager::loadObj($manager_name, 'manager_name');

    $__balance = $oManager->data['balance'];

    if (empty($__balance)) {
      $__balance = '0.00';
    }
    include \view('client__allot');
  }



  function aj_clients_by_manager() {
    $manager_name = $_SESSION['user']['name'];
  	$list = \model\client::finds("where manager_name='".$manager_name."' AND manager_name <> ''", 'id,deposit,storename');
  	$ls = [];
  	foreach($list as $item) {
  		$item['allot_amount'] = "0.00";
  		$ls[] = $item;
  	}
  	$this->data(['ls'=>$ls]);
  }


  function aj_allot_deposit() {
  	$manager_name = $_SESSION['user']['name'];
  	// $manager_id = 1;
  	$ls = str_replace('\\', '', $_GET['ls']);
  	$ls = json_decode($ls, true);
  	$total_allot_amount = 0;
  	foreach ($ls as $item) {
  		$total_allot_amount += $item['allot_amount'];
  		if ($item['allot_amount'] > 0) {
  			$oClient = \model\client::loadObj($item['id']);
        $before_amount = $oClient->data['deposit'];
  			$oClient->data['deposit'] += $item['allot_amount'];

        $oLogAccount = new \model\log_account;
        // {"变动金额":90.9216,"变动原因":"货物分拣扣款","客户":"沈阳肇工街店B1","变动前余额":"1299.18","变动后余额":1208.2584}
        $oLogAccount->data = [
          'client_id' => $item['id'],
          'amount' => $item['allot_amount'], // $price * $send_amount
          'code' => \model\log_account::$CODE['代理人分配'], //单品的标识
          'operator' => $manager_name, //单品的标识
          'create_at' => time(),
          'date_time' => \datetime(),
          'extra' => \en([
              '变动前余额' => number_format($before_amount, 2),
              '变动金额' => number_format($item['allot_amount'], 2),
              '变动后余额' => number_format($oClient->data['deposit'], 2),
              '变动原因' => '代理人分配金额',
              '客户' => $oClient->data['storename'],
            ]),
        ];
        $oLogAccount->save();
  			$oClient->save();
  		}
  	}
  	$oManager = \model\client_manager::loadObj($manager_name,'manager_name');
  	$oManager->data['balance'] = $oManager->data['balance'] - $total_allot_amount;
  	$oManager->save();
  	$this->data(true);
  }


  // 改变client中order的顺序
  function order_num() {
    $data = $_GET;
    // 接到数组
    $index = $data['baseidx'] + 1;
    \vd($index,'基数');
    $orderNum = [];
    $orderNum = $data['order'];
    \vd($orderNum,'数据');
    foreach ($orderNum as $id) {
      $oClient = \model\client::loadObj($id);
      $oClient->data['order'] = $index;
      $oClient->save();
      $index++;
      \vd($index,'新基数');
    }
    $clients = \model\client::finds(" where deleted = 0 and id in (".implode(',', $orderNum).") order by `order` ASC"); 
    \vd($clients,'新数据');
    $this->data(true);
  }

  /**
   * 门店列表页面
   * @return [type] [description]
   */
  function ls() {
    $price_type = \model\price_type::finds('');
    if (count($price_type) > 0) {
      $__price_type = json_encode($price_type);
    } else {
      $__price_type = '[]';
    }
    $__factory_ids = json_encode(\DataConfig::$FACTORY);
    include \view('client__ls');
  }

  /**
   * 门店列表请求
   * @return [type] [description]
   */
  function aj_ls() {
    $data = $_GET;
    $factory_id = $_SESSION['user']['factory_id'];
    $total = 0;
    $page_param = [
      'length' => 10,
      'page' => $data['page'],
    ];

    $sql_add = "WHERE 1=1";
    if (!empty($factory_id)) {
      $sql_add .= " and (factory_id=".$factory_id." or factory_id=0)";
    }

    // 查询条件文本框
    if (!empty($data['search_txt'])) {
      $sql_add .= " and (storename like '%".$data['search_txt']."%' or py like '%".$data['search_txt']."%')";
    }

    // 是否显示已删除记录
    if (empty($data['show_deleted'])) {
     $sql_add .= " and deleted=0"; 
    }

    $res = \model\client::finds($sql_add.' order by `order` asc', 'id,storename', $total, $page_param);

    $this->data([
      'ls' => $res,
      'total' => $total
    ]);
  }

  /**
   * 门店详细
   * @return [type] [description]
   */
  function aj_detail() {
    $data = $_GET;
    $oClient = \model\client::loadObj($data['id']);

    $this->data($oClient->data);
  }

  /**
   * 保存店铺详细信息
   * @return [type] [description]
   */
  function aj_save_detail(){
    $data = $_GET;
    if (empty($data['id'])) {
      $oClient = new \model\client;
    } else {
      $oClient = \model\client::loadObj($data['id']);
    }
    $oClient->data=[
      'storename' => $data['name'],
      'address' => $data['addr'],
      'phone' => $data['phone'],
      'manager_name' => $data['client_manager'],
      'py' => $data['py'],
      'deleted' => $data['deleted'],
      'update_at' => time(),
      'factory_id' => empty($data['factory_id']) ? $_SESSION['user']['factory_id'] : $data['factory_id']
    ]; 

    if (empty($data['id'])) {

      $oClient->data['create_at'] = time();
      $oClient->data['order'] = 990000;
      $oClient->data['username'] = $data['name'];
    }

    if (!empty($data['price_type_id'])) {
      // 更新价格
      $old_price_type_id = $oClient->data['price_type_id'];
      if ($old_price_type_id != $data['price_type_id']) {
        \model\price::sqlQuery("delete from njzs_price where client_id=".$data['client_id']);
      }

      $oClient->data['price_type_id'] = $data['price_type_id'];
    }

    $oClient->save();
    $this->data(['ok'=>1]);
  }

  /**
   * 门店余额页面
   * @return [type] [description]
   */
  function deposit() {
    include \view('client__deposit');
  }

  /**
   * 门店余额列表
   * @return [type] [description]
   */
  function aj_deposit_list() {
    $data = $_GET;
    $total = 0;
    $factory_id = $_SESSION['user']['factory_id'];
    $page_param = [
      'length' => 10,
      'page' => $data['page'],
    ];
    $sql_add = "";
    if (!empty($data['search_txt'])) {
      $sql_add .= " and (storename like '%".$data['search_txt']."%' or py like '%".$data['search_txt']."%') ";
    }
    if (!empty($factory_id)) {
      $sql_add .= " and (factory_id=".$factory_id." or factory_id=0)";
    }
    $order_str = "order by `order` asc";
    if (!empty($data['order'])) {
      if ($data['order']['order'] == "normal") {
        $order_str = "order by `order` asc";
      } else if ($data['order']['key'] == "deposit") {
        $order_str = "order by deposit ".$data['order']['order'];
      } else {
        $order_str = "order by `order` ".$data['order']['order'];
      }
    }
    $clients = \model\client::finds('where deleted=0 '.$sql_add.' '.$order_str, 'id,storename,deposit', $total, $page_param);

    $this->data(['ls' => $clients, 'total' => $total]);
  }

  /**
   * 余额变更界面
   * @return [type] [description]
   */
  function change_deposit() {
    $data = $_GET;
    $__id = $data['id'];
    $__deposit = $data['deposit'];
    $__client_name = $data['storename'];

    include \view('client__change_deposit');
  }

  /**
   * 余额变动记录
   * @return [type] [description]
   */
  function log_account() {
    $__id = $_GET['id'];
    include \view('client__log_account');
  }

  /**
   * 门店登录信息页面
   * @return [type] [description]
   */
  function login_detail() {
    $data = $_GET;
    $oClient = \model\client::loadObj($data['id']);
    $__id = $data['id'];
    $__username = $oClient->data['username'];
    include \view('client__login_detail');
  }

  /**
   * 变更门店登录记录
   * @return [type] [description]
   */
  function aj_update_login() {
    $data = $_GET;
    $oClient = \model\client::loadObj($data['id']);
    $oClient->data['username'] = $data['username'];
    if (!empty($data['password'])) {
      $oClient->data['password'] = md5($data['password']);
    }

    $oClient->save();
    $this->data(true);
  }

  /**
   * 门店分区
   * @return [type] [description]
   */
  function sort_area() {
    include \view("client__sort_area");
  }

  /**
   * 请求门店列表
   * @return [type] [description]
   */
  function aj_area_list() {
    $data = $_GET;
    $factory_id = $_SESSION['user']['factory_id'];
    $total = 0;
    $page_param = [
      'length' => 10,
      'page' => $data['page'],
    ];

    $sql_add = "WHERE 1=1";
    if (!empty($factory_id)) {
      $sql_add .= " AND (factory_id=".$factory_id." OR factory_id=0)";
    } 

    if (!empty($data['search_txt'])) {
      $sql_add .= " AND (area_name like '%".$data['search_txt']."%')";
    }

    $areas = \model\sort_area::finds($sql_add, 'id,area_name', $total, $page_param);
    $this->data([
      'ls' => $areas,
      'total' => $total
    ]);
  }

  /**
   * 未分区门店
   * @return [type] [description]
   */
  function aj_unclassified_client() {
    $data = $_GET;
    $total = 0;
    $factory_id = $_SESSION['user']['factory_id'];
    $page_param = [
      'length' => 10,
      'page' => $data['page'],
    ];

    $sql_add = "WHERE 1=1 ";
    if (!empty($factory_id)) {
      $sql_add .= " AND (factory_id=".$factory_id." OR factory_id=0)";
    }

    $areas = \model\sort_area::finds($sql_add);
    $clientIds = [];
    foreach ($areas as $area) {
      if (!empty($area['client_ids'])) {
        $clientId = explode(',', $area['client_ids']);
        $clientIds = array_merge($clientIds, $clientId);
      }
    }

    if (!empty($clientIds)) {
      $clientIds = implode(',', $clientIds);
      $sql_add .= " AND id NOT IN(".$clientIds.")";
    }

    if (!empty($data['search_txt'])) {
      $sql_add .= " AND (storename like '%".$data['search_txt']."%' OR py like '%".$data['search_txt']."%')";
    }

    $clients = \model\client::finds($sql_add." AND deleted=0 order by `order` asc", 'id,storename', $total, $page_param);

    $this->data([
      'ls' => $clients,
      'total' => $total
    ]);
  }

  /**
   * 根据area取client
   * @return [type] [description]
   */
  function aj_client_by_area() {
    $data = $_GET;
    $total = 0;
    $page_param = [
      'length' => 10,
      'page' => $data['page'],
    ];
    $factory_id = $_SESSION['user']['factory_id'];
    $sql_add = "WHERE 1=1 ";
    if (!empty($factory_id)) {
      $sql_add .= " AND (factory_id=".$factory_id." OR factory_id=0)";
    }
    $oArea = \model\sort_area::loadObj($data['area_id']);

    $sql_add .= " AND id IN(".$oArea->data['client_ids'].") AND deleted=0";

    if (!empty($data['search_txt'])) {
      $sql_add .= " AND (storename like '%".$data['search_txt']."%' OR py like '%".$data['search_txt']."%')";
    }

    $clients = \model\client::finds($sql_add." order by `order` asc, id asc", 'id,storename', $total, $page_param);

    $this->data([
      'ls' => $clients,
      'total' => $total
    ]);
  }

  /**
   * 分区之间移动
   * @return [type] [description]
   */
  function aj_move_client_for_area() {
    $data = $_GET;
    $from = $data['from_area_id'];
    $to = $data['to_area_id'];
    $client_id = $data['client_id'];

    $factory_id = $_SESSION['user']['factory_id'];
    $sql_add = "WHERE 1=1";
    if (!empty($factory_id)) {
      $sql_add .= " AND (factory_id=".$factory_id." OR factory_id=0)";
    }

    $area_ls = \model\sort_area::finds($sql_add);
    $area_ls = \indexBy($area_ls, 'id');


    if (empty($from)) {
      // 从未分区移动至某分区
      $area_id = $data['to_area_id'];
      $area = $area_ls[$area_id];
      $client_ids = [];
      if (!empty($area['client_ids'])) {
        $client_ids = explode(',', $area['client_ids']);
      }
      $client_ids[] = $client_id;
      $client_ids = implode(',', $client_ids);
      
      \model\sort_area::sqlQuery("UPDATE njzs_sort_area set client_ids='".$client_ids."' WHERE id=".$area_id);

      $sql_add .= " AND id IN(".$area['client_ids'].")";
      $client_ls = \model\client::sqlQuery("SELECT * from njzs_client ".$sql_add." order by `order` desc limit 1");

      $idx = $area_id * 10000;
      if (!empty($client_ls)) {
        $idx = $client_ls[0]['order'] + 1;
      }

      \model\client::sqlQuery("UPDATE njzs_client set `order`=".$idx." where id=".$client_id);

    } else if (empty($to)) {
      // 从某分区移动至未分区
      $area_id = $data['from_area_id'];
      $area = $area_ls[$area_id];
      $client_ids = explode(',', $area['client_ids']);
      foreach ($client_ids as $k => $v) {
        if ($v == $client_id) {
          unset($client_ids[$k]);
        }
      }
      $client_ids = implode(',', $client_ids);
      \model\sort_area::sqlQuery("UPDATE njzs_sort_area set client_ids='".$client_ids."' WHERE id=".$area_id);

      $clientIds = [];
      foreach ($area_ls as $area) {
        if (!empty($area['client_ids'])) {
          $clientId = explode(',', $area['client_ids']);
          $clientIds = array_merge($clientIds, $clientId);
        }
      }

      if (!empty($clientIds)) {
        $clientIds = implode(',', $clientIds);
        $sql_add .= " AND id NOT IN(".$clientIds.")";
      }

      $client_ls = \model\client::finds($sql_add." AND deleted=0 order by `order` desc limit 1", 'id,storename,`order`', $total, $page_param);

      $idx = 99 * 10000;
      if (!empty($client_ls)) {
        $idx = $client_ls[0]['order'] + 1;
      }

      \model\client::sqlQuery("UPDATE njzs_client set `order`=".$idx." where id=".$client_id);
    }

    $this->data(true);
  }

  function aj_sort_client() {
    $data = $_GET;
    $client_id = $data['client_id'];
    $area_id = $data['area_id'];
    $type = $data['type'];

    $oClient = \model\client::loadObj($client_id);
    $old_sort = $oClient->data['order'];
    
    if ($type == "up" && $old_sort > $area_id * 10000) {
      $new_sort = $old_sort - 1;
      $client = \model\client::find('where `order` ='.$new_sort);
      if ($client) {
        \model\client::sqlQuery('UPDATE njzs_client SET `order`='.$old_sort." WHERE id=".$client['id']);
        \model\client::sqlQuery("UPDATE njzs_client SET `order`=".$new_sort." WHERE id=".$client_id);
      }
    } else if ($type == "down" && $old_sort >= $area_id * 10000) {
      $new_sort = $old_sort + 1;
      $client = \model\client::find('where `order` ='.$new_sort);
      if ($client) {
        \model\client::sqlQuery('UPDATE njzs_client SET `order`='.$old_sort." WHERE id=".$client['id']);
        \model\client::sqlQuery("UPDATE njzs_client SET `order`=".$new_sort." WHERE id=".$client_id);
      }
    }

    $this->data(true);

  }

  function create_area() {
    $__factory_ids = json_encode(\DataConfig::$FACTORY);
    include \view('client__create_area');
  }

  function aj_save_area(){
    $data = $_GET;
    $res = \model\sort_area::loadObj($data['area_name']);
    if (!empty($res)) {
      $this->error(-1,"该分区已经存在");
    }

    $sort_area = new \model\sort_area();
    $sort_area->data = [
      'area_name' => $data['area_name'],
      'factory_id' => $data['factory_id'],
    ];
    $sort_area->save(); 
    $this->data(true);
  }

}
