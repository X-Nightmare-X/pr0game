{block name="title" prepend}{$LNG.lm_fleet}{/block}
{block name="content"}
<form action="game.php?page=fleetStep3" method="post" onsubmit="return CheckResources()" id="form">
<input type="hidden" name="token" value="{$token}">
   	<table class="table519">
        <tr>
        	<th colspan="2">{$galaxy}:{$system}:{$planet} - {$LNG["type_planet_{$type}"]}</th>
        </tr>
		<tr>
			<th>{$LNG.fl_mission}</th>
        	<th>{$LNG.fl_resources}</th>
        </tr>
		<tr>
			<td class="left top" style="width:50%;margin:0;padding:0;"{if !empty($StaySelector)} rowspan="5"{/if}>
        <table border="0" cellpadding="0" cellspacing="0" style="margin:0;padding:0;">
          {foreach $MissionSelector as $MissionID}
					<tr style="height:20px;">
						<td class="transparent left">
						<input id="radio_{$MissionID}" type="radio" name="mission" value="{$MissionID}" {if $mission == $MissionID || $MissionID@total == 1}checked="checked"{/if} onchange="checkHold({$MissionID})" style="width:60px;"><label for="radio_{$MissionID}">{$LNG["type_mission_{$MissionID}"]}</label><br>
							{if $MissionID == 17}<br><div style="color:red;padding-left:13px;">{$LNG.fl_transfer_alert_message}</div><br>{/if}
							{if $MissionID == 15}<br><div style="color:red;padding-left:13px;">{$LNG.fl_expedition_alert_message}</div><br>{/if}
						</td>
					</tr>
					{/foreach}
					{if $Exchange}
					<tr style="height:50px;">
						<td class="transparent left" style="color:red;">
							{$LNG.market_info_no_return}
						</td>
					</tr>
					{/if}
        </table>
      </td>
      <td class="top">
				<table border="0" cellpadding="0" cellspacing="0">
          <tr style="height:20px;">
            <td class="transparent">{$LNG.tech.901}</td>
            <td class="transparent"><a href="javascript:maxResource('metal');">{$LNG.fl_max}</a></th>
            <td class="transparent"><input name="metal" size="10" onchange="calculateTransportCapacity();" type="number" style="max-width: 10em;"></td>
          </tr>
          <tr style="height:20px;">
            <td class="transparent">{$LNG.tech.902}</td>
            <td class="transparent"><a href="javascript:maxResource('crystal');">{$LNG.fl_max}</a></th>
            <td class="transparent"><input name="crystal" size="10" onchange="calculateTransportCapacity();" type="number" style="max-width: 10em;"></td>
          </tr>
          <tr style="height:20px;">
            <td class="transparent">{$LNG.tech.903}</td>
            <td class="transparent"><a href="javascript:maxResource('deuterium');">{$LNG.fl_max}</a></td>
            <td class="transparent"><input name="deuterium" size="10" onchange="calculateTransportCapacity();" type="number" style="max-width: 10em;"></td>
          </tr>
          <tr style="height:20px;">
            <td class="transparent">{$LNG.fl_resources_left}</td>
            <td class="transparent" colspan="2" id="remainingresources">-</td>
          </tr>
          <tr style="height:20px;">
            <td class="transparent" colspan="3"><a href="javascript:maxResources()">{$LNG.fl_all_resources}</a></td>
          </tr>
          <tr style="height:20px;">
            <td class="transparent" colspan="3"><a  id="selectedres" href="javascript:selectedResources(this)" data="{$predefinedRes['met']},{$predefinedRes['krist']},{$predefinedRes['deut']},{$consumption}">{$LNG.fl_selected_resources}</a></td>
          </tr>
          <tr style="height:20px;">
            <td class="transparent" colspan="3">{$LNG.fl_fuel_consumption}: <span id="consumption" class="consumption">{$consumption}</span></td>
          </tr>
          <tr style="height:20px;">
            <td class="transparent" colspan="3">{$LNG.fl_flying_arrival}: <span id="arr_time" duration="{$duration}"></span></td>
          </tr>
				</table>
			</td>
		</tr>
		{if $Exchange}
		<tr style="height:20px;">
			<th>{$LNG.fl_exchange}</th>
		</tr>
		<tr style="height:20px;">
			<td>
				<table>
				<tr class="no-border">
					<td >
						<select name="resEx">
							<option value="1">{$LNG.tech.901}</option>
							<option value="2">{$LNG.tech.902}</option>
							<option value="3">{$LNG.tech.903}</option>
						</select>
					</td>
					<td>
						<input name="exchange" size="10" type="text">
					</td>
				</tr>
				<tr class="no-border">
					<td>
						{$LNG.fl_visibility}
					</td>
					<td>
						<select name="visibility">
							<option value="2" selected>{$LNG.fl_visibility_no_enemies}</option>
							<option value="1">{$LNG.fl_visibility_alliance}</option>
							<option value="0">{$LNG.fl_visibility_all}</option>
						</select>
					</td>
				</tr>
				<tr class="no-border">
					<td>
						{$LNG.fl_market_type}
					</td>
					<td>
						<select name="markettype">
							{if isModuleAvailable($smarty.const.MODULE_MARKET_TRADE) && isModuleAvailable($smarty.const.MODULE_MARKET_TRANSFER)}
								<option value="0" selected>{$LNG.fl_mt_resources}</option>
								<option value="1">{$LNG.fl_mt_fleet}</option>
							{elseif isModuleAvailable($smarty.const.MODULE_MARKET_TRADE)}
								<option value="0" selected>{$LNG.fl_mt_resources}</option>
							{elseif isModuleAvailable($smarty.const.MODULE_MARKET_TRANSFER)}
								<option value="1" selected>{$LNG.fl_mt_fleet}</option>
							{/if}
						</select>
					</td>
				</tr>
			</table>
			<!--
			Max flight time (0 = unlimited):
			<input name="maxFlightTime" size="10" type="text" value="0"> hours<br/>
			-->
			</td>
		</tr>
		{/if}

		{if !empty($StaySelector)}
		<tr id="stay_head" style="height:20px;">
			<th>{$LNG.fl_hold_time}</th>
		</tr>
		<tr id="stay" style="height:20px;">
			<td>
			{html_options name=staytime options=$StaySelector} {$LNG.fl_hours}
			</td>
		</tr>
		{/if}
        <tr id="submit">
			{* <td colspan="2" style="height:20px;"><input value="{$LNG.fl_continue}" type="submit" onclick="return sendFleet({$question});"/></td> *}
			<td colspan="2" style="height:20px;"><input value="{$LNG.fl_continue}" type="submit" onclick="return sendFleet('Test');"/></td>
        </tr>
		<tr id="wait" style="display:none;">
			<td colspan="2" style="height:20px">{$LNG.fl_continue}</td>
		</tr>
    </table>
</form>
<script type="text/javascript">
data	= {$fleetdata|json_encode};
</script>
{/block}
