<template id="v_unclassified_client_list">
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
$$.comp('v_unclassified_client_list', {
  el: '#v_unclassified_client_list',
  EVENT:['MOVE_SUCC', 'SELECT_AREA'],
  props: ['show_deleted_'],
  data: function() {
    return {
      search_txt: '',
      data: [],
      current: 1,
      total: 0,
      loading: true,
      from_area_id: 0,
      to_area_id: 0,
      columns: [
        {
          title: '名称',
          key: 'storename'
        },
        {
          title: '操作',
          key: 'action',
          render (row, column, index) {
            return `<i-button type="success" size="small" icon="plus-round" @click="move(${index})">移入</i-button>`;
          }
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
        url: '/client/aj_unclassified_client',
        data: {
          page: self.current,
          search_txt: self.search_txt
        },
        succ: function(data){
          self.loading = false;
          self.data = data.ls;
          self.total = data.total;
        },
      })   
    },

    hd_SELECT_AREA: function(params) {
      this.to_area_id = params.area_id;
    },

    hd_MOVE_SUCC: function(){
      this.loadData()
    },

    clean: function() {
      this.search_txt = "";
    },
    move: function(idx) {
      var self = this;
      var item = self.data[idx];
      if (self.to_area_id == 0) {
        self.$Notice.warning({
          title: '请先选择分区',
          desc: '',
        });
        return;
      }

      this.$Loading.start();
      $$.ajax({
        url: '/client/aj_move_client_for_area',
        data: {
          from_area_id: self.from_area_id,
          to_area_id: self.to_area_id,
          client_id: item.id
        },
        succ: function(data){
          $$.event.pub('MOVE_SUCC');
          self.$Loading.finish();
        },
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