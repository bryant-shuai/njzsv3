<?php
namespace controller;

class log_account extends \app\controller
{
		function log_account_by_manual(){
			$data = $_GET;
			// $client = JSON_decode(str_replace('\\', '', $data['ls']),true);
			$client_id = $data['id'];
			$client_storename = $data['name'];
			$count = 0;
			$page = 1;
			$length = 50;


			$start_time = date("Y-m-d",strtotime("-8 days"));
			$end_time = date("Y-m-d",strtotime("+1 day"));

 			$logs = $this->di['LogAccountService']->getLogByManualUpdateDeposit($client_id,$start_time,$end_time,$count,[
 					'page' => $page,
 					'length' => $length,
 				]);
			\vd($logs,'__');
			$logs = \en($logs);
			include \view('log_account_by_manual_index');
		}



		function search_log_by_time(){
			$data = $_GET;
			$count = 0;
			$page = $data['page'];
			$length = $data['length'];
			$client_id = $data['client_id'];
			if(empty($page)) $page = 1;
			if(empty($length)) $length = 5;

			// if(empty($start_time) || empty($end_time)){
			// 	\except(-1,"日期不能为空");
			// }

			$start_time = $data['start_time'];
			$end_time = $data['end_time']." 23:59:59";
			\vd($start_time);

			$ls = $this->di['LogAccountService']->getLogByManualUpdateDeposit($client_id,$start_time,$end_time,$count,[
					'page' => $page,
					'length' => $length,
				]);


			$this->data([
					'ls'=>$ls,
					'page'=>$page,
					'count'=>$count,
					'length'=>$length,
				]);
		}



    // 代理人分配
    function aj_manager_history() {
      $data = $_GET;
      $name = $_SESSION['user']['name'];
      $code = \model\log_account::$CODE['代理人分配'];
      $count = 0;
      $page = 1;
      $length = 50;
      $start = $data['start_time'];
      $end = $data['end_time']." 23:59:59";
      $logs = $this->di['LogAccountService']->getManagerAllotHistory($name, $start, $end ,$code,$count,[
          'page' => $page,
          'length' => $length,
        ]);
    
      $this->data(['ls'=>$logs]);
    }















}