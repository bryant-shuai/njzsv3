<template id="v_diff_order_list">
  <style>
    .ivu-table .not-enough {
        background-color: #2db7f5;
        color: #fff;
    }
    .ivu-table .enough {
        background-color: #ff6600;
        color: #fff;
    }
  </style>
  <div style="position: relative; min-height: 100px;">
    <i-form inline style="width: 500px; ">
      <Form-item >
        <Date-picker type="date" :value.sync="thedate" placeholder="选择日期" style="width: 120px" format="yyyy-MM-dd" @on-change="dateChange" clearable="false" v-if="type_ == 'to'"></Date-picker>

        <Date-picker type="date" disabled :value.sync="thedate" placeholder="选择批次" style="width: 120px" format="yyyy-MM-dd" clearable="false" v-if="type_ == 'from'"></Date-picker>
      </Form-item>
      <Form-item label="批" :label-width="20">
        <Input-number :min="1" :value.sync="batch_id" style="width: 50px;" v-if="type_ == 'to'"></Input-number>
        <Input-number :min="1" disabled style="width: 50px;" :value.sync="batch_id" v-if="type_ == 'from'"></Input-number>
      </Form-item>
      <Form-item>
        <i-input type="text" :value.sync="search_txt" placeholder="门店/产品" style="width: 140px;">
          <Icon type="search" slot="prepend"></Icon>
        </i-input>
      </Form-item>
    </i-form>
    <i-table :columns.sync="columns" :data="data" border></i-table>
    <div style="margin: 10px;overflow: hidden" v-show="!loading">
      <div style="float: right;">
        <Page :total="total" :current.sync="current" @on-change="loadData"></Page>
      </div>
    </div>
  </div>
</template>


<script type="text/javascript">
$$.comp('v_diff_order_list', {
  el: '#v_diff_order_list',
  EVENT:['MOVE_ORDER_SUCC', 'SELECT_BATCH'],
  props: ['type_'],
  data: function() {
    return {
      data: [],
      thedate: '',
      batch_id: 1,
      thedate: '',
      total: 0,
      current: 1,
      search_txt: '',
      columns: [
        {
          title: '门店',
          key: 'storename'
        },
        {
          title: '产品',
          key: 'product_name'
        },
        {
          title: '报货',
          key: 'need_amount',
          width: 70
        },
        {
          title: '发货',
          key: 'send_amount',
          width: 70
        },
        {
          title: '差异',
          key: 'diff',
          width: 70,
          align: 'center',
          render (row, column, index) {
            return `<span>{{data[${index}]['diff']}}</span>
            <i-button v-if="data[${index}]['to_id'] == 0 && type_ == 'from'" size="small" @click="moveOrder(${index})">移动</i-button><i-button v-if="data[${index}]['from_id'] > 0 && type_ == 'to'" size="small" @click="moveOrder(${index})">移除</i-button>`;
          }
        }
      ]
    }
  },

  _init: function() {
  },

  methods: {
    loadData: function() {

      var self = this;
      this.$Loading.start();
      $$.ajax({
        url: '/order/diff',
        data: {
          search_txt: self.search_txt,
          thedate: self.thedate,
          idx: self.batch_id,
          page: self.current
        },
        succ: function(data){
        var arr = [];
          for (var idx in data.ls) {
            var item = data.ls[idx];
            if (item['diff'] > 0) {
              item['cellClassName'] = {
                diff: "enough"
              }
            } else if (item['diff'] < 0){
              item['cellClassName'] = {
                diff: "not-enough"
              }
            }
            
            arr.push(item);
          }
          self.data = arr;
          self.total = data.total;
          self.$Loading.finish();
        },
      })
    },

    hd_SELECT_BATCH: function(params){
      if (this.type_ == "from") {
        this.batch_id = parseInt(params.idx);
        this.thedate = params.thedate;
        this.loadData();
      }
    },

    dateChange: function() {
      var self = this;
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
      $$.event.pub('CHANGE_TO_DATE', self.thedate);
      this.loadData();
    },

    moveOrder: function(idx) {
      var self = this;
      var params = {
        type: self.type_,
        order_id: self.data[idx]['id']
      };
      $$.event.pub('MOVE_ORDER', params);
    },
    hd_MOVE_ORDER_SUCC: function() {
      this.loadData();
    }

  },

  watch: {
    'search_txt': function() {
      this.current = 1;
      this.loadData();
    },
    'batch_id': function() {
      var self = this;
      if (this.type_ == 'to') {
        $$.event.pub('CHANGE_TO_BATCH_ID', self.batch_id);
        this.current = 1;
        this.loadData();
      }

    }
  }
})
</script>