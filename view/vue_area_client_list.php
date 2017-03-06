<template id="v_area_client_list">
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
  </div>
</template>


<script type="text/javascript">
$$.comp('v_area_client_list', {
  el: '#v_area_client_list',
  EVENT:['SELECT_AREA', 'MOVE_SUCC'],
  props: ['show_deleted_'],
  data: function() {
    return {
      search_txt: '',
      data: [],
      current: 1,
      total: 0,
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
            return `<i-button type="error" size="small" icon="minus-round" @click="move(${index})">移出</i-button><i-button type="primary" size="small" style="margin-left: 3px;" icon="arrow-up-b" @click="sort(${index}, 'up')" class="btnSort">上移</i-button><i-button type="warning" size="small" style="margin-left: 3px;" icon="arrow-down-b" @click="sort(${index}, 'down')" class="btnSort">下移</i-button>`;
          },
          width: 220
        }
      ]
    }
  },

  _init: function() {
  },

  methods: {
    loadData: function() {
      var self = this;
      $$.ajax({
        url: '/client/aj_client_by_area',
        data: {
          page: self.current,
          search_txt: self.search_txt,
          area_id: self.from_area_id
        },
        succ: function(data){
          self.data = data.ls;
          self.total = data.total;
        },
      })   
    },

    hd_SELECT_AREA: function(params){
      this.from_area_id = params.area_id;
      this.loadData()
    },

    hd_MOVE_SUCC: function() {
      this.loadData();
    },

    clean: function() {
      this.search_txt = "";
    },
    move: function(idx) {
      var self = this;
      var item = self.data[idx];
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
    },
    sort: function(idx, type) {
      var self = this;
      if (idx == 0 && this.current == 1 && type == "up") {
        return;
      }
      var item = self.data[idx];
      $('.btnSort').attr("disabled", true);
      this.$Loading.start();
      $$.ajax({
        url: '/client/aj_sort_client',
        data: {
          area_id: self.from_area_id,
          client_id: item.id,
          type: type
        },
        succ: function(data){
          $('.btnSort').attr("disabled", false);
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