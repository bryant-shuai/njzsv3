<?php
session_start();
ini_set('date.timezone','Asia/Shanghai');
header('Content-Type: text/html; charset=UTF-8');
header('access-control-allow-origin: *');

$__BASE_PATH__ = rtrim(realpath(__FILE__), '/');
$__BASE_DIR__ = realpath(substr($__BASE_PATH__, 0, strrpos($__BASE_PATH__, '/') + 1).'/../');
define('__BASE_DIR__', $__BASE_DIR__);

define('__APP_DIR__', realpath($__BASE_DIR__.'/app/'));
define('__PUBLIC_DIR__', realpath( $__BASE_DIR__.'/public/') );
define('__VIEW_DIR__', realpath( $__BASE_DIR__.'/view/') );
define('__ERRLOG__', '/var/log/php.leopard.log');

require __APP_DIR__.'/app.php';
require __APP_DIR__.'/config/config.php';
require __APP_DIR__.'/config/dbconfig.php';
require __APP_DIR__.'/config/dataconfig.php';


try{
  \app\engine::run();
}catch(\Exception $e){
  echo '{"code":'.$e->getCode().',"msg":"'.$e->getMessage().'"}';
  if(__MODE__=='dev'){
    echo '<pre>'. print_r($e,true).'</pre>';
  }
}
























// require_once(__APP_DIR__.'/lib/wx_pay/WxPay.MicroPay.php');
// require_once(__APP_DIR__.'/lib/wx_pay/lib/WxPay.Api.php');
// require_once(__APP_DIR__.'/lib/wx_pay/lib/WxPay.Data.php');
// require_once(__APP_DIR__.'/lib/alipay/f2fpay/F2fpay.php');
// require_once(__APP_DIR__.'/lib/alipay/AopSdk.php');
// require_once(__APP_DIR__.'/lib/alipay/function.inc.php');
// require(__APP_DIR__.'/lib/alipay/config.php');
// echo \Config\ErrInfo::$CONF['-1'];


// //unset($_GET['debug']);
// if (isset($_GET['debug'])) {
//     vd($_GET, 'get');
//     vd($_POST, 'post');
//     // vd($_SESSION,'session');

//     // $sqls=explode("\n",\M\DB::get()->log());
//     // foreach($sqls as &$sql){
//     //     $sql=substr($sql,32);
//     // }
//     // $sqlstr=implode("\n",$sqls);
//     // if(isset($_GET['debug'])) echo '<pre style="text-align:left;">'. $sqlstr .'</pre>';
// }

