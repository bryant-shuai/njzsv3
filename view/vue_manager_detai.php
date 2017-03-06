<?php
require_once \view('vue_Datalist');
?>

<template id="v_manager_detail">
	<table width="100%">
		<tr style="height:60px;">
			<th style="width:30%;text-align:center;font-size:16px">门店</th>
			<th style="width:30%;text-align:center;font-size:16px">代理人名称</th>
			<th style="width:30%;text-align:center;font-size:16px">余额</th>
		</tr>

		<tr v-if="loading" style="height:60px;">
			<td colspan="10" style="font-size:16px">Loading...</td>
		</tr>

		<tr style="height:50px" v-for="(idx,v) in ls">
			<td style="text-align:center;font-size:14px">{{v.storename}}</td>
			<td style="text-align:center;font-size:14px">{{v.manager_name}}</td>
			<td style="text-align:center;font-size:14px">{{v.deposit}}</td>
		</tr>
	</table>
</template>

<script type="text/javascript">
	$$.comp('v_manager_detail',$$.vCopy(vue__Datalist(),{
		el:'#v_manager_detail',
	}))
</script>


















<style type="text/css">
	.update_option{
		font-size:14px;
		padding:7px 25px;
		border-radius:5px;
		color:#FFF;
		background:red;
		cursor:pointer;
	}

	.update_option_confirm{
		font-size:14px;
		padding:7px 10px;
		border-radius:5px;
		color:red;
		border:1px solid red;
		margin-left: 10px;
		cursor:pointer;
	}

	.update_option_log{
		font-size:14px;
		padding:7px 15px;
		border-radius:5px;
		color:#FFF;
		background:#00CED1;
		cursor:pointer;
		margin-left: 10px;
	}
</style>