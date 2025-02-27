{block name="title" prepend}{if $mode == "defense"}{$LNG.lm_defenses}{else}{$LNG.lm_shipshard}{/if}{/block}
{block name="content"}
	{include file='shared.messages.tpl'}

	{if !$NotBuilding}
		<table width="70%" id="infobox" style="border: 2px solid red; text-align:center;background:transparent">
			<tr><td>{$LNG.bd_building_shipyard}</td></tr>
		</table><br><br>
	{/if}

	{if !empty($BuildList)}
		{if $umode == 0}
			<div style="text-align: center;">
				<div id="bx" class="z"></div>
				<div class="ship">
					<form action="game.php?page=shipyard&amp;mode={$mode}" method="post" >
						<input type="hidden" name="action" value="delete">
						<div >
							<select name="auftr[]" id="auftr" onchange="this.form.myText.setAttribute('size', this.value);" multiple class="shipl"><option>&nbsp;</option></select><br><br>{$LNG.bd_cancel_warning}<br><input class="z" type="submit" value="{$LNG.bd_cancel_send}" />
						</div>
					</form>
					<br><span id="timeleft" data-umode="{$umode}"></span><br><br>
				</div>
			</div><br>
		{else}
			<div style="text-align: center;">
				<div id="bxumode" class="zumode"></div>
				<div class="shipumode">
					<form action="game.php?page=shipyard&amp;mode={$mode}" method="post" >
						<input type="hidden" name="action" value="delete">
						<div >
							<select name="auftrumode[]" id="auftrumode" onchange="this.form.myText.setAttribute('size', this.value);" multiple class="shipl"><option>&nbsp;</option></select><br><br><br><input class="z" value="{$LNG['bd_paused']}" />
						</div>
					</form>
					<br><span id="timeleft" data-umode="{$umode}"></span><br><br>
				</div>
			</div><br>
		{/if}
	{/if}

	{if $mode == "fleet"}
		<div class="planeto">
			<button id="ship1">{$LNG.fm_civil}</button> | <button id="ship2">{$LNG.fm_military}</button> | <button id="ship3" class="selected">{$LNG.fm_all}</button>
		</div>
	{/if}	

	{foreach $elementList as $ID => $Element}
		<div class="infos {if $Element.fade}unavailable{/if}" id="s{$ID}">
			<div class="buildn"><a href="#" onclick="return Dialog.info({$ID})">{$LNG.tech.{$ID}}</a>
				<span id="val_{$ID}">{if $Element.available != 0} ({$LNG.bd_available} {number_format($Element.available, 0, ",", ".")}){/if}</span>
			</div>
			<div class="buildl">
				<a href="#" onclick="return Dialog.info({$ID})">
					<img style="float: left;" src="{$dpath}gebaeude/{$ID}.gif" alt="{$LNG.tech.{$ID}}" width="120" height="120">
				</a>
				{if !empty($Element.requirements)}
					{$LNG.tt_requirements}: </br>
					{foreach $Element.requirements as $requireID => $NeedLevel}
						<a href="#" onclick="return Dialog.info({$requireID})">
						<span class="{if $NeedLevel.own < $NeedLevel.count}colorNeutral{else}colorPositive{/if}">
							{$LNG.tech.$requireID} ({$LNG.tt_lvl} {$NeedLevel.own}/{$NeedLevel.count})
						</span>
						</a>{if !$NeedLevel@last}<br>{/if}
					{/foreach}
				{else}
					{$LNG.bd_remaining}:<br>
					{foreach $Element.costOverflow as $ResType => $ResCount}
						<a href='#' onclick='return Dialog.info({$ResType})' class='tooltip' data-tooltip-content="
							<table>
								<tr><th>{$LNG.tech.{$ResType}}</th></tr>
								<tr>
									<table class='hoverinfo'>
									<tr>
										<td><img src='{$dpath}gebaeude/{$ResType}.{if $ResType >=600 && $ResType <= 699}jpg{else}gif{/if}'></td>
										<td>{$LNG.shortDescription.$ResType}</td>
									</tr>
									</table>
								</tr>
							</table>">{$LNG.tech.{$ResType}}</a>: <span style="font-weight:700">{number_format($ResCount, 0, ",", ".")}</span><br>
					{/foreach}
					<p>{$LNG.bd_max_ships_long}:<span style="font-weight:700"><br>{number_format($Element.maxBuildable, 0, ",", ".")}</p>
				{/if}
			</div>
			<div class="buildl">
				<span>
					{foreach $Element.costResources as $RessID => $RessAmount}
						<a href='#' onclick='return Dialog.info({$RessID})' class='tooltip' data-tooltip-content="
						<table>
							<tr><th>{$LNG.tech.{$RessID}}</th></tr>
							<tr>
								<table class='hoverinfo'>
								<tr>
									<td><img src='{$dpath}gebaeude/{$RessID}.{if $RessID >=600 && $RessID <= 699}jpg{else}gif{/if}'></td>
									<td>{$LNG.shortDescription.$RessID}</td>
								</tr>
								</table>
							</tr>
						</table>">{$LNG.tech.{$RessID}}</a>: <b><span class="{if $Element.costOverflow[$RessID] == 0}colorPositive{else}colorNegative{/if}">{number_format($RessAmount, 0, ",", ".")}</span></b>
					{/foreach}
				</span>
				<br><br>
				{if $ID==212} +{$SolarEnergy} {$LNG.tech.911}<br>{/if}
				<span>
					{if $vacation}
						<span class="colorNeutral">{$LNG.op_options_vacation_activated}</span>
					{elseif !empty($Element.requirements)}
						<span class="colorNeutral">{$LNG.bd_requirements}</span>
					{elseif $Element.AlreadyBuild}
						<span class="colorNegative">{$LNG.bd_protection_shield_only_one}</span>
					{elseif !$NotBuilding}
						<span class="colorNeutral">{$LNG.sys_buildlist_fail}</span>
					{elseif !$Element.buyable}
						<span class="colorNeutral">{$LNG.bd_remaining}</span>
					{else}
						<form action="game.php?page=shipyard&amp;mode={$mode}" method="post" id="s{$ID}">
							<input type="number" name="fmenge[{$ID}]" id="input_{$ID}" size="3" maxlength="{$maxlength}" value="0" tabindex="{$smarty.foreach.FleetList.iteration}" >
							<input type="button" value="{$LNG.bd_max_ships}" onclick="$('#input_{$ID}').val('{$Element.maxBuildable}')">
							<input class="b colorPositive" type="submit" value="{$LNG.bd_build_ships}">
						</form>
					{/if}
					<p>{$LNG.fgf_time} <span class="statictime" timestamp="{$Element.elementTime}"></span></p>
				</span>
			</div>
		</div>
	{/foreach}
{/block}
{block name="script" append}
	<script type="text/javascript">
		data			= {$BuildList|json_encode};
		bd_operating	= '{$LNG.bd_operating}';
		bd_available	= '{$LNG.bd_available}';
	</script>

	{if !empty($BuildList)}
	<script src="scripts/base/bcmath.js"></script>
	<script src="scripts/game/shipyard.js"></script>
	<script type="text/javascript">
		$(function() {
			ShipyardInit();
		});
	</script>
	{/if}
	<script>
		$(function() {
			$("#ship1").on('click', function() {
				$(".infos").hide();
				$("#s202, #s203, #s208, #s209, #s212").show();
				$("#ship2, #ship3").removeClass("selected");

				$(this).addClass("selected");
			});
		});
		$(function() {
			$("#ship2").on('click', function() {
				$(".infos").hide();
				$("#s204, #s205, #s206, #s207, #s210, #s211, #s213, #s214").show();
				$("#ship1, #ship3").removeClass("selected");

				$(this).addClass("selected");
			});
		});
		$(function() {
			$("#ship3").on('click', function() {
				$(".infos").show();

				$("#ship1, #ship2").removeClass("selected");

				$(this).addClass("selected");
			});
		});
	</script>
{/block}
