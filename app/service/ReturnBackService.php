<?php
namespace service;

use model\order as OrderModel;
use app\service as Service;

class ReturnBackService extends Service {

		function getfinance ($data){
			$finance = new \model\return_back;
			$finance->save([
				'client_id' => $data['client_id'],
				'create_at' => date('Y-m-d H:i:s'),
				'amount' => $data['finance'],
				'extra' => $data['remark'],
				'status' => 0,
				]);
			return;
		}
}