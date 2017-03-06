<div id="v_admin_users_detail">
  <i-form v-ref:form-validate :model="formItem" :rules="ruleValidate" :label-width="80">
    <Form-item label="变更类型">
      <i-select :model.sync="formItem.change_type" placeholder="请选择变更类型">
        <i-option v-for="item in change_types" :value="item.id">{{ item.name }}</i-option>
      </i-select>
    </Form-item>
    <Form-item label="变更金额" prop="amount">
      <i-input :value.sync="formItem.amount" placeholder="请输入变更金额"></i-input>
    </Form-item>
    <Form-item label="备注">
      <i-input :value.sync="formItem.remark" type="textarea" :autosize="{minRows: 2,maxRows: 5}" placeholder="请输入..."></i-input>
    </Form-item>
    <Form-item>
      <i-button type="primary" @click="submit" icon="checkmark-round">
        保存
      </i-button>
    </Form-item>
  </i-form>
  
  <Modal :visible.sync="show_modal" width="360">
    <p slot="header" style="color:#f60;text-align:center">
        <Icon type="information-circled"></Icon>
        <span>变更确认</span>
    </p>
    <div style="text-align:center">
      <p>是否将<span style="color: #00cc66;">{{client_name}}</span>的余额</p>
      <p>执行变更<span style="color:#ff6600">{{formItem.amount}}</span>元</p>
      <p>备注为<span style="color:#ff9900">{{formItem.remark}}</span></p>
      <p>确定执行操作？</p>
    </div>
    <div slot="footer">
        <i-button type="error" size="large" long :loading="modal_loading" @click="saveData">确定</i-button>
    </div>
  </Modal>
</div>
<script>
  $$.drawer = $$.vue({
    el:'#v_admin_users_detail',

    data: function(){
      return {
        self: this,
        client_id: '<?=$__id?>',
        client_name: '<?=$__client_name?>',
        formItem: {
          change_type: 0,
          amount: '',
          remark: '',
        },
        show_modal: false,
        modal_loading: false,
        change_types: [
          {id: 0, name: '直接变更'},
          {id: 1, name: '需确认变更'}
        ],
        ruleValidate: {
          amount: [
            { required: true, message: '变更金额不能为空', trigger: 'blur' }
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
        this.$refs['formValidate'].validate((valid) => {
          if (valid) {
            self.show_modal = true;
          }
        })
      },

      saveData: function() {
        var url = "";
        var item = this.formItem;
        if (item.change_type == 0) {
          if (parseFloat(item.amount) >= 0) {
            url = '/finance/top_up_money?client_id='+this.client_id+'&amount='+item.amount+'&remark='+item.remark;
          } else {
            url = '/finance/deduct_money?client_id='+this.client_id+'&amount='+item.amount+'&remark='+item.remark;
          }
        } else if (item.change_type == 1) {
          url ='/finance/aj_add_return_back?client_id=' + this.client_id + "&finance=" + item.amount + "&remark=" + item.remark;
        }

        var self = this;
        this.$Loading.start();
        $$.ajax({
          url: url,
          data: {
          },
          succ: function(data){
            self.$Loading.finish();
            $$.event.pub('CLOSE_DRAWER');
            self.$Message.success('保存成功');
            $$.event.pub('SAVE_SUCCESS');
            self.modal_loading = false;
          },
          fail: function(msg) {
            self.$Loading.finish();
            self.$Message.error(msg);
            self.modal_loading = false;
          }
        })
      }
    }
  })
</script>

