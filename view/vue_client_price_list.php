<template id="v_client_price_list">
<style>
  .ivu-table .up {
      background-color: #2db7f5;
      color: #fff;
  }
  .ivu-table .down {
      background-color: #ff6600;
      color: #fff;
  }
</style>
  <div style="position: relative; min-height: 100px;">
    <i-form inline style="width: 300px;">
      <Form-item>
        <i-input type="text" :value.sync="search_txt" placeholder="门店名称/门店拼音">
          <Icon type="search" slot="prepend"></Icon>
        </i-input>
      </Form-item>
      <Form-item>
        <i-button type="primary" icon="close-round" @click="clean">清除</i-button>
      </Form-item>
    </i-form>
    <i-table :columns="columns" :data="data" ></i-table>
    <div style="margin: 10px;overflow: hidden">
      <div style="float: right;">
        <Page :total="total" :current.sync="current" @on-change="loadData"></Page>
      </div>
    </div>
  </div>
</template>

<script type="text/javascript">
$$.comp('v_client_price_list', {
  el: '#v_client_price_list',
  EVENT:['SELECT_PRICE_TYPE', 'SAVE_PRICE_SUCC', 'SELECT_PRICE_CONFIG'],
  props: ['data_type_'],
  data: function() {
    return {
      search_txt: '',
      data: [],
      current: 1,
      total: 0,
      config_price: -1,
      product_id: 0,
      product_name: '',
      price_type: 0,
      price_type_name: '',

      columns: [
        {
          title: '门店',
          key: 'storename'
        },
        {
          title: '价格',
          key: 'price'
        },
        {
          title: '操作',
          key: 'action',
          render (row, column, index) {
            return `<i-button type="error" style="margin-left: 3px;" @click="edit(${index})" icon="edit">变更</i-button>`;
          }
        },
      ]
    }
  },

  _init: function() {
    // this.loadData()
  },

  methods: {
    hd_SELECT_PRICE_CONFIG: function(params) {
      this.product_id = params.product_id;
      this.product_name = params.product_name;
      this.config_price = params.config_price;
      this.loadData();
    },
    hd_SELECT_PRICE_TYPE: function(params) {
      this.price_type = params.id;
      this.price_type_name = params.name;
      // this.loadData();
    },
    loadData: function() {
      if (this.price_type == 0) {
        this.data = [];
        return;
      }
      var self = this;
      this.$Loading.start();
      $$.ajax({
        url: '/pricetype/aj_config_client_list',
        data: {
          config_id: self.price_type,
          product_id: self.product_id,
          search_txt: self.search_txt,
          page: self.current
        },
        succ: function(data){
          var arr = [];
          for (var idx in data.ls) {
            var item = data.ls[idx];
            if (parseFloat(item['price']) > parseFloat(self.config_price)) {
              item['cellClassName'] = {
                price: "up"
              }
            } else if (parseFloat(item['price']) < parseFloat(self.config_price)){
              item['cellClassName'] = {
                price: "down"
              }
            }
            
            arr.push(item);
          }
          console.log(arr);
          self.data = arr;
          self.total = data.total;
          self.$Loading.finish();
        },
      })   
    },
    hd_SAVE_PRICE_SUCC: function(){
      this.loadData()
    },
    clean: function() {
      this.search_txt = "";
    },
    edit: function(idx) {
      var self = this;
      var item = self.data[idx];
      $('#Id_Right_Drawer_Content').html('<Spin fix><div class="loader"><svg class="circular" viewBox="25 25 50 50"><circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="5" stroke-miterlimit="10" v-pre></svg></div></Spin>');

      $$.event.pub('OPEN_DRAWER', {width:400})
      $.get('/pricetype/change_price?id=' + item['id'] + "&price_type_name=" + self.price_type_name + "&product_name=" + self.product_name + "&price=" + item['price'] + "&storename=" + item['storename'] ,function(res){
        $('#Id_Right_Drawer_Content').html(res)
      })
    }
  },

  watch: {
    'search_txt': function() {
      this.current = 1;
      this.loadData();
    }
  }
})
</script>