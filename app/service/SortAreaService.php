<?php

namespace service;

use model\order as OrderModel;
use app\service as Service;

class SortAreaService extends Service {


  //将指定的店铺从一个分区移动到另一个分区
	function client_add_sortarea($sortarea_id,$client_id,$from_sortarea_id){
    //判断是否要将店铺移动到未分区，如果$sortarea_id为0，则移动到未分区
		if($sortarea_id == 0){  
			$oFromsortarea = \model\sort_area::loadObj($from_sortarea_id);
			$client_ids = $oFromsortarea->data['client_ids'];
      //取出来的$client_ids是字符串
			$client_ids = explode(',',$client_ids);
			foreach ($client_ids as $key => $v){
				if($client_id == $v){
					unset($client_ids[$key]);
				}
			}
			$client_ids = implode(',', $client_ids);
			$oFromsortarea->data['client_ids'] = $client_ids;
			$oFromsortarea->save();
		}else{
			$oSortarea = \model\sort_area::loadObj($sortarea_id);
      //判断店铺之前是否属于未分区，如果$from_sortarea_id为0，则之前属于为分区
			if($from_sortarea_id != 0){
				$oFromsortarea = \model\sort_area::loadObj($from_sortarea_id);
				$client_ids = $oFromsortarea->data['client_ids'];
				$client_ids = explode(',',$client_ids);
				foreach ($client_ids as $key => $v){
					if($client_id == $v){
						unset($client_ids[$key]);
					}
				}
				$client_ids = implode(',', $client_ids);
				$oFromsortarea->data['client_ids'] = $client_ids;
				$oFromsortarea->save();
			}

			if(!$oSortarea->data['client_ids']){
				$oSortarea->data['client_ids'] = $client_id;	
			}else{
				$oSortarea->data['client_ids'] =$oSortarea->data['client_ids'].','.$client_id;
			}
			$oSortarea->save();
		}
	}


  // 根据分区取出该分区下的所有店铺
	function get_client_by_sortarea($id,$factory_id,$sortbyid=null){
		$client_ids = \model\sort_area::finds("where id='".$id."' and factory_id='".$factory_id."'",'client_ids');
    // \vd($client_ids,'######');
		$client_ids = $client_ids[0];
		$client_ids = explode(',',$client_ids['client_ids']);
    // \vd($client_ids,'@@@@@');
    // array_pop($client_ids);
		// $clients = \model\client::findByIds($client_ids);
    $clients = \model\client::finds(" where id in (".implode(',', $client_ids).") order by `order` ASC ");
		if($sortbyid) $clients = \indexBy($clients,'id');
		// \vd($clients,'_____');
		return $clients;
  }



	//根据公司ID查出该公司的所有分区
	function getpartition (){
    $factory_id = $_SESSION['user']['factory_id'];
		$partitions = \model\sort_area::finds("where factory_id=".$factory_id);
		return $partitions;
	}

  //方法没有用到
  function unclassified($from_area_id = 0,$factory_id = 1) {

    if (!empty($from_area_id)) {
      $area_id = $from_area_id;
      \vd($area_id,'qqqq');
      $clients = $this->di['SortAreaService']->get_client_by_sortarea($area_id,$factory_id);
      // \vd($clients,'@@@@@@@@@@@@@@@@@@@');
    }else{
      // 查询client表找出所有店面
      $clients = \model\client::finds(" where factory_id = 1 and deleted = 0",'id,storename,factory_id,username');
      $clients = \indexBy($clients,'id');
    }
    // 查询已经分组的店面，筛选出未分组的店面
    $areas = \model\sort_area::finds("where id !=".$area_id);
    // \vd($areas,'++++++++++++++++++++');
    $clientIds = [];
    foreach ($areas as $area) {
      $clientIds[] = explode(',', $area['client_ids']);
    }
    \vd($clientIds,'3333333');
    foreach ($clientIds as $clientId) {
      foreach ($clientId as $key => $val) {
        // \vd($clientId,'#####');
        unset($clients[$val]);
      }
    }
    // \vd($clients,'#####');
    return $clients;
  }

  function getClientIds($id,$factory_id = 1) {
    $oSortarea = \model\sort_area::loadObj("where id='".$id."' and factory_id='".$factory_id."'",'client_ids');
    $client_ids = $oSortarea->data['client_ids'];
    return $clients;
  }

  // 拿到指定分区的订单
  function sortAreaOrders($thedate,$batch_id=null,$id=null,$factory_id = null) {
    $r = [];
    $sql = '';
    if($batch_id){
      $sql = " AND batch_id='".$batch_id."'";
    }
    if ($id) {
      $client_ids = $this->di['SortAreaService']->getClientIds($id);
      $sql .= " AND client_id in '(".implode(',', $client_ids).")'";
    }

    $orders = \model\order::finds(" WHERE thedate='$thedate' 
      ".$sql." and deleted=0 and factory_id= '".$factory_id." ' group by client_id,product_id ",' id,client_id,storename,product_id,product_name,price,need_amount,send_amount,get_amount,thedate');
    \vd($orders,'$——————————————————————');
    return $orders;
  }

  function clientIdsSortByArea($factory_id){
    $clientIds = \model\sort_area::finds("where factory_id='".$factory_id."'",'client_ids');
    foreach ($clientIds as $key => $id) {
      $ids .= $id['client_ids'].',';
    }
    $allClientids = \model\client::finds("where factory_id='".$factory_id."' and deleted=0",'id');
    $sort_area_clientids = explode(",",$ids);
    $sort_area_clientids = array_filter($sort_area_clientids);
    foreach ($allClientids as $k => $v) {
        foreach ($sort_area_clientids as $key => $value) {
            if($v['id'] == $value){
              unset($allClientids[$k]);
            }
        }
    }
    $not_area_clientids = [];
    foreach ($allClientids as $k => $id) {
      $not_area_clientids[] = $id['id'];
    }
    $not_area_clientids = implode(",", $not_area_clientids);
    $ids = $ids.$not_area_clientids;
    return $ids;
  }

  function getSortAreaByClientId() {
    
  }



}