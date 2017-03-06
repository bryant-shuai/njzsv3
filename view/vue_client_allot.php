<?php
require_once \view('vue_base');
?>

<script type="text/javascript">

var __allot__base = function(){
  return {

      // el: '#v_commonts',
      EVENT:['SAVE_DEPOSIT'],
      props:['url','need_reload','page_length','func_page_changed','manage_','id_','should_reload_', 'total_amount_'],
      props_watch: ['should_reload_','url','need_reload','page_length'],
      data: function () {
        return {
          loadtimes:0,
          loading: false,
          ls: [],
          page:1,
          length:2,
          count:0,
          init_total_amount: 0
        }
      },

      _init: function() {
        var self = this
        if(!this.func_page_changed){
          this.func_page_changed = function(){}
        }
        this.length = this.page_length
        self.goPage(1)
        self.init_total_amount = self.total_amount_;
        // self.init_total_amount = parseFloat(self.init_total_amount)
      },

      _change_should_reload_: function(){
        this.goPage(1)
      },

      _change: function(){
        // console.log('改变');
        // alert('reload chagne')
        this.goPage(1)
      },

      methods: {
        hd_SAVE_DEPOSIT: function() {
          var self = this;
          // console.log('save');
          $$.ajax({
            // method:'post',
            url:'/client/aj_allot_deposit',
            data:{
              ls: $$.js2str(self.ls)
            },
            succ:function(data){
              if (data) {
                window.location.reload();
              }              
            }
          })
        },

        resetState: function(data){
          var self = this
          self.ls = data.ls
          // self.setState({
          //   'ls': data.ls
          // });

          self.extra = data.extra || {}
          self.page = data.page
          self.length = data.length
          self.count = data.count
          self.loading = false
        },

        goPage: function(page){
          var self = this
          
          self.ls = []
          self.loading = true
          self.loadtimes += 1
          $$.ajax({
            url: this.url+'&page='+page+'&count='+this.count+'&length='+this.length+'&',
            succ: function(data){
              self.resetState(data)
            },
          })
        },

        go: function(v){
          alert(v.id)
        },

        reCountDeposit: function(v) {
          v.allot_amount = v.allot_amount.replace(/[^0-9\.]/g,'')
          var self = this;
          var total = self.total_amount_;
          // 重新计算已分配
          var amount = 0.00;
          for(var idx in self.ls) {
            var item = self.ls[idx];
            var count = item['allot_amount'];
            if (count == "") {
              count = 0;
            }
            amount += parseFloat(count);
          }
          self.total_amount_ = self.init_total_amount - amount;
          self.total_amount_ = self.total_amount_.toFixed(2);
        }
      },

  }
} 

</script>

<template id="v_client_allot">

  <div class="content pure-g" style="background:#FFFFFF;border:1px solid #F3F3F3;">
      <div class="pure-u-1 pure-u-sm-24-24">
        
        <v_td 
          val_="1" 
          diff_val_="2" 
          style_="color:#F1F1F1" 
        >
        </v_td>

        <table style="width:100%">
          <tr>
            <th>门店</th>
            <th>余额</th>
            <th>分配金额</th>
          </tr>
          <tr v-if="loading">
            <td colspan="100">加载中</td>
          </tr>
          <tr v-for="v in ls">
            <td>{{v.storename}}</td>
            <td>{{v.deposit}}</td>
            <td>
              <input
                type="text"
                v-model:value="v.allot_amount" 
                style_="color:#F1F1F1"
                @keyup="reCountDeposit(v)"
              >
              </input>
            </td>
          </tr>
        </table>
      </div>
  </div>

</template>


<script type="text/javascript">
$$.comp('v_client_allot', $$.vCopy(__allot__base(),{
  el: '#v_client_allot',
}))
</script>








