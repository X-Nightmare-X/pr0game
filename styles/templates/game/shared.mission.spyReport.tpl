<div class="spyRaport">

{literal}
<style>
	.danger {background-color: red; font-weight: bold;}
    .lowRess { background-color: indianred;}
    .midRess { background-color: chocolate;}
    .highRess { background-color: seagreen;}
    .realHighRess { background-color: royalblue;}
	/* .nonedanger {background-color: green;} */
</style>
{/literal}
	<div class="spyRaportHead">
		<a href="game.php?page=galaxy&amp;galaxy={$targetPlanet.galaxy}&amp;system={$targetPlanet.system}">{$title}</a>
	</div>
	<div class="spyRaportContainer">
		{if {$stbEnabled}}
			<div class="spyRaportContainerHead">
				{$LNG.spy_summary}
			</div>
			<div class="spyRaportContainerRow clearfix">
				<div class="spyRaportContainerCell 35">
					{$LNG.spy_total_resources}
				</div>
				<div class="spyRaportContainerCell 15">
          {number_format($ressources, 0, ",", ".")}
				</div>
				<div class="spyRaportContainerCell {$dangerClass}">
					{$LNG.spy_hazard_potential}
				</div>
				<div class="spyRaportContainerCell {$dangerClass}">
          {number_format($danger, 0, ",", ".")}
				</div>
			</div>
			<div class="spyRaportContainerRow clearfix">
				<div class="spyRaportContainerCell">
					{$LNG.spy_potential_resources}
				</div>
				<div class="spyRaportContainerCell">
					{number_format($ressourcesToRaid, 0, ",", ".")}
				</div>
				<div class="spyRaportContainerCell">
					{$LNG.spy_potential_recycling}
				</div>
				<div class="spyRaportContainerCell">
					{number_format($recyclePotential, 0, ",", ".")}
				</div>
			</div>
			<div class="spyRaportContainerRow clearfix">
				<div class="spyRaportContainerCell" >
					{$LNG.spy_necessary_transporters}
				</div>
				<div class="spyRaportContainerCell" >
					{number_format($nessesarrySmallTransporter, 0, ",", ".")} {$LNG.spy_small_transporter}  <br>
					{number_format($nessesarryLargeTransporter, 0, ",", ".")} {$LNG.spy_large_transporter}
				</div>
				<div class="spyRaportContainerCell" >
					{$LNG.spy_necessary_recycler}
				</div>
				<div class="spyRaportContainerCell" >
					{number_format($nessesarryRecy, 0, ",", ".")}
				</div>
			</div>
			<div class="spyRaportContainerRow clearfix">
				<div class="spyRaportContainerCell {$ressourcesByMarketValueClass}" >
					{$LNG.spy_market_value}
				</div>
				<div class="spyRaportContainerCell {$ressourcesByMarketValueClass}" >
					{number_format($ressourcesByMarketValue, 0, ",", ".")}
				</div>
				<div class="spyRaportContainerCell {$energyClass}" >
					{$LNG.spy_energy}
				</div>
				<div class="spyRaportContainerCell {$energyClass}" >
					{number_format($energy, 0, ",", ".")}
				</div>
			</div>
			<div class="spyRaportContainerRow clearfix">
				<div class="spyRaportContainerCell {$bestRessPerTimeClass}" >
					{$LNG.spy_best_resources_per_second}
				</div>
				<div class="spyRaportContainerCell {$bestRessPerTimeClass}" >
					{number_format($bestRessPerTime, 0, ",", ".")}
				</div>
				<div class="spyRaportContainerCell" >
					{$LNG.spy_best_planet}
				</div>
				<div class="spyRaportContainerCell" >
					{$bestPlanet}
				</div>
			</div>
			<div class="spyRaportContainerRow clearfix">
				<div class="spyRaportContainerCell {$dangerClass}" style="width: 50% !important; text-align:center;">
					<a href="game.php?page=fleetTable&amp;galaxy={$targetPlanet.galaxy}&amp;system={$targetPlanet.system}&amp;planet={$targetPlanet.planet}&amp;planettype={$targetPlanet.planet_type}&amp;target_mission=1&#35;ship_input[202]={$nessesarrySmallTransporter}" target:"_blank">
						<button type="button" style="text-align: center;">
							⚔️ {$nessesarrySmallTransporter} {$LNG.spy_small_transporter} ⚔️
						</button>
					</a>
				</div>
				<div class="spyRaportContainerCell {$dangerClass}" style="width: 50% !important; text-align:center;">
					<a href="game.php?page=fleetTable&amp;galaxy={$targetPlanet.galaxy}&amp;system={$targetPlanet.system}&amp;planet={$targetPlanet.planet}&amp;planettype={$targetPlanet.planet_type}&amp;target_mission=1&#35;ship_input[203]={$nessesarryLargeTransporter}"target="_blank">
						<button type="button" style="text-align: center;">
							⚔️  {$nessesarryLargeTransporter} {$LNG.spy_large_transporter} ⚔️
						</button>
					</a>
				</div>
			</div>
			</div>
		{/if}
	</div>
	<br>
	{foreach $spyData as $Class => $elementIDs}
	<div class="spyRaportContainer">
	<div class="spyRaportContainerHead spyRaportContainerHeadClass{$Class}">{$LNG.tech.$Class}</div>
	{foreach $elementIDs as $elementID => $amount}
	{if ($amount@iteration % 2) === 1}<div class="spyRaportContainerRow clearfix">{/if}
		<div class="spyRaportContainerCell"><a href='#' onclick='return Dialog.info({$elementID})' class='tooltip' data-tooltip-content="<table><tr><th>{$LNG.tech.{$elementID}}</th></tr><tr><table class='hoverinfo'><tr><td><img src='{$dpath}gebaeude/{$elementID}.{if $elementID >=600 && $elementID <= 699}jpg{else}gif{/if}'></td><td>{$LNG.shortDescription.$elementID}</td></tr></table></tr></table>">{$LNG.tech.$elementID}</a></div>
		<div class="spyRaportContainerCell">{number_format($amount, 0, ",", ".")}</div>
	{if ($amount@iteration % 2) === 0}</div>{/if}
	{/foreach}
	</div>
	{/foreach}
	<div class="spyRaportFooter">
		<a href="game.php?page=fleetTable&amp;galaxy={$targetPlanet.galaxy}&amp;system={$targetPlanet.system}&amp;planet={$targetPlanet.planet}&amp;planettype={$targetPlanet.planet_type}&amp;target_mission=1">{$LNG.type_mission_1}</a>
		<br>{if $totalShipDefCount > 0 && $targetChance >= $spyChance}{$LNG.sys_mess_spy_destroyed}{else}{sprintf($LNG.sys_mess_spy_lostproba, $targetChance)}{/if}
		{if $isBattleSim}<br><a href="game.php?page=battleSimulator{foreach $spyData as $Class => $elementIDs}{foreach $elementIDs as $elementID => $amount}&amp;im[{$elementID}]={$amount}{/foreach}{/foreach}">{$LNG.fl_simulate}</a>{/if}
	</div>
</div>
