<?php
include \view('inc_vue_header');
?>
  <style>
  .ivu-table-body {
      overflow: hidden;
  }
  </style>
  <div id="v_orders_export" >
    <div class="example ivu-row">
      <div class="example-demo ivu-col ivu-col-span-24" style="position:relative;">
        <div class="example-case">
          <h1><a name="top"></a>下载订单数据</h1>
          <i-form inline style="width: 500px; ">
            <Form-item >
              <Date-picker type="date" :value.sync="thedate" placeholder="选择日期" style="width: 120px" format="yyyy-MM-dd" @on-change="dateChange" clearable="false"></Date-picker>
            </Form-item>
            <Form-item label="批次" :label-width="50">
              <Input-number :min="1" :value.sync="batch_id" style="width: 50px;"></Input-number>
            </Form-item>
          </i-form>

          <i-table :content="self" :data="data" :columns="columns" stripe></i-table>
        </div>
      </div>
    </div>
  </div>
  <script>
    $$.vue({
      el:'#v_orders_export',

      data: function(){
        return {
          self: this,
          data: <?=$__sort_area?>,
          thedate: '<?=date('Y-m-d')?>',
          batch_id: 1,
          columns: [
            {
              title: '分区名称',
              key: 'area_name'
            },
            {
              title: '操作',
              key: 'action',
              width: 200,
              render (row, column, index) {
                return `<i-button type="primary"  @click="export(${index})" icon="archive">导出</i-button>`;
              }
            }
          ]
        }
      },

      _init: function() {
        // this.loadData();
      },


      methods: {
        export: function(idx) {
          var item = this.data[idx];
          location.href = '/orders/export?thedate='+this.thedate+'&batch_id='+this.batch_id+'&sortarea_id='+item.id+'&area_name='+item.area_name
        },
        dateChange: function() {
          if (this.thedate != '') {
            var dt = new Date(this.thedate);
            var month = (dt.getMonth()+1)
            if (parseInt(month) < 10) {
              month = "0" + month;
            }
            var day = dt.getDate()
            if (parseInt(day) < 10) {
              day = "0" + day;
            }
            this.thedate = dt.getFullYear()+'-'+month+'-'+day;
          }
        },

      }
    })
  </script>

<?php
include \view('inc_vue_footer');
