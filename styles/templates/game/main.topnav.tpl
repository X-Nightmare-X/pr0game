<label class="hamburger" for="toggle-menu" class="toggle-menu"><i class="fas fa-bars"></i></label>

<div class="planetImage no-mobile">
   <div>{$LNG.tn_player_title}</div>
   <div><a href="game.php?page=settings"><b>{$username}</b></div>
</div>

<div class="planetSelectorWrapper">
	<a href="game.php?page=overview"><img src="{$dpath}planeten/{$image}.jpg" width="50" height="50" alt="{$LNG.lm_overview}"></a>
	<div class="planetSelectorName" for="planetSelector"></div>
	<div class="no-mobile">&nbsp;</div>
	<div class="no-mobile">&nbsp;</div>
	<select id="planetSelector">
		{html_options options=$PlanetSelect selected=$current_pid}
	</select>
</div>

<div id="resources_mobile">
	{foreach $resourceTable as $resourceID => $resourceData}
	<div id="resource_mobile">
		<a href="#" onclick="return Dialog.info({$resourceID});">
			<img src="{$dpath}images/{$resourceData.name}.gif">
			<div class="colorNegative no-mobile">{$LNG.tech.$resourceID}</div>

			<div class="no-mobile">
				{if !isset($resourceData.current)}
					{$resourceData.currentt = $resourceData.max + $resourceData.used}
						<td class="res_current tooltip" data-tooltip-content="{number_format($resourceData.currentt, 0, ",", ".")}">
							<span{if $resourceData.currentt < 0} class="colorNegative"{/if}>{number_format($resourceData.currentt, 0, ",", ".")}&nbsp;/&nbsp;{number_format($resourceData.max, 0, ",", ".")} </span>
						</td>
				{else}
					<div class="res_current" id="current_{$resourceData.name}" data-real="{$resourceData.current}">{number_format($resourceData.current, 0, ",", ".")}</div>
				{/if}
				{if !isset($resourceData.current) || !isset($resourceData.max)}
					<div>&nbsp;</div>
				{else}
					<div class="res_max colorPositive" id="max_{$resourceData.name}" data-real="{$resourceData.current}">{number_format($resourceData.max, 0, ",", ".")}</div>
				{/if}
			</div>

			<div class="mobile">
				{if !isset($resourceData.current)}
					{$resourceData.currentt = $resourceData.max + $resourceData.used}
						<td class="res_current tooltip" data-tooltip-content="{number_format($resourceData.currentt, 0, ",", ".")}">
							<span{if $resourceData.currentt < 0} class="colorNegative"{/if}>{shortly_number($resourceData.currentt)}</span>
						</td>
{/if}
{if !isset($resourceData.max)}

						<td class="res_current" id="current_{$resourceData.name}" data-real="{$resourceData.current}">{shortly_number($resourceData.current)}</td>
				{/if}
				{if !isset($resourceData.current) || !isset($resourceData.max)}

				{else}
					<td class="res_current" id="current_{$resourceData.name}" data-real="{$resourceData.current}"><span{if $resourceData.current >= {$resourceData.max}} class="colorNegative"{/if}>{shortly_number($resourceData.current)}</span></td>
				{/if}
			</div>

			<!--
			<div class="mobile">
				{if !isset($resourceData.current)}
					{$resourceData.current = $resourceData.max + $resourceData.used}
						<td class="res_current tooltip mobile" data-tooltip-content="{number_format($resourceData.current, 0, ",", ".")}">
							<span{if $resourceData.current < 0} class="colorNegative"{/if}>!{shortly_number($resourceData.current)}</span>
							<span{if $resourceData.current < 0} class="colorNegative"{/if} class="no-mobile">&nbsp;/&nbsp;@{number_format($resourceData.max, 0, ",", ".")}</span>
						</td>
						<td class="res_current tooltip no-mobile" data-tooltip-content="{number_format($resourceData.current, 0, ",", ".")}">
							<span{if $resourceData.current < 0} class="colorNegative"{/if}>!{$resourceData.current}</span>
							<span{if $resourceData.current < 0} class="colorNegative"{/if} class="no-mobile">&nbsp;/&nbsp;@{number_format($resourceData.max, 0, ",", ".")}</span>
						</td>
				{else}
					<td class="res_current tooltip mobile" id="current_{$resourceData.name}" data-real="{$resourceData.current}" data-tooltip-content="{number_format($resourceData.current, 0, ",", ".")}">#{shortly_number($resourceData.current)}</td>
					<td class="res_current no-mobile" id="current_{$resourceData.name}" data-real="{$resourceData.current}">#{$resourceData.current}</td>
				{/if}
				{if !isset($resourceData.current) || !isset($resourceData.max)}
				{else}
					<div class="res_max no-mobile" id="max_{$resourceData.name}" data-real="{$resourceData.current}">{number_format($resourceData.max, 0, ",", ".")}</div>
				{/if}
			</div>
			-->

			<!--
			<div>
			{if true or $shortlyNumber}
				{if !isset($resourceData.current)}
				{$resourceData.current = $resourceData.max + $resourceData.used}
				<td class="res_current tooltip" data-tooltip-content="{number_format($resourceData.current, 0, ",", ".")}"><span{if $resourceData.current < 0} class="colorNegative"{/if}>{shortly_number($resourceData.current)}</span></td>
				{else}
				<td class="res_current tooltip" id="current_{$resourceData.name}" data-real="{$resourceData.current}" data-tooltip-content="{number_format($resourceData.current, 0, ",", ".")}">{shortly_number($resourceData.current)}</td>
				{/if}
			{else}
				{if !isset($resourceData.current)}
				{$resourceData.current = $resourceData.max + $resourceData.used}
				<div class="res_current"><span{if $resourceData.current < 0} class="colorNegative"{/if}>{number_format($resourceData.current, 0, ",", ".")}&nbsp;/&nbsp;{number_format($resourceData.max, 0, ",", ".")}</span></div>
				{else}
				<div class="res_current" id="current_{$resourceData.name}" data-real="{$resourceData.current}">{number_format($resourceData.current, 0, ",", ".")}</div>
				{/if}
				{if !isset($resourceData.current) || !isset($resourceData.max)}
				<div>&nbsp;</div>
				{else}
				<div class="res_max" id="max_{$resourceData.name}" data-real="{$resourceData.current}">{number_format($resourceData.max, 0, ",", ".")}</div>
				{/if}
			{/if}
			</div>
				-->
		</a>
	</div>
	{/foreach}

</div>

<!--
<table id="headerTable">
	<tbody>
		<tr>
			<td id="planetImage">
			   <img src="{$avatar}" width="50" height="50" alt="">
			   <div>{$LNG.tn_player_title} <b>{$username}</b></div>
			</td>
			<td id="planetSelectorWrapper">
			   <img src="{$dpath}planeten/{$image}.jpg" width="50" height="50" alt="">
				<label for="planetSelector"></label>
				<select id="planetSelector">
					{html_options options=$PlanetSelect selected=$current_pid}
				</select>
			</td>
			<td id="resourceWrapper">
				<table id="resourceTable">
					<tbody>
						<tr>
							{foreach $resourceTable as $resourceID => $resourceData}
							<td>
								<a href="#" onclick="return Dialog.info({$resourceID});">
									<img src="{$dpath}images/{$resourceData.name}.gif" alt="">
								</a>
							</td>
							{/foreach}
						</tr>
						<tr>
							{foreach $resourceTable as $resourceID => $resourceData}
							<td class="res_name">
								<a href="#" onclick="return Dialog.info({$resourceID});">
									<span class="colorNegative">
									{$LNG.tech.$resourceID}
									</span>
								</a>
							</td>
							{/foreach}
						</tr>
						{if $shortlyNumber}
						<tr>
							{foreach $resourceTable as $resourceID => $resourceData}
							{if !isset($resourceData.current)}
							{$resourceData.current = $resourceData.max + $resourceData.used}
							<td class="res_current tooltip" data-tooltip-content="{number_format($resourceData.current, 0, ",", ".")}&nbsp;/&nbsp;{number_format($resourceData.max, 0, ",", ".")}"><span{if $resourceData.current < 0} class="colorNegative"{/if}>{shortly_number($resourceData.current)}&nbsp;/&nbsp;{shortly_number($resourceData.max)}</span></td>
							{else}
							<td class="res_current tooltip" id="current_{$resourceData.name}" data-real="{$resourceData.current}" data-tooltip-content="{number_format($resourceData.current, 0, ",", ".")}">{shortly_number($resourceData.current)}</td>
							{/if}
							{/foreach}
						</tr>
						<tr>
							{foreach $resourceTable as $resourceID => $resourceData}
							{if !isset($resourceData.current) || !isset($resourceData.max)}
							<td>&nbsp;</td>
							{else}
							<td class="res_max tooltip" id="max_{$resourceData.name}" data-real="{$resourceData.max}" data-tooltip-content="{number_format($resourceData.max, 0, ",", ".")}">{shortly_number($resourceData.max)}</td>
							{/if}
							{/foreach}
						</tr>
						{else}
						<tr>
							{foreach $resourceTable as $resourceID => $resourceData}
							{if !isset($resourceData.current)}
							{$resourceData.current = $resourceData.max + $resourceData.used}
							<td class="res_current"><span{if $resourceData.current < 0} class="colorNegative"{/if}>{number_format($resourceData.current, 0, ",", ".")}&nbsp;/&nbsp;{number_format($resourceData.max, 0, ",", ".")}</span></td>
							{else}
							<td class="res_current" id="current_{$resourceData.name}" data-real="{$resourceData.current}">{number_format($resourceData.current, 0, ",", ".")}</td>
							{/if}
							{/foreach}
						</tr>
						<tr>
							{foreach $resourceTable as $resourceID => $resourceData}
							{if !isset($resourceData.current) || !isset($resourceData.max)}
							<td>&nbsp;</td>
							{else}
							<td class="res_max" id="max_{$resourceData.name}" data-real="{$resourceData.current}">{number_format($resourceData.max, 0, ",", ".")}</td>
							{/if}
							{/foreach}
						</tr>
						{/if}
					</tbody>
				</table>
			</td>
		</tr>
	</tbody>
</table>
-->
{if !$vmode}
<script type="text/javascript">
var viewShortlyNumber	= {$shortlyNumber|json_encode};
var vacation			= {$vmode};
$(function() {
{foreach $resourceTable as $resourceID => $resourceData}
{if isset($resourceData.production)}
	resourceTicker({
		available: {$resourceData.current|json_encode},
		limit: [0, {$resourceData.max|json_encode}],
		production: {$resourceData.production|json_encode},
		valueElem: "current_{$resourceData.name}"
	}, true);
{/if}
{/foreach}
});
</script>
<script src="scripts/game/topnav.js"></script>
{if $hasGate}<script src="scripts/game/gate.js"></script>{/if}
{/if}
