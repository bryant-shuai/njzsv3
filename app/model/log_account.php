<?php
namespace model;

class log_account extends \app\model
{
    static $table = "log_account";

    static $CODE = [
      '客户充值' => 100,
      '分拣扣款' => 101, //不列
      '分拣扣款汇总' => 102,
      '手动充值' => 103,
      '手动扣除' => 104,
      '客户确认' => 105,
      '代理人分配' => 106,
      
    ];

    // const MSG = [
    //   self::ACCOUNT_CHANGE_ADD => '余额添加',
    //   self::ACCOUNT_CHANGE_DED => '余额扣减',
    // ];
}