<template id="v_client_list">
  <div style="position: relative;">
    <i-form inline style="width: 300px;" v-show="!loading">
      <Form-item>
        <i-input type="text" :value.sync="search_txt" placeholder="门店名称/门店拼音">
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
$$.comp('v_client_list', {
  el: '#v_client_list',
  EVENT:['SAVE_CLIENT_SUCC', 'CLEAR_CLIENT_SELECTION'],
  props: ['show_deleted_'],
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
          key: 'storename'
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
        url: '/client/aj_ls',
        data: {
          page: self.current,
          search_txt: self.search_txt,
          show_deleted: self.show_deleted_
        },
        succ: function(data){
          self.loading = false;
          self.data = data.ls;
          self.total = data.total;
        },
      })   
    },

    hd_SAVE_CLIENT_SUCC: function(){
      this.loadData()
    },

    hd_CLEAR_CLIENT_SELECTION: function() {
      this.search_txt = "";
      // 清空选中项
      this.$children[1].objData = this.$children[1].makeObjData();
      $$.event.pub("SELECT_CLIENT", 0);
    },

    selRowChange: function(current, old) {
      $$.event.pub("SELECT_CLIENT", current['id']);
    },

    clean: function() {
      $$.event.pub('CLEAR_CLIENT_SELECTION');
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