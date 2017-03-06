<?php

namespace controller;

use \app\controller;
use common\ErrorCode;

class order extends controller {
  /**
   * 订单详细页
   * @return [type] [description]
   */
  function order_detail() {
    $data = $_GET;
    if (!empty($data['id'])) {
      $order = \model\order::loadObj($data['id']);
      $__order = $order->data;
    } else {
      $product_id = $data['product_id'];
      $oPro = \model\product::loadObj($product_id);

      $client_id = $data['client_id'];
      $oClient = \model\client::loadObj($client_id);
      $product_price = $this->di['PriceService']->get($client_id, $product_id);
      $__order = [
        'product_name' => $oPro->data['name'],
        'product_id' => $product_id,
        'price' => $product_price,
        'storename' => $oClient->data['storename'],
        'client_id' => $client_id,
        'need_amount' => '0.00'
      ];
    }

    $__thedate = $data['thedate'];
    $__batch_id = $data['batch_id'];
    if (!empty($data['is_direct'])) {
      $__is_direct = $data['is_direct'];
    }
    include \view('order__order_detail'); 
  }

  //显示汇总订单
  function sum() {
    $orders = $this->di['OrderService']->getList($_GET['thedate'],$_GET['idx']);
    \vd($orders,'$orders');
    $this->data(['ls'=>$orders]);
  }

  //显示汇总订单
  function index() {
    include \view('order__index');
  }

  function aj_change_needamount_byid() {
    $data = $_POST;
    $oOrder = \model\order::loadObj($_GET['id']);
    $oOrder->data['need_amount'] = $data['val'];
    $oOrder->save();
    $this->data(['ok']);
  }

  function aj_change_needamount() {
    $data = $_POST;
    $data['thedate'] = str_replace('-','/', $data['thedate'] );
    $client_id = $data['client_id'];
    $product_id = $data['product_id'];
    $need_amount = (float) $data['need_amount'];

    $oOrder = new \model\order;

    $oClient = \model\client::loadObj($client_id);
    $oProduct = \model\product::loadObj($product_id);
    $unitPrice = $this->di['PriceService']->get($client_id, $product_id); //todo 读价格

    $oOrder->data = [
      'client_id' => $data['client_id'],
      'product_id' => $data['product_id'],
      'thedate' => $data['thedate'],
      'batch_id' => $data['batch_id'],
      'storename' => $oClient->data['storename'],
      'product_name' => $oProduct->data['name'],
      'price' => $unitPrice,  
      'need_amount' => $data['need_amount'],
      'create_at' => time(),
      'factory_id' => $oClient->data['factory_id']
    ];

    $oOrder->save();
    $this->data(['get'=>$_GET,'post'=>$_POST,]);
  }


  function aj_change_needamount_direct_byid() {
    $data = $_POST;
    // $data = $_GET;
    $lastid = $_GET['id'];
    $send_amount = (float) $data['val'];
    $oOrder = \model\order::loadObj($lastid);
    $data = $oOrder->data;

    $this->di['ChangeOrderService']->directSend($data['product_id'], $data['client_id'], $data['thedate'], $data['batch_id'], $send_amount);

    $this->data(['ok']);
  }

  function aj_change_needamount_direct() {
    $data = $_POST;
    // $data = $_GET;
    $need_amount = (float) $data['need_amount'];
    $data['thedate'] = str_replace('-','/', $data['thedate'] );

    $this->di['ChangeOrderService']->directSend($data['product_id'], $data['client_id'], $data['thedate'], $data['batch_id'],$need_amount);

    $this->data(['get'=>$_GET,'post'=>$_POST,]);
  }


  //显示汇总订单
  function diff() {
    $total = 0;
    $factory_id = $_SESSION['user']['factory_id'];
    $_GET['thedate'] = str_replace('-','/', $_GET['thedate'] );
    $orders = $this->di['OrderService']->getDiffList($_GET['search_txt'], $_GET['thedate'], $_GET['idx'], $_GET['page'], $total, $factory_id);
    \vd($orders,'$orders');
    $this->data(['ls'=>$orders, 'total'=>$total]);
  }

  function order_list(){
    $data = $_GET;
    $date = $data['thedate'];
    $count = 0;
    $orders = $this->di['OrderService']->searchOrder($data);
    \vd($orders,'$orders___');
    $this->data(['ls'=>$orders]);
  }

  //得到产品总需求量
  function get_product_needamount(){
    $data = $_GET;
    $orders = $this->di['OrderService']->productBySumAmount($data);
    \vd($orders,'_____');
    $this->data(['ls'=>$orders]);
  }

  // 导出订单
  function order_export() {
    $data = $_GET;
    ini_set("max_execution_time", 600);
    ini_set("memory_limit", 1048576000);

    $fromdate = $data['fromdate'];
    $todate = $data['todate'];
    // 把区域的名称转换成大写
    // $areas = isset($data["areas"]) ? strtoupper($_GET["areas"]) : "";
    $clientIds = $data['clientIds'];
    // ？？？
    $print = isset($data["for"]) && strtoupper($_GET["for"]) == "PRINT" ? true : false;

    $result = $this->di["ExcelService"]->order_export($fromdate,$todate ,$clientIds, "need_amount", $print);
    $this->data([]);

  }


  // 测试

  // function test() {
  //   $orders = \model\order::finds(" where deleted = 0 limit 10");
  //   $sorted_orders = \indexSet($orders,'client_id');
  //   \vd($sorted_orders,'222222');
  //   foreach ($sorted_orders as $key => $sorted_order) {
  //     $sorted_orders[$key] = \indexArray($sorted_order, "product_id");
  //   }

  // }























}
