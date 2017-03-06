<?php
include \view('inc_vue_header');
include \view("vue_sort_area_list");
include \view("vue_unclassified_client_list");
include \view("vue_area_client_list");
?>
<style>
.example-split.first{left:25.5%;}
.example-split.second{left:59%;}
</style>
<div id="v_client_sortarea" >
  <div class="example ivu-row">
    <div class="example-demo ivu-col ivu-col-span-6">
      <div class="example-case">
        <h1><a name="top"></a>分区列表</h1>
        <div style="position:absolute;right:22px;top:36px;;">
          <i-button type="primary" icon="printer" @click="export">
            导出所有分区
          </i-button>
        </div>
        <v_sort_area_list></<v_sort_area_list>
      </div>
    </div>
    <div class="example-split first"></div>
    <div class="example-demo ivu-col ivu-col-span-8">
      <div class="example-case" >
        <h1>未分区门店</h1>
        <v_unclassified_client_list type_="from"></v_unclassified_client_list>
      </div>
    </div>
    <div class="example-split second"></div>
    <div class="example-demo ivu-col ivu-col-span-10">
      <div class="example-case" >
        <h1>{{area_name}}</h1>
        <v_area_client_list type_="from"></v_area_client_list>
      </div>
    </div>
  </div>
</div>
<script>
  $$.vue({
    el:'#v_client_sortarea',
    EVENT: ['MOVE_CLIENT', 'CHANGE_TO_DATE', 'CHANGE_TO_BATCH_ID', 'SELECT_AREA'],
    data: function(){
      return {
        area_id: 0,
        area_name: '选择左侧分区'
      }
    },

    _init: function() {

    },


    methods: {
      hd_SELECT_AREA: function(params) {
        this.area_id = params.area_id;
        this.area_name = params.area_name;
      },
      hd_CHANGE_TO_BATCH_ID: function(batch_id) {
        this.to_batch_id = batch_id;
      },

      hd_CHANGE_TO_DATE: function(thedate) {
        thedate = thedate.replace(/-/g,'/');
        this.to_thedate = thedate;
      },
      
      hd_MOVE_CLIENT: function(params) {
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
      },

      export: function() {
        location.href = "/excel/sort_area_export";
      }
    }
  })
</script>

<?php
include \view('inc_vue_footer');
