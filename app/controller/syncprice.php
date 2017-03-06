<?php
namespace controller;
use \app\controller;

class syncprice extends controller
{

  // 取出多有的产品
  function products() {
    $products = \model\product::finds(" where deleted = 0");
    $this->data(['ls'=>$products]);
  }


  // 取出多有的店
  function clients() {
    $clients = \model\client::finds(" where deleted = 0");
    $this->data(['ls'=>$clients]);
  }


  // 导入数据
  function import_data() {
    $data = $_GET;

    $oClient = \model\client::loadObj($data['client_id']);
    $price_type_id = $oClient->data['price_type_id'];

    $price = \model\price::find(" where client_id =".$data['client_id']." and product_id =".$data['product_id']);
    // 如果存在
    if($price){
      $id = $price['id'];
      $oPrice = \model\price::loadObj($id);
      $oPrice->data = [
        'price' => $data['price'],
        'price_type_id' => $price_type_id,
      ];
      $oPrice->save();
    }else{
      $oPrice = new \model\price;
      $oPrice->data = [
        'client_id' => $data['client_id'],
        'product_id' => $data['product_id'],
        'price' => $data['price'],
        'price_type_id' => $price_type_id,
      ];
      $oPrice->save();
    }
    $this->data(['ok' => 1]);
  }

}
