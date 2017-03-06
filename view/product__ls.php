<?php
include \view('inc_vue_header');
include \view("vue_product_list");
?>
  <div id="v_product_ls" >
   <Modal :visible.sync="show_modal" width="360" id="modal" style="display: none;">
      <p slot="header" style="color:#f60;text-align:center">
          <Icon type="information-circled"></Icon>
          <span>变更确认</span>
      </p>
      <div style="text-align:center">
        <p>是否将<span style="color:#ff9900;">{{formItem.name}}</span></p>
        <p>价格由<span style="color: #3399ff;">{{before_price}}</span>变更为<span style="color:#ff6600">{{formItem.price}}</span></p>
        <p v-if="is_direct != ''"><span style="color:red;">该操作将变更对应价格类型以及所有门店订该产品的价格。</span></p>
        <p>确定执行变更？</p>
      </div>
      <div slot="footer">
          <i-button type="error" size="large" long :loading="modal_loading" @click="saveData">确定</i-button>
      </div>
    </Modal>
    <div class="example ivu-row">
      <div class="example-demo ivu-col ivu-col-span-12">
        <div class="example-case">
          <h1><a name="top"></a>产品列表</h1>
          <div style="position:absolute;right:22px;top:36px;;">
            <i-button type="success" icon="plus-round" size="large" @click="addProduct">
              添加
            </i-button>
          </div>
          <v_product_list show_deleted_="1"></v_product_list>
          <!-- <i-table :content="self" :data="role_data" :columns="role_columns" stripe></i-table> -->
        </div>
      </div>
      <div class="example-split"></div>

      <div class="example-demo ivu-col ivu-col-span-12 ivu-col-split-right">
        <div class="example-case">
          <h1 v-if="product_id!=0">{{formItem.name}}の产品详细</h1>
          <h1 v-else>新增产品</h1>
          <Alert show-icon>
            <template slot="desc" v-if="product_id!=0">
              选择对应产品，显示该产品详细信息
            </template>
            <template slot="desc" v-if="product_id==0">
              录入完整产品信息，点击保存即可完成添加
            </template>
          </Alert>

          <i-form v-ref:form-validate :model="formItem" :rules="ruleValidate" :label-width="80">
            <Form-item label="产品名称" prop="name">
              <i-input :value.sync="formItem.name" placeholder="请输入产品名称"></i-input>
            </Form-item>
            <Form-item label="产品单位" prop="unit">
              <i-input :value.sync="formItem.unit" placeholder="请输入产品单位"></i-input>
            </Form-item>
            <Form-item label="产品拼音">
              <i-input :value.sync="formItem.py" placeholder="请输入产品拼音"></i-input>
            </Form-item>
            <Form-item label="产品价格">
              <Input-number :min="-1" :value.sync="formItem.price" style="width: 100%;"></Input-number>
            </Form-item>
            <Form-item label="显示顺序">
              <Input-number :min="0" :value.sync="formItem.order" style="width: 100%;"></Input-number>
            </Form-item>
            <Form-item label="计量类型" prop="weight_type">
              <i-select :model.sync="formItem.weight_type" placeholder="请选择计量类型">
                <i-option v-for="item in weight_types" :value="item.id">{{item.name}}</i-option>
              </i-select>
            </Form-item>
            <Form-item label="产品分类" prop="product_type">
              <i-select :model.sync="formItem.product_type" placeholder="请选择产品分类">
                <i-option v-for="item in product_types" :value="item.id">{{item.name}}</i-option>
              </i-select>
            </Form-item>
            <Form-item label="分拣类型" prop="sort_type">
              <i-select :model.sync="formItem.sort_type" placeholder="请选择分拣类型">
                <i-option v-for="item in product_sort_types" :value="item.name">{{item.name}}</i-option>
              </i-select>
            </Form-item>
            <Form-item label="所属工厂" v-if="factory_id == 0">
              <i-select :model.sync="formItem.factory_id" placeholder="请选择所属工厂">
                <i-option v-for="item in factory_ids" :value="$index+''">{{ item }}</i-option>
              </i-select>
            </Form-item>
            <Form-item>
                <i-button type="primary" icon="checkmark-round" size="large" @click="submit">保存</i-button>
            </Form-item>
          </i-form>
        </div>
      </div>
    </div>

   
  </div>
  <script>
    $$.vue({
      el:'#v_product_ls',
      EVENT:['SAVE_SUCCESS', 'SELECT_PRODUCT'],

      data: function(){
        return {
          product_id: 0,
          before_price: -1,
          formItem: {
            name: '',
            unit: '',
            price: -1,
            order: 0,
            weight_type: '',
            product_type: '',
            sort_type: '',
            // deleted: "0",
            py: '',
            factory_id: <?=$_SESSION['user']['factory_id']?>,
          },
          show_modal: false,
          modal_loading: false,
          factory_id: <?=$_SESSION['user']['factory_id']?>,
          factory_ids: <?=$__factory_ids?>,
          product_types: <?=$__product_types?>,
          product_sort_types: <?=$__product_sort_types?>,
          weight_types:[
            {id: "0", name: "以重计"},
            {id: "1", name: "以件计"}
          ],
          // deleted: [
          //   {id: "0", name: "上架"},
          //   {id: "1", name: "下架"},
          // ],
          ruleValidate: {
            name: [
              { required: true, message: '产品名称不能为空', trigger: 'blur' }
            ],
            unit: [
              { required: true, message: '产品单位不能为空', trigger: 'blur' }
            ],
            weight_type: [
              { required: true, message: '计量类型不能为空', trigger: 'blur' }
            ],
            product_type: [
              { required: true, message: '产品分类不能为空', trigger: 'blur' }
            ],
            sort_type: [
              { required: true, message: '分拣类型不能为空', trigger: 'blur' }
            ]
          }
        }
      },

      _init: function() {

      },

      methods: {
        hd_SELECT_PRODUCT: function(id) {
          var self = this;
          this.product_id = id;

          if (this.product_id != 0) {
            self.$Loading.start();
            $$.ajax({
              url: '/product/aj_detail',
              data: {
                id: self.product_id
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

        addProduct: function() {
          $$.event.pub('CLEAR_PRODUCT_SELECTION');
        },

        emptyDetail: function() {
          this.formItem = {
            name: '',
            unit: '',
            price: -1,
            order: 0,
            weight_type: '',
            product_type: '',
            sort_type: '',
            // deleted: "0",
            factory_id: <?=$_SESSION['user']['factory_id']?>,
          }
        },

        submit: function() {
          var self = this;
          this.$refs['formValidate'].validate((valid) => {
            if (valid) {
              if (self.before_price != self.formItem.price) {
                $('#modal').show();
                self.show_modal = true;
              } else {
                self.saveData();
              }
            }
          })
        },

        saveData: function() {
          var self = this;
          self.modal_loading = true;
          $$.ajax({
            url: '/product/aj_save_detail',
            data: {
              id: self.product_id,
              name: self.formItem.name,
              type: self.formItem.product_type,
              sort_type: self.formItem.sort_type,
              unit: self.formItem.unit,
              price: self.formItem.price,
              order: self.formItem.order,
              weight_type: self.formItem.weight_type,
              // deleted: self.formItem.deleted,
              py: self.formItem.py,
              factory_id: self.formItem.factory_id,
            },
            succ: function(data){
              self.modal_loading = false;
              self.show_modal = false;
              self.$Loading.finish();
              self.$Message.success('保存成功');
              $$.event.pub('SAVE_PRODUCT_SUCC');
              $$.event.pub('CLEAR_PRODUCT_SELECTION');
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
