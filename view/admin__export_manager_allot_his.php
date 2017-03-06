<div id="v_manager_his_export">
  <i-form :label-width="80">
    <Form-item label="起始时间" prop="start_time">
      <Date-picker type="date" :value.sync="start_time" placeholder="起始日期" format="yyyy-MM-dd" @on-change="startTimeChange" clearable="false"></Date-picker>
    </Form-item>
    <Form-item label="结束时间" prop="end_time">
      <Date-picker type="date" :value.sync="end_time" placeholder="结束日期" format="yyyy-MM-dd" @on-change="endTimeChange" clearable="false"></Date-picker>
    </Form-item>
    <Form-item>
      <i-button type="primary" @click="submit" icon="printer">
        导出
      </i-button>
    </Form-item>
  </i-form>
  <?=$__type?>
</div>
<script>
  $$.drawer = $$.vue({
    el:'#v_manager_his_export',

    data: function(){
      return {
        self: this,
        start_time: '<?=date('Y-m-d')?>',
        end_time: '<?=date('Y-m-d')?>'
      }
    },

    _init: function() {
      // this.loadData();
      // alert(roles);
    },

    methods: {
      submit: function() {
        var self = this;
        location.href = "/excel/manager_his_export?start_time=" + self.start_time + "&end_time=" + self.end_time;
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
      },
    }
  })
</script>

