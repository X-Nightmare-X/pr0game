{block name="title" prepend}{$LNG.lm_fleet}{/block}
{block name="content"}
<form action="game.php?page=fleetStep2" method="post" onsubmit="return CheckTarget()" id="form">
	<input type="hidden" name="token" value="{$token}">
	<input type="hidden" name="fleet_group" value="0">
	<input type="hidden" name="target_mission" value="{$mission}">
	<table class="table519" style="table-layout: fixed;">
		<tr style="height:20px;">
			<th colspan="2">{$LNG.fl_send_fleet}</th>
		</tr>
		<tr style="height:20px;">
			<td style="width:50%">{$LNG.fl_destiny}</td>
			<td>
				<input type="number" id="galaxy" name="galaxy" maxlength="4" oninput="updateVars()" value="{$galaxy}" style="max-width:4em">
				<input type="number" id="system" name="system" maxlength="6" oninput="updateVars()" value="{$system}" style="max-width:6em">
				<input type="number" id="planet" name="planet" maxlength="4" oninput="updateVars()" value="{$planet}" style="max-width:4em">
				<select id="type" name="type" onchange="updateVars()">
					{html_options options=$typeSelect selected=$type}
				</select>
			</td>
		</tr>
		<tr style="height:20px;">
			<td>{$LNG.fl_fleet_speed}</td>
			<td>
				<select id="speed" name="speed" onChange="updateVars(false)">
					{html_options options=$speedSelect}
				</select> %
			</td>
		</tr>
		<tr style="height:20px;">
			<td>{$LNG.fl_distance}</td>
			<td id="distance">-</td>
		</tr>
		<tr style="height:20px;">
			<td>{$LNG.fl_flying_time}</th>
			<td id="duration">-</td>
		</tr>
		<tr style="height:20px;">
			<td>{$LNG.fl_flying_arrival}</th>
			<td id="arrival">-</td>
		</tr>
		<tr style="height:20px;">
			<td>{$LNG.fl_flying_return}</th>
			<td id="return">-</td>
		</tr>
		<tr style="height:20px;">
			<td>{$LNG.fl_fuel_consumption}</td>
			<td id="consumption">-</td>
		</tr>
		<tr style="height:20px;">
			<td>{$LNG.fl_max_speed}</td>
			<td id="maxspeed">-</td>
		</tr>
		<tr style="height:20px;">
			<td>{$LNG.fl_cargo_capacity}</td>
			<td id="storage">-</td>
		</tr>
	</table>

<table class="table519" style="table-layout: fixed;">
	<tr style="height:20px;">
		<th colspan="2">{$LNG.fl_system_places}</th>
	</tr>
	<tr style="height:20px;">
		<td>
			<a href="javascript:setTarget({$galaxy},{$system},{$config.max_planets+1},1);updateVars();">{$LNG.type_mission_15}[{$galaxy}:{$system}:{$config.max_planets+1}]</a>
		</td>
		<td>
			<a href="javascript:setTarget({$galaxy},{$system},{$config.max_planets+2},1);updateVars();">{$LNG.type_mission_16}[{$galaxy}:{$system}:{$config.max_planets+2}]</a>
		</td>
	</tr>
</table>

	{if isModuleAvailable($smarty.const.MODULE_SHORTCUTS)}
	<table class="table519 shortcut" style="table-layout: fixed;">
		<tr style="height:20px;">
			<th colspan="{$themeSettings.SHORTCUT_ROWS_ON_FLEET1}">{$LNG.fl_shortcut} (<a href="#" onclick="EditShortcuts();return false" class="shortcut-link-edit shortcut-link">{$LNG.fl_shortcut_edition}</a><a href="#" onclick="SaveShortcuts();return false" class="shortcut-edit">{$LNG.fl_shortcut_save}</a>)</th>
		</tr>

		{foreach $shortcutList as $shortcutID => $shortcutRow}
			{if ($shortcutRow@iteration % $themeSettings.SHORTCUT_ROWS_ON_FLEET1) === 1}<tr style="height:20px;" class="shortcut-row">{/if}
			<td style="width:{100 / $themeSettings.SHORTCUT_ROWS_ON_FLEET1}%" class="shortcut-colum shortcut-isset">
				<div class="shortcut-link">
					<a href="javascript:setTarget({$shortcutRow.galaxy},{$shortcutRow.system},{$shortcutRow.planet},{$shortcutRow.type});updateVars();">{$shortcutRow.name}{if $shortcutRow.type == 1}{$LNG.fl_planet_shortcut}{elseif $shortcutRow.type == 2}{$LNG.fl_debris_shortcut}{elseif $shortcutRow.type == 3}{$LNG.fl_moon_shortcut}{/if} [{$shortcutRow.galaxy}:{$shortcutRow.system}:{$shortcutRow.planet}]</a>
				</div>
				<div class="shortcut-edit">
					<input type="text" class="shortcut-input" name="shortcut[{$shortcutID}][name]" maxlength="32" value="{$shortcutRow.name}">
					<div class="shortcut-delete" title="{$LNG.fl_dlte_shortcut}">x</div>
				</div>
				<div class="shortcut-edit">
					<input type="number" class="shortcut-input" name="shortcut[{$shortcutID}][galaxy]" value="{$shortcutRow.galaxy}" maxlength="4" style="max-width:4em">:<input type="number" class="shortcut-input" name="shortcut[{$shortcutID}][system]" value="{$shortcutRow.system}" maxlength="6"  style="max-width:6em">:<input type="number" class="shortcut-input" name="shortcut[{$shortcutID}][planet]" value="{$shortcutRow.planet}"  style="max-width:4em" maxlength="6">
					<select class="shortcut-input" name="shortcut[{$shortcutID}][type]">
						{html_options selected=$shortcutRow.type options=$typeSelect}
					</select>
				</div>
			</td>
			{if $shortcutRow@last && ($shortcutRow@iteration % $themeSettings.SHORTCUT_ROWS_ON_FLEET1) !== 0}
				{$to = $themeSettings.SHORTCUT_ROWS_ON_FLEET1 - ($shortcutRow@iteration % $themeSettings.SHORTCUT_ROWS_ON_FLEET1)}
				{for $foo=1 to $to}
					<td class="shortcut-colum" style="width:{100 / $themeSettings.SHORTCUT_ROWS_ON_FLEET1}%">&nbsp;</td>
				{/for}
			{/if}
			{if ($shortcutRow@iteration % $themeSettings.SHORTCUT_ROWS_ON_FLEET1) === 0}</tr>{/if}
		{foreachelse}
			<tr style="height:20px;" class="shortcut-none">
				<td colspan="{$themeSettings.SHORTCUT_ROWS_ON_FLEET1}">{$LNG.fl_no_shortcuts}</td>
			</tr>
		{/foreach}
		<tr style="height:20px;" class="shortcut-edit shortcut-new">
			<td>
				<div class="shortcut-link">

				</div>
				<div class="shortcut-edit">
					<input type="text" class="shortcut-input" name="shortcut[][name]" maxlength="32" placeholder="{$LNG.fl_shortcut_name}">
					<div class="shortcut-delete" title="{$LNG.fl_dlte_shortcut}">x</div>
				</div>
				<div class="shortcut-edit">
					<input type="number" class="shortcut-input" name="shortcut[][galaxy]" value=""  style="max-width: 4em;" maxlength="2" placeholder="G">:<input type="number" class="shortcut-input" name="shortcut[][system]" value="" size="3" maxlength="3" placeholder="S" style="max-width: 6em;">:<input type="number" class="shortcut-input" name="shortcut[][planet]" value="" size="3" maxlength="2" placeholder="P" style="max-width: 4em;">
					<select class="shortcut-input" name="shortcut[][type]">
						{html_options options=$typeSelect}
					</select>
				</div>
			</td>
		</tr>
		<tr style="height:20px;" class="shortcut-edit">
			<td colspan="{$themeSettings.SHORTCUT_ROWS_ON_FLEET1}">
				<a href="#" onclick="AddShortcuts();return false">{$LNG.fl_shortcut_add}</a>
			</td>
		</tr>
	</table>
	{/if}
	<table class="table519" style="table-layout: fixed;">
		<tr style="height:20px;">
			<th colspan="{$themeSettings.COLONY_ROWS_ON_FLEET1}">{$LNG.fl_my_planets}</th>
		</tr>
		{foreach $colonyList as $ColonyRow}
			{if ($ColonyRow@iteration % $themeSettings.COLONY_ROWS_ON_FLEET1) === 1}<tr style="height:20px;">{/if}
			<td>
				<a href="javascript:setTarget({$ColonyRow.galaxy},{$ColonyRow.system},{$ColonyRow.planet},{$ColonyRow.type});updateVars();">{$ColonyRow.name}{if $ColonyRow.type == 3}{$LNG.fl_moon_shortcut}{/if} [{$ColonyRow.galaxy}:{$ColonyRow.system}:{$ColonyRow.planet}]</a>
			</td>
			{if $ColonyRow@last && ($ColonyRow@iteration % $themeSettings.COLONY_ROWS_ON_FLEET1) !== 0}
				{$to = $themeSettings.COLONY_ROWS_ON_FLEET1 - ($ColonyRow@iteration % $themeSettings.COLONY_ROWS_ON_FLEET1)}
				{for $foo=1 to $to}<td>&nbsp;</td>{/for}
			{/if}
			{if ($ColonyRow@iteration % $themeSettings.COLONY_ROWS_ON_FLEET1) === 0}</tr>{/if}
		{foreachelse}
			<tr style="height:20px;">
				<td colspan="{$themeSettings.COLONY_ROWS_ON_FLEET1}">{$LNG.fl_no_colony}</td>
			</tr>
		{/foreach}
	</table>
	{if $ACSList}
	<table class="table519" style="table-layout: fixed;">
		<tr style="height:20px;">
			<th colspan="{$themeSettings.COLONY_ROWS_ON_FLEET1}">{$LNG.fl_acs_title}</th>
		</tr>
		{foreach $ACSList as $ACSRow}
			{if ($ACSRow@iteration % $themeSettings.ACS_ROWS_ON_FLEET1) === 1}<tr style="height:20px;">{/if}
				<tr style="height:20px;">
					<td><a href="javascript:setACSTarget({$ACSRow.galaxy},{$ACSRow.system},{$ACSRow.planet},{$ACSRow.planet_type},{$ACSRow.id});">{$ACSRow.name} - [{$ACSRow.galaxy}:{$ACSRow.system}:{$ACSRow.planet}]</a></td>
				</tr>
			{if $ACSRow@last && ($ACSRow@iteration % $themeSettings.ACS_ROWS_ON_FLEET1) !== 0}
				{$to = $themeSettings.ACS_ROWS_ON_FLEET1 - ($ACSRow@iteration % $themeSettings.ACS_ROWS_ON_FLEET1)}
				{for $foo=1 to $to}<td>&nbsp;</td>{/for}
			{/if}
			{if ($ACSRow@iteration % $themeSettings.ACS_ROWS_ON_FLEET1) === 0}</tr>{/if}
		{/foreach}
	</table>
	{/if}
	<table class="table519" style="table-layout: fixed;">
		<tr style="height:20px;">
			<td><input type="submit" value="{$LNG.fl_continue}"></td>
		</tr>
	</table>
</form>
<script type="text/javascript">
	data			= {$fleetdata|json_encode};
	shortCutRows	= {$themeSettings.SHORTCUT_ROWS_ON_FLEET1};
	fl_no_shortcuts = '{$LNG.fl_no_shortcuts}';
	config 			= {$config|json_encode};

	$('form').submit(function(e) {
		$(':disabled').each(function(e) {
			$(this).removeAttr('disabled');
		})
	});
</script>
{/block}
