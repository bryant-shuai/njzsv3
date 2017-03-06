<?php
namespace model;

class client extends \app\model
{
  public static $table = "client";

  //
  function changeAmount($args){
    $amount = (float) $args['amount'];
    \vd($amount,'$amount_change');
    $this->data['deposit'] += $amount;
    $this->save([
      'deposit' => $this->data['deposit'],
    ]);
  }    





  function charge($amount = 0, $sms_id = 0)
  {
    \vd($this->data);
    $this->data['deposit'] = (float) $this->data['deposit'];
    $amount = (float) $amount;

    $charge_before = $this->data['deposit'];
    $this->data['deposit'] += $amount;
    $this->data['change'] += $amount;
    $charge_after = $this->data['deposit'];

    $charge_before = round($charge_before ,2);
    $charge_after = round($charge_after ,2);

    if ($amount >= 0) {
        $amountstr = '+' . $amount;
    } else {
        $amountstr = '' . $amount;
    }

    //log
    $code = \model\log_account::$CODE['客户充值'];

    $oLogAccount = new \model\log_account;
    $oLogAccount->save([
      'code' => $code,
      'client_id' => $this->data['id'],
      'amount' => $amountstr,
      'extra' => \en([
          '变动原因' => '客户充值',
          '变动金额' => number_format($amountstr ,2),
          '变动前余额' => number_format($charge_before, 2),
          '变动后余额' => number_format($charge_after, 2),
          '客户' => $this->data['storename'],
          '短信' => $sms_id,
        ]),
      'create_at' => time(),
    ]);

    $this->save();
  }



}