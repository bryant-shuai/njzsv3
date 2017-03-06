<?php
include \view('inc_vue_header');
?>
  <div id="v_deposit_ls" >
    <div class="example ivu-row">
      <div class="example-demo ivu-col ivu-col-span-24" style="position:relative;">
        <div class="example-case">
          <div style="position:absolute;right:22px;top:36px;;">
             <i-button type="primary" icon="printer" size="large" @click="export">
               导出门店余额
             </i-button>
          </div>
          <h1><a name="top"></a>门店余额</h1>
          <div style="width: 300px; padding-bottom: 10px;">
            <i-input type="text" :value.sync="search_txt" placeholder="门店名称/门店拼音">
                <Icon type="search" slot="prepend"></Icon>
            </i-input>
          </div>

          <i-table :content="self" :data="data" :columns="columns" stripe @on-sort-change="tableSortChange"></i-table>
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
      el:'#v_deposit_ls',
      EVENT:['SAVE_SUCCESS'],

      data: function(){
        return {
          self: this,
          data: [],
          current: 1,
          total: 0,
          search_txt: '',
          order: {
            key: 'order',
            order: 'asc'
          },
          columns: [
              {
                  title: '门店',
                  key: 'storename',
                  sortable: 'custom'
              },
              {
                  title: '余额',
                  key: 'deposit',
                  sortable: 'custom'
              },
              {
                  title: '操作',
                  key: 'action',
                  render (row, column, index) {
                    return `<i-button type="primary"  @click="change(${index})" icon="edit">余额变更</i-button> <i-button type="success" style="margin-left: 3px;" @click="view(${index})" icon="calendar">查看记录</i-button>`;
                  }
              }
          ]
        }
      },

      _init: function() {
        this.loadData();
      },

      methods: {
        hd_SAVE_SUCCESS: function() {
          this.loadData();
        },
        loadData: function() {
          var self = this;
          this.$Loading.start();
          $$.ajax({
            url: '/client/aj_deposit_list',
            data: {
              page: self.current,
              search_txt: self.search_txt,
              order: self.order
            },
            succ: function(data){
              self.data = data.ls;
              self.total = data.total; 
              self.$Loading.finish();
            },
          })    
        },

        export: function (){
          location.href = "/excel/account_deposit_export";
        },

        change: function(idx) {
          var id = this.data[idx]['id'];
          var storename = this.data[idx]['storename'];
          var deposit = this.data[idx]['deposit'];
          $('#Id_Right_Drawer_Content').html('<Spin fix><div class="loader"><svg class="circular" viewBox="25 25 50 50"><circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="5" stroke-miterlimit="10" v-pre></svg></div></Spin>')

          $$.event.pub('OPEN_DRAWER',{width:400})
          $.get('/client/change_deposit?id=' + id + "&storename=" + storename + "&deposit=" + deposit,function(res){
            $('#Id_Right_Drawer_Content').html(res)
          })
        },

        view: function(idx) {
          var self = this;
          var id = this.data[idx]['id'];
          var deposit = this.data[idx]['deposit'];
          $('#Id_Right_Drawer_Content').html('<Spin fix><div class="loader"><svg class="circular" viewBox="25 25 50 50"><circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="5" stroke-miterlimit="10" v-pre></svg></div></Spin>')

          $$.event.pub('OPEN_DRAWER',{width:900})
          $.get('/client/log_account?id=' + id,function(res){
            $('#Id_Right_Drawer_Content').html(res)
          })
        },

        tableSortChange: function(param) {
          this.order = {
            key: param.key,
            order: param.order
          };

          this.loadData();
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
