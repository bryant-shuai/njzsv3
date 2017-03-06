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
          <h1><a name="top"></a>导出Excel</h1>
          <i-form inline style="width: 500px; " :label-width="60">
            <Form-item label="开始时间">
              <Date-picker type="date" :value.sync="start_time" placeholder="选择日期" style="width: 120px" format="yyyy/MM/dd" @on-change="startTimeChange" clearable="false"></Date-picker>
            </Form-item>
            <Form-item label="结束时间" >
              <Date-picker type="date" :value.sync="end_time" placeholder="选择日期" style="width: 120px" format="yyyy/MM/dd" @on-change="endTimeChange" clearable="false"></Date-picker>
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
          start_time: '<?=date('Y/m/d')?>',
          end_time: '<?=date('Y/m/d')?>',
          columns: [
            {
              title: '功能名称',
              key: 'name'
            },
            {
              title: '操作',
              key: 'action',
              width: 200,
              render (row, column, index) {
                return `<i-button type="primary"  @click="export(${index})" icon="archive">导出</i-button>`;
              }
            }
          ],
          data: [
            {
              name: '订单导出',
              url: '/excel/order_export?'
            },
            {
              name: '订单分区汇总导出',
              url: '/excel/area_statistics_export?'
            },
            {
              name: '调拨总表导出',
              url: '/excel/sortout_export?'
            },
            {
              name: '门店调拨金额导出',
              url: '/excel/daily_account_export?'
            },
            {
              name: '调拨金额汇总导出',
              url: '/excel/sum_export?'
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
          var url = item['url'] + "fromdate="+this.start_time+'&todate='+this.end_time;
          location.href = url;
        },
        startTimeChange: function() {
          if (this.start_time != '') {
            var dt = new Date(this.start_time);
            var month = (dt.getMonth()+1)
            if (parseInt(month) < 10) {
              month = "0" + month;
            }
            var day = dt.getDate()
            if (parseInt(day) < 10) {
              day = "0" + day;
            }
            this.start_time = dt.getFullYear()+'/'+month+'/'+day;
          }
        },
        endTimeChange: function() {
          if (this.end_time != '') {
            var dt = new Date(this.end_time);
            var month = (dt.getMonth()+1)
            if (parseInt(month) < 10) {
              month = "0" + month;
            }
            var day = dt.getDate()
            if (parseInt(day) < 10) {
              day = "0" + day;
            }
            this.end_time = dt.getFullYear()+'/'+month+'/'+day;
          }
        },
      }
    })
  </script>

<?php
include \view('inc_vue_footer');
