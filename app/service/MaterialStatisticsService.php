<?php

namespace Service;

error_reporting(E_ALL);
set_include_path(get_include_path() . PATH_SEPARATOR . __BASE_DIR__ . "/app/lib/");
require_once __BASE_DIR__ . "/app/lib/PHPExcel.php";
require_once __BASE_DIR__ . "/app/lib/PHPExcel/Reader/Excel2007.php";
require_once __BASE_DIR__ . "/app/lib/PHPExcel/Reader/Excel5.php";
require_once __BASE_DIR__ . "/app/lib/PHPExcel/IOFactory.php";

use App\Model;
use App\Service;

use PHPExcel;
use PHPExcel_Cell;
use PHPExcel_IOFactory;
use PHPExcel_Reader_Excel5;
use PHPExcel_Reader_Excel2007;
use PHPExcel_Style_Alignment;

class MaterialStatisticsService extends Service
{
     public function query_list($dates, $strict = false, $factory_id)
    {
        if (!isset($dates["fromdate"])) {
            $dates["fromdate"] = date("Y/m/d");
        }
        if (!isset($dates["todate"])) {
            $dates["todate"] = date("Y/m/d", time() + 86400);
        }

        $product_statistics = $this->di["OrderService"]->queryOrderStatistics($dates, "product_id", $factory_id);
        $material_statistics = [];

        while (true) {
            //如果是按产品进行统计,则进行 产品-材料 转换
            //查询所有配方信息并分组
            $productMaterial = $this->di["ProductMaterialService"]->query();
            if (!$productMaterial) {
                break;
            };
            $productMaterial = Tools::indexSet($productMaterial, "product_id");

            //查询所有产品信息
            $products = $this->di["ProductService"]->query();
            if (!$products) {
                break;
            };
            $products = Tools::indexArray($products, "id");

            //查询所有材料信息
            $materials = $this->di["MaterialService"]->query();
            if (!$materials) {
                break;
            };
            $materials = Tools::indexArray($materials, "id");

            //统计
            foreach ($product_statistics as $key => $value) {
                $product_id = $value["product_id"];
                $need_amount = $value["need_amount"];
                if (isset($productMaterial[$product_id])) {
                    foreach ($productMaterial[$product_id] as $_key => $_value) {
                        $material_id = $_value["material_id"];
                        $amount = $need_amount * $_value["amount"];
                        $rate = $products[$product_id]["rate"];
                        if(empty($rate) || (int)$rate==0){
                          $amount_rate = 0;
                        }else{
                          $amount_rate = $amount / $rate;
                        }
                        if (!isset($material_statistics[$material_id])) {
                            $material_statistics[$material_id] = ["material_name" => "", "unit" => "", "amount" => 0, "amount_rate" => 0];
                        }
                        $material_statistics[$material_id]["material_id"] = $material_id;
                        $material_statistics[$material_id]["material_name"] = $materials[$material_id]["name"];
                        $material_statistics[$material_id]["unit"] = $materials[$material_id]["unit"];
                        $material_statistics[$material_id]["amount"] += number_format($amount, 3);
                        $material_statistics[$material_id]["amount_rate"] += number_format($amount_rate, 3);
                    }
                }
            }
            break;
        }

        return array_values($material_statistics);
    }

   
}
