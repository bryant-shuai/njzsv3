<div id="v_order_detail">
  <i-form v-ref:form-validate :model="formItem" :rules="ruleValidate" :label-width="80">
    <Form-item label="订单ID" v-if="id != ''">
      <i-input :value="id" disabled></i-input>
    </Form-item>
    <Form-item label="门店">
      <i-input :value="storename" disabled></i-input>
    </Form-item>
    <Form-item label="产品">
      <i-input :value="product_name" disabled></i-input>
    </Form-item>
    <Form-item label="产品单价">
      <i-input :value="price" disabled></i-input>
    </Form-item>
    <Form-item label="订单日期">
      <i-input :value="thedate" disabled></i-input>
    </Form-item>
    <Form-item label="订单批次">
      <i-input :value="batch_id" disabled></i-input>
    </Form-item>
    <Form-item label="需求数量" prop="need_amount">
      <Input-number :min="0" :value.sync="need_amount" style="width: 100%;"></Input-number>
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
      <p>是否将<span style="color: #00cc66;">{{thedate}}&{{batch_id}}</span>批次订单中的</p>
      <p><span style="color:#ff9900;">{{product_name}}</span>需求数量由<span style="color: #3399ff;">{{before_need_amount}}</span>变更为<span style="color:#ff6600">{{need_amount}}</span></p>
      <p v-if="is_direct != ''"><span style="color:red;">该操作将直接进行扣款，请认真核对。</span></p>
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
        id: '<?=$__order["id"]?>',
        client_id: '<?=$__order['client_id']?>',
        storename: '<?=$__order["storename"]?>',
        product_id: '<?=$__order["product_id"]?>',
        product_name: '<?=$__order['product_name']?>',
        price: '<?=$__order['price']?>',
        thedate: '<?=$__thedate?>',
        batch_id: '<?=$__batch_id?>',
        before_need_amount: '<?=$__order['need_amount']?>',
        need_amount: <?=$__order['need_amount']?>,
        is_direct: '<?=$__is_direct?>',
        show_modal: false,
        modal_loading: false,
        ruleValidate: {
          need_amount: [
            { required: true, message: '需求数量不能为空', trigger: 'blur' }
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
        if (this.id == '') {
          this.addNewOrder();
        } else {
          this.saveChangeAmount();
        }
      },

      addNewOrder: function() {
        var self = this;
        this.modal_loading = true;
        // 判断是否直接扣款
        var url = '/order/aj_change_needamount';
        if (self.is_direct != '') {
          url = "/order/aj_change_needamount_direct";
        }
        
        $$.ajax({
          url: url,
          type: 'POST',
          data: {
            need_amount: self.need_amount,
            thedate: self.thedate,
            batch_id: self.batch_id,
            client_id: self.client_id,
            product_id: self.product_id,
          },
          succ: function(data){
            self.data = data;
            self.modal_loading = false;
            $$.event.pub('CLOSE_DRAWER');
            self.$Message.success('保存成功');
            $$.event.pub('SAVE_ORDER_SUCC');
          },
          fail: function(msg) {
            self.modal_loading = false;
            self.$Message.error(msg);
          }
        })
      },

      saveChangeAmount: function() {
        var self = this;
        this.modal_loading = true;
        // 判断是否直接扣款
        var url = '/order/aj_change_needamount_byid?id='+self.id;
        if (self.is_direct != '') {
          url = "/order/aj_change_needamount_direct_byid?id=" + self.id;
        }
        $$.ajax({
          url: url,
          type: 'POST',
          data: {
            val: self.need_amount
          },
          succ: function(data){
            self.data = data;
            self.modal_loading = false;
            $$.event.pub('CLOSE_DRAWER');
            self.$Message.success('保存成功');
            $$.event.pub('SAVE_ORDER_SUCC');
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

