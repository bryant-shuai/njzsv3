<?php
include \view('inc_vue_header');
include \view("vue_batch_list");
include \view("vue_diff_order_list");
?>
<style>
.example-split.first{left:21%;}
.example-split.second{left:62.5%;}
</style>
<div id="v_orders_batch_list" >
  <div class="example ivu-row">
    <div class="example-demo ivu-col ivu-col-span-5">
      <div class="example-case">
        <h1><a name="top"></a>批次列表</h1>
        <v_batch_list></v_batch_list>
      </div>
    </div>
    <div class="example-split first"></div>
    <div class="example-demo ivu-col ivu-col-span-10">
      <div class="example-case" >
        <h1>订单记录</h1>
        <v_diff_order_list type_="from"></v_diff_order_list>
      </div>
    </div>
    <div class="example-split second"></div>
    <div class="example-demo ivu-col ivu-col-span-9 ivu-col-split-right">
      <div class="example-case">
        <h1>目标批次</h1>
        <v_diff_order_list type_="to"></v_diff_order_list>
      </div>
    </div>
  </div>
</div>
<script>
  $$.vue({
    el:'#v_orders_batch_list',
    EVENT: ['MOVE_ORDER', 'CHANGE_TO_DATE', 'CHANGE_TO_BATCH_ID', 'SELECT_BATCH'],
    data: function(){
      return {
        to_batch_id: 1,
        to_thedate: '',
        from_batch_id: 1,
        from_thedate: ''
      }
    },

    _init: function() {

    },


    methods: {
      hd_SELECT_BATCH: function(params) {
        this.from_batch_id = params.idx;
        this.from_thedate = params.thedate;
      },
      hd_CHANGE_TO_BATCH_ID: function(batch_id) {
        this.to_batch_id = batch_id;
      },

      hd_CHANGE_TO_DATE: function(thedate) {
        thedate = thedate.replace(/-/g,'/');
        this.to_thedate = thedate;
      },
      
      hd_MOVE_ORDER: function(params) {
        if (this.to_thedate == '') {
          this.$Notice.warning({
            title: '请选择移动到的日期',
            desc: ''
          });
          return;
        } else if (this.to_thedate == this.from_thedate && this.to_batch_id <= this.from_batch_id) {
          this.$Notice.warning({
            title: '请确认移动的批次有效性',
            desc: ''
          });
          return;
        }

        var url = '';
        if (params.type == 'from') {
          url = '/aj_batch/move_order?id='+params.order_id + "&batch_id=" + this.to_batch_id + "&thedate=" + this.to_thedate
        } else {
          url = '/aj_batch/remove_order?id='+params.order_id
        }
        
        this.moveOrder(url)
      },

      moveOrder: function(url) {
        var self = this;
        this.$Loading.start();
        $$.ajax({
          url: url,
          data: {
          },
          succ: function(data){
            $$.event.pub("MOVE_ORDER_SUCC");
            self.$Loading.finish();
          },
        })
      }
    }
  })
</script>

<?php
include \view('inc_vue_footer');
