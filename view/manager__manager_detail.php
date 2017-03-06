<?php
require_once \view('inc_home_header');
require_once \view('vue_manager_detai');
?>

<div id="v_finance_by_client" v-choak class="container" style="padding-top:0px;-background:red;height:100%;overflow-y:auto">
	
	<div style="margin-top:5px">
		<v_manager_detail 
		v-bind:url_="url"
		/>
	</div>


</div>

<script type="text/javascript">
	$$.vue({
		el:'#v_finance_by_client',
		data:function(){
			return {
				url:'',
			}
		},

		_init:function(){
			var self = this
			self.url = '/manager/manager_ls?'
		},
		
	})
</script>

<?php
include \view('inc_home_footer');
?>
