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
			<div class="spyRaportContainerHead">
				Zusammenfassung
			</div>
			<div class="spyRaportContainerRow clearfix">
				<div class="spyRaportContainerCell 35">
					Gesamte Ressourcen
				</div>
				<div class="spyRaportContainerCell 15">
					{$ressources}
				</div>
				<div class="spyRaportContainerCell {$dangerClass}">
					Gefahrenpotenzial
				</div>
				<div class="spyRaportContainerCell {$dangerClass}">
					{$danger}
				</div>
			</div>
			<div class="spyRaportContainerRow clearfix">
				<div class="spyRaportContainerCell">
					Potenzielle Ressourcen
				</div>
				<div class="spyRaportContainerCell">
					{$ressourcesToRaid}
				</div>
				<div class="spyRaportContainerCell">
					Recyclepotenzial
				</div>
				<div class="spyRaportContainerCell">
					{$recyclePotential}
				</div>
			</div>
			<div class="spyRaportContainerRow clearfix">
				<div class="spyRaportContainerCell" >
					Notwendige Transporter
				</div>
				<div class="spyRaportContainerCell" >
					{$nessesarrySmallTransporter} KT / {$nessesarryGreatTransporter} GT
				</div>
				<div class="spyRaportContainerCell" >
					Notwendige Recycler
				</div>
				<div class="spyRaportContainerCell" >
					{$nessesarryRecy}
				</div>
			</div>
			<div class="spyRaportContainerRow clearfix">
				<div class="spyRaportContainerCell {$ressourcesByMarketValueClass}" >
					Marktwert
				</div>
				<div class="spyRaportContainerCell {$ressourcesByMarketValueClass}" >
					{$ressourcesByMarketValue}
				</div>
				<div class="spyRaportContainerCell {$energyClass}" >
					Energy
				</div>
				<div class="spyRaportContainerCell {$energyClass}" >
					{$energy}
				</div>
			</div>
			<div class="spyRaportContainerRow clearfix">
				<div class="spyRaportContainerCell {$bestRessPerTimeClass}" >
					bestRessPerTime
				</div>
				<div class="spyRaportContainerCell {$bestRessPerTimeClass}" >
					{$bestRessPerTime}
				</div>
				<div class="spyRaportContainerCell" >
					bestPlanet
				</div>
				<div class="spyRaportContainerCell" >
					{$bestPlanet}
				</div>
			</div>
			<div class="spyRaportContainerRow clearfix">
				<div class="spyRaportContainerCell {$dangerClass}" style="width: 50% !important;">
					<a href="game.php?page=fleetTable&amp;galaxy={$targetPlanet.galaxy}&amp;system={$targetPlanet.system}&amp;planet={$targetPlanet.planet}&amp;planettype={$targetPlanet.planet_type}&amp;target_mission=1&#35;ship_input[202]={$nessesarrySmallTransporter}">mit {$nessesarrySmallTransporter} KT angreifen</a>
				</div>
				<div class="spyRaportContainerCell {$dangerClass}" style="width: 50% !important;">
					<a href="game.php?page=fleetTable&amp;galaxy={$targetPlanet.galaxy}&amp;system={$targetPlanet.system}&amp;planet={$targetPlanet.planet}&amp;planettype={$targetPlanet.planet_type}&amp;target_mission=1&#35;ship_input[203]={$nessesarryGreatTransporter}">mit {$nessesarryGreatTransporter} GT angreifen</a>
				</div>
			</div>
		</div>
	</div>
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
