<div id="v_orders_create_batch">
  <i-form :label-width="80">
    <Form-item label="分区名称">
      <i-input type="text" :value.sync="val" placeholder="分区名称">
      </i-input>
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
        val: '',
        factory_id: '<?=$_SESSION["user"]["factory_id"]?>',
        f_factory_id: '<?=$_SESSION["user"]["factory_id"]?>',
        factory_ids: <?=$__factory_ids?>,
      }
    },

    _init: function() {
    },

    methods: {

      saveData: function() {
        if (this.val == "") {
          this.$Message.error("名称不能为空");
          return;
        }
        var self = this;
        this.$Loading.start();
        $$.ajax({
          url: '/client/aj_save_area',
          data: {
            area_name: self.val,
            factory_id: self.f_factory_id
          },
          succ: function(data){
            self.$Loading.finish();
            $$.event.pub('CLOSE_DRAWER');
            self.$Message.success('保存成功');
            $$.event.pub('SAVE_AREA_SUCC');
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

