<script type="text/javascript">
  var __v_need_client_batch__common = function(){
    return {
      props:['product_id_','client_id_','url_add_','url_save_','url_','thedate_','batch_id_'],
      props_watch:['product_id_','client_id_','batch_id_','url_'],

      data: function () {
        return {
          loading: false,
          ls: [],
          key:null,
        }
      },

      _init: function() {
        var self = this
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

            // if(!self.thedate_ || !self.batch_id_){
            //   return ;
            // }else{
              // alert(self.url_)
            // }
            
            $$
              .then(
                $$.wait({
                  // url: '/orders/day_product_need',
                  url: self.url_,
                  // data:{
                  //   product_id: self.product_id_,
                  //   client_id: self.client_id_,
                  //   thedate: self.thedate_,
                  //   batch_id: self.batch_id_,
                  // },
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
            <th style="width:30px;">Id</th>
            <th>门店</th>
            <th>商品</th>
            <th style="width:40px;">数量</th>
          </tr>
          <tr v-if="loading">
            <td colspan="100">加载中</td>
          </tr>

          <tr v-if="!loading" v-for="v in ls">
            <td>{{v.id}}</td>
            <td>{{v.storename}}</td>
            <td>{{v.product_name}}</td>
            <td>

              <v_cell  
                v-bind:url_post_="this.url_save_+'?id='+v.id" 
                event_key_="ORDER_NEED_AMOUNT_SAVED" 
                v-bind:val_="v.need_amount"
                input_style_="width:40px;"
              >
              </v_cell>

            </td>
          </tr>

          
          <tr v-if="ls.length==0 && !loading && client_id_ && product_id_">
            <td colspan="100">
              <input type="text" v-model="need_amount" />
              <button @click="submitAmount">新增</button>
            </td>
          </tr>


        </table>
      </div>
  </div>

</template>

<script type="text/javascript">

$$.comp('v_need_client_batch', $$.vCopy(__v_need_client_batch__common(),{
  el: '#v_need__client',

    // _setup: function(){
    // },

    methods: {
      submitAmount: function(){
          var self = this

          var date = self.thedate_
          date = date.replace(/-/g,'/')
          // alert(date)
          $$.ajax({
            type:'POST',
            url: self.url_add_,
            data: {
              need_amount:self.need_amount,
              thedate:date,
              batch_id:self.batch_id_,
              client_id:self.client_id_,
              product_id:self.product_id_,
            },
            succ: function(data){
              // alert($$.js2str(data))
              self.loadData()
            },
            fail: function(msg){
              alert(msg)
            },
          })
      },
    },
}))
</script>