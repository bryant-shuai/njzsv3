<?php
namespace controller;

use common\ErrorCode;

// 从 SMS DOVE 服务器获取用户短信信息
class remotesms extends \app\controller
{
  // 获得短信信息
  function received() {
    // post 方式
    // $data = $_POST;
    // \errlog('$_POST');

    // $headers = \getallheaders();
    // \errlog($headers);

    $_data = file_get_contents("php://input");
    // $_data = '{"Id":120,"From":"+8618322695963","Msg":"贵公司尾号0368的账户12月12日12时xx分货款收入人民币91102.00元,余额11.811元。对方户名：刘洋。[建设银行]1467445480888","Time":"2017-01-19T07:48:43.973Z","DeviceId":16}';
    // \vd($_data,'接受数据');
    \errlog(md5( $_data . 'cb3bd09c5221bd109ff868ac973306bd'));
    \errlog($_SERVER);

    if((isset($_SERVER['X-Body-Sign']) && md5( $_data . 'cb3bd09c5221bd109ff868ac973306bd' ) !== $_SERVER['X-Body-Sign']) ||
      (isset($_SERVER['HTTP_X_BODY_SIGN']) && md5( $_data . 'cb3bd09c5221bd109ff868ac973306bd' ) !== $_SERVER['HTTP_X_BODY_SIGN'])
      ){
      exit('token err');
    }


    $data = \de($_data);
    \vd($data,'数据');
    \errlog($_data);
    \errlog($data);

    // 建设银行识别
    if ($data['From'] != "95533") {
      exit('phone number err');
    }

    // 收入识别（屏蔽支出）
    if (strpos($data['Msg'], '收入') === false) {
      exit('not money in');
    }

    \vd($xmldata,'bodybodybodybody');
    \vd($data,'#########');

    if(!empty($data['Msg'])){
      \vd($data['Msg'],'信息');
      $find = \model\sms::find("where msg='".$data['Msg']."'");
      \vd($find,'$find$find$find');
      if(!$find){
        $oSms = new \model\sms;
        $oSms->save([
          'msg' => $data['Msg'],
          'msg_receive_at' => time(),
          'create_at' => time(),
        ]);
        $oSms->parse(); 
      }else{
        \vd("没有找到");
      }
    }
    echo 'OK';
  }





  







}
