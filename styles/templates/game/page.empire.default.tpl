{block name="title" prepend}{$LNG.lm_empire}{/block}
{block name="content"}
<table>
	<tbody>
		<tr>
			<th colspan="{$colspan}">{$LNG.lv_imperium_title}</th>
		</tr>
		<tr>
			<td style="width:100px">{$LNG.lv_planet}</td>
			<td style="width:100px;font-size: 50px;">&Sigma;</td>
			{foreach $planetList.image as $planetID => $image}
			<td style="width:100px"><a href="game.php?page=overview&amp;cp={$planetID}"><img width="80" height="80" border="0" src="{$dpath}planeten/{$image}.jpg"></a></td>
			{/foreach}
		</tr>
		<tr>
			<td>{$LNG.lv_name}</td>
			<td>{$LNG.lv_total}</td>
			{foreach $planetList.name as $name}
				<td>{$name}</td>
			{/foreach}
		</tr>
		<tr>
			<td>{$LNG.lv_coords}</td>
			<td>-</td>
			{foreach $planetList.coords as $coords}
				<td><a href="game.php?page=galaxy&amp;galaxy={$coords.galaxy}&amp;system={$coords.system}">[{$coords.galaxy}:{$coords.system}:{$coords.planet}]</a></td>
			{/foreach}
		</tr>
		<tr>
			<td>{$LNG.lv_fields}</td>
			<td>-</td>
			{foreach $planetList.field as $field}
				<td>{$field.current} / {$field.max}</td>
			{/foreach}
		</tr>
		<tr>
			<th colspan="{$colspan}">{$LNG.lv_resources}</th>
		</tr>
		{foreach $planetList.resource as $elementID => $resourceArray}
		<tr>
			<td><a href='#' onclick='return Dialog.info({$elementID});' class='tooltip' data-tooltip-content="<table><tr><th>{$LNG.tech.{$elementID}}</th></tr><tr><table class='hoverinfo'><tr><td><img src='{$dpath}gebaeude/{$elementID}.{if $elementID >=600 && $elementID <= 699}jpg{else}gif{/if}'></td><td>{$LNG.shortDescription.$elementID}</td></tr></table></tr></table>">{$LNG.tech.$elementID}</a></td>
			<td>{number_format(array_sum($resourceArray), 0, ",", ".")} {if in_array($elementID, array(901,902,903))}<span class="colorPositive">{number_format(array_sum($planetList.resourcePerHour[$elementID]), 0, ",", ".")}/h</span> <span class="{if min($planetList.resourceFull[$elementID])>23}colorPositive{/if}{if min($planetList.resourceFull[$elementID])<24 && min($planetList.resourceFull[$elementID])>6}colorNeutral{/if}{if min($planetList.resourceFull[$elementID])<7}colorNegative{/if}">{number_format(min($planetList.resourceFull[$elementID]),0,",",".")} h</span>{/if}</td>
			{foreach $resourceArray as $planetID => $resource}
				<td>{number_format($resource, 0, ",", ".")} {if in_array($elementID, array(901,902,903)) && $planetList.planet_type[$planetID] == 1}<span class="colorPositive">{number_format($planetList.resourcePerHour[$elementID][$planetID], 0, ",", ".")}/h</span> <span class="{if $planetList.resourceFull[$elementID][$planetID]>23}colorPositive{/if}{if $planetList.resourceFull[$elementID][$planetID]<24 && $planetList.resourceFull[$elementID][$planetID]>6}colorNeutral{/if}{if $planetList.resourceFull[$elementID][$planetID]<7}colorNegative{/if}">{number_format($planetList.resourceFull[$elementID][$planetID],0,",",".")} h</span>{/if}</td>
			{/foreach}
		</tr>
		{/foreach}
		<tr>
			<th colspan="{$colspan}">{$LNG.lv_buildings}</th>
		</tr>
		{foreach $planetList.build as $elementID => $buildArray}
		<tr>
			<td><a href='#' onclick='return Dialog.info({$elementID})' class='tooltip' data-tooltip-content="<table><tr><th>{$LNG.tech.{$elementID}}</th></tr><tr><table class='hoverinfo'><tr><td><img src='{$dpath}gebaeude/{$elementID}.{if $elementID >=600 && $elementID <= 699}jpg{else}gif{/if}'></td><td>{$LNG.shortDescription.$elementID}</td></tr></table></tr></table>">{$LNG.tech.$elementID}</a></td>
			<td>{number_format(array_sum($buildArray), 0, ",", ".")}</td>
			{foreach $buildArray as $planetID => $build}
				<td>{number_format($build, 0, ",", ".")}</td>
			{/foreach}
		</tr>
		{/foreach}
		<tr>
			<th colspan="{$colspan}">{$LNG.lv_technology}</th>
		</tr>
		{foreach $planetList.tech as $elementID => $tech}
		<tr>
			<td><a href='#' onclick='return Dialog.info({$elementID})' class='tooltip' data-tooltip-content="<table><tr><th>{$LNG.tech.{$elementID}}</th></tr><tr><table class='hoverinfo'><tr><td><img src='{$dpath}gebaeude/{$elementID}.{if $elementID >=600 && $elementID <= 699}jpg{else}gif{/if}'></td><td>{$LNG.shortDescription.$elementID}</td></tr></table></tr></table>">{$LNG.tech.$elementID}</a></td>
			<td>{number_format($tech, 0, ",", ".")}</td>
			{foreach $planetList.name as $name}
				<td>{number_format($tech, 0, ",", ".")}</td>
			{/foreach}
		</tr>
		{/foreach}
		<tr>
			<th colspan="{$colspan}">{$LNG.lv_ships}</th>
		</tr>
		{foreach $planetList.fleet as $elementID => $fleetArray}
		<tr>
			<td><a href='#' onclick='return Dialog.info({$elementID})' class='tooltip' data-tooltip-content="<table><tr><th>{$LNG.tech.{$elementID}}</th></tr><tr><table class='hoverinfo'><tr><td><img src='{$dpath}gebaeude/{$elementID}.{if $elementID >=600 && $elementID <= 699}jpg{else}gif{/if}'></td><td>{$LNG.shortDescription.$elementID}</td></tr></table></tr></table>">{$LNG.tech.$elementID}</a></td>
			<td>{number_format(array_sum($fleetArray), 0, ",", ".")}</td>
			{foreach $fleetArray as $planetID => $fleet}
				<td>{number_format($fleet, 0, ",", ".")}</td>
			{/foreach}
		</tr>
		{/foreach}
		<tr>
			<th colspan="{$colspan}">{$LNG.lv_defenses}</th>
		</tr>
		{foreach $planetList.defense as $elementID => $fleetArray}
		<tr>
			<td><a href='#' onclick='return Dialog.info({$elementID})' class='tooltip' data-tooltip-content="<table><tr><th>{$LNG.tech.{$elementID}}</th></tr><tr><table class='hoverinfo'><tr><td><img src='{$dpath}gebaeude/{$elementID}.{if $elementID >=600 && $elementID <= 699}jpg{else}gif{/if}'></td><td>{$LNG.shortDescription.$elementID}</td></tr></table></tr></table>">{$LNG.tech.$elementID}</a></td>
			<td>{number_format(array_sum($fleetArray), 0, ",", ".")}</td>
			{foreach $fleetArray as $planetID => $fleet}
				<td>{number_format($fleet, 0, ",", ".")}</td>
			{/foreach}
		</tr>
		{/foreach}
		<tr>
		    <th colspan="{$colspan}">{$LNG.tech.500}</th>
		</tr>
		{foreach $planetList.missiles as $elementID => $fleetArray}
		<tr>
			<td><a href='#' onclick='return Dialog.info({$elementID})' class='tooltip' data-tooltip-content="<table><tr><th>{$LNG.tech.{$elementID}}</th></tr><tr><table class='hoverinfo'><tr><td><img src='{$dpath}gebaeude/{$elementID}.{if $elementID >=600 && $elementID <= 699}jpg{else}gif{/if}'></td><td>{$LNG.shortDescription.$elementID}</td></tr></table></tr></table>">{$LNG.tech.$elementID}</a></td>
			<td>{number_format(array_sum($fleetArray), 0, ",", ".")}</td>
			{foreach $fleetArray as $planetID => $fleet}
				<td>{number_format($fleet, 0, ",", ".")}</td>
			{/foreach}
		</tr>
		{/foreach}
	</tbody>
</table>
{/block}
