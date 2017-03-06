<?php
namespace controller;

use common\ErrorCode;

class index extends \app\controller
{

  function index() {
    $__nav = 'home';
    include \view('index__index');
  }


}
