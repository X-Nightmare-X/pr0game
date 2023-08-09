{block name="title" prepend}{$LNG.lm_playercard}{/block}
{block name="content"}
<table style="width:95%">
	<tr>
		<th colspan="4">{$LNG.pl_overview}</th>
		</tr>
	<tr>
		<td style="width:40%">{$LNG.pl_name}</td>
		<td colspan="3">{$name}</td>
	</tr>
	<tr>
		<td>{$LNG.pl_homeplanet}</td>
		<td colspan="3">{$homeplanet} <a href="#" onclick="parent.location = 'game.php?page=galaxy&amp;galaxy={$galaxy}&amp;system={$system}';return false;">[{$galaxy}:{$system}:{$planet}]</a></td>
	</tr>
	<tr>
		<td>{$LNG.pl_ally}</td>
		<td colspan="3">{if $allyname}<a href="#" onclick="parent.location = 'game.php?page=alliance&amp;mode=info&amp;id={$allyid}';return false;">{$allyname}</a>{else}-{/if}</td>
	</tr>
	<tr>
		<th>&nbsp;</th>
		<th colspan="2">{$LNG.pl_points}</th>
		<th>{$LNG.pl_range}</th>
	</tr>
	<tr>
		<td>{$LNG.pl_builds}</td>
		<td>{$build_points}</td>
		<td>{$build_percent}%</td>
		<td>{$build_rank}</td>
	</tr>
	<tr>
		<td>{$LNG.pl_tech}</td>
		<td>{$tech_points}</td>
		<td>{$tech_percent}%</td>
		<td>{$tech_rank}</td>
	</tr>
	<tr>
		<td>{$LNG.pl_fleet}</td>
		<td>{$fleet_points}</td>
		<td>{$fleet_percent}%</td>
		<td>{$fleet_rank}</td>
	</tr>
	<tr>
		<td>{$LNG.pl_def}</td>
		<td>{$defs_points}</td>
		<td>{$defs_percent}%</td>
		<td>{$defs_rank}</td>
	</tr>
	<tr>
		<td>{$LNG.pl_total}</td>
		<td>{$total_points}</td>
		<td>100%</td>
		<td>{$total_rank}</td>
	</tr>
	<tr>
		<th colspan="4">{$LNG.pl_fightstats}</th>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td>{$LNG.pl_fights}</td>
		<td colspan="2">{$LNG.pl_fprocent}</td>
	</tr>
	<tr>
		<td>{$LNG.pl_fightwon}</td>
		<td>{$wons}</td>
		<td colspan="2">{$siegprozent}%</td>
	</tr>
	<tr>
		<td>{$LNG.pl_fightdraw}</td>
		<td>{$draws}</td>
		<td colspan="2">{$drawsprozent}%</td>
	</tr>
	<tr>
		<td>{$LNG.pl_fightlose}</td>
		<td>{$loos}</td>
		<td colspan="2">{$loosprozent}%</td>
	</tr>
	<tr>
		<td>{$LNG.pl_totalfight}</td>
		<td>{$totalfights}</td>
		<td colspan="2">100%</td>
	</tr>
	<tr>
		<th colspan="4">{$playerdestory}:</th>
	</tr>
	<tr>
		<td>{$LNG.pl_unitsshot}</td>
		<td colspan="3">{$desunits}</td>
	</tr>
	<tr>
		<td>{$LNG.pl_unitslose}</td>
		<td colspan="3">{$lostunits}</td>
	</tr>
	<tr>
		<td>{$LNG.pl_dermetal}</td>
		<td colspan="3">{$kbmetal}</td>
	</tr>
	<tr>
		<td>{$LNG.pl_dercrystal}</td>
		<td colspan="3">{$kbcrystal}</td>
	</tr>
{if $id != $yourid}
	<tr>
		<th colspan="4">{$LNG.pl_etc}</th>
	</tr>
	<tr>
		<td><a href="#" onclick="return Dialog.Buddy({$id})">{$LNG.pl_buddy}</a></td><td colspan="3"><a href="#" onclick="return Dialog.PM({$id});" title="{$LNG.pl_message}">{$LNG.pl_message}</a></td>
	</tr>
{/if}
	<tr>
		<th colspan="4">{$LNG.Achievements}:</th>
	</tr>
	<tr>
		<td colspan="4">
			{if $achievements}
				{$i = 0}
				{foreach from=$achievements item=achievement}
					{*if $i % 5 == 0}
						</td></tr><tr><td colspan="4">
					{/if*}
					<img src="{$dpath}achievements/{$achievement.image}.jpg" title="{$LNG.Achievement_names.{$achievement.id}}" alt="{$achievement.name}" style="width: 32px; height: 32px; margin: 2px;" />
					{$i = $i + 1}
				{/foreach}
			{/if}
		</td>
</table>
{/block}