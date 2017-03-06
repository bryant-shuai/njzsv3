<?php

namespace controller;

use \app\controller;
use common\ErrorCode;

class pricetype extends controller {
  /**
   * 价格类型页面
   * @return [type] [description]
   */
  function ls() {
    $factory_id = $_SESSION['user']['factory_id'];
    $sql_add = "where 1=1";
    if (!empty($factory_id)) {
      $sql_add .= " and (factory_id=".$factory_id." or factory_id=0)";
    }

    $price_type = \model\price_type::finds($sql_add);
    if (count($price_type) > 0) {
      $__price_type = json_encode($price_type);
    } else {
      $__price_type = '[]';
    }
    
    include \view('pricetype__ls');
  }

  /**
   * 添加价格类型
   * @return [type] [description]
   */
  function aj_add_price_type(){
    $data = $_GET;
    $name = $data['price_type'];

    $factory_id = $_SESSION['user']['factory_id'];
    $sql_add = "where 1=1";
    if (!empty($factory_id)) {
      $sql_add .= " and (factory_id=".$factory_id." or factory_id=0)";
    }

    if(empty($name)){
      \except(-1,'价格类型不能为空!');
    }
    $new_price_type = $this->di['PriceService']->addPriceType($name, $factory_id);


    $price_type = \model\price_type::finds($sql_add);
    
    $this->data($price_type);
  }

  /**
   * 根据价格类型获得商品价格
   * @return [type] [description]
   */
  function aj_price_config() {
    $data = $_GET;
    $factory_id = $_SESSION['user']['factory_id'];
    // 初始化
    $this->di['PriceService']->getConfigWithInit($data['id'], $factory_id);
    $total = 0;
    $page_param = [
      'length' => 10,
      'page' => $data['page'],
    ];
    $fac_add = "";
    if (!empty($factory_id)) {
      $fac_add = " and (factory_id=".$factory_id." or factory_id=0)";
    }
    if (!empty($data['search_txt'])) {
      $p_list = \model\product::finds("where (name like '%".$data['search_txt']."%' or py like '%".$data['search_txt']."%') ".$fac_add, 'id');
      $productIds = \pickBy($p_list, 'id');
      $sql_add .= " and product_id in (".implode(',', $productIds).")";
    }

    $res = \model\price_type_config::finds('where price_type_id='.$data['id'].$sql_add, 'id,product_id,price', $total, $page_param);
    $productIds = \pickBy($res, 'product_id');
    $products = \model\product::findByIds($productIds,'id,name','id');

    $ls = [];
    foreach ($res as $item) {
      if (!empty($products[$item['product_id']])) {
        $item['product_name'] = $products[$item['product_id']]['name'];
      }
      
      $ls[] = $item;
    }
    $this->data(['ls'=>$ls, 'total'=> $total]);
  }

  /**
   * 变更价格页面
   * @return [type] [description]
   */
  function change_price() {
    $data = $_GET;
    $__id = $data['id'];
    $__storename = $data['storename'];
    $__product_name = $data['product_name'];
    $__price_type_name = $data['price_type_name'];
    $__price = floatval($data['price']);
    include \view('pricetype__change_price');
  }

  /**
   * 根据商品ID和价格类型ID获得门店对应价格
   * @return [type] [description]
   */
  function aj_config_client_list() {
    $data = $_GET;
    $factory_id = $_SESSION['user']['factory_id'];
    // 初始化
    $this->di['PriceService']->getClientConfigWithInitByProduct($data['config_id'], $data['product_id'],$factory_id);
    $total = 0;
    $page_param = [
      'length' => 10,
      'page' => $data['page'],
    ];
    $sql_add = "";
    $fac_add = "";
    if (!empty($data['search_txt'])) {
      if (!empty($factory_id)) {
        $fac_add = " and (factory_id=".$factory_id." or factory_id=0)";
      }
      $list = \model\client::finds("where (storename like '%".$data['search_txt']."%' or py like '%".$data['search_txt']."%') ".$fac_add, 'id');
      $clientIds = \pickBy($list, 'id');
      $sql_add .= " and client_id in (".implode(',', $clientIds).")";
    }

    $res = \model\price::finds("where product_id='".$data['product_id']."' and  price_type_id='".$data['config_id']."'  ".$sql_add, 'id,client_id,price', $total, $page_param);
    $clientIds = \pickBy($res, 'client_id');
    $clients = \model\client::findByIds($clientIds,'id,storename','id');

    $ls = [];
    foreach ($res as $item) {
      if (!empty($clients[$item['client_id']])) {
        $item['storename'] = $clients[$item['client_id']]['storename'];
      }
      
      $ls[] = $item;
    }
    $this->data(['ls'=>$ls, 'total'=> $total]);
  }
  
  /**
   * 根据门店保存价格结果
   * @return [type] [description]
   */
  function aj_post_config_by_client(){
    $id = (int) $_GET['id'];
    $data = $_GET;
    $data = $_POST;
    $price = (float) $data['val'];
    $oPrice = \model\price::loadObj($id);
    $oPrice->data['price'] = $price;
    $oPrice->save();


    $data = [
      'get' => $_GET,
      'post' => $_POST,
      'data' => $oPrice->data,
    ];
    return $this->data($data);
  }

  /**
   * 根据商品保存价格结果
   * @return [type] [description]
   */
  function aj_post_config_by_product(){
    $id = (int) $_GET['id'];
    $data = $_GET;
    $data = $_POST;
    $price = (float) $data['val'];
    $oPriceConfig = \model\price_type_config::loadObj($id);
    $oldPrice = $oPriceConfig->data['price'];
    $oPriceConfig->data['price'] = $price;
    $oPriceConfig->save();

    //修改其他的
    \model\price::sqlQuery(" UPDATE ".\DbConfig::$mysql['prefix']."price SET  price='".$price."'  WHERE product_id='".$oPriceConfig->data['product_id']."' and price_type_id='".$oPriceConfig->data['price_type_id']."'");


    $data = [
      'get' => $_GET,
      'post' => $_POST,
      'data' => $oPriceConfig->data,
    ];
    return $this->data($data);
  }


  // function check_order_and_config_diff_price() {
  //   ini_set("max_execution_time", 600);
  //   ini_set("memory_limit", 1048576000);
  //   $res = [];
  //   // $thedate = '2017/02/14';
  //   $thedate = $_GET['thedate'];
  //   $orders = \model\order::sqlQuery("select * from njzs_order where thedate = '".$thedate."'");
  //   $count = 0;
  //   foreach ($orders as $item) {
  //     $arr = [];
  //     $clientId = $item['client_id'];
  //     $productId = $item['product_id'];
  //     $price3 = \model\price::find("where client_id='".$clientId."' AND product_id='".$productId."' order by id desc ");

  //     if(empty($price3)){
  //       $oClient = \model\client::loadObj($clientId);
  //       $client = $oClient->data;

  //       $price_type_id = $client['price_type_id'];

  //       //读2层配置
  //       $price2 = \model\price_type_config::find("where price_type_id='".$price_type_id."' AND product_id='".$productId."' order by id desc ");
  //       if(!empty($price2)){
  //         $newprice = $price2['price'];
  //       }else{
  //         //读取配置id，写入配置表
  //         $oProduct = \model\product::loadObj($productId);
  //         $product = $oProduct->data;
  //         $newprice = $product['price'];
  //       }
  //     }else{

  //       $newprice = $price3['price'];
  //     }



  //     if ($newprice != $item['price']) {
  //       // \model\price::sqlQuery("update njzs_order set price=".$newprice." where id=".$item['id']);
  //       // $count ++;
  //       $arr['订单ID'] = $item['id'];
  //       $arr['店铺名称'] = $item['storename'];
  //       $arr['店铺ID' ] = $item['client_id'];
  //       $arr['商品ID'] = $item['product_id'];
  //       $arr['商品名称'] = $item['product_name'];
  //       $arr['订单需求'] = $item['need_amount'];
  //       $arr['实际发货'] = $item['send_amount'];
  //       $arr['订单价格'] = $item['price'];
  //       $arr['配置价格'] = $newprice;
  //       $arr['实际扣款'] = $item['cost'];
  //       $res[] = $arr;
  //       // 
  //     }
  //   }
  //   // echo $count;

  //   $params = [
  //       'columns' => [
  //          '订单ID' => '订单ID',
  //          '店铺ID' => '店铺ID',
  //          '店铺名称' => '店铺名称',
  //          '商品ID' => '商品ID',
  //          '商品名称' => '商品名称',
  //          '订单需求' => '订单需求',
  //          '实际发货' => '实际发货',
  //          '订单价格' => '订单价格',
  //          '配置价格' => '配置价格',
  //          '实际扣款' => '实际扣款',
  //       ],
  //       'data' => $res,
  //       'filename' => $thedate.'错误价格.xlsx',
  //       'title' => $thedate.''
  //   ];

  //   $this->di['ExcelService']->exportExcel($params);
    

  // }

}
