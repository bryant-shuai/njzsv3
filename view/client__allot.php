<?php
include \view('inc_home_header');
include \view('vue_client_allot');
include \view('vue_batch');
include \view('vue_searcher');
?>




<div id="v_home" v-choak class="container pure-g" style="padding-top:0px;background:-red;height:100%;">
  <div class="pure-u-1 " style=";position:relative;">
    <h2>余额详情<b style="padding-left: 10px;">可分配金额：￥<small v-bind:class="parseFloat(total_amount) >= 0 ?'':'v_less'">{{total_amount}}</small></b></h2>
    <v_client_allot url="/client/aj_clients_by_manager?" need_reload="false" page_length="10000" v-bind:func_page_changed="FuncPageChanged" :total_amount_.sync="total_amount"></v_client_allot>
    <button @click="onClickSave">保存</button>
  </div>
    

</div>
    


<div style="clear:both;height:60px;"></div>
    
<script type="text/javascript">
  var v_home = $$.vue({
    el: '#v_home',
    data: function(){
      return {
        total_amount: '<?=$__balance?>',
        ls:[],
      }
    },

    _init:function(){
      var self = this
      self.total_amount = parseFloat(self.total_amount);
    },

    methods: {

      'onClickSave': function() {
        var self = this;
        if (parseFloat(self.total_amount) < 0) {
          alert("余额不足，请确认");
          return;
        }

        $$.event.pub('SAVE_DEPOSIT');
      }
    },
  })
</script>

<?php
include \view('inc_home_footer');
?>