<?php
  include \view('inc_home_header');
?> 


<div id="add_manager" style="width:100%;height:100%;position:fixed">
      <div class="login_div">
        <span class="admin_log_text">添加代理人帐号</span>


          <div class="log_div_input">
            <div>
              <div class="text_div">用户名:</div>
              <input id="user_name" class="log_input" type="text" v-model="name">
            </div>

            <div>
              <div class="text_div">设置密码:</div>
              <input id="password" class="log_input" type="password" v-model="password" >
            </div>

            <div>
              <div class="text_div">确认密码:</div>
              <input id="comfirm_password" class="log_input" type="password" v-model="comfirm_password" >
            </div>
          </div>
 
          


          <span id="cancel_submit" class="cancel_button transition" @click="saveData" >
            保存
          </span>


      </div>
    </div>

    <script type="text/javascript">
      $$.vue({
        el:'#add_manager',
        data:function(){
          return {
            name:'',
            password:'',
            comfirm_password:'',
          }
        },

        methods:{
          saveData:function(){
            var self = this
            $$.ajax({
              url:'/manager/add_manager',
              data:{
                name:self.name,
                password:self.password,
                comfirm_password:self.comfirm_password,
              },
              succ:function(data){
                alert("添加成功")
                self.name = ''
                self.password = ''
                self.comfirm_password = ''
              }
            })
          },
        },
      });
    </script>
    
    <style type="text/css">
      .transition{
        transition:0.3s;
      }

      .log_button{
        border: 1px solid #000000;
        position: absolute;
        top:70%;
        left:22%;
        padding:10px 80px 10px 80px;
        cursor:pointer;
        font-size:20px;
      }

      .cancel_button:hover{
        background:#999;
        border:1px solid #999;
        color: #FFF;
      }

      .cancel_button{
        border: 1px solid #000000;
        position: absolute;
        top:80%;
        left:23%;
        padding:10px 200px 10px 200px;
        cursor:pointer;
        font-size:20px;
        white-space:nowrap;
      }

      .log_button:hover{
        border:1px solid #000000;
        background:#000000;
        color: #FFF;
      }


      .log_input{
        width: 330px;
        height:24px;
        border:1px solid #000000;
        font-size:14px; 
      }

      .login_div{
        width:800px;
        height: 450px;
        border:1px solid #CDCDCD;
        position:relative;
        left:50%;
        top:48%;
        margin-left:-400px;
        margin-top: -300px;
        box-shadow: 0px 0px 20px #CDCDCD;
      }

      .admin_log_text{
        font-size: 30px;
        font-weight:bold;
        padding: 20px 120px 20px 120px;
        background: #CDCDCD;
        border-radius:5px;
        position: absolute;
        top:15%;
        left:22%;
      }

      .log_div_input{
        position: absolute;
        top: 40%;
        left: 23%
      }

      .text_div{
        width: 100px;
        height: 30px;
        display: inline-block;
        font-size: 22px; 
        margin-top:15px;
      } 
    </style>