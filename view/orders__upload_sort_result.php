<?php
include \view('inc_vue_header');
?>
  <style>
  .ivu-table-body {
      overflow: hidden;
  }
  </style>
  <div id="v_orders_export" >
    <div class="example ivu-row">
      <div class="example-demo ivu-col ivu-col-span-24" style="position:relative;">
        <div class="example-case">
          <h1><a name="top"></a>上传分拣结果</h1>
          <Upload
            show-upload-list="false"
            type="drag"
            :on-success="uploaded"
            action="/orders/aj_upload_sort_result">
            <div style="padding: 20px 0">
              <Icon type="ios-cloud-upload" size="52" style="color: #3399ff"></Icon>
              <p>点击或将文件拖拽到这里上传</p>
            </div>
          </Upload>
          <i-table style="margin-top: 10px;" :content="self" :data="data" :columns="columns" stripe></i-table>
        </div>
      </div>
    </div>
  </div>
  <script>
    $$.vue({
      el:'#v_orders_export',

      data: function(){
        return {
          self: this,
          data: <?=$__sort_files?>,
          columns: [
              {
                title: '文件名',
                key: 'file_name'
              },
              {
                title: '上传时间',
                key: 'create_at'
              }
          ],
        }
      },

      _init: function() {
        // this.loadData();
      },


      methods: {
        loadData: function(idx) {
          var self = this;
          self.$Loading.start();
          $$.ajax({
            url: '/orders/aj_sort_file_list',
            data: {
            },
            succ: function(data){
              self.data = data;
              self.$Loading.finish();
            }
          })
        },

        uploaded: function(res) {
          if (res.code == -1) {
            this.$Notice.warning({
              title: res.msg,
              desc: ''
            });
            return;
          }

         this.$Notice.success({
            title: '上传成功',
            desc: ''
          });
         this.loadData();
        } 
      }
    })
  </script>

<?php
include \view('inc_vue_footer');
