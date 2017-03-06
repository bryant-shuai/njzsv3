<?php
namespace model;

class sms extends \app\model
{
    static $table = "sms";

    public static $CONST = [
      'SMS_NEW_RECEIVED' => 0,
      'SMS_CHARGED' => 1,
      'SMS_NO_CLIENT_CONFIG' => 2,
      'SMS_NO_NEED_TO_DEAL' => 3,
    ];

    function parse()
    {
      if( \model\sms::$CONST['SMS_CHARGED'] == $this->data['status'] ){
        return ;
      }

        //关键词
        // print_r($this->data);
        $split = "/人民币|元|户名[：|:]|[。|.]\[建设银行\]/u";
        $msg_arr = $result = preg_split($split, $this->data["msg"]);

        \vd($msg_arr);

        $charger_name = $msg_arr[4];
        $amount = (float) $msg_arr[1];

        $this->data['amount'] = $msg_arr[1];
        $this->data['client_name'] = $msg_arr[4];
        \vd($this->data);


        //判断是否是多帐号
        $res = \model\client::finds("where manager_name='".$charger_name."'",' id');

        if (count($res) > 1) {
          $oChargerManager = \model\client_manager::loadObj($charger_name, 'manager_name');

          $amount_before = $oChargerManager->data['balance'];
          $amount_after = $oChargerManager->data['balance'] + $amount;

          $oChargerManager->save([
            'balance' => $amount_after,
          ]);
          
          $this->data['status'] = \model\sms::$CONST['SMS_CHARGED'];
        } else {
          $oClient = \model\client::loadObj($charger_name,'manager_name');
          if($oClient){
            $this->data['client_id'] = $oClient->data['id'];

            if(\model\sms::$CONST['SMS_NEW_RECEIVED']==$this->data['status']){
              //判断是否是唯一帐号，如果不唯一，同步到外网


              //把钱充值到账户
              $oClient->charge($amount, $this->data['id']);
              $this->data['status'] = \model\sms::$CONST['SMS_CHARGED'];
            }
          }else{
            $this->data['status'] = \model\sms::$CONST['SMS_NO_CLIENT_CONFIG'];
          }
        }




        // $oChargerManager = \model\client_manager::loadObj($charger_name, 'manager_name');

        // $f = \model\client_manager::finds(" WHERE deleted=0 and factory_id='".$factory_id."' ".$sql." ".$order." ",' id,storename,username,deposit,py,price_type_id,manager_name');

        // if( $oChargerManager ){
        //   $amount_before = $oChargerManager->data['balance'];
        //   $amount_after = $oChargerManager->data['balance'] + $amount;

        //   $oChargerManager->save([
        //     'balance' => $amount_after,
        //   ]);
          
        //   $this->data['status'] = \model\sms::$CONST['SMS_CHARGED'];

        // // exit('001');
        // }else{

        // // exit('002');
        //   $oClient = \model\client::loadObj($charger_name,'manager_name');
        //   if($oClient){
        //     $this->data['client_id'] = $oClient->data['id'];

        //     if(\model\sms::$CONST['SMS_NEW_RECEIVED']==$this->data['status']){
        //       //判断是否是唯一帐号，如果不唯一，同步到外网


        //       //把钱充值到账户
        //       $oClient->charge($this->data['amount'], $this->data['id']);
        //       $this->data['status'] = \model\sms::$CONST['SMS_CHARGED'];
        //     }
        //   }else{
        //     $this->data['status'] = \model\sms::$CONST['SMS_NO_CLIENT_CONFIG'];
        //   }         
        // }

        $this->save(); 

    }


    static function getSms(&$count=null,$param=[]){
        $sqladd = ' id > 0 ';

      \vd($param);

      $key = null;
      if(!empty($param['key'])){
        $key = $param['key'];
        // 模糊查询问答的内容或者用户的ID
        $sqladd .= " and (client_name like '%".$key."%' or id like '%".$key."%')";
      }

      $order = 'create_at desc';
      if($param['order']){
        $order = $param['order'];
      }

      $ls = \model\sms::finds("where  ".$sqladd." order by ".$order."",'*',$count,$param);

      return $ls;
    }


}