<?php
namespace controller;

use common\ErrorCode;

class sort_area extends \app\controller
{

  function index() {
    $areas = \model\sort_area::finds('', 'id');
    $__areas_count = count($areas);
    include \view('sortarea__index');
  }
  // 前端传来要改变分区的店铺ID，店铺所在分区，店铺将要分到哪个分区
  function client_add_sort_area(){
  	$data = $_GET;
  	$client_id = $data['client_id'];   //将要操作的店铺ID
  	$sortarea_id = $data['id'];        //将要移动到的分区ID
    if(!$data['from_sortarea_id']){    //被操作店铺之前所在分区ID，如果为0，则该店铺之前属于未分区
      $from_sortarea_id = 0 ;          
    }
    $from_sortarea_id = $data['from_sortarea_id'];   //被操作店铺之前所在分区ID
  	$oSortarea = $this->di['SortAreaService']->client_add_sortarea($sortarea_id,$client_id,$from_sortarea_id);

  	$this->data(['ok'=>1]);
  }




  function get_client_by_sortarea(){
  	$data = $_GET;
  	$id = $data['id'];
  	$factory_id = $_SESSION['user']['factory_id'];
  	// $factory_id = $data['factory_id'];
  	$clients = $this->di['SortAreaService']->get_client_by_sortarea($id,$factory_id);
		\vd($clients,'_____');
		$this->data(['ls'=>$clients]);

  }

  // 得到分区
  function partition(){
  	$partitions = $this->di['SortAreaService']->getpartition();
  	$this->data(['ls'=>$partitions]);
  }


  // 显示未分区的店面
  function unclassified() {
    // 查询client表找出所有店面
    if(isset($_SESSION['user'])){
      $factory_id = $_SESSION['user']['factory_id'];
    }else{
      \except(-1,'请先登陆!');
    }
    $clients = \model\client::finds("where factory_id = '".$factory_id."' and deleted = 0",'id,storename,factory_id,username');
    $clients = \indexBy($clients,'id');
    // 查询已经分组的店面，筛选出未分组的店面
    $areas = \model\sort_area::finds("where id >0");
    $clientIds = [];
    foreach ($areas as $area) {
      $clientIds[] = explode(',', $area['client_ids']);
    }
    // \vd($client_ids,'3333333');
    foreach ($clientIds as $clientId) {
      foreach ($clientId as $key => $val) {
        unset($clients[$val]);
      }
    }
    \vd($clients,'#####');
    $this->data(['ls' => $clients]);
  }

  // 创建分区
  function save_sort_name(){
    // $data = $_GET;
    $res = \model\sort_area::loadObj($data['sort_name']);
    if (!empty($res)) {
      $this->error(-1,"该分区已经存在");
    }

    $sort_area = new \model\sort_area();
    $sort_area->data = [
      'area_name' => $_GET['sort_name'],
      'factory_id' => $_SESSION['user']['factory_id'],
    ];
    $sort_area->save(); 
    $this->data(true);
  }
 
  // 删除分区
  function remove_sort_area() {
    $data = $_GET;
    $oSortArea = \model\sort_area::deleteById($data['id']);
    $this->data(true);
  }


  // 修改client_ids中的值
  function modify_client_ids() {
    $data = $_GET;
    $client_ids = $data['order'];
    \vd($client_ids,'22222');
    $id = $data['sort_id'];
    \vd($id,'rrrrrrr');
    $oSortArea = \model\sort_area::loadObj($id);
    $client_ids = implode(',', $client_ids);
    $oSortArea->data['client_ids'] = $client_ids;
    $oSortArea->save();
    $this->data(true);
  }

}
