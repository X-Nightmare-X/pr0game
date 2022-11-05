{block name="title" prepend}{$LNG.lm_fleet}{/block}
{block name="content"}
<table class="table519">
	<tr style="height:20px">
		<th colspan="2" class="success">{$LNG.fl_fleet_sended}</span></th>
	</tr>
    <tr style="height:20px">
        <td>{$LNG.fl_mission}</td>
        <td>{$LNG["type_mission_{$targetMission}"]}</td>
	</tr>
    <tr style="height:20px">
        <td>{$LNG.fl_distance}</td>
        <td>{number_format($distance, 0, ",", ".")}</td>
    </tr>
    <tr style="height:20px">
        <td>{$LNG.fl_fleet_speed}</td>
        <td>{number_format($MaxFleetSpeed, 0, ",", ".")}</td>
    </tr>
    <tr style="height:20px">
        <td>{$LNG.fl_fuel_consumption}</td>
        <td>{number_format($consumption, 0, ",", ".")}</td>
    </tr>
    <tr style="height:20px">
        <td>{$LNG.fl_from}</td>
        <td>{$from}</td>
    </tr>
    <tr style="height:20px">
        <td>{$LNG.fl_destiny}</td>
        <td>{$destination}</td>
    </tr>
    <tr style="height:20px">
        <td>{$LNG.fl_arrival_time}</td>
        <td>{$fleetStartTime}</td>
    </tr>
    <tr style="height:20px">
        <td>{$LNG.fl_return_time}</td>
        <td>{$fleetEndTime}</td>
    </tr>
    <tr style="height:20px">
        <th colspan="2">{$LNG.fl_fleet}</th>
    </tr>
	{foreach $FleetList as $ShipID => $ShipCount}
	<tr>
		<td>{$LNG.tech.{$ShipID}}</td>
		<td>{number_format($ShipCount, 0, ",", ".")}</td>
	</tr>
	{/foreach}
</table>
{/block}