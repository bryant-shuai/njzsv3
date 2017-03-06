<?php
namespace controller;
use common\ErrorCode;

class orders extends \app\controller {
  function day_product_need() {
    $data = $_GET;
    $data['thedate'] = str_replace('-','/', $data['thedate'] );
    $data['factory_id'] = $_SESSION['user']['factory_id'];
    $products = $this->di['OrderService']->dayProductNeed($data);
    $this->data(['ls'=>$products]);
  }

  /**
   * 修改订单
   * @return [type] [description]
   */
  function change_order() {
    include \view('orders__change_order');
  }

  /**
   * 补填发货
   * @return [type] [description]
   */
  function change_order_direct() {
    include \view('orders__change_order_direct');
  }

  function download_orders() {
    $sql_add = "";
    if (!empty($_SESSION['user']['factory_id'])) {
      $sql_add .= "where (factory_id = 0 or factory_id=".$_SESSION['user']['factory_id'].")";
    }
    $sort_area = \model\sort_area::finds($sql_add, 'id,area_name');
    if (count($sort_area) > 0) {
      $__sort_area = json_encode($sort_area);
    } else {
      $__sort_area = '[]';
    }
    include \view('orders__download_orders');
  }

  // 导出数据
  function export(){
    $data = $_GET;
    if( !isset($_SESSION['user']) ){
      header("location:/login.html");
    }
    $data['thedate'] = str_replace('-','/', $data['thedate'] );
    $thedate = $data['thedate'];
    $batch_id = $data['batch_id'];
    $sortAreaId = $data['sortarea_id'];
    $area_name = $data['area_name'];
    $factory_id = $_SESSION['user']['factory_id'];
    
    $sort_orders = $this->di['OrderService']->getOrderInfoWithSortArea([
      'thedate' => $thedate,
      'batch_id' => $batch_id,
      'sort_area_id' => $sortAreaId,
      'factory_id' => $factory_id,
    ]);
    $orders = $sort_orders['orders'];
    $o_list = [];
    foreach ($orders as $o) {
      $o_list[] = $o;
    }
    $sort_orders['orders'] = $o_list;

   //下载
    $filename = $area_name.'_'.date("Ymd",strtotime($thedate)).'_'.$batch_id.'.txt';
    $ua = $_SERVER["HTTP_USER_AGENT"];
    $encoded_filename = urlencode($filename);    
    $encoded_filename = str_replace("+", "%20", $encoded_filename);

    header("Content-Type: application/octet-stream");      
    if (preg_match("/MSIE/", $_SERVER['HTTP_USER_AGENT']) ) {      
      header('Content-Disposition:  attachment; filename="' . $encoded_filename . '"');      
    } elseif (preg_match("/Firefox/", $_SERVER['HTTP_USER_AGENT'])) {      
    header('Content-Disposition: attachment; filename*="utf8' .  $filename . '"');      
    } else {      
    header('Content-Disposition: attachment; filename="' .  $filename . '"');      
    }
    echo (\en($sort_orders));
  }


  /**
   * 导出调拨单请求
   * @return [type] [description]
   */
  function export_diaobodan_excel() {
    // $_GET['debug'] = 1;
    $data = $_GET;
    $data['thedate'] = str_replace('-','/', $data['thedate'] );
    $thedate = $data['thedate'];
    ini_set("max_execution_time", 600);
    ini_set("memory_limit", 1048576000);
    
    $batch_id = $data['batch_id'];
    $area = $data['area'];
    $factory_id = $_SESSION['user']['factory_id'];
    $this->di['ExportService']->outputDiaoBoDan($thedate,$batch_id,$factory_id,$area);
    // $this->data(true);
  }


  /**
   * 批次列表页面
   * @return [type] [description]
   */
  function batch_list() {
    include \view('orders__batch_list');
  }

  /**
   * 添加批次
   * @return [type] [description]
   */
  function create_batch() {
    $__factory_ids = json_encode(\DataConfig::$FACTORY);
    include \view('orders__create_batch');
  }

  /**
   * 上传分拣结果页面
   * @return [type] [description]
   */
  function upload_sort_result() {
    $sort_files = \model\sortfile_log::finds('order by id desc limit 13');
    foreach ($sort_files as &$item) {
      $item['create_at'] = date('Y-m-d H:i:s', $item['create_at']);
    }
    if (count($sort_files) > 0) {
      $__sort_files = json_encode($sort_files);
    } else {
      $__sort_files = '[]';
    }

    include \view('orders__upload_sort_result');
  }

  /**
   * 请求已上传文件结果
   * @return [type] [description]
   */
  function aj_sort_file_list() {
    $sort_files = \model\sortfile_log::finds('order by id desc limit 13');
    foreach ($sort_files as &$item) {
      $item['create_at'] = date('Y-m-d H:i:s', $item['create_at']);
    }
    $this->data($sort_files);
  }

  /**
   * 处理上传分拣结果
   * @return [type] [description]
   */
  function aj_upload_sort_result() {
    // $_GET['debug'] = 1;
    $file = $_FILES["file"];
    $filename = $file["name"];
    // echo $filename;
    $upfile = __PUBLIC_DIR__."/uploads/".$filename;
    if(isset($file["tmp_name"])) {
      if(move_uploaded_file($file["tmp_name"], $upfile)) {
        $contents= file_get_contents($upfile);
        $read_content = strip_tags($contents);
        $json_content = $read_content;
        // 把文件进行MD5处理，避免上传重复的文件
        $md5 = md5($read_content);

        $read_content = \de($read_content.'');

        if(!$read_content['result']){
          \except(-1, '文件数据类型错误，请上传带有‘_’的文件！');
        }
        $sort_data = $read_content['result'];
        $res = $this->di['OrderService']->dealSortData($sort_data,$filename,$md5,$json_content);
      
        $this->data(true);
      }
    }
  }

  /**
   * 导出调拨单页面
   * @return [type] [description]
   */
  function export_sort_orders() {
    $sql_add = "";
    if (!empty($_SESSION['user']['factory_id'])) {
      $sql_add .= "where (factory_id = 0 or factory_id=".$_SESSION['user']['factory_id'].")";
    }
    $sort_area = \model\sort_area::finds($sql_add, 'id,area_name');
    if (count($sort_area) > 0) {
      $__sort_area = [
        [        
          'id' => '0',
          'area_name' => '全部'
        ]
      ];
      $__sort_area = array_merge($__sort_area, $sort_area);
      $__sort_area = json_encode($__sort_area);
    } else {
      $__sort_area = '[]';
    }
    include \view('orders__export_sort_orders');
  }
  
}
