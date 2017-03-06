<?php
namespace controller;

use common\ErrorCode;

class product extends \app\controller {

  /**
   * 产品列表页面
   * @return [type] [description]
   */
  function ls() {
    $product_types = $this->di['ProductService']->getProductTypeList();

    if (count($product_types) > 0) {
      $__product_types = json_encode($product_types);
    } else {
      $__product_types = '[]';
    }
    
    $product_sort_types = $this->di['ProductService']->getProductSortTypeList();
    if (count($product_sort_types) > 0) {
      $__product_sort_types = json_encode($product_sort_types);
    } else {
      $__product_sort_types = '[]';
    }

    $__factory_ids = json_encode(\DataConfig::$FACTORY);
    include \view("product__ls");
  }

  /**
   * 请求产品列表
   * @return [type] [description]
   */
  function aj_ls() {
    $data = $_GET;
    $factory_id = $_SESSION['user']['factory_id'];
    $total = 0;
    $page_param = [
      'length' => 10,
      'page' => $data['page'],
    ];

    $sql_add = "WHERE 1=1";
    if (!empty($factory_id)) {
      $sql_add .= " and (factory_id=".$factory_id." or factory_id=0)";
    }

    if (!empty($data['search_txt'])) {
      $sql_add .= " and (name like '%".$data['search_txt']."%' or py like '%".$data['search_txt']."%')";
    }

    // 判断索取数据类型 是否为工厂品 还是 非工厂品
    if ($data['data_type'] == 'normal') {
      $sql_add.= " and name not like '工厂%'";
    } else if ($data['data_type'] == 'special') {
      $sql_add.= " and name like '工厂%'";
    }

    // 是否显示已删除记录
    if (empty($data['show_deleted'])) {
     $sql_add .= " and deleted=0"; 
    }

    $res = \model\product::finds($sql_add.' order by `order` desc', 'id,name', $total, $page_param);
    $this->data([
      'ls' => $res,
      'total' => $total
    ]);
  }

  /**
   * 产品详细信息
   * @return [type] [description]
   */
  function aj_detail() {
    $data = $_GET;
    $oProduct = \model\product::loadObj($data['id']);
    $oProduct->data['price'] = floatval($oProduct->data['price']);
    $oProduct->data['order'] = floatval($oProduct->data['order']);
    $this->data($oProduct->data);
  }

  /**
   * 保存产品详细信息
   * @return [type] [description]
   */
  function aj_save_detail() {
    $data = $_GET;
    if(empty($data['id'])) {
      $this->di['ProductService']->addProduct($data);
    }else{
      $this->di['ProductService']->updateProduct($data);
    }
    $this->data(true);
  }

  /**
   * 产品分类页面
   * @return [type] [description]
   */
  function type() {
    $factory_id = $_SESSION['user']['factory_id'];
    $sql_add = "where 1=1";
    if (!empty($factory_id)) {
      $sql_add .= " and (factory_id=".$factory_id." or factory_id=0)";
    }
    $product_types = \model\product_type::finds($sql_add);
    if (count($product_types) > 0) {
      $__product_types = json_encode($product_types);
    } else {
      $__product_types = '[]';
    }

    $product_sort_types = \model\sort::finds($sql_add);
    if (count($product_sort_types) > 0) {
      $__product_sort_types = json_encode($product_sort_types);
    } else {
      $__product_sort_types = '[]';
    }
    include \view('product__type');
  }

  /**
   * 新增产品分类
   * @return [type] [description]
   */
  function aj_add_product_type() {
    $data = $_GET;
    $factory_id = $_SESSION['user']['factory_id'];
    // 获得类型名称
    $name = $data['name'];
    if (empty($name)) {
      $this->error(-1,'类型名称不能为空');
    }
    $this->di['ProductService']->addProductType($name, $factory_id);
    
    $sql_add = "where 1=1";
    if (!empty($factory_id)) {
      $sql_add .= " and (factory_id=".$factory_id." or factory_id=0)";
    }
    $product_types = \model\product_type::finds($sql_add);
    $this->data($product_types);
  }

  /**
   * 移除产品分类
   * @return [type] [description]
   */
  function aj_remove_product_type() {
    $data = $_GET;
    $id = $data['id'];
    if (!empty($id)) {
      $factory_id = $_SESSION['user']['factory_id'];
      $sql_add = "";
      if (!empty($factory_id)) {
        $sql_add .= " and (factory_id=".$factory_id." or factory_id=0)";
      }
      \model\product_type::sqlQuery("update njzs_product set product_type = 0 where product_type=$id".$sql_add);
      
      $product_types = \model\product_type::finds(' where 1=1'.$sql_add);
      \model\product_type::deleteById($id);
      $this->data($product_types);
    }else{
      $this->error(-1,'删除失败');
    }
  }

  /**
   * 新增分拣分类
   * @return [type] [description]
   */
  function aj_add_sort_type() {
    $data = $_GET;
    $name = $data['name'];
    if (empty($name)) {
      $this->error(-1,'类型名称不能为空');
    }
    $this->di['ProductService']->addSortType($name, $factory_id);

    $factory_id = $_SESSION['user']['factory_id'];
    $sql_add = "where 1=1";
    if (!empty($factory_id)) {
      $sql_add .= " and (factory_id=".$factory_id." or factory_id=0)";
    }

    $product_sort_types = \model\sort::finds($sql_add);

    $this->data($product_sort_types);
  }

  /**
   * 移除分拣分类
   * @return [type] [description]
   */
  function aj_remove_sort_type() {
    $data = $_GET;
    $id = $data['id'];
    if (!empty($id)) {
      $factory_id = $_SESSION['user']['factory_id'];
      $sql_add = "";
      if (!empty($factory_id)) {
        $sql_add .= " and (factory_id=".$factory_id." or factory_id=0)";
      }
      \model\product_type::sqlQuery("update njzs_product set sort_type = '' where sort_type = (select name from njzs_sort where id = $id)".$sql_add);
      \model\sort::deleteById($id);
      $product_sort_types = \model\sort::finds('where 1=1'.$sql_add);

      $this->data($product_sort_types);
    }else{
      $this->error(-1,'未知错误');
    }
  }





}
