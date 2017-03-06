<?php

namespace service;

use model\order as OrderModel;
use app\service as Service;

class LogAccountService extends Service {

  function saveAccountLog($clientId,$amount,$remark,$operator,$before_amount, $storename){
    // $oClient = \model\client::loadObj($clientId);
    // $deposit = $oClient->data['deposit'];
    // \vd($deposit,'变动前金额');

    if($amount > 0) {
      $type = '手动充值';
      $amount = '+'.$amount;
    }
    if ($amount < 0 ) {
      $type = '手动扣除';
    }
    $oLogAccount = new \model\log_account;
    $oLogAccount->data = [
      'client_id' => $clientId,
      'amount' => $amount,
      'operator' => $operator,
      'code' => \model\log_account::$CODE[$type], 
      'create_at' => time(),
      'date_time' => \datetime(),
      'extra' => \en([
          '变动金额' => number_format($amount, 2),
          '变动原因' => $type,
          '客户' => $storename,
          '变动前余额' => number_format($before_amount, 2),
          '变动后余额' => number_format($before_amount + $amount, 2),
          '备注' => $remark,
      ]),
    ];
    $oLogAccount->save();
    \vd($oLogAccount,'新的日志');
  }

  function getLogByManualUpdateDeposit($clientId_,$startTime_,$endTime_,&$count,$param_=[]){
    $sql = " and create_at > '".strtotime($startTime_)."' and create_at<'".strtotime($endTime_)."'";

    $logs = \model\log_account::finds("where client_id='".$clientId_."' and code in ('100','102','103','104','105', '106') ".$sql." order by id desc",'*',$count,$param_);

    $res = [];
    foreach ($logs as $item) {
      $item['date_time'] = date('Y-m-d H:i:s', $item['create_at']);
      $extra = json_decode($item['extra'], true);
      if (!empty($extra['客户'])) {
        $item['client_name'] = $extra['客户'];
      }
      $res[] = $item;
    }
    return $res;
  }

  function getManagerAllotHistory($name, $start, $end,$code,&$count,$param_=[]){
    $sql = " and create_at>=".strtotime($start)." and create_at <= ".strtotime($end)." and code =".$code;

    $logs = \model\log_account::finds("where operator='".$name."' ".$sql." order by date_time desc",'*',$count,$param_);

    $factory_id = $_SESSION['user']['factory_id'];
    $clients = $this->di['ClientService']->getClientByFactory($factory_id);
    $res = [];
    foreach ($logs as $key => $item) {
      $item['storename'] = $clients[$item['client_id']]['storename'];

      $res[] = $item;
    }
    return $res;
  }




}