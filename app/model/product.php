<?php
namespace model;

class product extends \app\model
{
    public static $table = "product";

    function isSpecial(){
        $is_special_product = false;
        $name = $this->data['name'];
        if ( strpos($name, "不良") !== false || strpos($name, "工厂") !== false || strpos($name, "活动") !== false ) {
            $is_special_product = true;
        }
        return $is_special_product;
    }

}