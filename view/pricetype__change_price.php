<div id="v_order_detail">
  <i-form v-ref:form-validate :model="formItem" :rules="ruleValidate" :label-width="80">
    <Form-item label="门店" v-if="storename != ''">
      <i-input :value="storename" disabled></i-input>
    </Form-item>
    <Form-item label="产品" v-if="product_name != ''">
      <i-input :value="product_name" disabled></i-input>
    </Form-item>
    <Form-item label="价格" prop="price">
      <Input-number :min="-1" :value.sync="price" style="width: 100%;"></Input-number>
    </Form-item>
    <Form-item>
        <i-button type="primary" @click="submit" icon="edit">
          变更
        </i-button>
    </Form-item>
  </i-form>
  <Modal :visible.sync="show_modal" width="360">
    <p slot="header" style="color:#f60;text-align:center">
        <Icon type="information-circled"></Icon>
        <span>变更确认</span>
    </p>
    <div style="text-align:center">
      <p>是否将<span style="color: #00cc66;">{{price_type_name}}</span>价格类型中的</p>
      <p v-if="storename == ''"><span style="color:#ff9900;">{{product_name}}</span>价格由<span style="color: #3399ff;">{{before_price}}</span>变更为<span style="color:#ff6600">{{price}}</span></p>
      <p v-if="storename != ''"><span style="color:#ff9900;">{{storename}}的{{product_name}}</span>价格由<span style="color: #3399ff;">{{before_price}}</span>变更为<span style="color:#ff6600">{{price}}</span></p>
      <p v-if="storename == ''"><span style="color:red;">该操作将影响该配置中所有的门店对应产品价格。</span></p>
      <p>确定执行变更？</p>
    </div>
    <div slot="footer">
        <i-button type="error" size="large" long :loading="modal_loading" @click="continueChange">确定</i-button>
    </div>
  </Modal>
</div>
<script>
  $$.drawer = $$.vue({
    el:'#v_order_detail',

    data: function(){
      return {
        self: this,
        id: '<?=$__id?>',
        storename: '<?=$__storename?>',
        product_name: '<?=$__product_name?>',
        price_type_name: '<?=$__price_type_name?>',
        before_price: <?=$__price?>,
        price: <?=$__price?>,
        show_modal: false,
        modal_loading: false,
        ruleValidate: {
          price: [
            { required: true, message: '价格不能为空', trigger: 'blur' }
          ]
        }
      }
    },

    _init: function() {
      // this.loadData();
      // alert(roles);
    },

    methods: {
      submit: function() {
        var self = this;
        this.show_modal = true;
      },

      continueChange: function() {
        var self = this;
        this.modal_loading = true;
        var url = "";
        if (self.storename == '') {
          url = '/pricetype/aj_post_config_by_product?id=' + self.id
        } else {
          url = '/pricetype/aj_post_config_by_client?id=' + self.id
        }
        $$.ajax({
          url: url,
          type: 'POST',
          data: {
            val: self.price
          },
          succ: function(data){
            self.modal_loading = false;
            $$.event.pub('CLOSE_DRAWER');
            self.$Message.success('保存成功');
            $$.event.pub('SAVE_PRICE_SUCC');
          },
          fail: function(msg) {
            self.modal_loading = false;
            self.$Message.error(msg);
          }
        })
      }
    }
  })
</script>

