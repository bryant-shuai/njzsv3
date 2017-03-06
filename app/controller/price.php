<?php
namespace controller;

use common\ErrorCode;

class price extends \app\controller
{

  function index() {
    $__nav = 'setting';
    include \view('price_typelist');
  }


}
