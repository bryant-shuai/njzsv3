<?php

namespace service;

use model\order as OrderModel;
use app\service as Service;

class OrderService extends Service {

  //数据列表，带id
  function getList($thedate,$idx=null,$factory_id = null){
    $r = [];
    $sql = '';
    if($idx){
      $sql = " AND batch_id='".$idx."'";
    }

    $orders = \model\order::finds(" WHERE thedate='$thedate' 
      ".$sql." and deleted=0 and factory_id= '".$factory_id." ' group by client_id,product_id ",' id,client_id,storename,product_id,product_name,price,need_amount,send_amount,get_amount,thedate');

    foreach ($orders as $order) {
      $r[] = $order;
    }
    \vd($r,'$r');
    return $r;
  }

  //有出入的数据列表
  function getDiffList($search_txt, $thedate,$idx=null, $page, &$total, $factory_id){
    $r = [];
    $sql = '';
    if($idx){
      $sql = " AND batch_id='".$idx."' ";
    }
    \vd($page);
    $page_param = [
      'length' => 10,
      'page' => $page,
    ];
    if (!empty($search_txt)) {
      $sql .= " AND (storename like '%".$search_txt."%' or product_name like '%".$search_txt."%')";
    }

    if (!empty($factory_id)) {
      $sql .= " AND (factory_id=".$factory_id." or factory_id=0)";
    }
    $orders = \model\order::finds(" WHERE thedate='$thedate' 
      ".$sql." and need_amount<>send_amount and deleted=0",'id,to_id,storename,product_name,need_amount,send_amount,from_id',$total,$page_param);

    foreach ($orders as $order) {
      $order['diff'] = number_format($order['need_amount'] - $order['send_amount'], 2);

      $r[] = $order;
    }
    \vd($r,'$r');
    return $r;
  }

  //保存商店操作
  function moveOrder($id, $thedate, $batch_id, $amount=0) {
    $oOrder = \model\order::loadObj($id);
    if($oOrder->data['to_id']>0){
      \except(-1,'已经转过');
    }
    $oNextOrder = new \model\order;

    $amount = $oOrder->data['need_amount'] - $oOrder->data['send_amount'];
    \copyArr($oNextOrder->data,$oOrder->data,'client_id,storename,product_id,product_name,price');
    $oNextOrder->data['thedate'] = $thedate;
    $oNextOrder->data['batch_id'] = $batch_id;
    $oNextOrder->data['need_amount'] = $amount;
    $oNextOrder->data['from_id'] = $id;
    $oNextOrder->data['create_at'] = time();
    $oNextOrder->data['factory_id'] = $oOrder->data['factory_id'];
    $oNextOrder->save();

    $oOrder->data['to_id'] = $oNextOrder->data['id'];
    $oOrder->save();

  }
  //删除之前批次，保存移动信息
  function removeOrder($id){
    $oNextOrder = \model\order::loadObj($id);
    $oOrder = \model\order::loadObj($oNextOrder->data['from_id'],'id');
    // 修改to_id->0
    $oOrder->data['to_id'] = 0;
    $oOrder->save();
    \model\order::deleteById($id);
  }


  //判断新建日期批次，保存新建信息
  function createBatch($thedate=null, $idx,$factory_id) {
    if (empty($idx) || empty($thedate)) {
      \except(-1,'请同时输入日期和批次');
    }
    $sql_add = "";
    if (!empty($factory_id)) {
      $sql_add = " and (factory_id=".$factory_id." or factory_id=0)";
    }
    $f = \model\order_batch::find(" WHERE thedate='".$thedate."' AND idx='".$idx."' ".$sql_add);
    if($f){
      \except(-1,'已找到');
    }
    $ob = new \model\order_batch;
    $ob->data = [
      'thedate' => $thedate,
      'idx' => $idx,
      'factory_id' => $factory_id,
    ];
    $ob->save();
    return $ob;
  }

  //查单条，或多条日期批次
  function getOrderBatches($thedate=null, $page=1, &$total=0) {
    $sql = "";
    if(!empty($thedate)){
      $sql .= " AND thedate='".$thedate."' ";
    }
    // if($facId){
    //   $sql = " AND factory_id='".$facId."' ";
    // }
    if ($page == 0) {
      $page = 1;
    }
    $page_param = [
      'length' => 10,
      'page' => $page,
    ];

    if (!empty($_SESSION['user']['factory_id'])) {
      $sql .= " AND (factory_id='".$_SESSION['user']['factory_id']."' or factory_id=0) ";  
    }
    $batchs = \model\order_batch::finds(" where id>0 ".$sql." ORDER BY thedate desc,idx desc", "thedate,idx", $total, $page_param);
    \vd($batchs);
    return $batchs;
  }

  //每日的产品需求
  function getProductNeed($thedate=null) {
    $sql = '';
    if ($thedate) {
      $sql = " AND thedate='".$thedate."'";
    }
    // if (empty($thedate)) {
    //   $sql = " AND thedate = '2016/10/13'";
    // }
    $products = \model\order::finds(" where id>0 ".$sql." GROUP BY product_name ORDER BY id"," id ,product_name as name");
    return $products;
  }

  // 根据client_id和product_id 批量查询
  function dayProductNeed($data) {
    $sql = '';

    if (!empty($data['thedate'])) {
      $sql .= " AND thedate='".$data['thedate']."'";
    }

    if (!empty($data['batch_id'])) {
      $sql .= " AND batch_id='".$data['batch_id']."'";
    }

    // $sql = " AND thedate = '2016/10/13'";
    if (!empty($data['product_id'])) {
      $sql .= " AND product_id in (".$data['product_id'].")";
    }
    if (!empty($data['client_id'])) {
      $sql .= " AND client_id in (".$data['client_id'].")";
    }
    if (!empty($data['factory_id'])) {
      $sql .= " AND (factory_id =".$data['factory_id']." or factory_id=0)"; 
    }
    $products = \model\order::finds(" where id>0 ".$sql, 'id,product_name,storename,need_amount');
    // \vd($products,'!!!!!!!!!');
    return $products;
  }

  //查看今天，或之前的记录
  function searchOrder($data){
    if(empty($data['thedate'])){
      $data['thedate'] = date("Y/m/d");
    }
    $batch_sql="";
    if(!empty($data['batch_id'])){
      $batch_sql = " and batch_id='".$data['batch_id']."'";
    }
    $store_sql="";
    if(!empty($data['client_id'])){
      $store_sql = " and client_id='".$data['client_id']."'";
    }
    $product_sql="";
    if(!empty($data['product_id'])){
      $product_sql = " and product_id='".$data['product_id']."'";
    }

    $count = 0;
    $orders = \model\order::finds("where thedate='".$data['thedate']."' ".$batch_sql." ".$store_sql." ".$product_sql." and deleted=0 group by client_id,product_id,storename,product_name",'*',$count);
    $orders = \indexBy($orders,'id');
    return $orders;
  }


  //某一天订单中产品需求量集合!
  function productBySumAmount($data){
    if(empty($data['thedate'])){
      $data['thedate'] = date("Y/m/d");
    }

    $sql_batch = "";
    if( !empty($data['batch_id']) ){
      $sql_batch = " and batch_id='".$data['batch_id']."' ";
    }

    $orders = \model\order::finds("where thedate='".$data['thedate']."' ".$sql_batch." group by product_name order by product_id asc",'id,product_id,product_name,sum(need_amount) as need_amount');
    return $orders;
  }


  
  //
  function getOrderBySortList($clients,$thedate,$batch_id,$factory_id){
    $r = [];
    $sql = "";
    // if($thedate){
    //   $thedate = data("Y/m/d",);
    // }
    if($clients){
      $sql.=" and o.client_id in (".$clients.")";
    }
    if($batch_id){
      $sql.=" and o.batch_id='".$batch_id."'"; 
    }
    if($factory_id){
      $sql.=" and o.factory_id='".$factory_id."'";
    }
    // $orders = \model\order::finds("where  ".$sql." group by client_id,product_id",' id,client_id,storename,product_id,product_name,price,need_amount,send_amount,get_amount,thedate,batch_id,cost');

    $orders = \model\order::sqlQuery("select o.* from njzs_order o, njzs_client c, njzs_product p where o.client_id = c.id and o.product_id = p.id and o.thedate='".$thedate."' ".$sql." group by o.client_id, o.product_id order by p.`order` desc,c.`order` asc");

    // \vd($r,'@@@@');
    return $orders;
  }

  // 处理分拣结果
  function dealSortData($sort_data,$filename,$md5,$read_content) {
    // 判断这个文件是不是处理过
    $oSortFileLog = \model\sortfile_log::find(" where md5 = '".$md5."'");
    // \vd($oSortFileLog,'查询结果');
    if (!empty($oSortFileLog)) {
      \except(-1,'此订单已经做了上传操作，请勿重复上传');
    }
    $arr = [];
    $before_amount = [];
    foreach ($sort_data as $order_id => $send_amount) {
      $send_amount = round($send_amount, 2);
      $oOrder = \model\order::loadObj($order_id,'id');

      $price = $oOrder->data['price'];
      $total_amount = round($send_amount * $price, 2);

      $client_id = $oOrder->data['client_id'];
      $oClient = \model\client::loadObj($client_id,'id');
      $deposit = $oClient->data['deposit'];
      if (!$before_amount[$client_id]) {
        $before_amount[$client_id] = $deposit;
      }
      // 余额不足直接扣成负的
      $oClient->data['deposit'] = $deposit - $total_amount;

      $oLogAccount = new \model\log_account;
      // {"变动金额":90.9216,"变动原因":"货物分拣扣款","客户":"沈阳肇工街店B1","变动前余额":"1299.18","变动后余额":1208.2584}
      $oLogAccount->data = [
        'client_id' => $oClient->data['id'],
        'amount' => -$total_amount, // $price * $send_amount
        'code' => \model\log_account::$CODE['分拣扣款'], //单品的标识
        'create_at' => time(),
        'date_time' => \datetime(),
        'extra' => \en([
            '变动金额' => number_format(-$total_amount, 2),
            '变动原因' => '分拣明细扣款',
            'order_id' => $order_id,
            '客户' => $oClient->data['storename'],
          ]),
      ];

      // 每一家店的汇总统计
      if(!$arr[$client_id]) $arr[$client_id] = 0;
      $arr[$client_id] += $total_amount; 

      $oLogAccount->save();
      $oOrder->save([
        'send_amount' => $send_amount,
        'cost' => $total_amount
      ]);
      $oClient->save();
    }
    // 保存某一家店的订单总金额
    foreach ($arr as $client_id => $total_amount) {
      $oClient = \model\client::loadObj($client_id);
      $oLogAccount = new \model\log_account;
      $oLogAccount->data = [
        'client_id' => $client_id,
        'operator' => $_SESSION['user']['name'],
        'amount' => -$total_amount,
        'code' => \model\log_account::$CODE['分拣扣款汇总'], // 店面汇总的标识
        'create_at' => time(),
        'date_time' => \datetime(),
        'extra' => \en([
            '变动金额' => number_format(-$total_amount, 2),
            '变动前余额' => number_format($before_amount[$client_id], 2),
            '变动后余额' => number_format($oClient->data['deposit'], 2),
            '变动原因' => '货物分拣扣款',
            '客户' => $oClient->data['storename'],
          ]),
      ];
      $oLogAccount->save();
    }
    $oSortFileLog = new \model\sortfile_log;
    $oSortFileLog->save([
        'user_id' => $_SESSION['user']['id'],
        'file_name' => $filename,
        'create_at' => time(),
        'md5' => $md5,
        'data' => $read_content,
      ]);

  }


 
  //获得订货的客户
  function getOrderInfoWithSortArea($args = [
      'thedate' => NULL,
      'batch_id' => NULL,
      'sort_area_id' => NULL,
      'factory_id' => NULL,
    ]){

    $thedate = $args['thedate'];
    $batch_id = $args['batch_id'];
    $sortAreaId = $args['sort_area_id'];
    $factory_id = $args['factory_id'];

    // 传入分区id
    $oSortArea = \model\sort_area::loadObj($sortAreaId);
    $clients = $oSortArea->data['client_ids'];
    $area_name = $oSortArea->data['area_name'];

    if( empty($thedate) || empty($batch_id) ){
      \except(-1,'请输入日期和批次!');
    }

    //取订单
    $orders = $this->di['OrderService']->getOrderBySortList($clients,$thedate, $batch_id, $factory_id);
    \vd($orders,'orders');
    $orders = \indexBy($orders,'id'); //做索引

    $pickInfos = \multiPickBy($orders,['client_id','product_id']);

    // var_dump($pickInfos);

    $client_ids = $pickInfos['client_id'];
    $product_ids = $pickInfos['product_id'];
    // 今日门店
    $clients = \model\client::findByIds(array_values($client_ids),'id,storename,username,py,factory_id,py,create_at,update_at', 'id', 'order by `order` asc');

    $client_cates = [];
    foreach ($clients as $key => $client) {
      $client_cates[$area_name][] = $client['id'];
    }
    \vd($client_cates,'####');

    // 今日产品
    $products = \model\product::findByIds(array_values($product_ids), '*', 'id', " order by `order` desc");
      // 今日产品分类
    $pickCates = \multiPickBy($products,['product_type']);
    $cateIds = $pickCates['product_type'];
    $cates = \model\product_type::findByIds(array_values($cateIds));

    // 重装数组
    $arr = [];

    $tmp_arr = array();
    foreach ($cates as $cate) {
      $tmp_arr[$cate['id']] = $cate['name'];
    }

    $pro_cate_arr = [];
    foreach ($products as $product) {
      $pro_cate_arr[$product['product_type']][] = $product['id'];
    }

    $cate_pro = [];
    foreach ($pro_cate_arr as $key => $value) {
      $cate_pro[$tmp_arr[$key]] = $value;
    }
    
    // \vd($cate_pro,'$$$$');

    $sort_orders = [
      'orders' => $orders ,
      'clients' => $clients,
      'products' => $products,
      'cates' => $cate_pro,
      'client_cates' => $client_cates,
    ];

    return $sort_orders;

  }


 
  //获得订货的客户
  function getOrderInfo($args = [
      'thedate' => NULL,
      'batch_id' => NULL,
      'factory_id' => NULL,
      'sort_area' => NULL
    ]){

    $thedate = $args['thedate'];
    $batchId = $args['batch_id'];
    $factory_id = $args['factory_id'];
    if (empty($args['sort_area'])) {
      $areas = \model\sort_area::finds("where factory_id='".$factory_id."'");   
    } else {
      $areas = \model\sort_area::finds("where id=".$args['sort_area']);   
    }
    

    $res = [
      'orders' => [],
      'clients' => [],
      'products' => [],
      'cates' => [],
      'client_cates' => [],
    ];

    foreach ($areas as $area) {
      $data = $this->getOrderInfoWithSortArea([
        'thedate' => $thedate,
        'batch_id' => $batchId ,
        'sort_area_id' => $area['id'], 
        'factory_id' => $factory_id,
      ]);

      $res['orders'] = $data['orders'] + $res['orders'];
      $res['clients'] = $data['clients'] + $res['clients'];
      $res['products'] = $data['products'] + $res['products'];
      $res['cates'] = $data['cates'] + $res['cates'];
      $res['client_cates'] = $data['client_cates'] + $res['client_cates'];
    }
    return $res;
  }



  function getOrderByClient($thedate,$batchId,$clientId){
    $data = \model\order::finds("where thedate='".$thedate."' and batch_id='".$batchId."' and client_id='".$clientId."'");
    return $data;
  }

  function getOrdersByDate($start_at, $end_at, $factory_id) {
    $orders = \model\order::sqlExec("SELECT `client_id`, `product_id`, `price`, SUM(`need_amount`) AS `need_amount`, SUM(`send_amount`) AS `send_amount`, SUM(`get_amount`) AS `get_amount`, FROM `njzs_order` 
      WHERE `thedate` >= '".$start_at."' AND `thedate` < '".$end_at."' AND `deleted`=0 AND factory_id=".$factory_id." GROUP BY `client_id`, `product_id`;");
    return $orders;
  }

  //////// export
  function queryOrderStatistics($dates, $type, $filter = [], $strict = false, $factory_id) {
    $fromdate = date("Y/m/d");
    if (isset($dates["fromdate"])) {
      $fromdate = $dates["fromdate"];
    }
    $todate = date("Y/m/d", time() + 86400);
    if (isset($dates["todate"])) {
      $todate = date("Y/m/d", strtotime($dates["todate"]) + 86400);
    }

    $statistics = $this->statistics($dates, $type, $factory_id);
    return $statistics;
  }

  function statistics($dates, $key = "product_id", $factory_id)
    {
        $fromdate = date("Y/m/d");
        if (isset($dates["fromdate"])) {
            $fromdate = $dates["fromdate"];
        }
        $todate = date("Y/m/d", time() + 86400);
        if (isset($dates["todate"])) {
            $todate = date("Y/m/d", strtotime($dates["todate"]) + 86400);
        }

        //准备订单统计的原生sql语句
        if ($key == "product_id") {
            $sql = "SELECT `$key`, SUM(`need_amount`) need_amount, SUM(`send_amount`) send_amount, SUM(`get_amount`) get_amount from `njzs_order` where `deleted` = 0 AND `thedate` >= '$fromdate' AND `thedate` < '$todate' AND `factory_id`= $factory_id GROUP BY `$key` ORDER BY `$key`;";
        } else {
            $sql = "SELECT `$key`, SUM(`need_amount` * `price`) price, SUM(`need_amount`) need_amount, SUM(`send_amount`) send_amount, SUM(`get_amount`) get_amount from `njzs_order` where `deleted` = 0 AND `thedate` >= '$fromdate' AND `thedate` < '$todate' AND `factory_id`=$factory_id GROUP BY `$key` ORDER BY `$key`;";
        }

        //数据查询
        $orderCounts = \model\order::sqlExec($sql);
        if ($orderCounts === false) {
            // throw new ErrorObject(ErrorCode::ORDER_STATISTICS_FAIL);
        }
        $orderCounts = \unsetNumberKey($orderCounts);

        if ($key == "product_id") {
            //查询Product信息,若查询到则向订单中拼入产品信息
            $products = $this->di["ProductService"]->getList();
            if (!$products) {
                $products = [];
            }
            $keymap = ["name" => "product_name"];
            $orderCounts = \combineArray($orderCounts, "product_id", $products, "id", $keymap);
        } else {
            $client = $this->di["ClientService"]->getList();
            // \vd($client,'$client');
            // $client = $this->di["ClientService"]->query($where);
            // if (!$client) {
            //     $client = [];
            // }
            $keyMap = [
                "storename" => "storename",
                "area" => "store_area",
                "phone" => "store_phone",
                "order" => "store_order",
                "deposit" => "store_deposit",
            ];
            $orderCounts = \combineArray($orderCounts, "client_id", $client, "id", $keyMap);
            $orderCounts = \sortBy($orderCounts,'store_order');
        }

        return $orderCounts;
    }











  // //汇总数据
  // function getGroupByList($thedate,$args){
  //   $r = [];
  //   $orders = \model\order::finds(" WHERE thedate='$thedate' group by client_id,product_id ",'*,sum(need_amount) as sum ');
  //   foreach ($orders as $order) {
  //     $r[] = $order;
  //   }
  //   \vd($r,'$r');
  //   return $r;
  // }


  // //汇总数据
  // function sumByDateSortByProduct($thedate){
  //   $r = [];
  //   $orders = \model\order::finds(" WHERE thedate='$thedate' group by client_id,product_id ",'*,sum(need_amount) as sum ');
  //   foreach ($orders as $order) {
  //     $r[] = $order;
  //   }
  //   \vd($r,'$r');
  //   return $r;
  // }
  
  // //汇总数据
  // function sumByDateSortByClient($thedate){
  //   $r = [];
  //   $orders = \model\order::finds(" WHERE thedate='$thedate' group by client_id,product_id ",'*,sum(need_amount) as sum ');
  //   foreach ($orders as $order) {
  //     $r[] = $order;
  //   }
  //   \vd($r,'$r');
  //   return $r;
  // }

}