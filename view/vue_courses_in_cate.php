
<!-- 子组件模板 -->
<template id="v-courses-in-cate">
  <div v-if="!loading" v-for="course in ls" class="pure-u-1-2 pure-u-sm-6-24 course-item">
    <a href="/course/detail?id={{course.id}}" target="_blank">
      <div class="pure-g">
        <div class="pure-u-23-24 o-hide">
          <div class="o-hide p-rl" style="position:relative;">
            <div class="p-image-frame" v-bind:style="'background-image: url(\''+course.pic+'\');'">
            </div>
            <div class="post-title">{{course.name}}</div>
          </div>
        </div>
      </div>
    </a>
  </div>

  <div v-if="loading">
    <div class="pure-g">
      <div class="pure-u-1-2">
        loading...
      </div>
    </div>
  </div>

</template>



<script type="text/javascript">
  $$.comp('v-courses-in-cate', {
    el: '#v-courses-in-cate',
    props_ext: ['cate_str'],
    data: function () {
      return {
        loading: false,
        ls:[],
      }
    },
    _init: function(){
      var self = this
      self.loadData()
    },

    watch: {
      'cate_str': function(val,old){
        this.loadData()
      }
    },

    methods: {
      loadData: function(){
        var self = this
        self.loading = true
        $$.ajax({
          url:'/course/aj_in_cate?cate_str='+self.cate_str,
          succ:function(data){
            console.log(data);
            window.setTimeout(function(){
              self.loading = false
              self.ls = data.ls
            },0)
          }
        })

      },

    },

  }) 
</script>

