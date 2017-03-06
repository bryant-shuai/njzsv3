<template id="v_order_list">
  <div style="position: relative; min-height: 100px;">
    <i-form inline style="width: 300px; ">
      <Form-item>
        <Date-picker type="date" :value.sync="thedate" placeholder="选择日期" style="width: 120px" format="yyyy-MM-dd" @on-change="dateChange" clearable="false"></Date-picker>
      </Form-item>
      <Form-item label="批" :label-width="20">
        <Input-number :min="1" :value.sync="batch_id" style="width: 50px;"></Input-number>
      </Form-item>
      <Form-item>
        <i-button type="success" icon="plus-round" @click="openCreateNewOrder" v-if="client_id != 0 && product_id != 0 && data.length == 0">添加订单</i-button>
      </Form-item>
    </i-form>
    
    <i-table :columns.sync="columns" :data="data" highlight-row @on-row-click="selRowChange" ></i-table>
  </div>
</template>


<script type="text/javascript">
$$.comp('v_order_list', {
  el: '#v_order_list',
  EVENT:['SAVE_ORDER_SUCC', 'SELECT_CLIENT', 'SELECT_PRODUCT'],
  props: ['is_direct_'],
  data: function() {
    return {
      data: [],
      thedate: '<?=date("Y-m-d")?>',
      batch_id: 1,
      client_id: 0,
      product_id: 0,
      columns: [
        {
          title: '产品',
          key: 'product_name'
        },
        {
          title: '数量',
          key: 'need_amount'
        }
      ],
      columns2: [
        {
          title: '产品',
          key: 'product_name'
        },
        {
          title: '数量',
          key: 'need_amount'
        }
      ],
      columns1: [
        {
          title: '门店',
          key: 'storename'
        },
        {
          title: '数量',
          key: 'need_amount'
        }
      ]
    }
  },

  _init: function() {
    // this.loadData()
  },

  methods: {
    loadData: function() {
      if (this.client_id == 0 && this.product_id == 0) {
        this.data = [];
        return;
      } else if (this.client_id != 0) {
        this.columns = this.columns2;
      } else {
        this.columns = this.columns1;
      }

      var self = this;
      this.$Loading.start();
      $$.ajax({
        url: '/orders/day_product_need',
        data: {
          product_id: self.product_id,
          client_id: self.client_id,
          batch_id: self.batch_id,
          thedate: self.thedate
        },
        succ: function(data){
          self.data = data.ls;
          self.$Loading.finish();
        },
      })   
    },

    hd_SAVE_ORDER_SUCC: function(){
      this.loadData()
    },

    hd_SELECT_CLIENT: function(id){
      this.client_id = id;
      this.loadData();
    },

    hd_SELECT_PRODUCT: function(id){
      this.product_id = id;
      this.loadData();
    },

    selRowChange: function(current, old) {
      var self = this;
      $('#Id_Right_Drawer_Content').html('<Spin fix><div class="loader"><svg class="circular" viewBox="25 25 50 50"><circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="5" stroke-miterlimit="10" v-pre></svg></div></Spin>');
      var is_direct = 0;
      if (self.is_direct_) {
        is_direct = 1;
      }

      $$.event.pub('OPEN_DRAWER',{width:400})
      $.get('/order/order_detail?id=' + current['id'] + "&thedate=" + self.thedate + "&batch_id=" + self.batch_id + "&is_direct=" + is_direct,function(res){
        $('#Id_Right_Drawer_Content').html(res)
      })
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
      this.loadData();
    },

    openCreateNewOrder: function() {
      var self = this;
      if (this.client_id != 0 && this.product_id != 0 && this.data.length == 0) {
        $('#Id_Right_Drawer_Content').html('<Spin fix><div class="loader"><svg class="circular" viewBox="25 25 50 50"><circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="5" stroke-miterlimit="10" v-pre></svg></div></Spin>');

        var is_direct = 0;
        if (self.is_direct_) {
          is_direct = 1;
        }

        $$.event.pub('OPEN_DRAWER',{width:400})
        $.get('/order/order_detail?thedate=' + self.thedate + "&batch_id=" + self.batch_id + "&client_id=" + self.client_id + "&product_id=" + self.product_id + "&is_direct=" + is_direct,function(res){
          $('#Id_Right_Drawer_Content').html(res)
        })
      }
    }
  },

  watch: {
    'batch_id': function() {
      this.loadData();
    }
  }
})
</script>