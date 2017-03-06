<style>
  .navigate a,p {
    font-size: 14px;
  }
</style>

<div id="side_bar" >
  <ul id="menu" v-if="user_name != ''" style="display: none;">
    <li>
      <!-- <div>组件</div> -->
      <ul>
        <li class="menu_group">
          <p>订单</p>
          <ul>
            <?php if (!empty($_SESSION['user']) && !empty($_SESSION['user']['permissions']['/orders/change_order'])) {?>
            <li>
              <a href="/orders/change_order">
                <i class="ivu-icon ivu-icon-clipboard"></i>修改订单
              </a>
            </li>
            <?php }?>
            <?php if (!empty($_SESSION['user']) && !empty($_SESSION['user']['permissions']['/orders/change_order_direct'])) {?>
            <li>
              <a href="/orders/change_order_direct">
                <i class="ivu-icon ivu-icon-document-text"></i>补填发货
              </a>
            </li>
            <?php } ?>
            <?php if (!empty($_SESSION['user']) && !empty($_SESSION['user']['permissions']['/orders/batch_list'])) {?>
            <li>
              <a href="/orders/batch_list">
                <i class="ivu-icon ivu-icon-navicon-round"></i>批次处理
              </a>
            </li>
            <?php } ?>
          </ul>
        </li>

        <li class="menu_group">
          <p>分拣</p>
          <ul>
            <?php if (!empty($_SESSION['user']) && !empty($_SESSION['user']['permissions']['/orders/download_orders'])) {?>
            <li>
              <a href="/orders/download_orders">
                <i class="ivu-icon ivu-icon-archive"></i>下载订单数据
              </a>
            </li>
            <?php }?>
            <?php if (!empty($_SESSION['user']) && !empty($_SESSION['user']['permissions']['/orders/upload_sort_result'])) {?>
            <li>
              <a href="/orders/upload_sort_result">
                <i class="ivu-icon ivu-icon-upload"></i>上传分拣结果
              </a>
            </li>
            <?php }?>
            <?php if (!empty($_SESSION['user']) && !empty($_SESSION['user']['permissions']['/orders/export_sort_orders'])) {?>
            <li>
              <a href="/orders/export_sort_orders">
                <i class="ivu-icon ivu-icon-printer"></i>导出调拨单
              </a>
            </li>
            <?php }?>
          </ul>
        </li>

        <li class="menu_group">
          <p>产品</p>
          <ul>
            <?php if (!empty($_SESSION['user']) && !empty($_SESSION['user']['permissions']['/product/ls'])) {?>
            <li>
              <a href="/product/ls">
                <i class="ivu-icon ivu-icon-ios-list-outline"></i>产品列表
              </a>
            </li>
            <?php }?>
            <?php if (!empty($_SESSION['user']) && !empty($_SESSION['user']['permissions']['/product/type'])) {?>
            <li>
              <a href="/product/type">
                <i class="ivu-icon ivu-icon-ios-pricetags-outline"></i>产品类型
              </a>
            </li>
            <?php }?>
            <?php if (!empty($_SESSION['user']) && !empty($_SESSION['user']['permissions']['/pricetype/ls'])) {?>
            <li>
              <a href="/pricetype/ls">
                <i class="ivu-icon ivu-icon-pricetags"></i>价格类型
              </a>
            </li>
            <?php }?>
          </ul>
        </li>

        <li class="menu_group">
          <p>门店</p>
          <ul>
            <?php if (!empty($_SESSION['user']) && !empty($_SESSION['user']['permissions']['/client/ls'])) {?>
            <li>
              <a href="/client/ls">
                <i class="ivu-icon ivu-icon-ios-home-outline"></i>门店列表
              </a>
            </li>
            <?php }?>
            <?php if (!empty($_SESSION['user']) && !empty($_SESSION['user']['permissions']['/client/sort_area'])) {?>
            <li>
              <a href="/client/sort_area">
                <i class="ivu-icon ivu-icon-link"></i>门店分区
              </a>
            </li>
            <?php }?>
            <?php if (!empty($_SESSION['user']) && !empty($_SESSION['user']['permissions']['/client/deposit'])) {?>
            <li>
              <a href="/client/deposit">
                <i class="ivu-icon ivu-icon-cash"></i>门店余额
              </a>
            </li>
            <?php }?>

          </ul>
        </li>

        <li class="menu_group">
          <p>历史数据</p>
          <ul>
            <?php if (!empty($_SESSION['user']) && !empty($_SESSION['user']['permissions']['/excel/export'])) {?>
            <li>
              <a href="/excel/export">
                <i class="ivu-icon ivu-icon-ios-printer-outline"></i>导出Excel
              </a>
            </li>
            <?php }?>
            <?php if (!empty($_SESSION['user']) && !empty($_SESSION['user']['permissions']['/sms/ls'])) {?>
             <li>
              <a href="/sms/ls">
                <i class="ivu-icon ivu-icon-email"></i>短信记录
              </a>
            </li>
            <?php }?>
          </ul>
        </li>

        <li class="menu_group">
          <p>账号</p>
          <ul>
            <?php if (!empty($_SESSION['user']) && !empty($_SESSION['user']['permissions']['/admin/users'])) {?>
            <li>
              <a href="/admin/users">
                <i class="ivu-icon ivu-icon-ios-person-outline"></i>账号列表
              </a>
            </li>
            <?php }?>
            <?php if (!empty($_SESSION['user']) && !empty($_SESSION['user']['permissions']['/admin/role'])) {?>
            <li>
              <a href="/admin/role">
                <i class="ivu-icon ivu-icon-eye"></i>权限配置
              </a>
            </li>
            <?php }?>
            <?php if (!empty($_SESSION['user']) && !empty($_SESSION['user']['permissions']['/admin/manager_list'])) {?>
            <li>
              <a href="/admin/manager_list">
                <i class="ivu-icon ivu-icon-social-freebsd-devil"></i>代理人列表
              </a>
            </li>
            <?php }?>
            <li>
              <a href="/admin/change_pwd">
                <i class="ivu-icon ivu-icon-gear-b"></i>修改密码
              </a>
            </li>
          </ul>
        </li>
      </ul>
    </li>
  </ul>
</div>

<script type="text/javascript">
  $$.vue({
    el:'#side_bar',

    data:function(){
      return {
        user_name: "<?=!empty($_SESSION['user']) ? $_SESSION['user']['name'] : ''?>",
        permissions: "<?=!empty($_SESSION['user']) ? $_SESSION['user']['permissions'] : ''?>",

      }
    },

    _init:function(){
      $('#menu').show();
      var self = this
      var items = $('#side_bar a');
      $.each($('#side_bar a'), function(idx, item) {
        if ($(item).attr('href') == window.location.pathname) {
          $(item).addClass('v-link-active');
        } else {
          $(item).removeClass('v-link-active');
        }

        var href = $(item).attr('href');
        $(item).attr('href', href + '#top');
      })

      $.each($('.menu_group'), function(idx, item) {
        if ($(item).find('li').length == 0) {
          $(item).hide();
        }
      })
    },

    methods:{
    },
  })
</script>