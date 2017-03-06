<?php
include \view('inc_vue_header');
?>
  <div id="v_admin_manager_ls" >
    <div class="example ivu-row">
      <div class="example-demo ivu-col ivu-col-span-24" style="position:relative;">
        <div class="example-case">
          <h1><a name="top"></a>短信记录</h1>
          <div style="width: 300px; padding-bottom: 10px;">
            <i-input type="text" :value.sync="search_txt" placeholder="代理人名称">
                <Icon type="search" slot="prepend"></Icon>
            </i-input>
          </div>

          <i-table :content="self" :data="data" :columns="columns" stripe></i-table>
          <div style="margin: 10px;overflow: hidden">
            <div style="float: right;">
              <Page :total="total" :current.sync="current" @on-change="loadData"></Page>
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>
  <script>
    $$.vue({
      el:'#v_admin_manager_ls',
      EVENT:['SAVE_SUCCESS'],

      data: function(){
        return {
          self: this,
          data: [],
          current: 1,
          total: 0,
          search_txt: '',
          columns: [
            {
              title: 'ID',
              key: 'id',
              width: 80,
            },
            {
              title: '代理人',
              key: 'client_name',
              width: 80,
            },
            {
              title: '金额',
              key: 'amount',
              width: 80,
            },
            {
              title: '信息',
              key: 'msg',
            },
            {
              title: '状态',
              key: 'status',
              width: 80,
            }
          ]
        }
      },

      _init: function() {
        this.loadData();
      },


      methods: {
        loadData: function() {
          var self = this;
          this.$Loading.start();
          $$.ajax({
            url: '/sms/aj_list',
            data: {
              page: self.current,
              search: self.search_txt
            },
            succ: function(data){
              self.data = data.ls;
              self.total = data.total; 
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
      },
    })
  </script>

<?php
include \view('inc_vue_footer');
