<template id="v_sort_area_list">
  <div style="position: relative; min-height: 100px;">
    <i-form inline style="width: 250px; ">
      <Form-item>
        <i-input type="text" :value.sync="search_txt" placeholder="分区名称" style="width: 140px;">
          <Icon type="search" slot="prepend"></Icon>
        </i-input>
      </Form-item>
      <Form-item>
        <i-button type="success" icon="plus-round" @click="openCreate" ></i-button>
      </Form-item>
    </i-form>
    
    <i-table :columns.sync="columns" :data="data" highlight-row @on-row-click="selRowChange" v-show="!loading"></i-table>
    <div style="margin: 10px;overflow: hidden" v-show="!loading">
      <div style="float: right;">
        <Page :total="total" :current.sync="current" @on-change="loadData"></Page>
      </div>
    </div>
    <Spin size="large" fix v-if="loading"></Spin>
  </div>
</template>


<script type="text/javascript">
$$.comp('v_sort_area_list', {
  el: '#v_sort_area_list',
  EVENT:['SAVE_AREA_SUCC'],
  data: function() {
    return {
      data: [],
      thedate: '',
      loading: true,
      current: 1,
      total: 0,
      columns: [
        {
          title: '分区',
          key: 'area_name'
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
        url: '/client/aj_area_list',
        data: {
          search_txt: self.search_txt,
          page: self.current
        },
        succ: function(data){
          self.loading = false;
          self.data = data.ls;
          self.total = data.total;
        },
      })   
    },

    hd_SAVE_AREA_SUCC: function(){
      this.loadData()
    },

    openCreate: function() {
      $('#Id_Right_Drawer_Content').html('<Spin fix><div class="loader"><svg class="circular" viewBox="25 25 50 50"><circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="5" stroke-miterlimit="10" v-pre></svg></div></Spin>');
      
      $$.event.pub('OPEN_DRAWER',{width:400})
      $.get('/client/create_area', function(res){
        $('#Id_Right_Drawer_Content').html(res)
      })
    },

    selRowChange: function(current, old) {
      var self = this;
      $$.event.pub('SELECT_AREA', {
        area_id: current['id'],
        area_name: current['area_name'],
      });
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