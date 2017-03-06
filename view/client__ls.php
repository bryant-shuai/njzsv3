<?php
include \view('inc_vue_header');
include \view("vue_client_list");
?>
  <div id="v_client_ls" >
    <div class="example ivu-row">
      <div class="example-demo ivu-col ivu-col-span-12">
        <div class="example-case">
          <h1><a name="top"></a>门店列表</h1>
          <div style="position:absolute;right:22px;top:36px;;">
            <i-button type="success" icon="plus-round" size="large" @click="addClient">
              添加
            </i-button>
          </div>
          <v_client_list show_deleted_="1"></v_client_list>
          <!-- <i-table :content="self" :data="role_data" :columns="role_columns" stripe></i-table> -->
        </div>
      </div>
      <div class="example-split"></div>

      <div class="example-demo ivu-col ivu-col-span-12 ivu-col-split-right">
        <div class="example-case">
          <h1 v-if="client_id!=0">{{formItem.storename}}</h1>
          <h1 v-else>新增门店</h1>
          <Alert show-icon>
            <template slot="desc" v-if="client_id!=0">
              选择对应门店，显示该门店详细信息
            </template>
            <template slot="desc" v-if="client_id==0">
              录入完整门店信息，点击保存即可完成添加
            </template>
          </Alert>

          <i-form v-ref:form-validate :model="formItem" :rules="ruleValidate" :label-width="80">
            <Form-item label="门店名称" prop="storename">
              <i-input :value.sync="formItem.storename" placeholder="请输入门店名称"></i-input>
            </Form-item>
            <Form-item label="店铺地址" prop="address">
              <i-input :value.sync="formItem.address" placeholder="请输入门店地址"></i-input>
            </Form-item>
            <Form-item label="手机号">
              <i-input :value.sync="formItem.phone" placeholder="请输入手机号"></i-input>
            </Form-item>
            <Form-item label="代理人" prop="manager_name">
              <i-input :value.sync="formItem.manager_name" placeholder="请输入代理人"></i-input>
            </Form-item>
            <Form-item label="门店拼音">
              <i-input :value.sync="formItem.py" placeholder="请输入门店拼音"></i-input>
            </Form-item>
            <Form-item label="价格类型">
              <i-select :model.sync="formItem.price_type_id" placeholder="请选择价格类型">
                <i-option v-for="item in price_types" :value="item.id">{{item.price_type_name}}</i-option>
              </i-select>
            </Form-item>
            <Form-item label="门店状态">
              <i-select :model.sync="formItem.deleted" placeholder="请选择门店状态">
                <i-option v-for="item in deleted" :value="item.id">{{item.name}}</i-option>
              </i-select>
            </Form-item>
            <Form-item label="所属工厂" v-if="factory_id == 0">
              <i-select :model.sync="formItem.factory_id" placeholder="请选择所属工厂">
                <i-option v-for="item in factory_ids" :value="$index+''">{{ item }}</i-option>
              </i-select>
            </Form-item>
            <Form-item>
                <i-button type="primary" icon="checkmark-round" size="large" @click="submit">保存</i-button>
                <i-button type="warning" style="margin-left: 30px;" icon="key" size="large" v-if="client_id != 0" @click="change">登录信息</i-button>
            </Form-item>
          </i-form>
        </div>
      </div>
    </div>

   
  </div>
  <script>
    $$.vue({
      el:'#v_client_ls',
      EVENT:['SAVE_SUCCESS', 'SELECT_CLIENT'],

      data: function(){
        return {
          client_id: 0,
          before_price: -1,
          formItem: {
            storename: '',
            address: '',
            phone: '',
            manager_name: '',
            py: '',
            price_type_id: '',
            deleted: "0",
            factory_id: <?=$_SESSION['user']['factory_id']?>,
          },
          factory_id: <?=$_SESSION['user']['factory_id']?>,
          factory_ids: <?=$__factory_ids?>,
          price_types: <?=$__price_type?>,
          deleted: [
            {id: "0", name: "营业"},
            {id: "1", name: "闭店"},
          ],
          ruleValidate: {
            storename: [
              { required: true, message: '门店名称不能为空', trigger: 'blur' }
            ],
            address: [
              { required: true, message: '门店地址不能为空', trigger: 'blur' }
            ],
            manager_name: [
              { required: true, message: '门店地址不能为空', trigger: 'blur' }
            ]
          }
        }
      },

      _init: function() {

      },

      methods: {
        hd_SELECT_CLIENT: function(id) {
          var self = this;
          this.client_id = id;

          if (this.client_id != 0) {
            self.$Loading.start();
            $$.ajax({
              url: '/client/aj_detail',
              data: {
                id: self.client_id
              },
              succ: function(data){
                self.formItem = data;
                self.before_price = data.price;
                self.$Loading.finish();
              },
              fail: function(msg) {
                self.$Loading.finish();
                self.$Message.error(msg);
              }
            })
          } else {
            this.emptyDetail();
          }
        },

        addClient: function() {
          $$.event.pub('CLEAR_CLIENT_SELECTION');
        },

        emptyDetail: function() {
          this.formItem = {
            storename: '',
            address: '',
            phone: '',
            manager_name: '',
            py: '',
            price_type_id: '',
            deleted: "0",
            factory_id: <?=$_SESSION['user']['factory_id']?>,
          }
        },

        submit: function() {
          var self = this;
          this.$refs['formValidate'].validate((valid) => {
            if (valid) {
              self.saveData();
            }
          })
        },

        change: function() {
          var id = this.client_id;
          $('#Id_Right_Drawer_Content').html('<Spin fix><div class="loader"><svg class="circular" viewBox="25 25 50 50"><circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="5" stroke-miterlimit="10" v-pre></svg></div></Spin>')

          $$.event.pub('OPEN_DRAWER',{width:400})
          $.get('/client/login_detail?id=' + id ,function(res){
            $('#Id_Right_Drawer_Content').html(res)
          })
        },

        saveData: function() {
          var self = this;
          $$.ajax({
            url: '/client/aj_save_detail',
            data: {
              id: self.client_id,
              name: self.formItem.storename,
              addr: self.formItem.address,
              phone: self.formItem.phone,
              price: self.formItem.price,
              client_manager: self.formItem.manager_name,
              py: self.formItem.py,
              price_type_id: self.formItem.price_type_id,
              deleted: self.formItem.deleted,
              factory_id: self.formItem.factory_id
            },
            succ: function(data){
              self.$Loading.finish();
              self.$Message.success('保存成功');
              $$.event.pub('SAVE_CLIENT_SUCC');
              $$.event.pub('CLEAR_CLIENT_SELECTION');
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
