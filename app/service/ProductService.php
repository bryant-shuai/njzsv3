<?php

namespace service;

use model\order as OrderModel;
use app\service as Service;

class ProductService extends Service {

  function getList(){
    $r = [];
    $f = \model\product::finds(" WHERE deleted=0 order by id desc",' id,price,name,py,unit');
    $r = \indexBy($f,'id');
    return $r;
  }

  //根据产品id查询详情
  function searchProductById($product_id){
  	$oProduct = \model\product::loadObj($product_id,'id');
  	if(!$oProduct){
  		\except(-1,'没有找到产品');
  	}
  	return $oProduct;
  }

  //得到全部产品类型
  function getProductTypeList(){
  	$product_types = \model\product_type::finds();
  	return $product_types;
  }

  //得到全部产品分检类型
  function getProductSortTypeList(){
  	$product_sorts = \model\sort::finds();
  	return $product_sorts;
  }


  // 添加产品类型
  function addProductType($name, $factory_id) {
    $res = \model\product_type::finds(" where  name ='".$name."'");
    if ($res) {
      \except(-1,'该类别已经存在') ;
    }
    $oProductType = new \model\product_type;
    $oProductType->data =[
      'name' => $name,
      'factory_id' => $factory_id
    ];
    $oProductType->save();
    return $oProductType;
  }


  // 添加产品分拣类型
  function addSortType($name, $factory_id) {
    $factory_id = $_SESSION['user']['factory_id'];
    $res = \model\sort::finds(" where name ='".$name."'");
    if ($res) {
      \except(-1,'该类别已经存在');
    }
    $oSort = new \model\sort;
    $oSort->data =[
      'name' => $name,
      'factory_id' => $factory_id
    ];
    $oSort->save();
    return $oSort;
  }

  //
  function addProduct($data){
    $product_name = $data['name'];
    $product_type = $data['type'];
    $product_sort_type = $data['sort_type'];
    $product_unit = $data['unit'];
    $product_price = $data['price'];
    $product_weight_type = $data['weight_type'];
    $product_order = $data['order'];
    // $product_deleted = $data['deleted'];
    $prodcut_py = $data['py'];
    $factory_id = empty($data['factory_id']) ? $_SESSION['user']['factory_id'] : $data['factory_id'];
    if(empty($product_name) || empty($product_unit) || empty($product_price) ) {
      \except(-1,'产品信息不能为空');
    }
    if(empty($product_type) || empty($product_sort_type) || empty($product_weight_type) ) {
      \except(-1,'请完善产品类型');
    }
    $oProduct = new \model\product;
    $oProduct->data=[
      'name' => $product_name,
      'unit' => $product_unit,
      'product_type' => $product_type,
      'sort_type' => $product_sort_type,
      'price' => $product_price,
      'weight_type' => $product_weight_type,
      'onsell' => 1,
      'py' => $prodcut_py,
      'order' => $product_order,
      // 'deleted' => $product_deleted,
      'create_at' => time(),
      'factory_id' => $factory_id
    ];
    $oProduct->save();
  }

  //
  function updateProduct($data){
    $product_id = $data['id'];
    $product_name = $data['name'];
    $product_type = $data['type'];
    $product_sort_type = $data['sort_type'];
    $product_unit = $data['unit'];
    $product_price = $data['price'];
    $product_weight_type = $data['weight_type'];
    $product_order = $data['order'];
    // $product_deleted = $data['deleted'];
    $prodcut_py = $data['py'];
    $factory_id = empty($data['factory_id']) ? $_SESSION['user']['factory_id'] : $data['factory_id'];
    $oProduct = $this->searchProductById($product_id);

    $old_price = $oProduct->data['price'];
    
    $oProduct->data=[
      'name' => $product_name,
      'unit' => $product_unit,
      'price' => $product_price,
      'product_type' => $product_type,
      'sort_type' => $product_sort_type,
      'weight_type' => $product_weight_type,
      'onsell' => 1,
      'order' => $product_order,
      // 'deleted' => $product_deleted,
      'py' => $prodcut_py,
      'update_at' => time(),
      'factory_id' => $factory_id
    ];

    if($old_price != $data['price']) {
      \model\price_type::sqlQuery('update njzs_price_type_config set price='.$data['price'].' where product_id='.$product_id);
      \model\price::sqlQuery('update njzs_price set price='.$data['price'].' where product_id='.$product_id);
    }

    $oProduct->save();
  } 

}