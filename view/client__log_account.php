<div id="v_log_account">
  <i-form inline style="width: 500px; ">
    <Form-item label="起始时间">
      <Date-picker type="date" :value.sync="start_time" placeholder="起始时间" style="width: 120px" format="yyyy-MM-dd" @on-change="startTimeChange" clearable="false"></Date-picker>
    </Form-item >
    <Form-item label="结束时间">
    <Date-picker type="date" :value.sync="end_time" placeholder="结束时间" style="width: 120px" format="yyyy-MM-dd" @on-change="endTimeChange" clearable="false"></Date-picker>
    </Form-item>
  </i-form>
  
  <i-table :columns.sync="columns" :data="data" ></i-table>
  <div style="margin: 10px;overflow: hidden">
    <div style="float: right;">
      <Page :total="total" :current.sync="current" @on-change="loadData"></Page>
    </div>
  </div>
  <Spin size="large" fix v-if="loading"></Spin>
</div>
<script>
  $$.drawer = $$.vue({
    el:'#v_log_account',

    data: function(){
      return {
        self: this,
        id: '<?=$__id?>',
        start_time: "<?=date('Y-m-d')?>",
        end_time: "<?=date('Y-m-d')?>",
        data: [],
        total: 0,
        current: 1,
        loading: false,
        columns: [
          {
            title: '操作人',
            key: 'operator',
            width: 80
          },
          {
            title: '客户',
            key: 'client_name',
            width: 100
          },
          {
            title: '金额变化',
            key: 'amount',
            width: 90
          },
          {
            title: '变化内容',
            key: 'extra'
          },
          {
            title: '变动时间',
            key: 'date_time',
            width: 150
          }
        ],
      }
    },

    _init: function() {
      this.loadData();
      // alert(roles);
    },

    methods: {
      loadData: function() {
        var self = this;
        self.loading = true;
        $$.ajax({
          url: '/log_account/search_log_by_time',
          data: {
            page: self.current,
            length: 10,
            start_time: self.start_time,
            end_time: self.end_time,
            client_id: self.id,
          },
          succ: function(data){
            self.data = data.ls;
            self.total = data.count;
            self.loading = false;
          },
        })
      },
      submit: function() {
        var self = this;
        this.$refs['formValidate'].validate((valid) => {
          if (valid) {
            self.saveData();
          }
        })
      },

      saveData: function() {
        var self = this;
        this.$Loading.start();
        $$.ajax({
          url: '/admin/aj_user_detail',
          data: {
            id: self.id,
            formItem: self.formItem,
            type: self.type
          },
          succ: function(data){
            self.$Loading.finish();
            $$.event.pub('CLOSE_DRAWER');
            self.$Message.success('保存成功');
            $$.event.pub('SAVE_SUCCESS');
          },
          fail: function(msg) {
            self.$Loading.finish();
            self.$Message.error(msg);
          }
        })
      },

      startTimeChange: function() {
        if (this.start_time != '') {
          var dt = new Date(this.start_time);
          var month = (dt.getMonth()+1)
          if (parseInt(month) < 10) {
            month = "0" + month;
          }
          var day = dt.getDate()
          if (parseInt(day) < 10) {
            day = "0" + day;
          }
          this.start_time = dt.getFullYear()+'-'+month+'-'+day;
        }
        this.loadData();
      },

      endTimeChange: function() {
        if (this.end_time != '') {
          var dt = new Date(this.end_time);
          var month = (dt.getMonth()+1)
          if (parseInt(month) < 10) {
            month = "0" + month;
          }
          var day = dt.getDate()
          if (parseInt(day) < 10) {
            day = "0" + day;
          }
          this.end_time = dt.getFullYear()+'-'+month+'-'+day;
        }
        this.loadData();
      },
    }
  })
</script>

