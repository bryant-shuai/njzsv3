<?php

namespace service;

use model\order as OrderModel;
use app\service as Service;

class ChargeService extends Service {

  function chargebySms($client_id, $amount, $sms_id){
    $oSms = \model\sms::loadObj($sms_id);
    if($oSms->charged()){
      \except(-1,'charged');
    }
    $amount = $oSms->getAmount();
    $amount = (float) $amount;
    $oClient = \model\client::loadObj($client_id);
    $old_deposit = (float) $oClient->data['deposit'];
    $new_deposit = $old_deposit + $amount;

    $oLogAccount = new \model\log_account;
    $oLogAccount->save([
      'code' => \model\log_account::$CODE['充值'],
      'client_id' => $client_id,
      'amount' => $amount,
      'extra' => \en([
          '变动金额' => $amount,
          '客户' => $oClient->data['storename'],
          '短信' => $sms_id,
        ]),
      'create_at' => time(),
    ]);

    $oClient->save([
      'deposit' => $new_deposit,
    ]);

  }

}