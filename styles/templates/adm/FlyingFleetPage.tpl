{include file="overall_header.tpl"}
<p><center><a href="admin.php?page=fleets&amp;sendfleetsback=1"><button>{$LNG.ff_sendFleetsBack}</button></a></center></p>
<table width="90%">
<tr>
	<th>{$LNG.ff_fleetid}</th>
	<th>{$LNG.ff_mission}</th>
	<th>{$LNG.ff_starttime}</th>
	<th>{$LNG.ff_ships}</th>
	<th>{$LNG.ff_startuser}</th>
	<th>{$LNG.ff_startplanet}</th>
	<th>{$LNG.ff_arrivaltime}</th>
	<th>{$LNG.ff_targetuser}</th>
	<th>{$LNG.ff_targetplanet}</th>
  <th>{$LNG.ff_endtime}</th>
  <th>{$LNG.ff_holdtime}</th>
  <th><a href="admin.php?page=fleets&amp;massunlock=1" class="colorPositive">{$LNG.ff_lock}</a></th>
  <th>{$LNG.ff_sendFleetBack}</th>
</tr>
{foreach $FleetList as $FleetRow}
<tr>
	<td>{$FleetRow.fleetID}</td>
	<td><a href="#" data-tooltip-content="<table style='width:200px'>{foreach $FleetRow.resource as $resourceID => $resourceCount}<tr><td style='width:50%'>{$LNG.tech.$resourceID}</td><td style='width:50%'>{number_format($resourceCount, 0, ',', '.')}</td></tr>{/foreach}</table>" class="tooltip">{$LNG["type_mission_{$FleetRow.missionID}"]}{if $FleetRow.acsID != 0}<br>{$FleetRow.acsID}<br>{$FleetRow.acsName}{/if}&nbsp;(R)</a></td>
	<td>{$FleetRow.starttime}</td>
	<td><a href="#" data-tooltip-content="<table style='width:200px'>{foreach $FleetRow.ships as $shipID => $shipCount}<tr><td style='width:50%'>{$LNG.tech.$shipID}</td><td style='width:50%'>{number_format($shipCount, 0, ',', '.')}</td></tr>{/foreach}</table>" class="tooltip">{number_format($FleetRow.count, 0, ",", ".")}&nbsp;(D)</a></td>
	<td>{$FleetRow.startUserName} (ID:&nbsp;{$FleetRow.startUserID})</td>
	<td>{$FleetRow.startPlanetName}&nbsp;[{$FleetRow.startPlanetGalaxy}:{$FleetRow.startPlanetSystem}:{$FleetRow.startPlanetPlanet}] (ID:&nbsp;{$FleetRow.startPlanetID})</td>
	<td>{if $FleetRow.state == 0}<span style="color:lime;">{/if}{$FleetRow.arrivaltime}{if $FleetRow.state == 0}</span>{/if}</td>
	<td>{if $FleetRow.targetUserID != 0}{$FleetRow.targetUserName} (ID:&nbsp;{$FleetRow.targetUserID}){/if}</td>
	<td>{$FleetRow.targetPlanetName}&nbsp;[{$FleetRow.targetPlanetGalaxy}:{$FleetRow.targetPlanetSystem}:{$FleetRow.targetPlanetPlanet}]{if $FleetRow.targetPlanetID != 0} (ID:&nbsp;{$FleetRow.targetPlanetID}){/if}</td>
	<td>{if $FleetRow.state == 1}<span style="color:lime;">{/if}{$FleetRow.endtime}{if $FleetRow.state == 0}</span>{/if}</td>
	<td>{if $FleetRow.stayhour != 0}{if $FleetRow.state == 2}<span style="color:lime;">{/if}{$FleetRow.staytime} ({$FleetRow.stayhour}&nbsp;h){if $FleetRow.state == 0}</span>{/if}{else}-{/if}</td>
  <td><a href="admin.php?page=fleets&amp;id={$FleetRow.fleetID}&amp;lock={if $FleetRow.lock}0" class="colorPositive">{$LNG.ff_unlock}{elseif $FleetRow.error}2" class="colorNegative">{$LNG.ff_del}{else}1" class="colorNegative">{$LNG.ff_lock}{/if}</a></td>
  <td>{if $FleetRow.state == 0}<a href="admin.php?page=fleets&amp;sendfleetback={$FleetRow.fleetID}"><button>{$LNG.ff_sendFleetBack}</button></a>{else}-{/if}</td>
</tr>
{foreachelse}
<tr>
	<td colspan="12">{$LNG.ff_no_fleets}</td>
</tr>
{/foreach}
</table>
</body>
{include file="overall_footer.tpl"}
