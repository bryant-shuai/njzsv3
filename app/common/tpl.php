<?php

function tpl_test(){
  echo '<hr />xxxxxxxxxxxxxxx<hr />';
}

function tpl_db(){
  $di = di::get();
  $res = $di['XXService'];
  echo '';
}



function tpl_process($prod){
    if(empty($prod['max_id'])){
      $prod['process'] = 0;
    }else{
      $prod['process'] = $prod['current_id']/$prod['max_id'];
      $prod['process']*=100;
    }
    if($prod['process']>10){
      return (int)$prod['process'];
    }else if($prod['process']>1){
      return sprintf("%.1f", $prod['process']);
    }
    return sprintf("%.2f", $prod['process']);
}


function tpl_products_cate($cate_id,$limit=4) {
	// $limit = 6;
	$di = \app\di::get();
	$cate = $di['CateService']->getCateById($cate_id);
	$res = $di['ProductService']->getProducts($limit, $cate_id);

	// if (count($res) > 0) {

    echo '<div id="cate_1" class="cate_frame" style="">';
    echo '<div class="cate_title" style="clear:both;width:100%;"><b>'.$cate['name'].'</b></div>';
    echo '<div class="cate_list" style="position:relative;display:block;width:100%;">';
      foreach ($res as $key => $product) {
        echo '<a href="/shop/detail?id='.$product['id'].'">';
              echo '<div class="cate tc" style="overflow:hidden;">';
                  echo '<div style="width:100%;">';
                      echo '<img class="weui_media_appmsg_thumb-" style="margin-top:-0px;max-width:120px;overflow:hidden;max-height:126px;" src="'.$product['pic'].'" alt="" >';

                  echo '</div>';
                  echo '<div class="product_price">';
                      echo '¥'.$product['price'].'';
                  echo '</div>';
                  echo '<div class="product_name">';
                      echo $product['name'];
                  echo '</div>';
              echo '</div>';
        echo '</a>';
    }
    echo '<div style="clear:both;"></div>';
    echo '</div>';
    echo '</div>';
	// }

}

function tpl_product_detail_img($class='', $style='') {
	for($i = 1; $i < 11; $i++) {
	  if (file_exists(__PUBLIC_DIR__."/products/".$_GET['id']."/".$i.".jpg")) {
	    $src = '<img src="/products/'.$_GET['id'].'/'.$i.'.jpg" width="100%" ';
	    if (!empty($class)) {
	    	$src .= " class='".$class."' ";
	    }

	    if (!empty($style)) {
			$src .= " style='".$style."' ";
	    }

	    $src .= "/>";

	    echo ($src);
	  }
	}
}

