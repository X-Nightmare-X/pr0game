{block name="title" prepend}{$LNG.gl_phalanx}{/block}
{block name="content"}
<table width="90%">
<tr>
    <th colspan="3">{$LNG.px_scan_position} [{$galaxy}:{$system}:{$planet}] ({$name})</th>
</tr>
<tr>
    <th colspan="3">{$LNG.px_fleet_movement}</th>
</tr>
	{foreach $fleetTable as $index => $fleet}
	<tr>
    <td style="color:yellow" data-time="{$fleet.returntime}" class="statictimer"></td> |
		<td id="fleettime_{$index}" class="fleets" data-fleet-end-time="{$fleet.returntime}" data-fleet-time="{$fleet.resttime}">00:00:00</td>
		<td>{$fleet.text}</td>
	</tr>
	{foreachelse}
		<tr><td colspan="3">{$LNG.px_no_fleet}</td></tr>
	{/foreach}
</table>
{/block}
