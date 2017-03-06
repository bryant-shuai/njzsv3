<?php
include \view('inc_vue_header');
include \view("vue_client_list");
include \view("vue_product_list");
include \view("vue_order_list");
?>
<style>
.example-split.first{left:33%;}
.example-split.second{left:66%;}
</style>
<div id="v_change_order" >
  <div class="example ivu-row">
    <div class="example-demo ivu-col ivu-col-span-8">
      <div class="example-case">
        <h1><a name="top"></a>门店列表</h1>
        <v_client_list></v_client_list>
      </div>
    </div>
    <div class="example-split first"></div>
    <div class="example-demo ivu-col ivu-col-span-8">
      <div class="example-case">
        <h1>产品列表</h1>
        <v_product_list data_type_="special"></v_product_list>
      </div>
    </div>
    <div class="example-split second"></div>
    <div class="example-demo ivu-col ivu-col-span-8 ivu-col-split-right">
      <div class="example-case">
        <h1>填补发货</h1>
        <v_order_list is_direct_="1"></v_order_list>
      </div>
    </div>
  </div>
</div>
<script>
  $$.vue({
    el:'#v_change_order',

    data: function(){
      return {
        
      }
    },

    _init: function() {

    },


    methods: {
      
    }
  })
</script>

<?php
include \view('inc_vue_footer');
