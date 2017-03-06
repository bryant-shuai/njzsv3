<script type="text/javascript">
  var _vue_loadData = function(){
    return {
      el:'',
      props:['event_key_','should_reload_','url_','save_url_','remove_url_','table_head_','type_name_'],
      props_watch:['should_reload_','url_'],
      data: function(){
        return {
          ls: [],
          loading: false,
          key:null,
          type_name:'',
        }
      },
      _init: function(){
        var self = this
        self.loadData()
      },
      _change: function(){
        var self = this
        self.loadData()
      },
      methods: {
        save: function(){
          var self = this
          $$.ajax({
            method:"get",
            url:self.save_url_,
            data:{
              name:self.type_name
            },
            succ: function(data){
              self.type_name = ''
              self.should_reload_ = $$.getTime()
            },
          })
        },
        remove: function(id) {
          var self = this
          if (confirm('确认删除?')){
              $$.ajax({
              method:'get',
              url:self.remove_url_,
              data:{
                id:id
              },
              succ: function(data){
                self.should_reload_ = $$.getTime()
              },
            })
          } 
        },

        loadData: function(){
          var self = this
          self.loading = true
          $$
            .then(
              $$.wait({
                url: self.url_,
                succ: function(data, cont){
                  self.ls = data.ls
                  cont(null)
                },
              })
            )
            .then(function(cont){
              self.loading = false
              self.search_result = $$.getTime()
            })
        },
      },
      watch: {
        'key': function(val){
          var self = this
          // console.log('key:'+val)
          self.search()
        },
      },
    }
  }
 </script>
 <style type="text/css">
  .clickstyle{
    background:#999;
  }
 </style>

<template id="v_product_type">
        <div style="width:100%;background:#FFFFFF;">
              <h2>{{table_head_}}</h2>
        </div>

        <table style="width:80%">
          <tr>
            <th style="text-align:center">{{type_name_}}</th>
            <th style="text-align:center">操作</th>
          </tr>
          <tr v-if="loading">
            <td colspan="100">加载中</td>
          </tr>
          <tr>

            <td style="margin:0;padding:2px 2px 2px 3px;text-align:center"><input style="text-align:center" type="text" v-model="type_name"  placeholder="请输入新类别"></td>
            <td @click="save" style="text-align:center"><a><nobr>保存</nobr></a></td>
          </tr>
          <tr v-for="v in ls">
            <td style="text-align:center">{{v.name}}</td>
            <a>
            <td style="text-align:center "@click="remove(v.id)"><a><nobr>删除</nobr></a></td>
            </a>
          </tr>
        </table>

</template>


<!-- 产品所属类别 -->
<script type="text/javascript">
  $$.comp('v_product_type',$$.vCopy(_vue_loadData(),{
    el:'#v_product_type',
  }))
</script>




<!-- 产品分拣类型 -->
<script type="text/javascript">
  $$.comp('v_product_sort',$$.vCopy(_vue_loadData(),{
    el:'#v_product_type',
  }))
</script>


