<template id="v_batch_list">
  <div style="position: relative; min-height: 100px;">
    <i-form inline style="width: 200px; ">
      <Form-item>
        <Date-picker type="date" :value.sync="thedate" placeholder="选择日期" style="width: 105px" format="yyyy-MM-dd" @on-change="dateChange"></Date-picker>
      </Form-item>
      <Form-item>
        <i-button type="success" icon="plus-round" @click="openCreateBatch" ></i-button>
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
$$.comp('v_batch_list', {
  el: '#v_batch_list',
  EVENT:['SAVE_BATCH_SUCC'],
  data: function() {
    return {
      data: [],
      thedate: '',
      loading: true,
      current: 1,
      total: 0,
      columns: [
        {
          title: '日期',
          key: 'thedate'
        },
        {
          title: '批',
          key: 'idx',
          width: 50
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
        url: '/aj_batch/ls',
        data: {
          thedate: self.thedate,
          page: self.current
        },
        succ: function(data){
          self.loading = false;
          self.data = data.ls;
          self.total = data.total;
        },
      })   
    },

    hd_SAVE_BATCH_SUCC: function(){
      this.loadData()
    },

    openCreateBatch: function() {
      $('#Id_Right_Drawer_Content').html('<Spin fix><div class="loader"><svg class="circular" viewBox="25 25 50 50"><circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="5" stroke-miterlimit="10" v-pre></svg></div></Spin>');
      
      $$.event.pub('OPEN_DRAWER',{width:400})
      $.get('/orders/create_batch', function(res){
        $('#Id_Right_Drawer_Content').html(res)
      })
    },

    selRowChange: function(current, old) {
      var self = this;
      $$.event.pub('SELECT_BATCH', {thedate: current['thedate'], idx: current['idx']});
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
      this.current = 1;
      this.loadData();
    }
  },
})
</script>