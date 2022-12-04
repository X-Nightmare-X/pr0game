{$count = count($productionTable.usedResource)}

<table>
	<tbody>
		<tr>
			<td colspan="2">
				<table>
				<tr>
					<th>{$LNG.in_level}</th>
					{foreach $productionTable.usedResource as $resourceID}
					<th colspan="2">{$LNG.tech.$resourceID}</th>
					{/foreach}
				</tr>
				<tr>
					<th>&nbsp;</th>
					{foreach $productionTable.usedResource as $resourceID}
					<th>{$LNG.in_prod_p_hour}</th>
					<th>{$LNG.in_difference}</th>
					{/foreach}
				</tr>
				{foreach $productionTable.production as $elementLevel => $productionData}
				<tr>
					<td><span{if $CurrentLevel == $elementLevel} class="colorNegative"{/if}>{$elementLevel}</span></td>
					{foreach $productionData as $resourceID => $production}
					{$productionDiff = $production - $productionTable.production.$CurrentLevel.$resourceID}
					<td><span class="{if $production > 0}colorPositive{elseif $production < 0}colorNegative{else}white{/if}">{number_format($production, 0, ",", ".")}</span></td>
					<td><span class="{if $productionDiff > 0}colorPositive{elseif $productionDiff < 0}colorNegative{else}white{/if}">{number_format($productionDiff, 0, ",", ".")}</span></td>
					{/foreach}
				</tr>
				{/foreach}
				</table>
			</td>
		</tr>
	</tbody>
</table>