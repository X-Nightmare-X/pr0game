{$count = $productionTable.usedResource}

<table>
	<tbody>
		<tr>
			<td colspan="2">
				<table>
				<tr>
					<th>{$LNG.in_level}</th>
				{if $count > 1}
					{foreach $productionTable.usedResource as $resourceID}
					<th colspan="2">{$LNG.tech.$resourceID}</th>
					{/foreach}
				</tr>
				<tr>
					<th>&nbsp;</th>
				{/if}
					{foreach $productionTable.usedResource as $resourceID}
					<th>{$LNG.in_storage}</th>
					<th>{$LNG.in_difference}</th>
					{/foreach}
				</tr>
				{foreach $productionTable.storage as $elementLevel => $productionData}
				<tr>
					<td><span{if $CurrentLevel == $elementLevel} class="colorNegative"{/if}>{$elementLevel}</span></td>
					{foreach $productionData as $resourceID => $storage}
					{$storageDiff = $storage - $productionTable.storage.$CurrentLevel.$resourceID}
					<td><span class="{if $storage > 0}colorPositive{elseif $storage < 0}colorNegative{else}white{/if}">{number_format($storage, 0, ",", ".")}</span></td>
					<td><span class="{if $storageDiff > 0}colorPositive{elseif $storageDiff < 0}colorNegative{else}white{/if}">{number_format($storageDiff, 0, ",", ".")}</span></td>
					{/foreach}
				</tr>
				{/foreach}
				</table>
			</td>
		</tr>
	</tbody>
</table>