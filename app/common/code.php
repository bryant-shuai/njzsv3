<?php

class CODE
{
    //常用区
    const NO_ERROR = 0;                          //请求成功
    const UNKNOW_ERROR = -1;                     //未知错误
    const PARAMETER_ERROR = 1;                     //未知错误
    const USER_ALREADY_EXIST = 2;
    const USER_NOT_FOUND = 3;
    const PASSWORD_ERROR = 4;
    const ORDER_ALREADY_PAIED = 5;
    const NEED_LOGIN = 6;
    const NO_DATA = 7;


    static $MSG = [
        //常用区
        self::NO_ERROR => '请求成功',
        self::UNKNOW_ERROR => '未知错误',
        self::PARAMETER_ERROR => '参数错误',
        self::USER_ALREADY_EXIST => '该手机已经注册过',
        self::USER_NOT_FOUND => '帐号信息没找到',
        self::PASSWORD_ERROR => '密码错误',
        self::ORDER_ALREADY_PAIED => '订单已经支付过',
        self::NEED_LOGIN => '请先登录',
        self::NO_DATA => '找不到记录',

    ];
}
