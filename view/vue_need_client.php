<script type="text/javascript">
  var __v_need_client__common = function(){
    return {
      // 直接传递url_ 在组件实例中传递
      props:['product_id_','client_id_','url_','manage_'],
      // 监听这两个数据是否变化，发生变化就自动调用_change方法
      props_watch:['product_id_','client_id_'],

      data: function () {
        return {
          loading: false,
          ls: [],
          key:null,
        }
      },

      _init: function() {
        var self = this
        self.loading = true
        self.loadData()
      },
      _change: function() {
        var self = this
        self.loadData()
      },

      methods: {
        loadData: function(){
        var self = this
        self.loading = true
        // alert(self.url_)
        
        $$
          .then(
            $$.wait({
              url: '/orders/day_product_need',
              // url: self.url_,
              data:{
                product_id: self.product_id_,
                client_id: self.client_id_,
              },
              succ: function(data, cont){
                // alert($$.js2str(data))
                self.ls = data.ls
                cont(null)
              },
            })
          )
          .then(function(cont){
            self.loading = false
          })
        },

      },
    }
  }

</script>


<template id="v_need__client">
  <div class="content pure-g" style="background:#FFFFFF;border:1px solid #F3F3F3;">
      <div class="pure-u-1 pure-u-sm-24-24">

        <table width="100%">
          <tr>
            <th>门店</th>
            <td style="position:relative;">
              <input type="text"/>
            </td>
          </tr>
        </table>

        <table width="100%">
          <tr>
            <th style="width:30px;">Id</th>
            <th>门店</th>
            <th>商品</th>
            <th style="width:40px;">数量</th>
            <th style="width:40px;">出入</th>

          </tr>
          <tr v-if="loading">
            <td colspan="100">加载中</td>
          </tr>
          <tr v-for="v in ls">
            <td>{{v.id}}</td>
            <td>{{v.storename}}</td>
            <td>{{v.product_name}}</td>
            <td>{{v.need_amount}}</td>
            <td>
<!--               <v_num 
                v-bind:diff_val_="v.send_amount" 
                v-bind:val_="v.need_amount" 
                style_="color:#F1F1F1" 
              >
              </v_num> -->

              <slot></slot>

              <partial v-if="manage_" v-bind:name="manage_"></partial>
            </td>
          </tr>
        </table>
      </div>
  </div>

</template>

<script type="text/javascript">

$$.part('v_p__order_uncomplete_move', '<br><a v-bind:id="this.client_id_+\'_id_\'+v.id" @click="move(v,$index)" v-if=true >移动 {{batch_id_to_}} </a>')

$$.comp('v_need_client', $$.vCopy(__v_need_client__common(),{
  el: '#v_need__client',

    _setup: function(){
    this.manage_ = 'v_p__order_uncomplete_move'
  },
}))
</script>