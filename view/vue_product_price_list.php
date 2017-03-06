<template id="v_product_price_list">
  <div style="position: relative; min-height: 100px;">
    <i-form inline style="width: 300px;" v-show="!loading">
      <Form-item>
        <i-input type="text" :value.sync="search_txt" placeholder="产品名称/产品拼音">
          <Icon type="search" slot="prepend"></Icon>
        </i-input>
      </Form-item>
      <Form-item>
        <i-button type="primary" icon="close-round" @click="clean">清除</i-button>
      </Form-item>
    </i-form>
    <i-table :columns="columns" :data="data" v-show="!loading"></i-table>
    <div style="margin: 10px;overflow: hidden" v-show="!loading">
      <div style="float: right;">
        <Page :total="total" :current.sync="current" @on-change="loadData"></Page>
      </div>
    </div>
    <Spin size="large" fix v-if="loading"></Spin>
  </div>
</template>

<script type="text/javascript">
$$.comp('v_product_price_list', {
  el: '#v_product_price_list',
  EVENT:['SELECT_PRICE_TYPE', 'SAVE_PRICE_SUCC', 'CLEAR_CONFIG_SELECTION'],
  props: ['data_type_'],
  data: function() {
    return {
      search_txt: '',
      data: [],
      current: 1,
      total: 0,
      loading: true,
      price_type: 0,
      price_type_name: '',
      sel_idx: -1,

      columns: [
        {
          title: '产品',
          key: 'product_name'
        },
        {
          title: '价格',
          key: 'price'
        },
        {
          title: '操作',
          key: 'action',
          width: 195,
          render (row, column, index) {
            return `<i-button type="primary" :id="'btnChoose'+${index}"  @click="choose(${index})" icon="paper-airplane">选择</i-button> <i-button type="error" style="margin-left: 3px;" @click="edit(${index})" icon="edit">变更</i-button>`;
          }
        },
      ]
    }
  },

  _init: function() {
    this.loadData()
  },

  methods: {
    hd_SELECT_PRICE_TYPE: function(params) {
      this.price_type = params.id;
      this.price_type_name = params.name;
      this.loadData();
    },
    loadData: function() {
      if (this.price_type == 0) {
        this.data = [];
        this.loading = false;
        return;
      }
      var self = this;
      $$.ajax({
        url: '/pricetype/aj_price_config',
        data: {
          id: self.price_type,
          search_txt: self.search_txt,
          page: self.current
        },
        succ: function(data){
          self.loading = false;
          self.data = data.ls;
          self.total = data.total; 
          setTimeout(function() {
            $('#btnChoose'+self.sel_idx).attr('disabled');
          }, 1000)
        },
      })   
    },
    hd_SAVE_PRICE_SUCC: function(){
      this.loadData()
    },
    hd_CLEAR_CONFIG_SELECTION: function() {
      var self = this;
      this.search_txt = "";
      // 清空选中项
      $('#btnChoose'+self.sel_idx).removeAttr('disabled');
      this.sel_idx = 0;
      $$.event.pub('SELECT_PRICE_CONFIG', {
        product_id: 0,
        product_name: '',
        config_price: -1
      });
    },
    choose: function(idx) {
      var self = this;
      var item = this.data[idx];
      // 禁用按钮
      $('#btnChoose'+self.sel_idx).removeAttr('disabled');
      this.sel_idx = idx;
      $('#btnChoose'+self.sel_idx).attr('disabled', true);
      $$.event.pub("SELECT_PRICE_CONFIG", {
        product_id: item['product_id'],
        product_name: item['product_name'],
        config_price: item['price'],
      });
    },
    clean: function() {
      $$.event.pub('CLEAR_CONFIG_SELECTION');
    },
    edit: function(idx) {
      var self = this;
      var item = self.data[idx];
      $('#Id_Right_Drawer_Content').html('<Spin fix><div class="loader"><svg class="circular" viewBox="25 25 50 50"><circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="5" stroke-miterlimit="10" v-pre></svg></div></Spin>');

      $$.event.pub('OPEN_DRAWER', {width:400})
      $.get('/pricetype/change_price?id=' + item['id'] + "&price_type_name=" + self.price_type_name + "&product_name=" + item['product_name'] + "&price=" + item['price'] ,function(res){
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