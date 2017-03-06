<?php
include \view('inc_home_header');
include \view('vue_sortarea_main');
include \view('vue_sortarea_detail');
?>

<script type="text/javascript" src="https://code.jquery.com/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="http://borayazilim.com/projects/rowsorter/dist/RowSorter.js"></script>


<style>

table td {background-color: #ddd; padding: 5px 8px;}

table.sorting-table {cursor: move;}
table tr.sorting-row td {background-color: #8b8;}
</style>

<div id="v_sortarea__index" v-cloak  style="height:100%;overflow-x:scroll;white-space:nowrap;">

<style type="text/css">
  div .area{
    width:120px;
    height:100px;
    border: 1px solid #000000;
    background: #F1F1F1;
    float: left;
    clear: none;
    margin:10px;
    cursor:pointer;
    position: relative;
    border-radius: 5px;
    box-shadow:0 0 10px #000000;
  }

  .area:hover{
    background:#444444; 
    color:#FFF;
    border:1px solid #444444;
    box-shadow:0 0 10px #444444;
    transition:0.4s; 
  }

  .area_text{
    font-size: 20px;
    position: absolute;
    top:25%;
    margin-left: 3%;
  }

  .not_area_text{
    font-size: 20px;
    position: absolute;
    top:25%;
    margin-left: 25%;
  }
/*  div .area_selected{
    background: #AAA;
  }*/
</style>
    
<div id="sort_areas_chooser" style="display:none;">
  <div style="background:#FFF;">
    <div class="area" onClick="chooseSortArea(0)">
      <span class="not_area_text">未分区</span>
    </div>
    <div 
      v-bind:id="'sort_areas_chooser_item_'+v.id" v-for="v in ls" class="area"
      v-bind:onClick="'chooseSortArea('+v.id+')'"
    >
      <span class="area_text">{{v.area_name}}</span>
    </div>
  </div>
</div>
        

            <div style="-border:1px solid red;height:100%;display:inline-block;max-height:100%">
              <v_sortarea_main
                 v-bind:should_reload_="should_reload" 
                 v-bind:select_type_="select_type"
                 v-bind:url_="'/sort_area/partition'"
                 />
            </div>

            <div style="-border:1px solid red;display:inline-block;height:100%;"> 
              <div style="width:100%;background:#FFFFFF;" ><h2>未分区店面</h2>
              </div>
              <vue_sortarea_detail_null 
               v-bind:url_="'/sort_area/unclassified'"
               v-bind:ls_="ls"
               event_key_="CLIENTADD_SORTAREA"
               v-bind:should_reload_="should_reload"
                v-bind:sortarea_id_="v.id"
                v-bind:from_sortarea_id_="from_sortarea_id"
                v-bind:baseidx_="null_base_idx"
              />
            </div>

  
            <div v-for="v in ls"  style="display:inline-block;margin: auto 4px 0 0;height:100%;">
                <div style="background:#FFFFFF;">
                  <h2>{{v.area_name}}</h2>
                </div>

                <vue_sortarea_detail 
                  v-bind:sortarea_id_="v.id"
                  v-bind:url_="'/sort_area/get_client_by_sortarea?id='+v.id"
                  v-bind:should_reload_="should_reload"
                  v-bind:from_sortarea_id_="from_sortarea_id"
                  v-bind:ls_="ls"
                  v-bind:baseidx_="$index"
                  event_key_="CLIENTADD_SORTAREA"
                />
            </div>

</div>
<script type="text/javascript">
$(function(){
  // alert(window.innerHeight)
  // $('.autoheight').css({
  //   height: (window.innerHeight-150)+'px',
  // })
})


var v_sortarea__index = $$.vue({
  el: '#v_sortarea__index',
  EVENT: ['CLIENTADD_SORTAREA'],
  _init: function(){
    this.loadData()
  },
  data: function(){
    return {
      ls: [],
      should_reload:false,
      from_sortarea_id:0,
      null_base_idx: <?=$__areas_count?>,
    }
  },
  methods: {
    hd_CLIENTADD_SORTAREA:function(v){
      var self = this
      var from_id = v.from_sortarea_id
      if(!v.from_sortarea_id){
        from_id = 0        
      }
      var to_id = v.sortarea_id
      $$
        .then($$.wait({
          url:'/sort_area/client_add_sort_area?id='+v.sortarea_id+'&client_id='+v.client_id+'&from_sortarea_id='+from_id,
          succ: function(data, cont){
            cont(null)
          },
        }))
        .then(function(cont){
          $$.event.pub('CLIENT_SORTAREA_MOVE_SUCC',{from_id:from_id,to_id:to_id})
          $$.event.pub('CLOSE_DRAWER')

        })
    },
    loadData: function(){
      var self = this
      $$.ajax({
        url: '/sort_area/partition',
        succ: function(data){
          self.ls = data.ls
          self.should_reload = $$.getTime()
        },
      })
    },

  },
})


var chooseSortArea= function(id){
  var event = {
    client_id:$$.data.SELECT_CLIENT,
    sortarea_id:id,
    from_sortarea_id:$$.data.SELECT_CLIENT_FROM,
  }
  $$.event.pub('CLIENTADD_SORTAREA',event)

}


</script>
<?php
include \view('inc_home_footer');
?>