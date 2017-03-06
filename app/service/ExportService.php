<?php
namespace service;

error_reporting(E_ALL);
set_include_path(get_include_path() . PATH_SEPARATOR . __BASE_DIR__ . "/app/lib/");
require_once __BASE_DIR__ . "/app/lib/PHPExcel.php";
require_once __BASE_DIR__ . "/app/lib/PHPExcel/Reader/Excel2007.php";
require_once __BASE_DIR__ . "/app/lib/PHPExcel/Reader/Excel5.php";
require_once __BASE_DIR__ . "/app/lib/PHPExcel/IOFactory.php";

use PHPExcel;
use PHPExcel_Cell;
use PHPExcel_IOFactory;
use PHPExcel_Reader_Excel5;
use PHPExcel_Reader_Excel2007;
use PHPExcel_Style_Alignment;

class ExportService extends \app\service
{
  // private static $ExcelFilePath = __BASE_DIR__ . "/public/file/";


  function __outputDiaoBoDan__(){

        $fromdate = '20160101';
        $batchId = '1';
        $title = '标题';
        $filename = "调拨单_".$fromdate."_".$batchId.".xlsx";

        //列处理
        $columns = [
            '客户ID' => 'client_id',
            '客户名字' => 'client_name',
            '产品名字' => 'product_name',
            '发货数量' => 'send_amount',
        ];

        $data = [
          ['client_id' => '1','client_name' => 'A1','product_name' => 'bv1','send_amount' => 0.5,],
          ['client_id' => '2','client_name' => 'B1','product_name' => 'bv2','send_amount' => 0.5,],
        ];

        $params = [
            'columns' => $columns,
            'data' => $data,
            'filename' => $filename,
            'title' => $title,
        ];

        $this->exportExcel($params);
    // echo 'sortResult';
  }


  // function _getDiaoBoDanDataByClientId($thedata, $batchId ){

  //   return $data;
  // }

  function outputDiaoBoDan($thedate,$batch_id,$factory_id, $area=NULL){

        $fromdate = '20160101';
        $batchId = '1';
        \vd($thedate,"日期");
        $title = '调拨出库单';
        $filename = "调拨单".$thedate."_".$batch_id;
        \vd($filename,'文件名');
        //生成Excel对象
        $phpExcel = new PHPExcel();
        $phpExcel->setActiveSheetIndex(0);
        $worksheet = $phpExcel->getActiveSheet();
        $worksheet->setTitle($title);

        $ordersInfo = $this->di['OrderService']->getOrderInfo([
          'thedate' => $thedate,
          'batch_id' => $batch_id,
          'factory_id' => $factory_id,
          'sort_area' => $area
        ]);

        \vd($ordersInfo, "orders");

        \vd($ordersInfo['clients'],'$clients$clients$clients$clients');

        $sumedOrdersByClients = [];
        foreach ($ordersInfo['orders'] as $order) {
          if(empty($sumedOrdersByClients[''.$order['client_id']])){
            $sumedOrdersByClients[''.$order['client_id']] = [];
          }
          $sumedOrdersByClients[''.$order['client_id']][] = $order;
        }



        $rowindex = 0;
        foreach ($ordersInfo['clients'] as $client) {
          // echo '<hr>导一家店开始';
          // echo '<hr>导一家店';
          // print_r($sumedOrdersByClients[''.$client['id']]);
          $this->exportDiaoBoDan_OneClient($worksheet, $rowindex, $client,$sumedOrdersByClients[''.$client['id']],$ordersInfo['products'], $thedate);

          // 空4行，方便撕纸
          $rowindex += 4;

          // echo '<hr>导一家店结束';
        }

        $phpExcel->setActiveSheetIndex(0);
        //文件名
        $fileName = $filename . ".xlsx";
        // \vd($fileName,'新文件名')
        //文件写入句柄
        $objWriter = PHPExcel_IOFactory::createWriter($phpExcel, 'Excel2007');
        //生成文件
      
        //直接下载
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');
        $objWriter->save("php://output");
        exit();

        // $this->exportExcel($params);
    // echo 'sortResult';
  }



  function exportDiaoBoDan_OneClient(&$currsheet, &$rowindex, $client, $orders, $products, $thedate){

    $fileNamePerfix = '工厂调拨单';
    //写入表头

    $rowindex++;

    $rowindex++;
    $currsheet->mergeCells("A" . ($rowindex) . ":F" . ($rowindex));
    $currsheet->setCellValue("A" . ($rowindex), $fileNamePerfix);


    $rowindex++;
    $currsheet->mergeCells("A" . ($rowindex) . ":B" . ($rowindex));
    $currsheet->setCellValue("A" . ($rowindex), "出库门店/仓库：工厂");
    $currsheet->setCellValue("E" . ($rowindex), "订单日期");
    $currsheet->setCellValue("F" . ($rowindex), $thedate);

    $rowindex++;
    $currsheet->mergeCells("A" . ($rowindex) . ":B" . ($rowindex));
    $currsheet->setCellValue("A" . ($rowindex), "进库门店/仓库：" . $client["storename"]);
    $currsheet->mergeCells("C" . ($rowindex) . ":D" . ($rowindex));
    $currsheet->setCellValue("C" . ($rowindex), "单号:-");
    $currsheet->mergeCells("E" . ($rowindex) . ":F" . ($rowindex));
    $currsheet->setCellValue("E" . ($rowindex), "发货日期:");
    $currsheet->setCellValue("F" . ($rowindex), "\\");

    $rowindex++;
    $currsheet->setCellValue("A" . ($rowindex), "商品编号");
    $currsheet->setCellValue("B" . ($rowindex), "商品名称");
    $currsheet->setCellValue("C" . ($rowindex), "单位");
    $currsheet->setCellValue("D" . ($rowindex), "数量");
    $currsheet->setCellValue("E" . ($rowindex), "调拨价");
    $currsheet->setCellValue("F" . ($rowindex), "调拨金额");
    // $currsheet->setCellValue("G" . ($rowindex), "开始");


    $rowindex++;

    $startRowIdx = $rowindex +0;
    $endRowIdx = $rowindex+0;

    foreach ($orders as $key => $order) {
      if ($order['send_amount'] == 0) {
        continue;
      }
      $i = 0;
      $product = $products[$order['product_id']];
      //产品编号
      // $_pid = $worksheet->getCellByColumnAndRow(0, $_fromrow)->getValue();
      $currsheet->getCellByColumnAndRow($i, $rowindex)->setValue($order['product_id'].'');
      $currsheet->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($i) . '')->setAutoSize(true);
      //产品名字
      // $_pname = $worksheet->getCellByColumnAndRow(1, $_fromrow)->getValue();
      $i++;
      $currsheet->getCellByColumnAndRow($i, $rowindex)->setValue($order['product_name'].'');
      $currsheet->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($i) . '')->setAutoSize(true);

      $i++;
      $currsheet->getCellByColumnAndRow($i, $rowindex)->setValue($product['unit'].'');
      $currsheet->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($i) . '')->setAutoSize(true);

      $i++;
      $currsheet->getCellByColumnAndRow($i, $rowindex)->setValue($order['send_amount'].'');
      $currsheet->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($i) . '')->setAutoSize(true);

      $i++;
      $currsheet->getCellByColumnAndRow($i, $rowindex)->setValue($order['price'].'');
      $currsheet->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($i) . '')->setAutoSize(true);

      $i++;
      $currsheet->getCellByColumnAndRow($i, $rowindex)->setValue($order['cost'].'');
      $currsheet->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($i) . '')->setAutoSize(true);

      // $i++;
      // $currsheet->getCellByColumnAndRow($i, $rowindex)->setValue($rowindex);


      $endRowIdx = $rowindex+0;
      $rowindex++;
    }

    //生成合计数据
    $_start = PHPExcel_Cell::stringFromColumnIndex(5) . '' . $startRowIdx;
    $_end = PHPExcel_Cell::stringFromColumnIndex(5) . '' . $endRowIdx;

    $currsheet->getCellByColumnAndRow(0, $rowindex)->setValue("合计");
    if ($startRowIdx == $endRowIdx) {
      $currsheet->getCellByColumnAndRow(5, $rowindex)->setValue("0.00");
    } else {
      $currsheet->getCellByColumnAndRow(5, $rowindex)->setValue("=ROUND(SUM($_start:$_end),2)");

    }
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