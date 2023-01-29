{include file="overall_header.tpl"}
<form method="post">
<input type="hidden" name="action" value="send">
<!-- Zielplaneten definieren -->
<table width="760px" style="color:#FFFFFF">
	<tr>
    	<th colspan="3">{$LNG.ga_definetarget}</th>
	</tr>
	<tr style="height:26px;">
		<td width="50%">{$LNG.ga_planettypes}:</td>
		<td width="50%">
			<table style="color:#FFFFFF">
				<tr>
					<td class="transparent"><input type="checkbox" name="planet" value="1" checked></td>
					<td class="transparent left">{$LNG.fcm_planet}</td>
				</tr>
				<tr>
					<td class="transparent"><input type="checkbox" name="moon" value="1"></td>
					<td class="transparent left">{$LNG.fcm_moon}</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr style="height:26px;"><td width="50%">{$LNG.ga_homecoordinates}:</td><td width="50%"><input type="checkbox" name="mainplanet" value="1"></td></tr>
	<tr style="height:26px;"><td width="50%">{$LNG.ga_no_inactives}:</td><td width="50%"><input type="checkbox" name="no_inactive" value="1"></td></tr>

	<!-- Rohstoffe -->
	<tr>
			<th colspan="2">{$LNG.tech.900}</th>
	</tr>
	{foreach item=Element from=$reslist.resstype.1}
	<tr><td width="50%">{$LNG.tech.{$Element}}:</td><td width="50%"><input type="text" name="element_{$Element}" value="0" ></td></tr>
	{/foreach}
	{foreach item=Element from=$reslist.resstype.3}
	<tr><td width="50%">{$LNG.tech.{$Element}}:</td><td width="50%"><input type="text" name="element_{$Element}" value="0" ></td></tr>
	{/foreach}

	<!-- Gebäude -->
	<tr>
			<th colspan="2">{$LNG.tech.0}</th>
	</tr>
	{foreach item=Element from=$reslist.build}
	<tr><td width="50%">{$LNG.tech.{$Element}}:</td><td width="50%"><input type="text" name="element_{$Element}" value="0" ></td></tr>
	{/foreach}

	<!-- Technologie -->
	<tr>
			<th colspan="2">{$LNG.tech.100}</th>
	</tr>
	{foreach item=Element from=$reslist.tech}
	<tr><td width="50%">{$LNG.tech.{$Element}}:</td><td width="50%"><input type="text" name="element_{$Element}" value="0" ></td></tr>
	{/foreach}

	<!-- Schiffe -->
	<tr>
			<th colspan="2">{$LNG.tech.200}</th>
	</tr>
	{foreach item=Element from=$reslist.fleet}
	<tr><td width="50%">{$LNG.tech.{$Element}}:</td><td width="50%"><input type="text" name="element_{$Element}" value="0" ></td></tr>
	{/foreach}

	<!-- Verteidigung -->
	<tr>
			<th colspan="2">{$LNG.tech.400}</th>
	</tr>
	{foreach item=Element from=$reslist.defense}
	<tr><td width="50%">{$LNG.tech.{$Element}}:</td><td width="50%"><input type="text" name="element_{$Element}" value="0" ></td></tr>
	{/foreach}

	<tr>
		<td colspan="3"><input type="submit" value="{$LNG.qe_send}"></td>
	</tr>
</table>
</form>
{include file="overall_footer.tpl"}
