<?php

namespace service;

use model\order as OrderModel;
use app\service as Service;

class ClientService extends Service {

  //数据列表，带id
  function getList(){
    $r = [];
    $f = \model\client::finds(" WHERE deleted=0 ",' id,storename,username,deposit,py,price_type_id,manager_name');
    $r = \indexBy($f,'id');
    return $r;
  }


  function getListByDeposit($factory_id,$key,$sortarea,$deposit,$client_ids){
    $r = [];
    $sql = '';
    if(!empty($key)){
      $sql = " and storename like '%".$key."%'";
    }
    

    if($sortarea){
      $f = \model\client::finds(" WHERE id in (".$client_ids.") order by field(id,".$client_ids.") ", 'id,storename,username,deposit,py,price_type_id,manager_name');
      \vd($f,'@@@@@@');
      
      return $f;
    }

    if($deposit){
      $f = \model\client::finds(" WHERE deleted=0 and factory_id='".$factory_id."' ".$sql." order by deposit asc ",' id,storename,username,deposit,py,price_type_id,manager_name');
      return $f;
    }

    $f = \model\client::finds(" WHERE deleted=0 and factory_id='".$factory_id."' ".$sql." ",' id,storename,username,deposit,py,price_type_id,manager_name');
    \vd($f,'111');
    $r = \indexBy($f,'id');
    return $f;
  }

  //查询工厂店铺
  function getClientByFactory($factory_id){
    $clients = \model\client::finds("where deleted=0 and factory_id='".$factory_id."'");
    $clients = \indexBy($clients,'id');
    return $clients;
  }

  //查询店铺详情
  function getClientDetailById($client_id){
    $oClient = \model\client::loadObj($client_id,'id');
    if(!$oClient){
      \except(-1,'没有找到店铺信息');
    }
    return $oClient;
  }

  //
  function getArea(){
    $areas = \model\area::finds();
    $areas = \indexBy($areas,'id');
    return $areas;
  }


  // 基本查询业务
  function queryById($clientIds) {

    $client = \model\client::finds(" WHERE deleted = 0 AND id in'(".$clientIds.")'",'id,storename,area,order');
  }

  function queryByArea($area='') {
    $clients = \model\client::finds(" WHERE deleted = 0 AND area in'(".$areas.")'",'id,storename,area,order');
    return $clients;
  }

  function addClient($data){
    if( empty($data['client_username']) || empty($data['client_passwd'])){
       \except(-1,'用户名密码不能为空！');
    }
    if( empty($data['client_name']) ){
       \except(-1,'请输入店铺名称!');
    }
    if( empty($data['client_addr']) ){
       \except(-1,'请输入店铺地址!');
    }
    if( empty($data['client_phone']) ){
       \except(-1,'请输入手机号!');
    }
    $oClient = \model\client::loadObj($data['client_username'],'username');
    if($oClient){
       \except(-1,'用户名已存在!');
    }

    $oClient = new \model\client;
      $oClient->data=[
        'username' => $data['client_username'],
        'password' => $data['client_passwd'],
        'storename' => $data['client_name'],
        'address' => $data['client_addr'],
        'phone' => $data['client_phone'],
        'area' => $data['client_area'],
        'price_type_id' => $data['price_type_id'],
        'manager_name' => $data['client_manager'],
        'factory_id' => $data['client_factory_id'],
        'create_at' => time(),
      ];
      $oClient->save();
  }

  // 处理金额
  function DealMoney($clientId,$amount,$remark,$operator) {
    $oClient = \model\client::loadObj($clientId);
    $before_amount = $oClient->data['deposit'];
    $storename = $oClient->data['storename'];
    $oClient->data['deposit'] += $amount;
   // 记录金额变动日志
    $this->di['LogAccountService']->saveAccountLog($clientId,$amount,$remark,$operator, $before_amount, $storename);
    $oClient->save();
  }


}