{block name="title" prepend}{$LNG.lm_alliance}{/block}
{block name="content"}
<table class="table519">
	<tr>
		<th colspan="2">{$LNG.al_your_ally}</th>
	</tr>
	{if $ally_image}
	<tr>
		<td colspan="2"><img class="alliance-image" src="{$ally_image}"></td>
	</tr>
	{/if}
	<tr>
		<td style="width:50%">{$LNG.al_ally_info_tag}</td>
		<td style="width:50%">{$ally_tag}</td>
	</tr>
	<tr>
		<td>{$LNG.al_ally_info_name}</td>
		<td>{$ally_name}</td>
	</tr>
	<tr>
		<td>{$LNG.al_ally_info_members}</td>
		<td>{$ally_members} / {$ally_max_members}{if $rights.MEMBERLIST} (<a href="?page=alliance&amp;mode=memberList">{$LNG.al_user_list}</a>){/if}</td>
	</tr>
	<tr>
		<td>{$LNG.al_rank}</td>
		<td>{$rankName}{if $rights.ADMIN} (<a href="?page=alliance&amp;mode=admin">{$LNG.al_manage_alliance}</a>){/if}</td>
	</tr>
	{if $rights.SEEAPPLY && $applyCount > 0}
	<tr>
		<td>{$LNG.al_requests}</td><td><a href="?page=alliance&amp;mode=admin&amp;action=manageApply">{$requests}</a></td>
	</tr>
	{/if}
	{if $rights.DIPLOMATIC && ($diploCountIn > 0 || $diploCountOut > 0)}
	<tr>
		<td>{$LNG.al_diplo}</td>
		<td>
			{if $diploCountIn > 0}<a href="?page=alliance&amp;mode=admin&amp;action=diplomacy">{$diploRequestsIn}</a>{/if}
			{if $diploCountIn > 0 && $diploCountOut > 0}<br>{/if}
			{if $diploCountOut > 0}<a href="?page=alliance&amp;mode=admin&amp;action=diplomacy">{$diploRequestsOut}</a>{/if}
		</td>
	</tr>
	{/if}
	{if $rights.ROUNDMAIL}
	<tr>
		<td>{$LNG.al_circular_message}</td><td><a href="game.php?page=alliance&mode=circular" onclick="return Dialog.open(this.href, 650, 300);">{$LNG.al_send_circular_message}</a></td>
	</tr>
	{/if}
	<tr>
		<td colspan="2" style="height:100px" class="bbcode">{if $ally_description}{$ally_description}{else}{$LNG.al_description_message}{/if}</td>
	</tr>
	{if $ally_web}
	<tr>
		<td>{$LNG.al_web_text}</td>
		<td><a href="{$ally_web}">{$ally_web}</a></td>
	</tr>
	{/if}
	<tr>
		<th colspan="2">{$LNG.al_inside_section}</th>
	</tr>
	<tr>
		<td colspan="2" height="100" class="bbcode">{$ally_text}</td>
	</tr>
	<tr>
		<th colspan="2">{$LNG.al_diplo}</th>
	</tr>
	<tr>
		<td colspan="2">
		{if $diplomats}
			<b><u>{$LNG.al_rank_diplo}</u></b><br><br>
			{foreach $diplomats as $diplomat}{$diplomat.username} - {$diplomat.rankName} <a href="#" onclick="return Dialog.PM({$diplomat.id})"><img src="{$dpath}img/m.gif" title="{$LNG.write_message}" alt=""></a><br>{/foreach}
			<br>
		{/if}
		{if $DiploInfo && (!empty($DiploInfo.0) || !empty($DiploInfo.1) || !empty($DiploInfo.2) || !empty($DiploInfo.3) || !empty($DiploInfo.4))}
			{if !empty($DiploInfo.0)}<b><u>{$LNG.al_diplo_level.0}</u></b><br><br>{foreach item=PaktInfo from=$DiploInfo.0}<a href="?page=alliance&mode=info&amp;id={$PaktInfo.1}">{$PaktInfo.0}</a><br>{/foreach}<br>{/if}
			{if !empty($DiploInfo.1)}<b><u>{$LNG.al_diplo_level.1}</u></b><br><br>{foreach item=PaktInfo from=$DiploInfo.1}<a href="?page=alliance&mode=info&amp;id={$PaktInfo.1}">{$PaktInfo.0}</a><br>{/foreach}<br>{/if}
			{if !empty($DiploInfo.2)}<b><u>{$LNG.al_diplo_level.2}</u></b><br><br>{foreach item=PaktInfo from=$DiploInfo.2}<a href="?page=alliance&mode=info&amp;id={$PaktInfo.1}">{$PaktInfo.0}</a><br>{/foreach}<br>{/if}
			{if !empty($DiploInfo.3)}<b><u>{$LNG.al_diplo_level.3}</u></b><br><br>{foreach item=PaktInfo from=$DiploInfo.3}<a href="?page=alliance&mode=info&amp;id={$PaktInfo.1}">{$PaktInfo.0}</a><br>{/foreach}<br>{/if}
			{if !empty($DiploInfo.4)}<b><u>{$LNG.al_diplo_level.4}</u></b><br><br>{foreach item=PaktInfo from=$DiploInfo.4}<a href="?page=alliance&mode=info&amp;id={$PaktInfo.1}">{$PaktInfo.0}</a><br>{/foreach}<br>{/if}
		{else}
			{$LNG.al_no_diplo}
		{/if}
		</td>
	</tr>
	<tr>
		<th colspan="2">{$LNG.pl_fightstats}</th>
	</tr>
	<tr>
		<td>{$LNG.pl_totalfight}</td><td>{$totalfight|number}</td>
	</tr>
	<tr>
		<td>{$LNG.pl_fightwon}</td><td>{$fightwon|number} {if $totalfight}({round($fightwon / $totalfight * 100, 2)}%){/if}</td>
	</tr>
	<tr>	
		<td>{$LNG.pl_fightlose}</td><td>{$fightlose|number} {if $totalfight}({round($fightlose / $totalfight * 100, 2)}%){/if}</td>
	</tr>
	<tr>	
		<td>{$LNG.pl_fightdraw}</td><td>{$fightdraw|number} {if $totalfight}({round($fightdraw / $totalfight * 100, 2)}%){/if}</td>
	</tr>
	<tr>
		<td>{$LNG.pl_unitsshot}</td><td>{$unitsshot}</td>
	</tr>
	<tr>
		<td>{$LNG.pl_unitslose}</td><td>{$unitslose}</td>
	</tr>
	<tr>
		<td>{$LNG.pl_dermetal}</td><td>{$dermetal}</td>
	</tr>
	<tr>
		<td>{$LNG.pl_dercrystal}</td><td>{$dercrystal}</td>
	</tr>
</table>
{if !$isOwner}
<table class="table519">
	<tr>
		<th>{$LNG.al_leave_alliance}</th>
	</tr>
	<tr>
		<td><a href="game.php?page=alliance&amp;mode=close" onclick="return confirm('{$LNG.al_leave_ally}');"><button>{$LNG.al_continue}</button></a></td>
	</tr>
</table>
{/if}
{/block}
