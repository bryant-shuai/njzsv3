<?php
namespace controller;

use common\ErrorCode;

class area extends \app\controller
{

  // 得到现有的分区
  function area() {
    // 获取管理员的工厂ID
    $factory_id = $_SESSION['user']['factory_id'];
    $areas = \model\area::finds(" where factory_id = ".$factory_id);
    \vd($areas,'分区');
  }

}
