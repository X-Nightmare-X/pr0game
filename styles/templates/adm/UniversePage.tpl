{include file="overall_header.tpl"}
<table width="760px">
	<tr>
		<th>{$LNG.uvs_id}</th>
		<th>{$LNG.uvs_name}</th>
		<th colspan="5" title="{$LNG.uvs_speeds_full}">{$LNG.uvs_speeds}</th>
		<th>{$LNG.uvs_players}</th>
		<th>{$LNG.uvs_planets}</th>
		<th>{$LNG.uvs_inactive}</th>
		<th>{$LNG.uvs_open}</th>
		<th>{$LNG.uvs_actions}</th>
	</tr>
	{foreach $uniList as $uniID => $uniRow}
	<tr style="height:23px;">
		<td>{$uniID}</td>
		<td>{number_format($uniRow.uni_name, 0, ",", ".")}</td>
		<td>{number_format(($uniRow.game_speed / 2500), 0, ",", ".")}</td>
		<td>{number_format(($uniRow.fleet_speed / 2500), 0, ",", ".")}</td>
		<td>{number_format($uniRow.resource_multiplier, 0, ",", ".")}</td>
		<td>{number_format($uniRow.halt_speed, 0, ",", ".")}</td>
		<td>{number_format($uniRow.energySpeed, 0, ",", ".")}</td>
		<td>{number_format($uniRow.users_amount, 0, ",", ".")}</td>
		<td>{number_format($uniRow.planet, 0, ",", ".")}</td>
		<td>{number_format($uniRow.inactive, 0, ",", ".")}</td>
		<td>{if $uniRow.game_disable == 1}<span style="color:lime;">{$LNG.uvs_on}</span>{else}<span style="color:red;">{$LNG.uvs_off}</span>{/if}</td>
		<td>{if $uniRow.game_disable == 1}<a href="?page=universe&amp;action=closed&amp;uniID={$uniID}&amp;sid={$SID}&amp;reload=t"><img src="styles/resource/images/icons/closed.png" alt=""></a>{else}<a href="?page=universe&amp;action=open&amp;uniID={$uniID}&amp;sid={$SID}&amp;reload=t"><img src="styles/resource/images/icons/open.png" alt=""></a>{/if}{if $uniID != $smarty.const.ROOT_UNI}<a href="?page=universe&amp;action=delete&amp;uniID={$uniID}&amp;sid={$SID}&amp;reload=t" onclick="return confirm('{$LNG.uvs_delete}');" title="{$LNG.uvs_delete}"><img src="styles/resource/images/false.png" alt=""></a>{/if}</td>
	</tr>
	{/foreach}
	<tr><td colspan="12"><a href="?page=universe&action=create&amp;sid={$SID}&amp;reload=t">{$LNG.uvs_new}</a></td></tr>
</table>
{include file="overall_footer.tpl"}