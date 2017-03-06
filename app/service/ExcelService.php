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

class ExcelService extends Service {
    //查询订单/分捡数据
    public function order_export($args) {
      $dates = $args['dates'];
      $type = isset($args['type']) ? $args['type'] : "send_amount";
      $factory_id = $args['factory_id'];
      $message = "";

      $fromdate = date("Y/m/d");
      if (isset($dates["fromdate"])) {
          $fromdate = $dates["fromdate"];
      }
      $todate = date("Y/m/d", time() + 86400);
      if (isset($dates["todate"])) {
          $todate = date("Y/m/d", strtotime($dates["todate"]) + 86400);
      }

      //1.查询客户信息
      $sql_add = "";
      if (!empty($factory_id)) {
        $sql_add .= " AND (factory_id=".$factory_id." or factory_id=0)";
      }

      $clients = \model\client::finds("WHERE 1=1 ".$sql_add." order by `order` asc", 'id,storename');
      if (!$clients) {
        $message .= "没有查询到客户信息。";
      } else {
        $clients = \indexArray($clients, "id");
      }
      //客户的id
      $client_ids = array_keys($clients);
      $client_ids_str = implode(",", $client_ids);

      //2.查询商品信息
      $products = \model\product::finds("WHERE 1=1 ".$sql_add." order by `order` desc", "id,name,unit");
      if (!$products) {
        $message .= "没有查询到商品信息。";
      } else {
        $products = \indexArray($products, "id");
      }

      //3.查询指定日期已确认订单信息
      $subwhere = $client_ids_str ? "AND `client_id` IN (" . $client_ids_str . ") " : "";

      $orders = Model::sqlExec("SELECT `client_id`,`storename`,`product_id`,`product_name`,`price`,SUM(`need_amount`) AS `need_amount`,SUM(`send_amount`) AS `send_amount`,SUM(`get_amount`) AS `get_amount`,SUM(`cost`) AS `cost` FROM `njzs_order` WHERE `thedate` >= '" . $fromdate . "' AND `thedate` < '" . $todate . "' AND `deleted` = 0  ".$sql_add." " . ($subwhere) . "GROUP BY `client_id`,`product_id`;");

      if (!$orders) {
          $message .= "没有查询到订单信息。";
      }
      // \vd($orders,'ordersordersorders');


      \vd($orders,'$orders');
      $this->generateOrderExcel($clients, $products, $orders, $dates, $type);
    }

    //生成订单/分捡数据表
    private function generateOrderExcel($clients, $products, $orders, $dates, $type = "send_amount") {
      $fileNamePerfix = $type == "send_amount" ? "调拨出库单" : "订单";

      //生成Excel
      $phpExcel = new PHPExcel();
      $firstSheetTitle = $fileNamePerfix;

      $phpExcel->setActiveSheetIndex(0);
      $worksheet = $phpExcel->getActiveSheet();
      $worksheet->setTitle($firstSheetTitle);

      $clientStartCol = 4;
      $clientStartRow = 2;
      $clientCount = count($clients);
      //产品数据开始位置
      $productStartCol = 0;
      $productStartRow = 3;
      $productCount = count($products);
      $productEndRow = $productStartRow + $productCount - 1;
      //订单数据开始位置
      $orderStartCol = $clientStartCol;
      $orderStartRow = $productStartRow;
      $orderCount = count($orders);

      //基本设置
      $worksheet->getDefaultRowDimension()->setRowHeight(14);
      $worksheet->getDefaultColumnDimension()->setWidth(14);
      $worksheet->getDefaultStyle()->getFont()->setSize(10);

      //设置日期单元格
      $worksheet->setCellValue('A1', "订单日期");
      $worksheet->mergeCells('B1:C1');
      $worksheet->setCellValue('B1', $dates["fromdate"] . "-" . $dates["todate"]);

      //左上写入表头
      $worksheet->setCellValue('A2', "商品编号");
      $worksheet->setCellValue('B2', "商品名称");
      $worksheet->setCellValue('C2', "单位");
      $worksheet->setCellValue('D2', "商品合计");

      //写入商品列头,合计
      $col = $productStartCol;
      $row = $productStartRow;
      foreach ($products as $product) {
        $worksheet->getCellByColumnAndRow($col, $row)->setValue($product["id"]);
        $worksheet->getCellByColumnAndRow($col + 1, $row)->setValue($product["name"]);
        $worksheet->getCellByColumnAndRow($col + 2, $row)->setValue($product["unit"]);
        $_start = PHPExcel_Cell::stringFromColumnIndex($clientStartCol) . '' . $row;
        $_end = PHPExcel_Cell::stringFromColumnIndex($clientStartCol + $clientCount - 1) . '' . $row;
        $worksheet->getCellByColumnAndRow($col + 3, $row++)->setValue("=ROUND(SUM($_start:$_end),2)");
      }

      //写入店铺行头及纵向统计数据:总表统计数据已注释
      $col = $clientStartCol;
      $row = $clientStartRow;
      foreach ($clients as $client) {
        $worksheet->getCellByColumnAndRow($col, $row)->setValue($client["storename"] . "(" . $client["id"] . ")");
        $col++;
      }

      //写入订单分拣信息
      if ($orders) {
        $sumedOrdersByClients= [];
        foreach ($orders as $order) {
          \vd($order,'$order');
          $cpos = array_keys(array_keys($clients), $order["client_id"])[0];
          $ppos = array_keys(array_keys($products), $order["product_id"])[0];
          $col = $orderStartCol + $cpos;
          $row = $orderStartRow + $ppos;
          $worksheet->getCellByColumnAndRow($col, $row)->setValue($order[$type]);

          if(empty($sumedOrdersByClients[''.$order['client_id']])){
            $sumedOrdersByClients[''.$order['client_id']] = [];
          }
          $sumedOrdersByClients[''.$order['client_id']][] = $order;
        }
        // var_dump($sumedOrdersByClients);
      } else {
        $worksheet->getCellByColumnAndRow(2, 3)->setValue("没有分拣数据");
      }

      //循环添加订单数据
      $currsheet = $worksheet;
      $_pcurrrow = $productStartRow + $productCount;
      foreach ($clients as $id => $client) {
        //当前客户分表订单数据开始/结束行
        $_currclientstartrow = $_pcurrrow + 9;
        $_currclientendrow = 0;

        // header
        $_pcurrrow += 5;
        $currsheet->mergeCells("A" . ($_pcurrrow + 0) . ":F" . ($_pcurrrow + 0));
        $currsheet->setCellValue("A" . ($_pcurrrow + 0), $fileNamePerfix);

        $currsheet->mergeCells("A" . ($_pcurrrow + 1) . ":B" . ($_pcurrrow + 1));
        $currsheet->setCellValue("A" . ($_pcurrrow + 1), "出库门店/仓库：工厂");
        $currsheet->setCellValue("E" . ($_pcurrrow + 1), "订单日期");
        $currsheet->setCellValue("F" . ($_pcurrrow + 1), $dates["fromdate"] . "-" . $dates["todate"]);

        $currsheet->mergeCells("A" . ($_pcurrrow + 2) . ":B" . ($_pcurrrow + 2));
        $currsheet->setCellValue("A" . ($_pcurrrow + 2), "进库门店/仓库：" . $client["storename"]);
        $currsheet->mergeCells("C" . ($_pcurrrow + 2) . ":D" . ($_pcurrrow + 2));
        $currsheet->setCellValue("C" . ($_pcurrrow + 2), "单号:-");
        $currsheet->mergeCells("E" . ($_pcurrrow + 2) . ":F" . ($_pcurrrow + 2));
        $currsheet->setCellValue("E" . ($_pcurrrow + 2), "发货日期:");
        $currsheet->setCellValue("F" . ($_pcurrrow + 2), "\\");
        $currsheet->setCellValue("A" . ($_pcurrrow + 3), "商品编号");
        $currsheet->setCellValue("B" . ($_pcurrrow + 3), "商品名称");
        $currsheet->setCellValue("C" . ($_pcurrrow + 3), "单位");
        $currsheet->setCellValue("D" . ($_pcurrrow + 3), "数量");
        $currsheet->setCellValue("E" . ($_pcurrrow + 3), "调拨价");
        $currsheet->setCellValue("F" . ($_pcurrrow + 3), "金额");

        $_pcurrrow += 4;

        if (!empty($sumedOrdersByClients[$id])) {
          foreach ($sumedOrdersByClients[$id] as $order) {
            if ($order[$type] == 0) {
              continue;
            }
            $product = $products[$order['product_id']];
            $currsheet->getCellByColumnAndRow(0, $_pcurrrow)->setValue($order['product_id'].'');
            $currsheet->getCellByColumnAndRow(1, $_pcurrrow)->setValue($order['product_name'].'');
            $currsheet->getCellByColumnAndRow(2, $_pcurrrow)->setValue($product['unit'].'');
            $currsheet->getCellByColumnAndRow(3, $_pcurrrow)->setValue($order[$type].'');
            $currsheet->getCellByColumnAndRow(4, $_pcurrrow)->setValue($order['price'].'');
            if ($type == "send_amount") {
              $currsheet->getCellByColumnAndRow(5, $_pcurrrow)->setValue($order['cost'].'');
            } else {
              $currsheet->getCellByColumnAndRow(5, $_pcurrrow)->setValue(number_format($order[$type] * $order['price'], 2, ".", "").'');
            }
            
            $_pcurrrow++;
          }
        }
        $_currclientendrow = $_pcurrrow - 1;

        $_start = PHPExcel_Cell::stringFromColumnIndex(5) . '' . $_currclientstartrow;
        $_end = PHPExcel_Cell::stringFromColumnIndex(5) . '' . $_currclientendrow;

        $currsheet->getCellByColumnAndRow(0, $_currclientendrow + 1)->setValue("合计");
        if (!empty($sumedOrdersByClients[$id])) {
          $currsheet->getCellByColumnAndRow(5, $_currclientendrow + 1)->setValue("=ROUND(SUM($_start:$_end),2)");
        } else {
          $currsheet->getCellByColumnAndRow(5, $_currclientendrow + 1)->setValue("0.00");
        }
      }

      $phpExcel->setActiveSheetIndex(0);
      //文件名
      $fileName = $fileNamePerfix . "." . $dates["fromdate"] . "-" . $dates["todate"] . "." . time() . ".xlsx";
      //文件写入句柄
      $objWriter = PHPExcel_IOFactory::createWriter($phpExcel, 'Excel2007');
      //直接下载
      header('Content-Type: application/vnd.ms-excel');
      header('Content-Disposition: attachment;filename="' . $fileName . '"');
      header('Cache-Control: max-age=0');
      $objWriter->save("php://output");
      exit();
    }

    //分区汇总
    public function area_statistics_export($dates, $factory_id) {
      $fromdate = date("Y/m/d");
      if (isset($dates["fromdate"])) {
        $fromdate = $dates["fromdate"];
      }

      $todate = date("Y/m/d", time() + 86400);
      if (isset($dates["todate"])) {
        $todate = date("Y/m/d", strtotime($dates["todate"]) + 86400);
      }

      $sql_add = "";
      if (!empty($factory_id)) {
        $sql_add .= " AND (`njzs_order`.`factory_id`=".$factory_id." OR `njzs_order`.`factory_id`=0)";
      }
      //统计信息
      $statistics = \model\order::sqlExec("SELECT `njzs_order`.`product_id`, `njzs_client`.`area`, SUM(`njzs_order`.`need_amount`) need_amount from `njzs_order`, `njzs_client` where `njzs_client`.`id` = `njzs_order`.`client_id` AND `njzs_order`.`deleted` = 0 AND `njzs_order`.`thedate` >= '" . $fromdate . "' AND `njzs_order`.`thedate` < '" . $todate . "' ".$sql_add." GROUP BY `njzs_order`.`product_id`, `njzs_client`.`area` ORDER BY `njzs_order`.`product_id`, `njzs_client`.`area`;");

      $statistics = \indexSet($statistics, "product_id");

      $products = \model\product::finds("WHERE 1=1 ".$sql_add." order by `order` desc", "id,name,unit");

      $product = \indexArray($products, "id");
      
      //分区信息
      $areas = \model\sort_area::finds("where 1=1 ".$sql_add, 'id, area_name');

      //信息处理
      $data = [];
      foreach ($statistics as $s_key => $s_value) {
        $tmp = [
          "product_id" => $s_key,
          "product_name" => $product[$s_key]["name"],
        ];
        foreach ($s_value as $r_key => $r_value) {
          $tmp[$r_value["area"]] = $r_value["need_amount"];
        }
        $data[] = $tmp;
      }

      //列处理
      $columns = [
          '产品ID' => 'product_id',
          '产品名字' => 'product_name',
      ];

      foreach ($areas as $key => $area) {
        $columns[$area["area_name"]] = $area["area_name"];
        $columns[$area["area_name"] . "(实分)"] = "";
      }

      $fromdate = str_replace("/", '', $fromdate);
      $todate = date("Ymd", strtotime($todate) - 86400);
      $params = [
          'columns' => $columns,
          'data' => $data,
          'filename' => "分区汇总$fromdate-$todate.xlsx",
          'title' => "分区汇总$fromdate-$todate"
      ];

      $this->exportExcel($params);
    }

    //查询订单/分捡数据
    public function sum_export($args = array()) {
      $dates = $args['dates'];
      $type = isset($args['type']) ? $args['type'] : "send_amount";
      $print = isset($args['print'])  ? $args['print'] : false;
      $factory_id = $args['factory_id'];
      $message = "";

      $fromdate = date("Y/m/d");
      if (isset($dates["fromdate"])) {
          $fromdate = $dates["fromdate"];
      }
      $todate = date("Y/m/d", time() + 86400);
      if (isset($dates["todate"])) {
          $todate = date("Y/m/d", strtotime($dates["todate"]) + 86400);
      }

      //1.查询客户信息
      $sql_add = "";
      if (!empty($factory_id)) {
        $sql_add .= " AND (factory_id=".$factory_id." or factory_id=0)";
      }


      $clients = \model\client::finds("WHERE 1=1 ".$sql_add." order by `order` asc", 'id,storename');
      if (!$clients) {
        $message .= "没有查询到客户信息。";
      } else {
        $clients = \indexArray($clients, "id");
      }
        //客户的id
      $client_ids = array_keys($clients);
      $client_ids_str = implode(",", $client_ids);

      //2.查询商品信息
      $products = \model\product::finds("WHERE 1=1 ".$sql_add." order by `order` desc", "id,name,unit");
      if (!$products) {
        $message .= "没有查询到商品信息。";
      } else {
        $products = \indexArray($products, "id");
      }

        //3.查询指定日期已确认订单信息
      $subwhere = $client_ids_str ? "AND `client_id` IN (" . $client_ids_str . ") " : "";
        // $subwhere = ' AND `client_id` IN (65) AND `product_id`=207 ';
        // echo $subwhere;
        // exit;

      $orders = \model\order::sqlExec("SELECT `client_id`,`storename`,`product_id`,`product_name`,`price`,SUM(`need_amount`) AS `need_amount`,SUM(`send_amount`) AS `send_amount`,SUM(`get_amount`) AS `get_amount`, SUM(`cost`) AS `cost` FROM `njzs_order` WHERE `thedate` >= '" . $fromdate . "' AND `thedate` < '" . $todate . "' AND `deleted` = 0 ".$sql_add." " . ($subwhere) . "GROUP BY `client_id`,`product_id`, `price`;");


      $list = [];
      foreach ($orders as $item) {
        $client_id = $item['client_id'];
        $product_id = $item['product_id'];
        if (!isset($list[$client_id."_".$product_id])) {
          $item['sum_price'] = 0;
          $list[$client_id."_".$product_id] = $item;
        }
        $list[$client_id."_".$product_id]['sum_price'] += $item['cost'];
      }

      if (!$orders) {
          $message .= "没有查询到订单信息。";
      }
        // \vd($orders,'ordersordersorders');

      if ($message) {
          // throw new ErrorObject(ErrorCode::QUERY_NO_DATA, $message);
      }
      \vd($list,'$orders');

      $this->generateSumExcel($clients, $products, $list, $dates, $type, $print);
    }

    private function generateSumExcel($clients, $products, $orders, $dates, $type = "send_amount", $print = false)
    {
      $fileNamePerfix = $type == "send_amount" ? "调拨出库单" : "订单";

      //生成Excel
      $phpExcel = new PHPExcel();
      $firstSheetTitle = $fileNamePerfix;

      $phpExcel->setActiveSheetIndex(0);
      $worksheet = $phpExcel->getActiveSheet();
      $worksheet->setTitle($firstSheetTitle);

      $clientStartCol = 5;
      $clientStartRow = 2;
      $clientCount = count($clients);
      //产品数据开始位置
      $productStartCol = 0;
      $productStartRow = 5;
      $productCount = count($products);
      $productEndRow = $productStartRow + $productCount - 1;
      //订单数据开始位置
      $orderStartCol = $clientStartCol;
      $orderStartRow = $productStartRow;
      $orderCount = count($orders);

      //基本设置
      $worksheet->getDefaultRowDimension()->setRowHeight(14);
      $worksheet->getDefaultColumnDimension()->setWidth(14);
      $worksheet->getDefaultStyle()->getFont()->setSize(10);

      //设置日期单元格
      $worksheet->setCellValue('A1', "订单日期");
      $worksheet->mergeCells('B1:C1');
      $worksheet->setCellValue('B1', $dates["fromdate"] . "-" . $dates["todate"]);

      //左上写入表头
      $worksheet->mergeCells('A2:A4');
      $worksheet->setCellValue('A2', "商品编号");
      $worksheet->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
      $worksheet->getStyle('A2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
      $worksheet->mergeCells('B2:B4');
      $worksheet->setCellValue('B2', "商品名称");
      $worksheet->getStyle('B2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
      $worksheet->getStyle('B2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
      $worksheet->mergeCells('C2:C4');
      $worksheet->setCellValue('C2', "单位");
      $worksheet->getStyle('C2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
      $worksheet->getStyle('C2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
      $worksheet->mergeCells('D2:E3');
      $worksheet->setCellValue('D2', "商品合计");
      $worksheet->getStyle('D2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
      $worksheet->getStyle('D2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
      $worksheet->mergeCells('D4:E4');
      $worksheet->setCellValue('D4', "=ROUND(SUM(D".$productStartRow.":D".$productEndRow  ."),2)");
          
      //写入商品列头,合计
      $col = $productStartCol;
      $row = $productStartRow;
      foreach ($products as $product) {
        $worksheet->getCellByColumnAndRow($col, $row)->setValue($product["id"]);
        $worksheet->getCellByColumnAndRow($col + 1, $row)->setValue($product["name"]);
        $worksheet->getStyle(PHPExcel_Cell::stringFromColumnIndex($col + 2).$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $worksheet->getCellByColumnAndRow($col + 2, $row)->setValue($product["unit"]);
        $str = "=ROUND(SUM(";
        $str2 = "=ROUND(SUM(";
        $c_col = $clientStartCol;
        foreach ($clients as $client) {
            $str .= PHPExcel_Cell::stringFromColumnIndex($c_col).$row.",";
            $str2 .= PHPExcel_Cell::stringFromColumnIndex($c_col+1).$row.",";
            $c_col = $c_col + 2;
        }
        $str.= "),2)";
        $str2.= "),2)";
        $worksheet->getCellByColumnAndRow($col + 3, $row)->setValue($str);
        $worksheet->getCellByColumnAndRow($col + 4, $row)->setValue($str2);
        $row ++;
      }


      $client_amount_cols = [];
      //写入店铺行头及纵向统计数据:总表统计数据已注释
      $col = $clientStartCol;
      $row = $clientStartRow;
      foreach ($clients as $client) {
        $worksheet->mergeCells(PHPExcel_Cell::stringFromColumnIndex($col) . $row.":" . PHPExcel_Cell::stringFromColumnIndex($col + 1) . $row);
        $worksheet->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $worksheet->getCellByColumnAndRow($col, $row)->setValue($client["id"]);
        $worksheet->mergeCells(PHPExcel_Cell::stringFromColumnIndex($col).($row+1).":".PHPExcel_Cell::stringFromColumnIndex($col + 1).($row+1));
        $worksheet->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . ($row+1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $worksheet->getCellByColumnAndRow($col, $row+1)->setValue($client["storename"]);
        $worksheet->mergeCells(PHPExcel_Cell::stringFromColumnIndex($col).($row+2).":".PHPExcel_Cell::stringFromColumnIndex($col + 1).($row+2));
        $client_col = PHPExcel_Cell::stringFromColumnIndex($col);
        $str = "=ROUND(SUM(".$client_col.$productStartRow.":".$client_col.$productEndRow."),2)";
        $worksheet->getCellByColumnAndRow($col, $row+2)->setValue($str);
        $col = $col + 2;
      }

      //写入订单分拣信息
      if ($orders) {
        //var_dump($orders);
        foreach ($orders as $order) {
          \vd($order,'$order');

          $cpos = array_keys(array_keys($clients), $order["client_id"])[0];
          $ppos = array_keys(array_keys($products), $order["product_id"])[0];
          $col = $orderStartCol + $cpos * 2 + 1;
          $row = $orderStartRow + $ppos;
          $worksheet->getCellByColumnAndRow($col, $row)->setValue($order['send_amount']);
          $col = $orderStartCol + $cpos * 2;
          $worksheet->getCellByColumnAndRow($col, $row)->setValue("=ROUND(".$order["sum_price"].",2)");
        }
      } else {
          $worksheet->getCellByColumnAndRow(2, 3)->setValue("没有分拣数据");
      }

      $phpExcel->setActiveSheetIndex(0);
      //文件名
      $fileName = $fileNamePerfix . "." . $dates["fromdate"] . "-" . $dates["todate"] . "." . time() . ".xlsx";
      //文件写入句柄
      $objWriter = PHPExcel_IOFactory::createWriter($phpExcel, 'Excel2007');
      //直接下载
      header('Content-Type: application/vnd.ms-excel');
      header('Content-Disposition: attachment;filename="' . $fileName . '"');
      header('Cache-Control: max-age=0');
      $objWriter->save("php://output");
      exit();
    }
    

    //通用导出
    public function exportExcel($params)
    {
        $phpExcel = new PHPExcel();
        $phpExcel->setActiveSheetIndex(0);
        $worksheet = $phpExcel->getActiveSheet();
        $column_count = count($params['columns']);
        if (isset($params['title'])) {
            $worksheet->setTitle($params['title']);
            // excel标题
            $worksheet->mergeCells(PHPExcel_Cell::stringFromColumnIndex(0) . "1:" . PHPExcel_Cell::stringFromColumnIndex($column_count - 1) . "1");
            $worksheet->setCellValue('A1', $params['title']);
            $worksheet->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $worksheet->getStyle('A1')->getFont()->setSize(16);
            $worksheet->getStyle('A1')->getFont()->setBold(true);
        }

        $column_idx = 0;

        $total_data_length = count($params['data']);
        foreach ($params['columns'] as $column_name => $column_en) {
            // 列标题
            $row_idx = 2;
            $cell = PHPExcel_Cell::stringFromColumnIndex($column_idx) . $row_idx;
            $worksheet->setCellValue($cell, $column_name);
            $worksheet->getStyle($cell)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $worksheet->getStyle($cell)->getFont()->setSize(12);
            $worksheet->getStyle($cell)->getFont()->setBold(true);

            // 列数据
            if (count($params['data']) > 0) {
                foreach ($params['data'] as $row_data) {
                    $worksheet->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($column_idx) . '')->setAutoSize(true);
                    $row_idx++;
                    if (!isset($row_data[$column_en])) {
                        continue;
                    }
                    $cell = PHPExcel_Cell::stringFromColumnIndex($column_idx) . $row_idx;
                    $worksheet->setCellValue($cell, $row_data[$column_en]);
                }
            }

            $column_idx++;
        }

        if (count($params['data']) == 0) {
            $worksheet->setCellValue('A4', "没有相关数据");
        }
        //文件名
        $fileName = $params['filename'];
        //文件写入句柄
        $objWriter = PHPExcel_IOFactory::createWriter($phpExcel, 'Excel2007');
        //生成文件
        //$filePathName = iconv("utf-8", "gb2312", self::$ExcelFilePath . $fileName);
        //$objWriter->save($filePathName);
        //直接下载
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');
        $objWriter->save("php://output");
        exit();
    }

   
}
