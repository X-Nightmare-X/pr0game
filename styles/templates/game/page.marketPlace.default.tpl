{block name="title" prepend}{$LNG.lm_marketplace}{/block}
{block name="content"}

<table style="width:80%">
	<tr>
		<th colspan="2">
			{$LNG.market_info_header}
		</th>
	</tr>
	<tr>
		{if isModuleAvailable($smarty.const.MODULE_MARKET_TRADE) && isModuleAvailable($smarty.const.MODULE_MARKET_TRANSFER)}
			<td>
				<button id="resourceMBtn" class="marketOption selected">Resource market</button>
			</td>
			<td>
				<button id="fleetMBtn" class="marketOption">Fleet market</button>
			</td>
		{elseif isModuleAvailable($smarty.const.MODULE_MARKET_TRADE)}
			<td colspan="2">
				<button id="resourceMBtn" class="marketOption selected">Resource market</button>
			</td>
		{elseif isModuleAvailable($smarty.const.MODULE_MARKET_TRANSFER)}
			<td colspan="2">
				<button id="fleetMBtn" class="marketOption selected">Fleet market</button>
			</td>
		{/if}
	</tr>
	<tr>
		<td colspan="2">
			<p>
				{$LNG.market_info_description}
			</p>
		</td>
	</tr>

	<tr>
		<td>
			{$LNG.market_ship_as_first}
		</td>
		<td>
			<select id="shipT">
				<option value="1">{$LNG.shortNames[202]}</option>
				<option value="2" selected>{$LNG.shortNames[203]}</option>
			</select>
		</td>
	</tr>
</table>

<table style="width:50%">
	<tr class="ratio">
		<td>Reference ratio:</td>
		<td>
			<input type="number" name="ratio-metal" value="{$ratio_metal}" style="width: 30%"/>:<input type="number" name="ratio-cristal" value="{$ratio_crystal}" style="width: 30%"/>:<input type="number"name="ratio-deuterium" value="{$ratio_deuterium}" style="width: 30%"/>
		</td>
	</tr>
</table>

{if $message}
<table style="width:80%">
	<tr>
		<th>
			{$LNG.fcm_info}
		</th>
	</tr>
	<tr>
		<td>
			{$message}
		</td>
	</tr>
</table>
{/if}

<br/><br/>
<div id="resourceMarketBox" style="display:none">
<table id="tradeList" style="width:80%;white-space: nowrap;" class="tablesorter">
	<thead>
		<tr class="no-background no-border center">
			<td></th>
			<th><img src="{$dpath}images/metal.gif"/></th>
			<th><img src="{$dpath}images/crystal.gif"/></th>
			<th><img src="{$dpath}images/deuterium.gif"/></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th class="LC" style="display: none;"></th>
			<th class="HC"></th>
			<th></th>
		</tr>
		<tr>
			<th>ID</th>
			<th>{$LNG['tech'][901]}</th>
			<th>{$LNG['tech'][902]}</th>
			<th>{$LNG['tech'][903]}</th>
			<th>{$LNG.market_p_total}</th>
			<th>{$LNG.market_p_ratio}</th>
			<th>{$LNG.market_p_end}</th>
			<th  class="no-background no-border center">-></th>
			<th>{$LNG.market_p_cost_type}</th>
			<th>{$LNG.market_p_cost_amount}</th>
			<th class="LC" style="display: none;">{$LNG.market_p_to_duration}</th>
			<th class="HC">{$LNG.market_p_to_duration}</th>
			<th>{$LNG.market_p_buy}</th>
		</tr>
	</thead>
	<tbody>

	{foreach name=FlyingFleets item=FlyingFleetRow from=$FlyingFleetList}
	{if $FlyingFleetRow.type == 0}
	<tr class='{if {$FlyingFleetRow.diplo} == 5}
	 trade-enemy
		{elseif ({$FlyingFleetRow.diplo} != NULL && {$FlyingFleetRow.diplo} <= 3) || {$FlyingFleetRow.from_alliance} == 1}
		 trade-ally
		  {/if}
	{if $FlyingFleetRow.possible_to_buy != true} trade-disallowed {/if}'>
		<td>{$smarty.foreach.FlyingFleets.iteration}</td>
		<td class="resource_metal">{number_format($FlyingFleetRow.fleet_resource_metal, 0, ",", ".")}</td>
		<td class="resource_crystal">{number_format($FlyingFleetRow.fleet_resource_crystal, 0, ",", ".")}</td>
		<td class="resource_deuterium">{number_format($FlyingFleetRow.fleet_resource_deuterium, 0, ",", ".")}</td>
		<td class="total_value"></td>
		<td class="ratio"></td>
		<td data-time="{$FlyingFleetRow.end}">{getRestTimeFormat({$FlyingFleetRow.end})}</td>
		<td class="no-background no-border">
			{if $FlyingFleetRow.fleet_wanted_resource_id == 1}
			<img src="{$dpath}images/metal.gif"/>
			{elseif $FlyingFleetRow.fleet_wanted_resource_id == 2}
			<img src="{$dpath}images/crystal.gif"/>
			{elseif $FlyingFleetRow.fleet_wanted_resource_id == 3}
			<img src="{$dpath}images/deuterium.gif"/>
			{/if}
		</td>
		<td class="wanted-resource-{$FlyingFleetRow.fleet_wanted_resource_id}">{$FlyingFleetRow.fleet_wanted_resource}</td>
		<td class="wanted-resource-amount">{number_format($FlyingFleetRow.fleet_wanted_resource_amount, 0, ",", ".")}</td>
		<td class="LC" style="display: none;">{pretty_fly_time({$FlyingFleetRow.to_lc_duration})}</td>
		<td class="HC">{pretty_fly_time({$FlyingFleetRow.to_hc_duration})}</td>
		<td>
			{if $FlyingFleetRow.possible_to_buy == true}
			<form class="market_form" action="game.php?page=marketPlace&amp;action=buy" method="post">
				<input name="fleetID" value="{$FlyingFleetRow.id}" type="hidden">
				<input value="{$LNG.market_p_submit}" type="button" class="market_buy_button">
        <input type="submit" style="display: none">
			</form>
			{else}
				{$FlyingFleetRow.reason}
			{/if}
		</td>
	</tr>

	{/if}
	{/foreach}
	</tbody>
</table>
<hr>

<table id="resourceHistoryList" style="width:80%;white-space: nowrap;" class="tablesorter">
	<thead>
		<tr>
			<th>ID</th>
			<th>{$LNG.tkb_datum}</th>
			<th>{$LNG['tech'][901]}</th>
			<th>{$LNG['tech'][902]}</th>
			<th>{$LNG['tech'][903]}</th>
			<th  class="no-background no-border center">-></th>
			<th>{$LNG.market_p_cost_amount}</th>
		</tr>
	</thead>
	<tbody>
		{foreach name=History item=row from=$resourceHistory}
		<tr>
			<td>{$smarty.foreach.History.iteration}</td>
			<td>{$row.time}</td>
			<td>{$row.metal}</td>
			<td>{$row.crystal}</td>
			<td>{$row.deuterium}</td>
			<td class="no-background no-border center">
				{if $row.res_type == 1}<img src="{$dpath}images/metal.gif"/>
				{elseif $row.res_type == 2}<img src="{$dpath}images/crystal.gif"/>
				{elseif $row.res_type == 3}<img src="{$dpath}images/deuterium.gif"/>{/if}</td>
			<td>{$row.amount}</td>
		</tr>
		{/foreach}
	</tbody>
</table>

</div>
<div id="fleetMarketBox"  style="display:none">
<table id="tradeFleetList" style="width:80%;white-space: nowrap;" class="tablesorter">
	<thead>
		<tr>
			<th>ID</th>
			<th>{$LNG['market_fleet']}</th>
			<th>{$LNG.market_p_ratio}</th>
			<th>{$LNG.market_p_end}</th>
			<th class="no-background no-border center">-></th>
			<th>{$LNG.market_p_cost_type}</th>
			<th>{$LNG.market_p_cost_amount}</th>
			<th>{$LNG.market_p_from_duration}</th>
			<th class="LC" style="display: none;">{$LNG.market_p_to_duration}</th>
			<th class="HC">{$LNG.market_p_to_duration}</th>
			<th>{$LNG.market_p_buy}</th>
		</tr>
	</thead>
	<tbody>

	{foreach name=FlyingFleets item=FlyingFleetRow from=$FlyingFleetList}
	{if $FlyingFleetRow.type == 1}
	<tr class='{if {$FlyingFleetRow.diplo} == 5}
	 trade-enemy
		{elseif ({$FlyingFleetRow.diplo} != NULL && {$FlyingFleetRow.diplo} <= 3) || {$FlyingFleetRow.from_alliance} == 1}
		 trade-ally
		  {/if}
	{if $FlyingFleetRow.possible_to_buy != true} trade-disallowed {/if}'>
		<td>{$smarty.foreach.FlyingFleets.iteration}</td>
		<td>{$FlyingFleetRow.fleet}</td>
		<td class="ratio">{$FlyingFleetRow.fleet_ratio}</td>
		<td data-time="{$FlyingFleetRow.end}">{getRestTimeFormat({$FlyingFleetRow.end})}</td>
		<td class="no-background no-border">
			{if $FlyingFleetRow.fleet_wanted_resource_id == 1}
			<img src="{$dpath}images/metal.gif"/>
			{elseif $FlyingFleetRow.fleet_wanted_resource_id == 2}
			<img src="{$dpath}images/crystal.gif"/>
			{elseif $FlyingFleetRow.fleet_wanted_resource_id == 3}
			<img src="{$dpath}images/deuterium.gif"/>
			{/if}
		</td>
		<td class="wanted-resource-{$FlyingFleetRow.fleet_wanted_resource_id}">{$FlyingFleetRow.fleet_wanted_resource}</td>
		<td class="wanted-resource-amount">{number_format($FlyingFleetRow.fleet_wanted_resource_amount, 0, ",", ".")}</td>
		<td>{pretty_fly_time({$FlyingFleetRow.from_duration})}</td>
		<td class="LC" style="display: none;">{pretty_fly_time({$FlyingFleetRow.to_lc_duration})}</td>
		<td class="HC">{pretty_fly_time({$FlyingFleetRow.to_hc_duration})}</td>
		<td>
			{if $FlyingFleetRow.possible_to_buy == true}
			<form class="market_form" action="game.php?page=marketPlace&amp;action=buy" method="post">
				<input name="fleetID" value="{$FlyingFleetRow.id}" type="hidden">
				<input value="{$LNG.market_p_submit}" type="submit">
			</form>
			{else}
				{$FlyingFleetRow.reason}
			{/if}
		</td>
	</tr>

	{/if}
	{/foreach}
	</tbody>
</table>
<hr/>

<table id="fleetHistoryList" style="width:80%;white-space: nowrap;" class="tablesorter">
	<thead>
		<tr>
			<th>ID</th>
			<th>{$LNG.tkb_datum}</th>
			<th>{$LNG['market_fleet']}</th>
			<th  class="no-background no-border center">-></th>
			<th>{$LNG.market_p_cost_amount}</th>
		</tr>
	</thead>
	<tbody>
		{foreach name=History item=row from=$fleetHistory}
		<tr>
			<td>{$smarty.foreach.History.iteration}</td>
			<td>{$row.time}</td>
			<td>{$row.fleet_str}</td>
			<td class="no-background no-border center">
				{if $row.res_type == 1}<img src="{$dpath}images/metal.gif"/>
				{elseif $row.res_type == 2}<img src="{$dpath}images/crystal.gif"/>
				{elseif $row.res_type == 3}<img src="{$dpath}images/deuterium.gif"/>{/if}</td>
			<td>{$row.amount}</td>
		</tr>
		{/foreach}
	</tbody>
</table>

</div>
{/block}
{block name="script" append}
<script src="scripts/base/jquery.tablesorter.js"></script>
<script>
function reloadMarketBox() {
	var clRes = $("#resourceMBtn").attr("class");
	var clFleet = $("#fleetMBtn").attr("class");

	var resB = $("#resourceMarketBox");
	if(clRes !=null && (clRes.indexOf("selected") != -1 || clFleet ==null))
		resB.attr("style","display: inline");
	else
		resB.attr("style","display: none");

	var fleetB = $("#fleetMarketBox");
	if(clFleet !=null && (clFleet.indexOf("selected") != -1 || clRes ==null))
		fleetB.attr("style","display: inline");
	else
		fleetB.attr("style","display: none");
}

$(function() {
// Set the default
reloadMarketBox();
//
$("#resourceMBtn, #fleetMBtn").on("click", function(){
	$(".marketOption").removeClass("selected");
	$(this).addClass("selected");
	reloadMarketBox();
});

$("#tradeList").tablesorter({
	headers: {},
	debug: false
});

$("#tradeFleetList").tablesorter({
	headers: {},
	debug: false
});

$('#shipT').on('change', function (e) {
    var optionSelected = $("option:selected", this);
    var valueSelected = this.value;
		if(valueSelected == 1) {
			$('.HC').hide();
			$('.LC').show();
		}
		if(valueSelected == 2) {
			$('.LC').hide();
			$('.HC').show();
		}
});

$('#shipT').trigger("change");

$(".market_buy_button").click( function() {
	var c = confirm("{$LNG.market_confirm_are_you_sure}");
	if (c) {
		$(this).append('<input type="hidden" name="shipType" value="' + $("#shipT").val() + '">')
      this.parentElement.submit()
	}
	return c;
});
});</script>
{/block}
