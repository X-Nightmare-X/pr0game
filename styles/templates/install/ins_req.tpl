{include file="ins_header.tpl"}
<tr>
	<td class="left">
		<h2>{$LNG.req_head}</h2>
		<p>{$LNG.req_desc}</p>
		<table class="req border">
			<tr>
				<td class="transparent left"><p>{$LNG.req_php_need}</p><p class="desc">{$LNG.req_php_need_desc}</p></td>
				<td class="transparent">{$PHP}</td>
			</tr>
			<tr>
				<td class="transparent left"><p>{$LNG.reg_global_need}</p><p class="desc">{$LNG.reg_global_desc}</p></td>
				<td class="transparent">{$global}</th>
			</tr>
			<tr>
				<td class="transparent left"><p>{$LNG.reg_pdo_active}</p><p class="desc">{$LNG.reg_pdo_desc}</p></td>
				<td class="transparent">{$pdo}</th>
			</tr>
			<tr>
				<td class="transparent left"><p>{$LNG.reg_gd_need}</p><p class="desc">{$LNG.reg_gd_desc}</p></td>
				<td class="transparent">{$gdlib}</td>
			</tr>
			<tr>
				<td class="transparent left"><p>{$LNG.reg_json_need}</p></td>
				<td class="transparent">{$json}</td>
			</tr>
			<tr>
				<td class="transparent left"><p>{$LNG.reg_iniset_need}</p></td>
				<td class="transparent">{$iniset}</td>
			</tr>
			{$dir}
			{$config}
			{$done}
		</table>
	</td>
</tr>
{include file="ins_footer.tpl"}