<?php
include \view('inc_vue_header');
?>
  <div id="v_change_pwd" >
    <div class="example ivu-row">
      <div class="example-demo ivu-col ivu-col-span-12">
        <div class="example-case">
          <h1><a name="top"></a>修改密码</h1>
            <i-form >
              <Form-item>
                <i-input type="password" :value.sync="password" placeholder="新密码">
                  <Icon type="ios-locked-outline" slot="prepend"></Icon>
                </i-input>
              </Form-item>
              <Form-item>
                <i-input type="password" :value.sync="s_password" placeholder="确认密码">
                  <Icon type="ios-locked-outline" slot="prepend"></Icon>
                </i-input>
              </Form-item>
              <Form-item>
                <i-button type="primary" @click="submit">保存</i-button>
              </Form-item>
            </i-form>
        </div>
      </div>
      <div class="example-split"></div>
  </div>
  <script>
    $$.vue({
      el:'#v_change_pwd',
      data: function(){
        return {
          password: '',
          s_password: ''
        }
      },

      _init: function() {


      },


      methods: {

        submit: function() {
          var self = this;
          if (self.password == "") {
            self.$Notice.warning({
              title: '密码不能为空',
              desc: ''
            });
            return;
          }

          if (self.password != self.s_password) {
            self.$Notice.warning({
              title: '两次密码输入不一致',
              desc: ''
            });
            return;
          }


          this.$Loading.start();
          $$.ajax({
            url: '/admin/aj_update_pwd',
            type: 'POST',
            data: {
              password: self.password
            },
            succ: function(data){
              self.$Loading.finish();
              location.href = "/admin/logout";
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
