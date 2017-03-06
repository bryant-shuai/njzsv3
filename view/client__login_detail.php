<div id="v_client_login_detail">
  <i-form v-ref:form-validate :model="formItem" :rules="ruleValidate" :label-width="80">
    <Form-item label="用户名" prop="name">
      <i-input :value.sync="formItem.name" placeholder="请输入用户名"></i-input>
    </Form-item>
    <Form-item label="密码" >
      <i-input type="password" :value.sync="formItem.password" placeholder="请输入密码"></i-input>
    </Form-item>
    <Form-item>
      <i-button type="primary" @click="submit" icon="checkmark-round">
        保存
      </i-button>
    </Form-item>
  </i-form>
</div>
<script>
  $$.drawer = $$.vue({
    el:'#v_client_login_detail',

    data: function(){
      return {
        self: this,
        id: '<?=$__id?>',
        formItem: {
          name: '<?=$__username?>',
          password: ''
        },
        ruleValidate: {
          name: [
            { required: true, message: '用户名不能为空', trigger: 'blur' }
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
            self.saveData();
          }
        })
      },

      saveData: function() {
        var self = this;
        this.$Loading.start();
        $$.ajax({
          url: '/client/aj_update_login',
          data: {
            id: self.id,
            username: self.formItem.name,
            password: self.formItem.password
          },
          succ: function(data){
            self.$Loading.finish();
            $$.event.pub('CLOSE_DRAWER');
            self.$Message.success('保存成功');
            $$.event.pub('SAVE_SUCCESS');
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

