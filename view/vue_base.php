<?php
?>

<style type="text/css">
  .v_select__on {
    background: #DDD;
  }

  .v_option_more {
    background: blue;
    color:#FFF;
    padding:0 2px 0 2px;
  }
  .v_option_less {
    background: red;
    color:#FFF;
    padding:0 2px 0 2px;
  }
</style>

<template id="v_num">
  <span v-bind:class="'v_option_'+diff_result" v-bind:style="style_">
    {{diff_num_}}
  </span>
</template>


<script type="text/javascript">
$$.comp('v_num',{

  el: '#v_num',

  props:['val_','diff_val_','style_'],
  props_watch:[],
  props_ext:[],
  props_watch_ext:[],

  data: function(){
    return {
      diff_result: '', 
      diff_num_:null,
    }
  },

  _init: function() {
    var self = this
    window.setTimeout(function(){
      self.diffchange()
    },1)
  },

  methods: {

    diffchange: function() {
      var _old = parseFloat(this.val_)
      var _new = parseFloat(this.diff_val_)
      var _diff = _new - _old 
      if(_diff==0){

      }else{
        this.diff_num_ = _diff.toFixed(2)

        if(_diff > 0){
          this.diff_result = 'more'
        }else {
          this.diff_result = 'less'
        }
      } 

    },

  },

})
</script>



















<style type="text/css">
  .v_cell__succ {
    background: green;
    color:#FFF;
    padding:0 2px 0 2px;
  }
  .v_cell__waiting {
    background: gray;
    color:#FFF;
    padding:0 2px 0 2px;
  }
  .v_cell__changed {
    background: yellow;
    color:#000;
    padding:0 2px 0 2px;
  }
  .v_cell__fail {
    background: red;
    color:#FFF;
    padding:0 2px 0 2px;
  }
</style>

<template id="v_cell">
  <div v-bind:style="style_">
    <input type="text" 
      v-model="newval" 
      v-on:keyup.enter="submit"
      v-bind:style="input_style_"
      v-bind:class="'v_cell__'+sync_status"
    />
  </div>
</template>


<script type="text/javascript">
$$.comp('v_cell',{

  el: '#v_cell',

  props:['val_','url_get_','url_post_','input_style_'],

  data: function(){
    return {
      val    : null, 
      newval : 'xx',
      url_get : null,
      url_post : null,
      diff_result: '',
      sync_status:'',
    }
  },

  _change: function() {
    // this.props_change()
    this.loadData()
  },

  _init: function() {
    this.loadData()
  },

  methods: {

    loadData: function(){
      var self = this
      if( this.url_get_ ){
        $$.ajax({
          url: this.url_get_,
          succ: function(data){
            self.val_ = data.price
            self.props_change()
          },
          fail: function(){

          },
        })
      }else{
        this.props_change()
      }
    },

    props_change: function() {
      var self = this
      self.val = self.val_
      self.newval = self.val_
    },

    submit: function(){
      var self = this
      self.sync_status = 'waiting'
      $$.ajax({
        type:'POST',
        url: self.url_post_,
        data: {val:self.newval },
        succ: function(data){
          // alert($$.js2str(data))
          self.sync_status = 'succ'
        },
        fail: function(msg){
          // alert(msg)
          self.sync_status = 'fail'
        },
      })
    },

  },

  watch: {
    'newval' : function(val){
      var self = this
      if(self.newval!=self.val){
        self.sync_status = 'changed'
      }
    }
  },

})
</script>

