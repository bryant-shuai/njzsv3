<!DOCTYPE html>
<html class="root" lang="zh">
<head>
    <title>那记猪手-<?=$__title?></title>
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
    <link rel="stylesheet" href="/assets/libs/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="/assets/css/pure-all.css" />
    <link rel="stylesheet" href="/assets/mini-global.css" />
    


 <!--    <link rel="stylesheet" type="text/css" href="http://unpkg.com/iview/dist/styles/iview.css"> -->

    <!-- <link rel="stylesheet" href="https://unpkg.com/mint-ui@1/lib/style.css"> -->

    <style type="text/css">
    a {
      cursor: pointer;
    }
    </style>

</head>
<script type="text/javascript" src="/assets/libs/zepto.js"></script>
<script type="text/javascript" src="/assets/libs/zepto_fastclick.js"></script>
<script type="text/javascript" src="/assets/libs/vue.min.js"></script>
<script type="text/javascript" src="/assets/libs/then.js"></script>
<script type="text/javascript" src="/assets/libs/aaa_init.js"></script>
<script type="text/javascript" src="/assets/libs/naji.js"></script>



<!-- 
<link rel="stylesheet" type="text/css" href="http://unpkg.com/iview/dist/styles/iview.css">
<script type="text/javascript" src="http://v1.vuejs.org/js/vue.min.js"></script>
<script type="text/javascript" src="http://unpkg.com/iview/dist/iview.min.js"></script> -->

<body>
<?php
// include \view('vue_login_regist');
// include \view('vue_user_profile');
?>
<script>

// console.log('$_SESSION'+ '<?=\en($_SESSION)?>)' )

var switchMenu = function(){
    $('#top-nav-sm').toggle();
    if($('#top-nav-sm').css('display') == 'block'){
        $('#mask-sm').show().css('opacity',0.5)
    }else{
        $('#mask-sm').hide()
    }
}
    
  $(document).ready(function(){
      
    $('.menuswitchbutton').on('fastclick', function(evt) {
        switchMenu()
    });
      
    $('#top-nav-sm').html($('#top-nav').html())
    $('#top-nav-sm .main_nav')
        .click(function(){
            if($(this).hasClass('select_off')){
                $(this).removeClass('select_off')
                $(this).addClass('select_on')
            }else{
                $(this).removeClass('select_on')
                $(this).addClass('select_off')
            }
        });
 
    $('#top-nav .main_nav')
        .mouseover(function(){
            $(this).removeClass('select_off')
            $(this).addClass('select_on')
        })
        .mouseout(function(){
            $(this).removeClass('select_on')
            $(this).addClass('select_off')
        })
  })
  
</script>
  
    
    
    <div class="full-container-bar" style="display:-none;">
        <header id="header" class="header " style="margin:0 auto 0 auto;position:relative;">
            
            <div style="float:left;">
                <img id="logo" src="/assets/images/logo.png" />
            </div>
            

            <div id="top-nav" style="">
                <div class="nav_main">
                    <ul class="nav_item">
                      <?php if( $_SESSION['user']['type'] != \model\admin::$TYPE['MANAGER'] && $_SESSION['user']['type'] != \model\admin::$TYPE['FINANCE']){?>
                        <a href="/">

                            <li class="on">

                            <li <?php if($__nav=='home'){ echo ' class="on"'; }?> style="">
                                首页
                            </li>
                        </a>

                        
                        <li id="id_cates_trigger" class="have-submenu" style="cursor:pointer;">
                            业务
                            <span class="caret" style="left:52px;"></span>

                            <div id="id_cates_outter" class="float-out-pannel" style="width:420px;left:-0px;top:50px; ">
                              <div class="float-out-pannel-arrow" style="left:25px;"></div>
                              <div id="xid_cates" loaded="0" style="">



                                     <div style="padding:0 20px 0 20px; ">
                                        <div class="nav-autoheight">

                                          <h2 class="nav-h2"><a href="/cate?id=1">订单</a></h2>


                                          <div style="padding:0;margin:0;">
                                          <!-- 
                                                      <div class="nav-sub3"><a href="/orders/index">所有订单状态</a></div>
 -->
                                                      <div class="nav-sub3"><a href="/orders/product_need_page">修改订单</a></div>

                                                      <div class="nav-sub3"><a href="/orders/product_need_page_direct">补填发货</a></div>

                                                      <div class="nav-sub3"><a href="/orders/batch">增加处理批次</a></div>

                                                      <div class="nav-sub3"><a href="/orders/export_excel">导出Excel</a></div>

                                                      <div class="nav-sub3"><a href="/orders/export_excel_by_batch">按批次打印调拨单</a></div>


                                                      <div class="nav-sub3"><a href="/orders/download_orders">导出订单数据到U盘</a></div>

                                                      <div class="nav-sub3"><a href="/order/upload_sort_data">上传数据到服务器</a></div>


                                                 

                                                      <div style="width:100%;clear:both;"></div>
                                          </div>

<!-- 
                                          <h2 class="nav-h2"><a href="/cate?id=1">材料</a></h2>


                                          <div style="padding:0;margin:0;">
                                                      <div class="nav-sub3"><a href="/cate?id=9">汇总</a></div>
                                                      <div class="nav-sub3"><a href="/cate?id=9">变化</a></div>
                                                      <div style="width:100%;clear:both;"></div>
                                          </div>




                                          <h2 class="nav-h2"><a href="/cate?id=2">库存</a></h2>

                                          <div style="padding:0;margin:0;">
                                      
                                                      <div class="nav-sub3"><a href="/cate?id=9">现状</a></div>
                                                      <div class="nav-sub3"><a href="/cate?id=9">历史变化</a></div>
                                                      <div style="width:100%;clear:both;"></div>
                                          </div>
 -->




                                        </div>

                                    </div>




                              </div>
                            </div>

                        </li>






                        <li id="id_prod_setting_trigger" class="have-submenu <?php if($__nav=='setting'){ echo ' on'; }?>" style="cursor:pointer;">
                            数据设置
                            <span class="caret" style="left:85px;"></span>

                            <div id="id_prod_setting_outter" class="float-out-pannel" style="width:400px;left:-0px;top:50px; ">
                              <div class="float-out-pannel-arrow" style="left:40px;"></div>
                              <div id="id_cates" loaded="0" style="">




                                     <div style="padding:0 20px 0 20px; ">
                                        <div class="nav-autoheight">





                                          <h2 class="nav-h2"><a href="/cate?id=1">商品</a></h2>


                                          <div style="padding:0;margin:0;">
                                                <div class="nav-sub3"><a href="/setting/products">商品信息修改</a></div>
                                                
                                                <div class="nav-sub3"><a href="/setting/product_type">产品类型配置</a></div>

                                                <div style="width:100%;clear:both;"></div>
                                          </div>


                                          <h2 class="nav-h2"><a href="/cate?id=1">门店</a></h2>


                                          <div style="padding:0;margin:0;">
                                                    <div class="nav-sub3"><a href="/setting/clients">门店信息修改</a></div>

                                                    <div class="nav-sub3"><a href="/sort_area/index">门店分区修改</a></div>

                                                    <div style="width:100%;clear:both;"></div>
                                          </div>


                                          <h2 class="nav-h2"><a href="/cate?id=1">价格</a></h2>


                                          <div style="padding:0;margin:0;">
                                              <div class="nav-sub3"><a href="/setting/pricetype_list">价格类型配置</a></div>

                                                      <div class="nav-sub3"><a href="/setting/pricetype_client">修改店铺价格</a></div>

                                                      <div style="width:100%;clear:both;"></div>
                                          </div>

                                        <h2 class="nav-h2"><a href="">代理人账号</a></h2>


                                          <div style="padding:0;margin:0;">
                                                <div class="nav-sub3"><a href="/manager/index">添加代理人账号</a></div>
                                                
                                                <div style="width:100%;clear:both;"></div>
                                          </div>


                                        </div>

                                    </div>







                              </div>
                            </div>

                        </li>
                    <?php } ?>


                    <?php if($_SESSION['user']['type'] == \model\admin::$TYPE['FINANCE']) { ?>
                     <!--  <a href="/finance/index">
                        <li id="id_cates_trigger" style="cursor:pointer;">
                            财务管理
                            <span class="caret" style="left:52px;"></span>

                            <div id="id_cates_outter" class="float-out-pannel" style="width:600px;left:-20px; ">
                              <div class="float-out-pannel-arrow" style="left:70px;"></div>
                              <div id="id_cates" loaded="0" style="">

                                    <div class="spinner">
                                      <div class="rect1"></div>
                                      <div class="rect2"></div>
                                      <div class="rect3"></div>
                                      <div class="rect4"></div>
                                      <div class="rect5"></div>
                                    </div>

                              </div>
                            </div>

                        </li>
                      </a> -->



                      <li id="id_cates_trigger" class="have-submenu" style="cursor:pointer;">
                            财务管理
                            <span class="caret" style="left:85px;"></span>

                            <div id="id_cates_outter" class="float-out-pannel" style="width:420px;left:-0px;top:50px; ">
                              <div class="float-out-pannel-arrow" style="left:25px;"></div>
                              <div id="xid_cates" loaded="0" style="">



                                     <div style="padding:0 20px 0 20px; ">
                                        <div class="nav-autoheight">

                                          <div style="padding:0;margin:0;">
                                          
                                                      <div class="nav-sub3"><a href="/manager/manager_detail">查询代理人</a></div>

                                                      <div class="nav-sub3"><a href="/finance/index">手动修改店铺余额</a></div>
                                                      
                                                      <div class="nav-sub3"><a href="/orders/export_excel">导出Excel</a></div>

                                                      <div class="nav-sub3"><a href="/orders/export_excel_by_batch">按批次打印调拨单</a></div>

                                                       <div class="nav-sub3"><a href="/log_account/manager_history">导出代理人分配记录Excel</a></div>



                                        </div>

                                    </div>




                              </div>
                            </div>

                        </li>
                    <?php } ?>

                    <?php if( $_SESSION['user']['type'] != \model\admin::$TYPE['MANAGER'] && $_SESSION['user']['type'] != \model\admin::$TYPE['FINANCE']){?>
                        <li id="id_cates_trigger" class="-have-submenu" style="cursor:pointer;">
                            管理
                            <span class="caret" style="left:52px;"></span>

                            <div id="id_cates_outter" class="float-out-pannel" style="width:600px;left:-20px; ">
                              <div class="float-out-pannel-arrow" style="left:70px;"></div>


                              <!-- <div id="id_cates" loaded="0" style="">

                                    <div class="spinner">
                                      <div class="rect1"></div>
                                      <div class="rect2"></div>
                                      <div class="rect3"></div>
                                      <div class="rect4"></div>
                                      <div class="rect5"></div>
                                    </div>



                              </div> -->


                            </div>

                        </li>


                      <?php } ?>

                        


                        <?php if($_SESSION['user']){ ?>
                          <li id="user_info" class="">
                            用户名:<?=$_SESSION['user']['name']?>
                          </li>
                        <?php }?>


                        <?php if($_SESSION['user']['type'] == \model\admin::$TYPE['MANAGER']){ ?>
                          <a href="/log_account/manager_history_view" onclick="void(0);">
                              <li id="user_logout" class="">
                                  分配记录
                              </li>
                          </a>
                        <?php }?>



                        <?php if($_SESSION['user']['type'] == \model\admin::$TYPE['MANAGER']){ ?>
                          <a href="/manager/update_manager">
                            <li id="user_info" class="">
                              修改密码
                            </li>
                          </a>
                        <?php }?>

                        

                        <?php if($_SESSION['user']){ ?>
                          <a href="/admin/logout" onclick="void(0);">
                              <li id="user_logout" class="">
                                  退出登陆
                              </li>
                          </a>
                        <?php }?>





                          

                        

                    </ul>
                </div>
                
            </div>


            
            
            <div id="main_nav_more" style="position:absolute;top:0;right:0;">                
                <div id="main_nav_icon" class="menuswitchbutton" style="padding:0px 10px 0 10px;height:100%;"><i class="fa fa-bars" aria-hidden="true"></i></div>
            
            </div>


            
            
            <div id="top-nav-sm" style=";padding:5px 0 20px 0;">
            </div>
            
            
        </header>
    </div>
    
    <div class="clear_header"></div>
    
    <div id="mask-sm" style="position:fixed;width:100%;height:2000px;background:#000000;z-index:1;opacity:0.5;" onclick="javascript:switchMenu();"></div>
    
    


<script type="text/javascript">

  $(document).ready(function(){

    $('.have-submenu')
      .mouseover(function(){
        $($(this).find('.float-out-pannel')[0]).show()
      })
      .mouseout(function(){
        $($(this).find('.float-out-pannel')[0]).hide()
      })


    // $('#id_user_profile_outter').show()
    // $$.loadToDiv('id_user_profile','/user/miniprofile')


    // $$.event.pub('OPEN_DRAWER',{center:1,url:'/user/v_login',width:600})

    // $('#id_cates_outter').show()
    // $$.loadToDiv('id_cates','/cate/v_nav')
  })

</script>


<?php
require_once \view('vue_base');
?>

<div style="padding-top:60px;overflow-y:hidden;top:0;bottom:0;left:0;right:0;background:-gray;display:block;position:absolute;">
  <div style="background:-green;height:100%;">