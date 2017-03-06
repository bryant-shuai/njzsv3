<?php
include \view('inc_vue_header');
unset($_SESSION['user']);
?>
  <div id="v_login" >
    <div class="example ivu-row">
      <div class="example-demo ivu-col ivu-col-span-12">
        <div class="example-case">
          <h1><a name="top"></a>登录</h1>
            <i-form v-ref:form-inline :model="formInline" :rules="ruleInline" >
              <Form-item prop="user">
                <i-input type="text" :value.sync="formInline.user" placeholder="用户名">
                  <Icon type="ios-person-outline" slot="prepend"></Icon>
                </i-input>
              </Form-item>
              <Form-item prop="password">
                <i-input type="password" :value.sync="formInline.password" placeholder="密码" @keyup.enter="login">
                  <Icon type="ios-locked-outline" slot="prepend"></Icon>
                </i-input>
              </Form-item>
              <Form-item>
                <i-button type="primary" @click="login">登录</i-button>
              </Form-item>
            </i-form>
        </div>
      </div>
      <div class="example-split"></div>
  </div>
  <script>
    $$.vue({
      el:'#v_login',
      data: function(){
        return {
          formInline: {
            user: '',
            password: ''
          },
          ruleInline: {
            user: [
              { required: true, message: '请填写用户名', trigger: 'blur' }
            ],
            password: [
              { required: true, message: '请填写密码', trigger: 'blur' }
            ]
          }
        }
      },

      _init: function() {


      },


      methods: {

        login: function() {
          var self = this;
          this.$refs['formInline'].validate((valid) => {
            if (valid) {
              self.doLogin();
              console.log(self.formItem);
            }
          })
        },

        doLogin: function() {
          var self = this;
          this.$Loading.start();
          $$.ajax({
            url: '/admin/aj_login',
            type: 'POST',
            data: {
              name: self.formInline.user,
              password: self.formInline.password
            },
            succ: function(data){
              self.$Loading.finish();
              location.href = "/";
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
