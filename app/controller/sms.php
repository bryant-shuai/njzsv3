<?php
namespace controller;

class sms extends \app\controller {
  /**
   * 短信列表页面
   * @return [type] [description]
   */
  function ls() {
    include \view('sms__list');
  }

  /**
   * 加载短信信息
   * @return [type] [description]
   */
  function aj_list() {
    $data = $_GET;

    if(empty($data['page'])) $data['page'] = 1;
    if(empty($data['length'])) $data['length'] = 10;
    // 定义count，统计查询数据的总数

    $count = 0;
    $ls = \model\sms::getSms($count,[
        'page' => $data['page'],
        'length' => $data['length'],
        'key' => $data['search'],
      ]);
      // 去除关键字
    foreach ($ls as &$item) {
      $pattern = "/余额(.*)元/i";
      $item['msg'] = preg_replace($pattern, '', $item['msg']).'';
      if ($item['status'] == \model\sms::$CONST['SMS_CHARGED']) {
        $item['status'] = "已到账";
      } else if ($item['status'] == \model\sms::$CONST['SMS_NO_CLIENT_CONFIG']) {
        $item['status'] = "未找到匹配代理人";
      }
    }

    $this->data([
      'total' => $count,
      'ls' => $ls,
    ]);
  }
}