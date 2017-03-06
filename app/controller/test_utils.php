<?php
namespace controller;
use \app\controller;

class test_utils extends controller {
	
  // 测试indexWith
  function indexWith() {
    $areas = \model\area::finds();
    \vd($areas,'地区');
    $r = \indexWith($areas,'factory_id');
    \vd($r,'测试结果');
  }



 /** 
     测试multiPickBy 筛选出查询出来的订单中涉及到的client_id，
     这一个方法的筛选条件是一个数组，
     只有一个元素的时候，那就是等价于pickBy（）方法 
 **/
  function multiPickBy() {
    $res = \model\client::finds(" where id < 10 ",'id');
    \vd($res,'店铺');
    $resInx = \indexBy($res,'id');
    $keys = array_keys($resInx);
    \vd($keys,'全部索引');
    $orders = \model\order::finds("where client_id <10");
    // 只有加上索引才可以进行筛选数据
    $ordersInx = \indexBy($orders,'client_id');
    \vd($ordersInx,'订单');
    // \vd($orders,'3333');
    $keys = ['client_id'];
    $pickOrders = \multiPickBy($orders,$keys);
    \vd($pickOrders,'筛选数据');
  }


  // 测试pickBy 这一个方法适用于只筛选一个字段的时候 传入的是一个字符串
  function pickBy() {
    $orders = \model\order::finds("where client_id <10");
    // 只有加上索引才可以进行筛选数据
    $ordersInx = \indexBy($orders,'client_id');
    \vd($ordersInx,'订单');
    $keys = 'client_id';
    $res = \pickBy($ordersInx,$keys);
    \vd($res,'筛选结果');
  }


  // 测试copyArr 当数据库需要存入一条和一条相关的数据有相同的字段 ，就可以用这个方法复制相同的字段
  function copyArr() {
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
    $oNextOrder->save();

    $oOrder->data['to_id'] = $oNextOrder->data['id'];
    $oOrder->save();

  }


  // 测试 view
  function view() {
    $res = \view('inc_page');
    \vd($res,'路径');
    $r = realpath(__FILE__);
    // 路径结尾添加字符
    $r = $r.'///';
    \vd($r,'路径路径路径路径路径');
    // 删除路径字符串后面的'/'
    $re = rtrim($r,'/');
    \vd($re,'_____');
  }


  // 测试路径的回去
  function test_index() {
    $__BASE_PATH__ = rtrim(realpath(__FILE__), '/');
    \vd($__BASE_PATH__,'此文件所在的路径');
    // 斜杠最后一次出现的位置
    $res = strrpos($__BASE_PATH__, '/');
    \vd($res,'最后一次出现的位置');
    $resres = substr($__BASE_PATH__, 0, strrpos($__BASE_PATH__, '/') + 1);
    \vd($resres,'22222222');
    // 得到当前目录的上一级目录 .. 类似于 cd .. 命令
    $__BASE_DIR__ = realpath($resres.'/../');
    \vd($__BASE_DIR__,'应该是什么');
  }


  // 测试_in_data
  function _in_data() {
    // 
  }

  // 转成关联数组
  function toMap() {
    $client = \model\client::loadObj(1);
    // 只有加上索引才可以进行筛选数据
    // $ordersInx = \indexBy($orders,'client_id');
    \vd($client,'订单');
    $res = \toMap($client);
    \vd($res,'map');
  }


  // 测试arrRmDup
  function arrRmDup() {
    $res = \model\client::finds(" where id < 10 ",'id');
    \vd($res,'店铺');
    $resInx = \indexBy($res,'id');
    \vd($resInx,'店铺店铺店铺店铺');
    $keys = array_keys($resInx);
    $rm = \arrRmDup($keys);
    \vd($rm,'%%%%%%%%');
    \vdx($rm,'#######',true);
  }


  // 测试arrRmEmpty create_function() 创建匿名函数的方法
  // function arrRmEmpty() {
  //   $res = \model\client::finds(" where id < 10 ",'id');
  //   \vd($res,'店铺');
  //   $resInx = \indexBy($res,'id');
  //   \vd($resInx,'店铺店铺店铺店铺');
  //   $keys = array_keys($resInx);
  //   $rm = \arrRmEmpty($keys);
  //   \vd($rm,'%%%%%%%%');
  // }

  function addadd() {
    $a = 0;
    $r = ++$a;
    \vd($r,'结果1');
    $p = $a++;
    \vd($p,'结果2');
    $d += $a;
    \vd($d,'结果3'); 
  }


  // 测试indexArray 
  function indexArray() {
    $clients = \model\client::finds(" where id < 10 ",'id');
    \vd($clients,'店铺');
    $res = \indexArray($clients,'id');
    \vd($res,'祭奠');
  }


  // 测试indexSet
  function indexSet() {
    $clients = \model\client::finds(" where id < 10 ",'id');
    \vd($clients,'店铺');

    $res = \indexSet($clients,'id');
    \vd($res,'indexSet');
  }



  // 测试$value["$unique_index"]
  function unique_index() {
    $clients = \model\client::loadObj(10);
    \vd($clients,'店铺');
    $id = 'id';
    $res = $clients->data["$id"];
    \vd($res,'idididid');
  }

























}
