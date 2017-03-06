<?php
namespace controller;
use \app\controller;

class import_data extends controller
{

  function import_client_id() {
    // 查询所有的客户
    $clients = \model\client::finds(" where id=1 and deleted = 0");
    $products = \model\product::finds(" where id < 70 and deleted = 0");
    foreach ($clients as $client) {
      $oClient = \model\client::loadObj($client['id']);
      print_r($oClient->data);
    }
      
  }



  function import_client_id_bk() {
    // 查询所有的客户
    $clients = \model\client::finds(" where id=1 and deleted = 0");
    $products = \model\product::finds(" where deleted = 0");
    foreach ($clients as $client) {
      $oClient = \model\client::loadObj($client['id']);
      $price_type_id = $oClient->data['price_type_id'];
      foreach ($products as $product) {
        // echo $product['id'];
        // echo $client['id'];
        // 掉原始数据库的价格
        $old_price = $this->ImportData($client['id'],$product['id']);
        \vd($old_price,'$old_price$old_price$old_price');
        // $old_price = 5;
        $price = \model\price::find(" where client_id =".$client['id']." and product_id =".$product['id']);
        // 如果存在
        if($price){
          $id = $price['id'];
          $oPrice = \model\price::loadObj($id);
          $oPrice->data = [
            'client_id' => $client['id'],
            'product_id' => $product['id'],
            'price' => $old_price,
            'price_type_id' => $price_type_id,
          ];
          $oPrice->save();
        }
        // 如果不存在
        if (empty($price) ) {
          $oPrice = new \model\price;

          $oPrice->data = [
            'client_id' => $client['id'],
            'product_id' => $product['id'],
            'price' => $old_price,
            'price_type_id' => $price_type_id,
          ];
          $oPrice->save();
        }

      }
    }
      
  }




  // 获取价格

  function ImportData($clientId_,$productId_){

    $http = new \model\http(5);
    $url = 'http://nj.tiangoutech.com/order/get_price?client_id=1&product_id=172&debug-';

    $ret = $http->get($url);

    try {
        $retarr = \de($ret);
        //拿到price
        $price = $retarr['result']['price'];
        //做你自己的事
        return $price;

    } catch (\Exception $e) {
      //取price失败
      exit('取price失败 client_id:'.$client_id.' product_id:'.$product_id);
    }

  }












































}
