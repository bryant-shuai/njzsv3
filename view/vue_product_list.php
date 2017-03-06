<template id="v_product_list">
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
    <i-table highlight-row :columns="columns" :data="data" @on-current-change="selRowChange" v-show="!loading"></i-table>
    <div style="margin: 10px;overflow: hidden" v-show="!loading">
      <div style="float: right;">
        <Page :total="total" :current.sync="current" @on-change="loadData"></Page>
      </div>
    </div>
    <Spin size="large" fix v-if="loading"></Spin>
  </div>
</template>


<script type="text/javascript">
$$.comp('v_product_list', {
  el: '#v_product_list',
  EVENT:['SAVE_PRODUCT_SUCC', 'CLEAR_PRODUCT_SELECTION'],
  props: ['data_type_', 'show_deleted_'],
  data: function() {
    return {
      search_txt: '',
      data: [],
      current: 1,
      total: 0,
      loading: true,
      columns: [
        {
          title: 'ID',
          key: 'id',
          width: 70
        },
        {
          title: '名称',
          key: 'name'
        }
      ]
    }
  },

  _init: function() {
    this.loadData()
  },

  methods: {
    loadData: function() {
      var self = this;
      $$.ajax({
        url: '/product/aj_ls',
        data: {
          page: self.current,
          search_txt: self.search_txt,
          data_type: self.data_type_,
          show_deleted: self.show_deleted_
        },
        succ: function(data){
          self.loading = false;
          self.data = data.ls;
          self.total = data.total; 
        },
      })   
    },
    hd_SAVE_PRODUCT_SUCC: function(){
      this.loadData()
    },
    hd_CLEAR_PRODUCT_SELECTION: function() {
      this.search_txt = "";
      // 清空选中项
      this.$children[1].objData = this.$children[1].makeObjData();
      $$.event.pub("SELECT_PRODUCT", 0);
    },
    selRowChange: function(current, old) {
      $$.event.pub("SELECT_PRODUCT", current['id']);
    },
    clean: function() {
      $$.event.pub('CLEAR_PRODUCT_SELECTION');
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