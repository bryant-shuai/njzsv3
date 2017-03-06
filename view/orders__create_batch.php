<div id="v_orders_create_batch">
  <i-form :label-width="80">
    <Form-item label="日期">
      <Date-picker type="date" :value.sync="thedate" placeholder="选择日期" format="yyyy-MM-dd" @on-change="dateChange" clearable="false"></Date-picker>
    </Form-item>
    <Form-item label="批次">
      <Input-number :min="1" :value.sync="idx" style="width: 100%;"></Input-number>
    </Form-item>
    <Form-item label="所属工厂" v-if="factory_id == 0">
      <i-select :model.sync="f_factory_id" placeholder="请选择所属工厂">
        <i-option v-for="item in factory_ids" :value="$index+''">{{ item }}</i-option>
      </i-select>
    </Form-item>
    <Form-item>
      <i-button type="primary" @click="saveData" icon="checkmark-round">
        保存
      </i-button>
    </Form-item>
  </i-form>
</div>
<script>
  $$.drawer = $$.vue({
    el:'#v_orders_create_batch',

    data: function(){
      return {
        self: this,
        thedate: '<?=date("Y-m-d")?>',
        idx: 1,
        factory_id: '<?=$_SESSION["user"]["factory_id"]?>',
        f_factory_id: '<?=$_SESSION["user"]["factory_id"]?>',
        factory_ids: <?=$__factory_ids?>,
      }
    },

    _init: function() {
    },

    methods: {
      dateChange: function() {
        var dt = new Date(this.thedate);
        var month = (dt.getMonth()+1)
        if (parseInt(month) < 10) {
          month = "0" + month;
        }
        var day = dt.getDate()
        if (parseInt(day) < 10) {
          day = "0" + day;
        }
        this.thedate = dt.getFullYear()+'-'+month+'-'+day;
      },

      saveData: function() {
        var self = this;
        this.$Loading.start();
        $$.ajax({
          url: '/aj_batch/aj_create',
          data: {
            thedate: self.thedate,
            idx: self.idx,
            factory_id: self.f_factory_id
          },
          succ: function(data){
            self.$Loading.finish();
            $$.event.pub('CLOSE_DRAWER');
            self.$Message.success('保存成功');
            $$.event.pub('SAVE_BATCH_SUCC');
          },
          fail: function(msg) {
            self.$Loading.finish();
            self.$Message.error(msg);
          }
        })
      }
    }
  })
</script>

