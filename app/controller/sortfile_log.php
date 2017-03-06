<?php
namespace controller;

use common\ErrorCode;

class sortfile_log extends \app\controller
{
  // 显示已经上传的文件
  function index() {
    $sort_files = $this->di['SortFileLogService']->getList();
    \vd($sort_files,'已经上传的文件');
    $this->data(['ls' => $sort_files]);
  }
  

  // 测试save方法
  // function test() {
  //   $res = \model\sortfile_log::loadObj(24);
  //   // $res = new \model\sortfile_log;

  //   \vd($res,'loadObj返回结果');
  //   $res->save(['user_id' => 76],['key'=>'user_id','value'=>67]);
  //   // $res = new \model\sortfile_log;
  //   // $res->save(['user_id' => 76],);

  // }

}
