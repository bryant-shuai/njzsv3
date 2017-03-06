<?php

namespace service;

use model\order as OrderModel;
use app\service as Service;

class ChangeOrderService extends Service {

  function directSend($product_id, $client_id, $thedate, $batch_id, $send_amount){
    $send_amount = (float) $send_amount;

    $oProduct = \model\product::loadObj($product_id);
    if(!$oProduct->isSpecial()){
      \except(-1,'不是特殊商品,不能操作');
    }

    $find = \model\order::find("where thedate='".$thedate."' and batch_id='".$batch_id."' and client_id='".$client_id."' and product_id='".$product_id."' order by id desc limit 1 ");
    if($find){
      $this->returnMoneyBackById($find['id']);
      // 返还金钱后，移除有关订单的LOG_ACCOUNT
      \model\log_account::sqlQuery("delete from njzs_log_account where client_id=".$client_id." and extra like '%\"order_id\":\"".$find['id']."\"%'");

      $oOrder = \model\order::loadObj($find['id']);
    }else{
      $oOrder = new \model\order;
      // \vd('noooooooooooooo');
      // exit;
    }

    $unitPrice = $this->di['PriceService']->get($client_id, $product_id); //todo 读价格
    \vd($unitPrice,'$unitPrice$unitPrice$unitPrice');
    $sumPrice = $unitPrice * $send_amount;
    \vd($sumPrice,'$sumPrice$sumPrice$sumPrice');

    $oClient = \model\client::loadObj($client_id);
    $before_deposit = $oClient->data['deposit'];
    $oClient->changeAmount(['amount'=>-$sumPrice]);

    $data = [
      'client_id' => $client_id,
      'product_id' => $product_id,
      'thedate' => $thedate,
      'batch_id' => $batch_id,
      'storename' => $oClient->data['storename'],
      'product_name' => $oProduct->data['name'],
      'price' => $unitPrice,  
      'need_amount' => $send_amount,
      'send_amount' => $send_amount,
      'cost' => $sumPrice ,
      'create_at' => time(),
    ];

    $oOrder->save($data);

    $oLogAccount = new \model\log_account;
    // {"变动金额":90.9216,"变动原因":"货物分拣扣款","客户":"沈阳肇工街店B1","变动前余额":"1299.18","变动后余额":1208.2584}
    $oLogAccount->data = [
      'client_id' => $client_id,
      'amount' => -$sumPrice, // $price * $send_amount
      'code' => \model\log_account::$CODE['分拣扣款汇总'], //单品的标识
      'create_at' => time(),
      'date_time' => \datetime(),
      'extra' => \en([
          '变动前余额' => number_format($before_deposit, 2),
          '变动后余额' => number_format($before_deposit - $sumPrice, 2),
          '变动金额' => number_format(-$sumPrice, 2),
          '变动原因' => '货物分拣扣款',
          'order_id' => $oOrder->data['id'],
          '客户' => $oClient->data['storename'],
        ]),
    ];
    $oLogAccount->save();
    


  }



  function returnMoneyBackById($order_id_){
    $oOrder = \model\order::loadObj($order_id_);
    $amount = ( (float) $oOrder->data['need_amount'] ) * ( (float) $oOrder->data['price'] ) ;

    $clientId = $oOrder->data['client_id'];
    $oClient = \model\client::loadObj($clientId);

    $oClient->changeAmount(['amount'=>$amount]);

  }

}