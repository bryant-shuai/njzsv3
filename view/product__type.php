<?php
include \view('inc_vue_header');
?>
<style>
.ivu-table-body {
    overflow: hidden;
}
</style>
  <div id="v_admin_users_ls" >
    <div class="example ivu-row">
      <div class="example-demo ivu-col ivu-col-span-12">
        <div class="example-case">
          <h1><a name="top"></a>产品分类</h1>
          <i-form inline style="width: 400px; ">
            <Form-item>
              <i-input type="text" :value.sync="product_type" placeholder="请输入产品分类名称">
                <Icon type="edit" slot="prepend"></Icon>
              </i-input>
            </Form-item>
            <Form-item>
              <i-button type="success" icon="plus-round" @click="addProductType">添加</i-button>
            </Form-item>
          </i-form>
          <i-table :content="self" :data="product_type_data" :columns="type_columns" stripe></i-table>
        </div>
      </div>
      <div class="example-split"></div>
      <div class="example-demo ivu-col ivu-col-span-12 ivu-col-split-right">
        <div class="example-case">
          <h1>产品分拣分类</h1>
          <i-form inline style="width: 400px; ">
            <Form-item>
              <i-input type="text" :value.sync="sort_type" placeholder="请输入分拣分类名称">
                <Icon type="edit" slot="prepend"></Icon>
              </i-input>
            </Form-item>
            <Form-item>
              <i-button type="success" icon="plus-round" @click="addProductSortType">添加</i-button>
            </Form-item>
          </i-form>
          <i-table :content="self" :data="product_sort_type_data" :columns="sort_columns" stripe></i-table>
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
          product_type: '',
          sort_type: '',
          product_type_data: <?=$__product_types?>,
          product_sort_type_data: <?=$__product_sort_types?>,
          sel_idx: -1,
          auto_select: false,
          type_columns: [
            {
              title: '类型名称',
              key: 'name'
            },
            {
              title: '操作',
              key: 'action',
              width: 150,
              render (row, column, index) {
                return `<i-button type="error" style="margin-left: 3px;" @click="delProductType(${index})" icon="close-round">删除</i-button>`;
              }
            },
          ],
          sort_columns: [
            {
              title: '类型名称',
              key: 'name'
            },
            {
              title: '操作',
              key: 'action',
              width: 150,
              render (row, column, index) {
                return `<i-button type="error" style="margin-left: 3px;" @click="delProductSortType(${index})" icon="close-round">删除</i-button>`;
              }
            },
          ],
        }
      },

      _init: function() {

      },

      methods: {
        addProductType: function() {
          var self = this;
          if (this.product_type == "") {
            this.$Notice.warning({
                title: '请输入产品分类名称',
                desc:  ''
            });
            return;
          }
          this.$Loading.start();
          $$.ajax({
            url: '/product/aj_add_product_type',
            data: {
              name: self.product_type
            },
            succ: function(data){
              self.product_type_data = data;
              self.$Loading.finish();
              self.$Message.success('保存成功');
            },
            fail: function(msg) {
              self.$Loading.finish();
              self.$Message.error(msg);
            }
          })
        },

        addProductSortType: function() {
          var self = this;
          if (this.sort_type == "") {
            this.$Notice.warning({
                title: '请输入分拣分类名称',
                desc:  ''
            });
            return;
          }
          this.$Loading.start();
          $$.ajax({
            url: '/product/aj_add_sort_type',
            data: {
              name: self.sort_type
            },
            succ: function(data){
              self.product_sort_type_data = data;
              self.$Loading.finish();
              self.$Message.success('保存成功');
            },
            fail: function(msg) {
              self.$Loading.finish();
              self.$Message.error(msg);
            }
          })
        },

        delProductType: function(idx) {
          var self = this;
          var item = this.product_type_data[idx];
          self.$Loading.start();
          $$.ajax({
            url: '/product/aj_remove_product_type',
            data: {
              id: item.id
            },
            succ: function(data){
              self.product_type_data = data;
              self.$Loading.finish();
              self.$Message.success("删除成功");
            },
            fail: function(msg) {
              self.$Loading.finish();
              self.$Message.error(msg);
            }
          })
        },

        delProductSortType: function(idx) {
          var self = this;
          var item = this.product_sort_type_data[idx];
          self.$Loading.start();
          $$.ajax({
            url: '/product/aj_remove_sort_type',
            data: {
              id: item.id
            },
            succ: function(data){
              self.product_sort_type_data = data;
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
