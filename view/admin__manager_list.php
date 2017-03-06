<?php
include \view('inc_vue_header');
?>
  <div id="v_admin_manager_ls" >
    <div class="example ivu-row">
      <div class="example-demo ivu-col ivu-col-span-24" style="position:relative;">
        <div class="example-case">
          <div style="position:absolute;right:22px;top:36px;;">
             <i-button type="success" icon="plus-round" size="large" @click="add">
               添加
             </i-button>
             <i-button type="primary" icon="printer" size="large" @click="exportInfo">
               导出代理人信息
             </i-button>
             <i-button type="primary" icon="printer" size="large" @click="exportAllotHis">
               导出余额分配记录
             </i-button>
          </div>
          <h1><a name="top"></a>代理人列表</h1>
          <div style="width: 300px; padding-bottom: 10px;">
            <i-input type="text" :value.sync="search_txt" placeholder="代理人/门店名称">
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
                  title: '代理人',
                  key: 'name'
              },
              {
                  title: '总余额',
                  key: 'deposit'
              },
              {
                title: '可分配余额',
                key: 'can_allot',
              },
              {
                title: '所属工厂',
                key: 'factory_id'
              },
              {
                  title: '门店',
                  key: 'clients',
                  render (row, column, index) {
                      return `<Poptip trigger="hover" title="${row.clients.length}个门店" placement="bottom">
                                  <tag>${row.clients.length}</tag>
                                  <div slot="content">
                                      <ul><li v-for="item in data[${index}].clients" style="text-align: center;padding: 4px">{{ item['storename'] }}</li></ul>
                                  </div>
                              </Poptip>`;
                  },
                  width: 60

              },
              {
                  title: '状态',
                  key: 'status',
                  render (row) {
                      const color = row.deleted == 0 ? 'green' : 'red';
                      const text = row.deleted == 0 ? '有效' : '无效';
                      return `<tag type="dot" color="${color}">${text}</tag>`;
                  }
              },
              {
                  title: '操作',
                  key: 'action',
                  width: 200,
                  render (row, column, index) {
                    return `<i-button type="primary"  @click="edit(${index})" icon="edit">编辑</i-button> <i-button type="error" style="margin-left: 3px;" @click="remove(${index})" icon="close-round">删除</i-button>`;
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
            url: '/admin/aj_manager_list',
            data: {
              page: self.current,
              search_txt: self.search_txt
            },
            succ: function(data){
              self.data = data.ls;
              self.total = data.total; 
              self.$Loading.finish();
            },
          })    
        },

        add: function (){
          $('#Id_Right_Drawer_Content').html('<Spin fix><div class="loader"><svg class="circular" viewBox="25 25 50 50"><circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="5" stroke-miterlimit="10" v-pre></svg></div></Spin>')

          $$.event.pub('OPEN_DRAWER',{width:400})
          $.get('/admin/user_detail?type=1',function(res){
            $('#Id_Right_Drawer_Content').html(res)
          })
        },
        edit: function(idx) {
          var id = this.data[idx]['id'];
          $('#Id_Right_Drawer_Content').html('<Spin fix><div class="loader"><svg class="circular" viewBox="25 25 50 50"><circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="5" stroke-miterlimit="10" v-pre></svg></div></Spin>')

          $$.event.pub('OPEN_DRAWER',{width:400})
          $.get('/admin/user_detail?id=' + id + "&type=1",function(res){
            $('#Id_Right_Drawer_Content').html(res)
          })
        },
        remove(idx) {
          var self = this;
          var id = this.data[idx]['id'];
          $$.ajax({
            url: '/admin/aj_user_delete',
            data: {
              id: id,
            },
            succ: function(data){
              self.loadData();
            },
          })  
        },

        exportInfo: function() {
          location.href = "/excel/manager_list_export";
        },

        exportAllotHis: function(idx) {
          $('#Id_Right_Drawer_Content').html('<Spin fix><div class="loader"><svg class="circular" viewBox="25 25 50 50"><circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="5" stroke-miterlimit="10" v-pre></svg></div></Spin>')

          $$.event.pub('OPEN_DRAWER',{width:400})
          $.get('/admin/export_manager_allot_his', function(res){
            $('#Id_Right_Drawer_Content').html(res)
          })
        },
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
