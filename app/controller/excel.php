<?php
namespace Controller;
use Log\LogConfig;

ini_set("max_execution_time", 600);
ini_set("memory_limit", 1048576000);

class excel extends \app\controller {
  public function export() {
    include \view('excel__export');
  }


  public function order_export() {
    ini_set("max_execution_time", 600);
    ini_set("memory_limit", 1048576000);

    $para = $this->fetchData("GET", ["fromdate", "todate"], true);
    $factory = $_SESSION['user']['factory_id'];
    $params = [
        'dates' => $para,
        'type' => 'need_amount',
        'factory_id' => $factory
    ];
    $result = $this->di["ExcelService"]->order_export($params);
    $this->data([]);
  }

  //导出调拨单
  public function sortout_export() {
    ini_set("max_execution_time", 600);
    ini_set("memory_limit", 1048576000);

    $para = $this->fetchData("GET", ["fromdate", "todate"], true);

    $factory = $_SESSION['user']['factory_id'];
    $params = [
        'dates' => $para,
        'type' => 'send_amount',
        'factory_id' => $factory
    ];

    $result = $this->di["ExcelService"]->order_export($params);
    // $this->di['LogService']->insert([
    //     'content' => LogConfig::EXCEL_EXPORT_SORTOUT
    // ]);
    $this->data([]);
  }

    public function sum_export() {
      ini_set("max_execution_time", 600);
      ini_set("memory_limit", 1048576000);

      $para = $this->fetchData("GET", ["fromdate", "todate"], true);
      $areas = isset($_GET["areas"]) ? strtoupper($_GET["areas"]) : "";
      $print = isset($_GET["for"]) && strtoupper($_GET["for"]) == "PRINT" ? true : false;

      $factory = $_SESSION['user']['factory_id'];
      $params = [
          'dates' => $para,
          'area' => $areas,
          'type' => 'send_amount',
          'print' => $print,
          'factory_id' => $factory,
      ];

      $result = $this->di["ExcelService"]->sum_export($params);
      // $this->di['LogService']->insert([
      //     'content' => LogConfig::EXCEL_EXPORT_SORTOUT
      // ]);
      $this->data([]);
    }

    //订单分区汇总导出
    public function area_statistics_export()
    {
        $dates = $this->fetchData("GET", ["fromdate", "todate"], false);
        $factory_id = $_SESSION['user']['factory_id'];

        $result = $this->di["ExcelService"]->area_statistics_export($dates, $factory_id);
        // $this->di['LogService']->insert([
        //     'content' => LogConfig::EXCEL_EXPORT_AREA_STATISTICS
        // ]);
        $this->data([]);
    }

    public function test_export() {
       $data = [
           [
               'client' => 'tianshanlu',
               'operator' => 'xiaoming',
               'amount' => '10',
               'change_before' => '5',
               'change_after' => '15',
               'reason' => 'tiaozheng'
           ],
           [
               'client' => 'tianshanlu333',
               'operator' => 'xiaoming222',
               'amount' => '12',
               'change_before' => '66',
               'change_after' => '99',
               'reason' => 'tiaozheng'
           ]
       ];
        $params = [
            'columns' => [
               '客户' => 'client',
               '操作人' => 'operator',
               '变动金额' => 'amount',
               '变动前' => 'change_before',
               '变动后' => 'change_after',
               '原因' => 'reason'
            ],
            'data' => $data, 
            'filename' => 'aaa.xlsx',
            'title' => 'aaa'
        ];

        $this->di['ExcelService']->exportExcel($params);
        $this->data([]);
    }

    public function account_deposit_export() {
       $data = [
           [
               'client' => 'tianshanlu',
               'operator' => 'xiaoming',
               'amount' => '10',
               'change_before' => '5',
               'change_after' => '15',
               'reason' => 'tiaozheng'
           ],
           [
               'client' => 'tianshanlu333',
               'operator' => 'xiaoming222',
               'amount' => '12',
               'change_before' => '66',
               'change_after' => '99',
               'reason' => 'tiaozheng'
           ]
       ];

       $data = $this->di['ClientService']->getList();
       // \vd($data,'$data');

       $config = \model\sms_config::finds("where id>0");
       $config = \indexBy($config,'client_id');
       // \vd($config,'$config');

       foreach ($data as &$v) {
        // print_r($v);
        // echo '---<br>';
         $v['sms'] = $config[''.$v['id']]['client_name'];
       }
       \vd($data,'$data');

        $params = [
            'columns' => [
               '店铺编号' => 'id',
               '客户' => 'storename',
               '余额' => 'deposit',
               '登录帐号' => 'username',
               '充值帐号' => 'sms',
            ],
            'data' => $data, 
            'filename' => '客户余额.xlsx',
            'title' => '客户余额'
        ];

        $this->di['ExcelService']->exportExcel($params);
        $this->data([]);
    }

    public function daily_account_export() {
      $factory_id = $_SESSION['user']['factory_id'];
      $q = $_GET;
      $q['thedate'] = $q['fromdate'];

      // $res = \Model\Order::execSql("select client_id,sum(price*send_amount) as sum,storename from ", " where thedate='2016/09/11' group by storename;");
      $sql_add = "";
      if (!empty($factory_id)) {
        $sql_add .= " AND (factory_id=".$factory_id." OR factory_id=0)";
      }
      $data = \model\order::finds(" where thedate='".$q['thedate']."' ".$sql_add." group by storename ", 'client_id,sum(cost) as amount,storename');
      \vd($data,'res');

      $params = [
          'columns' => [
             '客户ID' => 'client_id',
             '店铺' => 'storename',
             '金额' => 'amount'
          ],
          'data' => $data,
          'filename' => '调拨金额'.$q['thedate'].'.xlsx',
          'title' => '调拨金额'.str_replace('/','-',$q['thedate']).''
      ];

      $this->di['ExcelService']->exportExcel($params);
      $this->data([]);
    }

    public function manager_his_export() {
      $data = $_GET;
      $data['start_time'] = str_replace('-','/', $data['start_time'] );
      $data['end_time'] = str_replace('-','/', $data['end_time'] );

      $start = $data['start_time'];
      $end = $data['end_time']." 23:59:59";
      $res = \model\log_account::finds("where create_at >= ".strtotime($start) . " and create_at <=".strtotime($end)." and code = ".\model\log_account::$CODE['代理人分配']);
      $factory_id = $_SESSION['user']['factory_id'];

      $clients = $this->di['ClientService']->getClientByFactory($factory_id);
      $ex_data = [];
      foreach ($res as $key => $item) {
        $arr = [];
        $arr['operator'] = $item['operator'];
        $arr['client_name'] = $clients[$item['client_id']]['storename'];
        $extra = json_decode($item['extra'], true);
        $arr['change_money'] = $extra['变动金额'];
        $arr['after_money'] = $extra['变动后金额'];
        $arr['time'] = $item['date_time'];
        $ex_data[] = $arr;
      }


      $params = [
          'columns' => [
             '代理人' => 'operator',
             '店铺' => 'client_name',
             '分配金额' => 'change_money',
             '分配后金额' => 'after_money',
             '时间' => 'time',
          ],
          'data' => $ex_data,
          'filename' => '代理人分配金额'.str_replace('/','-',$start).'~'.str_replace('/','-',$data['end_time']).'.xlsx',
          'title' => '代理人分配金额'.str_replace('/','-',$start).'~'.str_replace('/','-',$data['end_time'])
      ];

      $this->di['ExcelService']->exportExcel($params);
      $this->data([]);
    }

    public function manager_list_export() {
      $data = $_GET;
      $m_list = \model\admin::sqlQuery("SELECT a.id, a.name, c.storename, c.deposit, count(c.storename) AS clients_count, a.deleted FROM njzs_admin a LEFT JOIN njzs_client c ON a.name = c.manager_name WHERE a.type=".\model\admin::$TYPE['MANAGER'].$sql_add." GROUP BY a.name ORDER BY c.`order` DESC, a.id ASC");

      foreach ($m_list as &$item) {
        $item['can_allot'] = 0;
        if ($item['clients_count'] == 0) {
          $item['storename'] = "";
        } else if ($item['clients_count'] >= 1) {
          $manager = \model\client_manager::find("where manager_name='".$item['name']."'");
          $item['can_allot'] = $manager['balance'];
          if ($item['clients_count'] > 1) {
            $clients = \model\client::finds("where manager_name='".$item['name']."'", 'storename, deposit');
            $deposit = $manager['balance'];
            foreach ($clients as $c) {
              $deposit += $c['deposit'];
              $storename .= $c['storename'].",";
            }
            $item['storename'] = $storename;
            $item['deposit'] = $deposit;
          }
        }
      }

      $params = [
          'columns' => [
             '代理人' => 'name',
             '店铺' => 'storename',
             '总余额' => 'deposit',
             '可分配余额' => 'can_allot'
          ],
          'data' => $m_list,
          'filename' => '代理人信息'.date('Y-m-d').'.xlsx',
          'title' => '代理人信息'.date('Y-m-d')
      ];

      $this->di['ExcelService']->exportExcel($params);
      $this->data([]);
    }


    public function sort_area_export() {
      ini_set("max_execution_time", 600);
      ini_set("memory_limit", 1048576000);
      $factory_id = $_SESSION['user']['factory_id'];
      $sql_add = "";
      if (!empty($factory_id)) {
        $sql_add = " WHERE (factory_id=".$factory_id." OR factory_id=0)";
      }
      $sort_area = \model\sort_area::finds($sql_add, 'area_name,client_ids');

      $data = [];
      $client_data = [];
      $params = [];
      $max_count = 0;
      foreach ($sort_area as $item) {
        $client_ids = $item['client_ids'];
        $clients = \model\client::finds('WHERE id in ('.$client_ids.') ORDER BY `order` asc', 'id,storename');
        $area_s = [];
        foreach ($clients as $c) {
          $area_s[] = $c['storename'];
        }

        $client_data[$item['area_name']] = $area_s;
        if ($max_count < count($area_s)) {
          $max_count = count($area_s);
        }
        $params['columns'][$item['area_name']] = $item['area_name'];
      }

      for ($i = 0; $i < $max_count; $i++) {
        $arr = [];
        foreach ($sort_area as $item) {
          $arr[$item['area_name']] = "";
          if (!empty($client_data[$item['area_name']][$i])) {
            $arr[$item['area_name']] = $client_data[$item['area_name']][$i];
          }
        }
        $data[] = $arr;
      }

      $params['data'] = $data;
      $params['filename'] = '所有分区.xlsx';
      $params['title'] = '所有分区';
      $this->di['ExcelService']->exportExcel($params);
      $this->data([]);
    }

  


}