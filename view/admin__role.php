<?php
include \view('inc_vue_header');
?>
  <div id="v_admin_users_ls" >
    <div class="example ivu-row">
      <div class="example-demo ivu-col ivu-col-span-12">
        <div class="example-case">
          <h1><a name="top"></a>角色列表</h1>
          <i-form inline style="width: 400px; ">
            <Form-item>
              <i-input type="text" :value.sync="new_role" placeholder="请输入新建角色名称">
                <Icon type="edit" slot="prepend"></Icon>
              </i-input>
            </Form-item>
            <Form-item>
              <i-button type="success" icon="plus-round" @click="addRole">添加</i-button>
            </Form-item>
          </i-form>
          <i-table :content="self" :data="role_data" :columns="role_columns" stripe></i-table>
        </div>
      </div>
      <div class="example-split"></div>

      <div class="example-demo ivu-col ivu-col-span-12 ivu-col-split-right">
        <div class="example-case">
          <a name="top"><h1>{{sel_name}}</h1></a>
          <Alert show-icon>
            <template slot="desc">
              选择对应角色后，显示该角色拥有权限
            </template>
          </Alert>
          <i-table style="margin-top: -6px;" :content="self" :data="permission_data" :columns="permission_columns" stripe @on-selection-change="allotPermission" @on-select-all="allotPermission"></i-table>
        </div>
      </div>
    </div>
  </div>
  <script>
    $$.vue({
      el:'#v_admin_users_ls',
      EVENT:['SAVE_SUCCESS'],

      data: function(){
        return {
          self: this,
          new_role: '',
          role_data: <?=$__roles?>,
          sel_name: '权限列表',
          sel_idx: -1,
          auto_select: false,
          role_columns: [
            {
              title: '角色名',
              key: 'name'
            },
            {
              title: '操作',
              key: 'action',
              render (row, column, index) {
                return `<i-button type="primary" :id="'btnChoose'+${index}"  @click="chooseRole(${index})" icon="paper-airplane">选择</i-button> <i-button type="error" style="margin-left: 3px;" @click="deleteRole(${index})" icon="close-round">删除</i-button>`;
              }
            },
          ],
          permission_data: <?=$__permissions?>,
          o_pers: {},
          permission_columns: [
            {
                type: 'selection',
                width: 120,
                align: 'center'
            },
            {
              title: '角色名',
              key: 'name'
            }
          ]
        }
      },

      _init: function() {

        var o_pers = {};
        var permission_data = this.permission_data;
        // 整理权限集合
        for (var i = 0; i < permission_data.length; i++) {
          var item = permission_data[i];
          o_pers['' + item['id']] = i;
        }
        this.o_pers = o_pers;
      },


      methods: {
        addRole: function() {
          var self = this;
          if (this.new_role == "") {
            this.$Notice.warning({
                title: '请输入新建角色名称',
                desc:  ''
            });
            return;
          }
          this.$Loading.start();
          $$.ajax({
            url: '/admin/aj_role_add',
            data: {
              name: self.new_role
            },
            succ: function(data){
              self.role_data = data;
              self.$Loading.finish();
              self.$Message.success('保存成功');
            },
            fail: function(msg) {
              self.$Loading.finish();
              self.$Message.error(msg);
            }
          })
        },

        allotPermission: function(selection) {
          // console.log(selection);
          if (this.auto_select){
            return;
          }
          
          if (this.sel_idx == -1) {
            return;
          }
          var self = this;
          var role = this.role_data[this.sel_idx];
          var permissions = "";
          for (var idx in selection) {
            permissions += selection[idx]['id'] + ",";
          }

          $$.ajax({
            url: '/admin/aj_set_permission',
            data: {
              role: role.id,
              permissions: permissions
            },
            succ: function(data){
              self.$Loading.finish();
              self.$Message.success('保存成功');
            },
            fail: function(msg) {
              self.$Loading.finish();
              self.$Message.error(msg);
            }
          })
        },

        chooseRole: function(idx) {
          var self = this;
          var role = this.role_data[idx];
          this.sel_name = role['name'] + "の权限列表";
          // 禁用按钮
          $('#btnChoose'+self.sel_idx).removeAttr('disabled');
          this.sel_idx = idx;
          $('#btnChoose'+self.sel_idx).attr('disabled', true);
          this.emptySelection();

          this.$Loading.start();
          $$.ajax({
            url: '/admin/aj_load_permission',
            data: {
              role: role.id
            },
            succ: function(data){
              self.selectPermission(data);
              self.$Loading.finish();
            },
            fail: function(msg) {
              self.$Loading.finish();
              self.$Message.error(msg);
            }
          })
        },

        emptySelection: function() {
          var self = this;
          this.auto_select = true;
          // 取消已选择项
          var selection = this.$children[3].getSelection();
          for (var idx in selection) {
            this.$children[3].toggleSelect(self.o_pers[selection[idx]['id']]);
          }
          this.auto_select = false;
        },

        selectPermission: function(p_list) {
          var o_pers = this.o_pers;

          this.auto_select = true;
          // 选择目标拥有权限
          for (var idx in p_list) {
            this.$children[3].toggleSelect(o_pers[p_list[idx]]);
          }
          this.auto_select = false;
        },

        deleteRole: function(idx) {
          var self = this;
          var role = this.role_data[idx];
          $$.ajax({
            url: '/admin/aj_delete_role',
            data: {
              role: role.id
            },
            succ: function(data){
              self.emptySelection();
              self.role_data = data;
              self.$Loading.finish();
              self.$Message.success("删除成功");
            },
            fail: function(msg) {
              self.$Loading.finish();
              self.$Message.error(msg);
            }
          })
        }
      }
    })
  </script>

<?php
include \view('inc_vue_footer');
