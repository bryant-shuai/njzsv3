<?php
namespace controller;

use common\ErrorCode;

class aj_batch extends \app\controller
{

  /**
   * 显示批次列表
   * @return [type] [description]
   */
  function ls() {
    $data = $_GET;
    if (!empty($data['thedate'])) {
      $data['thedate'] = str_replace('-','/', $data['thedate'] );
    }
    $total = 0;
    $ls = $this->di['OrderService']->getOrderBatches($data['thedate'], $data['page'], $total);
    \vd($ls,'$ls');
    $this->data(['ls'=>$ls, 'total'=> $total]);
  }

  /**
   * 添加批次
   * @return [type] [description]
   */
  function aj_create() {
    $data = $_GET;
    $data['thedate'] = str_replace('-','/', $data['thedate'] );
    $thedate = $data['thedate'];
    $thedate = date("Y/m/d",strtotime($thedate));
    $idx = $data['idx'];

    $factory_id = $data['factory_id'];
    
    $oOrderBatch = $this->di['OrderService']->createBatch($thedate, $idx,$factory_id);
    $this->data(['batch'=>$oOrderBatch->data]);
  }

  /**
   * 移动订单
   * @return [type] [description]
   */
  function move_order() {
    $id = (int) $_GET['id'];
    $thedate = $_GET['thedate'];
    $batch_id = (int) $_GET['batch_id'];
    $res = \model\order_batch::find(" WHERE thedate='".$thedate."' AND idx='".$batch_id."' ");
    if (empty($res)) {
      $this->error(-1,'请先添加批次');
    }

    if (empty($thedate) ||empty($batch_id) ) {
      $this->error(-1,'请指定日期和批次!');
    }
    $this->di['OrderService']->moveOrder($id,$thedate,$batch_id);
    $this->data(['ok'=>1]);
  }


  function remove_order(){
    $data = $_GET;
    $id = (int)$data['id'];
    $this->di['OrderService']->removeOrder($id);
    $this->data(['ok'=>1]);
  }





























}
