<div class="spyRaport">
	<div class="spyRaportHead" coords="{$targetPlanet.galaxy}:{$targetPlanet.system}:{$targetPlanet.planet}">
		<a href="game.php?page=galaxy&amp;galaxy={$targetPlanet.galaxy}&amp;system={$targetPlanet.system}">{$title}</a>
	</div>
    {if {$stbEnabled}}
	<div class="spyRaportContainer" name="scavengeruwu">
			<div class="spyRaportContainerHead">
				{$LNG.spy_summary}
			</div>
			<div class="spyRaportContainerRow clearfix">
				<div class="spyRaportContainerCell 35">
					{$LNG.spy_total_resources}
				</div>
				<div class="spyRaportContainerCell 15" name="totalRes"></div>
				<div class="spyRaportContainerCell {$dangerClass}">
					{$LNG.spy_hazard_potential}
				</div>
				<div class="spyRaportContainerCell {$dangerClass}" name="dangerValue">
          			{number_format($danger, 0, ",", ".")}
				</div>
			</div>
			<div class="spyRaportContainerRow clearfix">
				<div class="spyRaportContainerCell">
					{$LNG.spy_potential_resources}
				</div>
				<div class="spyRaportContainerCell" name="resToRaid"></div>
				<div class="spyRaportContainerCell">
					{$LNG.spy_potential_recycling}
				</div>
				<div class="spyRaportContainerCell" name="resToRec"></div>
			</div>
			<div class="spyRaportContainerRow clearfix">
				<div class="spyRaportContainerCell" >
					{$LNG.spy_necessary_transporters}
				</div>
				<div class="spyRaportContainerCell" >
          {pretty_number($smallCargoNeeded)} {$LNG.spy_small_transporter}  <br>
          {pretty_number($largeCargoNeeded)} {$LNG.spy_large_transporter}
				</div>
				<div class="spyRaportContainerCell" >
					{$LNG.spy_necessary_recycler}
				</div>
				<div class="spyRaportContainerCell" name="recNeeded"></div>
			</div>
			<div class="spyRaportContainerRow clearfix">
				<div class="spyRaportContainerCell" classadd="marketClass" >
					{$LNG.spy_market_value}
				</div>
				<div class="spyRaportContainerCell" name="marketValue" classadd="marketClass"></div>
				<div class="spyRaportContainerCell {$energyClass}" >
					{$LNG.spy_energy}
				</div>
				<div class="spyRaportContainerCell {$energyClass}" name="energyValue">
					{number_format($energy, 0, ",", ".")}
				</div>
			</div>
			<div class="spyRaportContainerRow clearfix">
				<div class="spyRaportContainerCell" classadd="timePerResClass">
					{$LNG.spy_best_resources_per_second}
				</div>
				<div class="spyRaportContainerCell" name="resPerSec" classadd="timePerResClass"></div>
				<div class="spyRaportContainerCell" >
					{$LNG.spy_best_planet}
				</div>
				<div class="spyRaportContainerCell" name="bestPlanet"></div>
			</div>
			<div class="spyRaportContainerRow clearfix">
				<div class="spyRaportContainerCell {$dangerClass}" style="width: 50% !important; text-align:center;">
					<a href="game.php?page=fleetTable&amp;galaxy={$targetPlanet.galaxy}&amp;system={$targetPlanet.system}&amp;planet={$targetPlanet.planet}&amp;planettype={$targetPlanet.planet_type}&amp;target_mission=1&#35;ship_input[202]={$smallCargoNeeded}" target="_blank">
						<button type="button" style="text-align: center;">
              ⚔️ {pretty_number($smallCargoNeeded)} {$LNG.spy_small_transporter} ⚔️
						</button>
					</a>
				</div>
				<div class="spyRaportContainerCell {$dangerClass}" style="width: 50% !important; text-align:center;">
					<a href="game.php?page=fleetTable&amp;galaxy={$targetPlanet.galaxy}&amp;system={$targetPlanet.system}&amp;planet={$targetPlanet.planet}&amp;planettype={$targetPlanet.planet_type}&amp;target_mission=1&#35;ship_input[203]={$largeCargoNeeded}" target="_blank">
						<button type="button" style="text-align: center;">
              ⚔️ {pretty_number($largeCargoNeeded)} {$LNG.spy_large_transporter} ⚔️
						</button>
					</a>
				</div>
			</div>
			</div>
	</div>
{/if}
	{foreach $spyData as $Class => $elementIDs}
	<div class="spyRaportContainer">
    <div class="spyRaportContainerHead spyRaportContainerHeadClass{$Class}">{$LNG.tech.$Class}</div>
    {foreach $elementIDs as $elementID => $amount}
      {if ($amount@iteration % 2) === 1}
        <div class="spyRaportContainerRow clearfix">
      {/if}
        <div class="spyRaportContainerCell">
          <a href='#' onclick='return Dialog.info({$elementID})' class='tooltip' data-tooltip-content="<table><tr><th>{$LNG.tech.{$elementID}}</th></tr><tr><table class='hoverinfo'><tr><td><img src='{$dpath}gebaeude/{$elementID}.
          {if $elementID >=600 && $elementID <= 699}jpg{else}gif{/if}'></td><td>{$LNG.shortDescription.$elementID}</td></tr></table></tr></table>">{$LNG.tech.$elementID}</a>
        </div>
        <div class="spyRaportContainerCell" data-info="{$Class}_{$elementID}">
          {number_format($amount, 0, ",", ".")}{if isset($repair_order[$elementID])} <span style="color: yellowgreen;">(+ {$repair_order[$elementID]})</span>{/if}
        </div>
      {if ($amount@iteration % 2) === 0}</div>{/if}
    {/foreach}
	</div>
  {if !empty($repair_order) && $Class == 200}
    <div><span style="color: yellowgreen;">(+ X)</span> {$LNG.sys_mess_spy_repair_order}</div>
  {/if}
  <br>
	{/foreach}
	<div class="spyRaportFooter">
		<a href="game.php?page=fleetTable&amp;galaxy={$targetPlanet.galaxy}&amp;system={$targetPlanet.system}&amp;planet={$targetPlanet.planet}&amp;planettype={$targetPlanet.planet_type}&amp;target_mission=1">{$LNG.type_mission_1}</a>
		<br>{if $totalShipDefCount > 0 && $targetChance >= $spyChance}{$LNG.sys_mess_spy_destroyed}{else}{sprintf($LNG.sys_mess_spy_lostproba, $targetChance)}{/if}
		{if $isBattleSim}<br><a href="game.php?page=battleSimulator{foreach $spyData as $Class => $elementIDs}{foreach $elementIDs as $elementID => $amount}&amp;im[{$elementID}]={$amount}{/foreach}{/foreach}">{$LNG.fl_simulate}</a>{/if}
	</div>
</div>
