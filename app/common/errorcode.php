<?php

class CODE
{
    //常用区
    const NO_ERROR = 0;                          //请求成功
    const UNKNOW_ERROR = -1;                     //未知错误
    const PARAMETER_ERROR = 1;                     //未知错误


    static $MSG = [
        //常用区
        self::NO_ERROR => '请求成功',
        self::UNKNOW_ERROR => '未知错误',
        self::PARAMETER_ERROR => '参数错误',

    ];
}
