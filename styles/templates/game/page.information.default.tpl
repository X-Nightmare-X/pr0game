{block name="title" prepend}{$LNG.lm_info}{/block}
{block name="content"}
<table>
	<tbody>
	<tr>
		<th>{$LNG.tech.$elementID}</th>
	</tr>
	<tr>
		<td>
			<table>
				<tr>
					<td class="transparent left"><p>{$LNG.longDescription.$elementID} {if $elementID == 106 && $spytechStatsEnabled == true}<br>{$LNG.spytechStatsDescription}{/if}</p>
					{if !empty($Bonus)}<p>
					<b>{$LNG.in_bonus}</b><br>
					{foreach $Bonus as $BonusName => $elementBouns}{if $elementBouns[0] < 0}-{else}+{/if}{if $elementBouns[1] == 0}{abs($elementBouns[0] * 100)}%{else}{floatval($elementBouns[0])}{/if} {$LNG.bonus.$BonusName}<br>{/foreach}
					</p>{/if}	
					{if !empty($FleetInfo)}
					{if !empty($FleetInfo.rapidfire.to)}<p>
					{foreach $FleetInfo.rapidfire.to as $rapidfireID => $shoots}
					{$LNG.in_rf_again} {$LNG.tech.$rapidfireID}: <span class="colorPositive">{number_format($shoots, 0, ",", ".")}</span><br>
					{/foreach}
					</p>{/if}
					{if !empty($FleetInfo.rapidfire.from)}<p>
					{foreach $FleetInfo.rapidfire.from as $rapidfireID => $shoots}
					{$LNG.in_rf_from} {$LNG.tech.$rapidfireID}: <span class="colorNegative">{number_format($shoots, 0, ",", ".")}</span><br>
					{/foreach}
					</p>{/if}
					{/if}				
					</td>
				</tr>
			</table>
		</td>
	</tr>
	</tbody>
</table>
{if !empty($FleetInfo)}
{include file="shared.information.shipInfo.tpl"}
{/if}
{if !empty($gateData)}
{include file="shared.information.gate.tpl"}
{/if}
{if !empty($MissileList)}
{include file="shared.information.missiles.tpl"}
{/if}
{if !empty($productionTable.production)}
{include file="shared.information.production.tpl"}
{/if}
{if !empty($productionTable.storage)}
{include file="shared.information.storage.tpl"}
{/if}
{/block}