<table>
	<tbody>
		{if !empty($FleetInfo.tech)}
		<tr>
			<td style="width:50%">{$LNG.in_engine}</td>
			<td style="width:50%">
				{if $FleetInfo.tech == 1}{$LNG.tech.115}
				{elseif $FleetInfo.tech == 2}{$LNG.tech.117}
				{elseif $FleetInfo.tech == 3}{$LNG.tech.118}
				{elseif $FleetInfo.tech == 4}{$LNG.tech.115}<br><span style="color:yellow">({$LNG.tech.117} >= 5)</span>
				{elseif $FleetInfo.tech == 5}{$LNG.tech.117}<br><span style="color:yellow">({$LNG.tech.118} >= 8)</span>
				{elseif $FleetInfo.tech == 6}{$LNG.tech.115}<br><span style="color:yellow">({$LNG.tech.117} >= 17)</span><br><span style="color:pink">({$LNG.tech.118} >= 15)</span>
				{else}-
				{/if}
			</td>
		</tr>
		{/if}
		<tr>
			<td style="width:50%">{$LNG.in_struct_pt}</td>
			<td style="width:50%">{number_format($FleetInfo.structure, 0, ",", ".")}</td>
		</tr>
		<tr>
			<td style="width:50%">{$LNG.in_attack_pt}</td>
			<td style="width:50%">{number_format($FleetInfo.attack, 0, ",", ".")}</td>
		</tr>
		<tr>
			<td style="width:50%">{$LNG.in_shield_pt}</td>
			<td style="width:50%">{number_format($FleetInfo.shield, 0, ",", ".")}</td>
		</tr>
		{if !empty($FleetInfo.capacity)}
		<tr>
			<td style="width:50%">{$LNG.in_capacity}</td>
			<td style="width:50%">{number_format($FleetInfo.capacity, 0, ",", ".")}</td>
		</tr>
		{/if}
		{if !empty($FleetInfo.speed1)}
		<tr>
			<td style="width:50%">{$LNG.in_base_speed}</td>
			<td style="width:50%">{number_format($FleetInfo.speed1, 0, ",", ".")}
				{if $FleetInfo.speed1 != $FleetInfo.speed2} <span style="color:yellow">({number_format($FleetInfo.speed2, 0, ",", ".")})</span>{/if}
				{if $FleetInfo.speed3 != null} <span style="color:pink">({number_format($FleetInfo.speed3, 0, ",", ".")})</span>{/if}
			</td>
		</tr>
		{/if}
		{if !empty($FleetInfo.consumption1)}
		<tr>
			<td style="width:50%">{$LNG.in_consumption}</td>
			<td style="width:50%">{number_format($FleetInfo.consumption1, 0, ",", ".")}
				{if $FleetInfo.consumption1 != $FleetInfo.consumption2} <span style="color:yellow">({number_format($FleetInfo.consumption2, 0, ",", ".")})</span>{/if}
			</td>
		</tr>
		{/if}
	</tbody>
</table>