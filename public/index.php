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


// function errlog2($err)
// {
//     \error_log("\n".print_r($err, true)."\n", 3, __ERRLOG__);
// }


//     \errlog2("\n\n\n\n\n");
//     \errlog2('$_POST');
//     \errlog2($_POST);
//     \errlog2('$_REQUEST');
//     \errlog2($_REQUEST);

//     \errlog2("\n\n\n\n\n");

require __APP_DIR__.'/app.php';
require __APP_DIR__.'/config/config.php';
require __APP_DIR__.'/config/dbconfig.php';
require __APP_DIR__.'/config/dataconfig.php';

// \vd($_SERVER);
\Config::INIT();

// print_r($_SESSION);

  // $_SESSION['user'] = [
  //   'id' => 24,
  //   'type' => 3,
  //   'name' => 'qiye',
  //   'avatar' => '',
  //   'real_name' => '管理员',
  //   'company_id' => 1,
  // ];

// if(empty($_SESSION['user'])){
//   $_SESSION['user'] = [
//     'id' => 24,
//     'type' => 3,
//     'name' => 'qiye',
//     'avatar' => '',
//     'real_name' => '管理员',
//     'company_id' => 1,
//   ];
// }


// $find = strpos('_'.$_GET['_url'],'admin');
// $find2 = strpos('_'.$_GET['_url'],'upload');
// if(  false===$find && $find2===false ){
//   header('Location: /admin');
// }




\safequery();
// \vd($_GET,'$_GET');

try{
  \app\model::connect();
  \app\model::sqlQuery("SET autocommit=0;");
  \app\model::sqlQuery("START TRANSACTION;");
  \app\engine::run();
  // throw new \Exception("Error Processing Request", 1);
  \app\model::sqlQuery("COMMIT;");
}catch(\Exception $e){
  \app\model::sqlQuery("ROLLBACK;");
  echo '{"code":'.$e->getCode().',"msg":"'.$e->getMessage().'"}';
  if(__MODE__=='dev' && isset($_GET['debug']) ){
    echo '<pre>'. print_r($e,true).'</pre>';
  }
}



