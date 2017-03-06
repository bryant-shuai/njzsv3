<?php
namespace controller;

class finance extends \app\controller
{
	function index(){
		include \view('finance_index');
	}

	//该变金额页面
	function finance_chenge(){
		$data = $_GET;
		$ls =  json_decode(str_replace("\\","",$data['ls']),true);
		\vd($ls,'______');
		include \view('finance_chenge_index');
	}

  //
  function finance_chenge_need_confirm(){
    $data = $_GET;
    $ls =  json_decode(str_replace("\\","",$data['ls']),true);
    \vd($ls,'______');
    include \view('finance_chenge_need_confirm_index');
  }

	function client_balance_ls (){
    $data = $_GET;
    $factory_id = $_SESSION['user']['factory_id'];
    $key = $data['key'];
    $sortarea = $data['sortarea'];
    $deposit = $data['deposit']; 
    $client_ids = $this->di['SortAreaService']->clientIdsSortByArea($factory_id);
    $clients = $this->di['ClientService']->getListByDeposit($factory_id,$key,$sortarea,$deposit,$client_ids);
    \vd($clients,'__');
    $this->data(['ls'=>$clients]);
	}


  // 充钱
  function top_up_money() {
    $data = $_GET;
    // 获取用户Id
    $clientId = $data['client_id'];
    $remark = $data['remark'];
    $operator = $_SESSION['user']['name'];
    if(empty($remark)){
      $remark = '';
    }
    $amount = $data['amount'];
    if (0 > $amount) {
      $this->error(-1,'请输入正确的金额');
    }
    $this->di['ClientService']->DealMoney($clientId,$amount,$remark,$operator);
    $this->data(true);
  }

  // 扣除
  function deduct_money() {
    $data = $_GET;
    // 获取用户Id
    $clientId = $data['client_id'];
    $amount = $data['amount'];
    $remark = $data['remark'];
    $operator = $_SESSION['user']['name'];
    if(empty($remark)){
      $remark = '';
    }
    if ($amount > 0) {
      $this->error(-1,'请输入正确的金额');
    }
    $this->di['ClientService']->DealMoney($clientId,$amount,$remark,$operator);
    $this->data(true);
  }


  function aj_add_return_back (){
    $data = $_GET;
    
    $this->di['ReturnBackService']->getfinance($data);
    
    $this->data(['ok']);
  }




















}