<?php
include \view('inc_vue_header');
include \view('vue_product_price_list');
include \view('vue_client_price_list');
?>
<style>
.example-split.first{left:21%;}
.example-split.second{left:62.5%;}
</style>
<div id="v_price_type_list" >
  <div class="example ivu-row">
    <div class="example-demo ivu-col ivu-col-span-5">
      <div class="example-case">
        <h1><a name="top"></a>价格类型</h1>
        <i-form inline style="width: 250px; ">
          <Form-item>
            <i-input type="text" :value.sync="new_price_type" placeholder="请输入类型名称" style="width: 150px">
              <Icon type="edit" slot="prepend"></Icon>
            </i-input>
          </Form-item>
          <Form-item>
            <i-button type="success" icon="plus-round" @click="addPriceType"></i-button>
          </Form-item>
        </i-form>
        <i-table :content="self" :data="data" :columns="columns" highlight-row @on-row-click="selRowChange"></i-table>
      </div>
    </div>
    <div class="example-split first"></div>
    <div class="example-demo ivu-col ivu-col-span-10">
      <div class="example-case" >
        <h1>产品列表</h1>
        <v_product_price_list type_="from"></v_product_price_list>
      </div>
    </div>
    <div class="example-split second"></div>
    <div class="example-demo ivu-col ivu-col-span-9 ivu-col-split-right">
      <div class="example-case">
        <h1>门店列表</h1>
        <v_client_price_list type_="to"></v_client_price_list>
      </div>
    </div>
  </div>
</div>
<script>
  $$.vue({
    el:'#v_price_type_list',
    data: function(){
      return {
        self: this,
        new_price_type: '',
        data: <?=$__price_type?>,
        columns:[
          {
            title: '配置名称',
            key: 'price_type_name'
          },
        ],
      }
    },

    _init: function() {

    },


    methods: {
      addPriceType: function() {
        var self = this;
        if (this.new_price_type == "") {
          this.$Notice.warning({
              title: '请输入新建类型名称',
              desc:  ''
          });
          return;
        }
        this.$Loading.start();
        $$.ajax({
          url: '/pricetype/add_price_type',
          data: {
            price_type: self.new_price_type
          },
          succ: function(data){
            self.data = data;
            self.$Loading.finish();
            self.$Message.success('保存成功');
          },
          fail: function(msg) {
            self.$Loading.finish();
            self.$Message.error(msg);
          }
        })
      },

      selRowChange: function(current) {
        $$.event.pub("SELECT_PRICE_TYPE", {
          id: current.id,
          name: current.price_type_name,
        });
      },
    }
  })
</script>

<?php
include \view('inc_vue_footer');
