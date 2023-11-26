{block name="title" prepend}{$LNG.lm_alliance}{/block}
{block name="content"}
<style>
	.tooltip1 {
		position: relative;
		display: inline-block;
		border-bottom: 1px dotted black;
	}

	.tooltip1 .tooltiptext {
		visibility: hidden;
		width: 120px;
		background-color: black;
		color: #fff;
		text-align: center;
		border-radius: 6px;
		padding: 5px 0;
		position: absolute;
		z-index: 1;
		top: 150%;
		left: 50%;
		margin-left: -60px;
	}

	.tooltip1 .tooltiptext::after {
		content: "";
		position: absolute;
		bottom: 100%;
		left: 50%;
		margin-left: -5px;
		border-width: 5px;
		border-style: solid;
		border-color: transparent transparent black transparent;
	}

	.tooltip1:hover .tooltiptext {
		visibility: visible;
	}
</style>
<table class="table519">
<tr>
	<th colspan="3">{$LNG.al_ally_information}</th>
</tr>
{if $ally_image}
<tr>
	<td colspan="3">
		<img class="alliance-image" src="{$ally_image}" alt="{$ally_tag}">
	</td>
</tr>
{/if}
<tr>
	<td style="width:50%" colspan="2">{$LNG.al_ally_info_tag}</td>
	<td style="width:50%">{$ally_tag}</td>
</tr>
<tr>
	<td colspan="2">{$LNG.al_ally_info_name}</td>
	<td>{$ally_name}</td>
</tr>
<tr>
	<td colspan="2">{$LNG.al_ally_info_members}</td>
	<td>{$ally_member_scount} / {$ally_max_members}</td>
</tr>
{if $ally_request}
<tr>
	<td colspan="2">{$LNG.al_request}</td>
	{if $ally_request_min_points}
	<td><a href="game.php?page=alliance&amp;mode=apply&amp;id={$ally_id}">{$LNG.al_click_to_send_request}</a></td>
	{else}
		<td>{$ally_request_min_points_info}
	{/if}
</tr>
{/if}
<tr>
	<td colspan="3" style="height:100px">{if !empty($ally_description)}{$ally_description}{else}{$LNG.al_description_message}{/if}</td>
</tr>
{if $ally_web}
<tr>
	<td colspan="2">{$LNG.al_web_text}</td>
	<td><a href="{$ally_web}">{$ally_web}</a></td>
</tr>
{/if}
{if $diplomaticData}
<tr>
	<th colspan="3">{$LNG.al_diplo}</th>
</tr>
<tr>
	<td colspan="3">
	{if $diplomats}
		<b><u>{$LNG.al_rank_diplo}</u></b><br><br>
		{foreach $diplomats as $diplomat}{$diplomat.username} - {$diplomat.rankName} <a href="#" onclick="return Dialog.PM({$diplomat.id})"><img src="{$dpath}img/m.gif" title="{$LNG.write_message}" alt=""></a><br>{/foreach}
		<br>
	{/if}
	{if $diplomaticData && (!empty($diplomaticData.0) || !empty($diplomaticData.1) || !empty($diplomaticData.2) || !empty($diplomaticData.3) || !empty($diplomaticData.4))}
		{if !empty($diplomaticData.0)}<b><u>{$LNG.al_diplo_level.0}</u></b><br><br>{foreach item=PaktInfo from=$diplomaticData.0}<a href="?page=alliance&mode=info&amp;id={$PaktInfo.1}">{$PaktInfo.0}</a><br>{/foreach}<br>{/if}
		{if !empty($diplomaticData.1)}<b><u>{$LNG.al_diplo_level.1}</u></b><br><br>{foreach item=PaktInfo from=$diplomaticData.1}<a href="?page=alliance&mode=info&amp;id={$PaktInfo.1}">{$PaktInfo.0}</a><br>{/foreach}<br>{/if}
		{if !empty($diplomaticData.2)}<b><u>{$LNG.al_diplo_level.2}</u></b><br><br>{foreach item=PaktInfo from=$diplomaticData.2}<a href="?page=alliance&mode=info&amp;id={$PaktInfo.1}">{$PaktInfo.0}</a><br>{/foreach}<br>{/if}
		{if !empty($diplomaticData.3)}<b><u>{$LNG.al_diplo_level.3}</u></b><br><br>{foreach item=PaktInfo from=$diplomaticData.3}<a href="?page=alliance&mode=info&amp;id={$PaktInfo.1}">{$PaktInfo.0}</a><br>{/foreach}<br>{/if}
		{if !empty($diplomaticData.4)}<b><u>{$LNG.al_diplo_level.4}</u></b><br><br>{foreach item=PaktInfo from=$diplomaticData.4}<a href="?page=alliance&mode=info&amp;id={$PaktInfo.1}">{$PaktInfo.0}</a><br>{/foreach}<br>{/if}
	{else}
		{$LNG.al_no_diplo}
	{/if}
	</td>
</tr>
{/if}
{if !empty($statisticData)}
<tr>
	<th colspan="3">{$LNG.pl_fightstats}</th>
</tr>
<tr>
	{if $show}<td></td>{else}<td><div class="tooltip1"><img src="./styles/resource/images/icons/lock.png" height="10" width="10"><span class="tooltiptext">{$LNG.spytech_playercard.level6}</span></div></td>{/if}
	<td>{$LNG.pl_totalfight}</td>
	<td>{if $show}{number_format($totalfight, 0, ",", ".")}{else}-{/if}</td>
</tr>
<tr>
	{if $show}<td></td>{else}<td><div class="tooltip1"><img src="./styles/resource/images/icons/lock.png" height="10" width="10"><span class="tooltiptext">{$LNG.spytech_playercard.level6}</span></div></td>{/if}
	<td>{$LNG.pl_fightwon}</td>
	<td>{if $show}{number_format($fightwon, 0, ",", ".")} {if $totalfight}({round($fightwon / $totalfight * 100, 2)}%){/if}{else}-{/if}</td>
</tr>
<tr>	
	{if $show}<td></td>{else}<td><div class="tooltip1"><img src="./styles/resource/images/icons/lock.png" height="10" width="10"><span class="tooltiptext">{$LNG.spytech_playercard.level6}</span></div></td>{/if}
	<td>{$LNG.pl_fightlose}</td>
	<td>{if $show}{number_format($fightlose, 0, ",", ".")} {if $totalfight}({round($fightlose / $totalfight * 100, 2)}%){/if}{else}-{/if}</td>
<tr>	
	{if $show}<td></td>{else}<td><div class="tooltip1"><img src="./styles/resource/images/icons/lock.png" height="10" width="10"><span class="tooltiptext">{$LNG.spytech_playercard.level6}</span></div></td>{/if}
	<td>{$LNG.pl_fightdraw}</td>
	<td>{if $show}{number_format($fightdraw, 0, ",", ".")} {if $totalfight}({round($fightdraw / $totalfight * 100, 2)}%){/if}{else}-{/if}</td></tr>
<tr>
	{if $show}<td></td>{else}<td><div class="tooltip1"><img src="./styles/resource/images/icons/lock.png" height="10" width="10"><span class="tooltiptext">{$LNG.spytech_playercard.level6}</span></div></td>{/if}
	<td>{$LNG.pl_unitsshot}</td>
	<td>{if $show}{$unitsshot}{else}-{/if}</td>
</tr>
<tr>
	{if $show}<td></td>{else}<td><div class="tooltip1"><img src="./styles/resource/images/icons/lock.png" height="10" width="10"><span class="tooltiptext">{$LNG.spytech_playercard.level6}</span></div></td>{/if}
	<td>{$LNG.pl_unitslose}</td>
	<td>{if $show}{$unitslose}{else}-{/if}</td>
</tr>
<tr>
	{if $show}<td></td>{else}<td><div class="tooltip1"><img src="./styles/resource/images/icons/lock.png" height="10" width="10"><span class="tooltiptext">{$LNG.spytech_playercard.level6}</span></div></td>{/if}
	<td>{$LNG.pl_dermetal}</td>
	<td>{if $show}{$dermetal}{else}-{/if}</td>
</tr>
<tr>
	{if $show}<td></td>{else}<td><div class="tooltip1"><img src="./styles/resource/images/icons/lock.png" height="10" width="10"><span class="tooltiptext">{$LNG.spytech_playercard.level6}</span></div></td>{/if}
	<td>{$LNG.pl_dercrystal}</td>
	<td>{if $show}{$dercrystal}{else}-{/if}</td>
</tr>
{/if}
</table>
{/block}