{block name="title" prepend}{$LNG.lm_options}{/block}
{block name="content"}
<form action="game.php?page=settings" method="post">
<input type="hidden" name="mode" value="send">
	<table>
	<tbody>
		{if $userAuthlevel > 0}
		<tr>
			<th colspan="2">{$LNG.op_admin_title_options}</th>
		</tr>
		<tr>
			<td>{$LNG.op_admin_planets_protection}</td>
			<td><input name="adminprotection" type="checkbox" value="1" {if $adminProtection > 0}checked="checked"{/if}></td>
		</tr>
		{/if}
		<tr>
			<th colspan="2">{$LNG.op_user_data}</th>
		</tr>
		<tr>
			<td width="50%">{$LNG.op_username}</td>
	    <td width="50%" style="height:22px;">{if $changeNickTime < 0}<input name="username" size="20" value="{$username}" type="text">{else}{$username}{/if}</td>
	</tr>
	<tr>
	    <td>{$LNG.op_old_pass}</td>
	    <td><input name="password" size="20" type="password" class="autocomplete"></td>
	</tr>
	<tr>
	    <td>{$LNG.op_new_pass}</td>
	    <td><input name="newpassword" size="20" maxlength="40" type="password" class="autocomplete"></td>
	</tr>
	<tr>
	    <td>{$LNG.op_repeat_new_pass}</td>
	    <td><input name="newpassword2" size="20" maxlength="40" type="password" class="autocomplete"></td>
	</tr>
	<tr>
	    <td><a title="{$LNG.op_email_adress_descrip}">{$LNG.op_email_adress}</a></td>
	    <td><input name="email" maxlength="64" size="20" value="{$email}" type="text"></td>
	</tr>
	<tr>
	    <td style="height:22px;">{$LNG.op_permanent_email_adress}</td>
	    <td>{$permaEmail}</td>
		</tr>
		<tr>
			<th colspan="2">{$LNG.op_general_settings}</th>
		</tr>
		<tr>
			<td>{$LNG.op_timezone}</td>
			<td>{html_options name=timezone options=$Selectors.timezones selected=$timezone}</td>
		</tr>
		{if count($Selectors.lang) > 1}
		<tr>
			<td>{$LNG.op_lang}</td>
			<td>{html_options name=language options=$Selectors.lang selected=$userLang}</td>
		</tr>
		{/if}
		<tr>
			<td>{$LNG.op_sort_planets_by}</td>
			<td>{html_options name=planetSort options=$Selectors.Sort selected=$planetSort}</td>
		</tr>
		<tr>
			<td>{$LNG.op_sort_kind}</td>
			<td>
				{html_options name=planetOrder options=$Selectors.SortUpDown selected=$planetOrder}
			</td>
		</tr>
		{if count($Selectors.Skins) > 1}
		<tr>
			<td>{$LNG.op_skin_example}</td>
			<td>{html_options options=$Selectors.Skins selected=$theme name="theme" id="theme"}</td>
		</tr>
		{/if}
		<tr>
			<td>{$LNG.op_active_build_messages}</td>
			<td><input name="queueMessages" type="checkbox" value="1" {if $queueMessages == 1}checked="checked"{/if}></td>
		</tr>
		<tr>
			<td>{$LNG.op_active_spy_messages_mode}</td>
			<td><input name="spyMessagesMode" type="checkbox" value="1" {if $spyMessagesMode == 1}checked="checked"{/if}></td>
		</tr>
		<tr>
			<td>{$LNG.op_block_pm}</td>
			<td><input name="blockPM" type="checkbox" value="1" {if $blockPM == 1}checked="checked"{/if}></td>
		</tr>
		<tr>
			<th colspan="2">{$LNG.op_galaxy_settings}</th>
		</tr>
		<tr>
			<td><a title="{$LNG.op_spy_probes_number_descrip}">{$LNG.op_spy_probes_number}</a></td>
			<td><input name="spycount" size="{$spycount|count_characters + 3}" value="{$spycount}" type="int"></td>
		</tr>
		<tr>
			<td>{$LNG.op_max_fleets_messages}</td>
			<td><input name="fleetactions" maxlength="2" size="{$fleetActions|count_characters + 2}" value="{$fleetActions}" type="int"></td>
		</tr>
		<tr>
			<th>{$LNG.op_shortcut}</th>
			<th>{$LNG.op_show}</th>
		</tr>
		<tr>
			<td><img src="{$dpath}img/e.gif" alt="">{$LNG.op_spy}</td>
			<td><input name="galaxySpy" type="checkbox" value="1" {if $galaxySpy == 1}checked="checked"{/if}></td>
		</tr>
		<tr>
			<td><img src="{$dpath}img/m.gif" alt="">{$LNG.op_write_message}</td>
			<td><input name="galaxyMessage" type="checkbox" value="1" {if $galaxyMessage == 1}checked="checked"{/if}></td>
		</tr>
		<tr>
			<td><img src="{$dpath}img/b.gif" alt="">{$LNG.op_add_to_buddy_list}</td>
			<td><input name="galaxyBuddyList" type="checkbox" value="1" {if $galaxyBuddyList == 1}checked="checked"{/if}></td>
		</tr>
		<tr>
			<td><img src="{$dpath}img/r.gif" alt="">{$LNG.op_missile_attack}</td>
			<td><input name="galaxyMissle" type="checkbox" value="1" {if $galaxyMissle == 1}checked="checked"{/if}></td>
		</tr>
		<tr>
			<th colspan="2">{$LNG.own_colors}</th>
		</tr>
		<tr>
			<td>{$LNG.type_mission_1_own}</td>
			<td><input type="color" name="colorMission1Own" value="{$colors.colorMission1Own}"></td>
		</tr>
		<tr>
			<td>{$LNG.type_mission_2_own}</td>
			<td><input type="color" name="colorMission2Own" value="{$colors.colorMission2Own}"></td>
		</tr>
		<tr>
			<td>{$LNG.type_mission_2_friend}</td>
			<td><input type="color" name="colorMission2friend" value="{$colors.colorMission2friend}"></td>
		</tr>
		<tr>
			<td>{$LNG.type_mission_3_own}</td>
			<td><input type="color" name="colorMission3Own" value="{$colors.colorMission3Own}"></td>
		</tr>
		<tr>
			<td>{$LNG.type_mission_4_own}</td>
			<td><input type="color" name="colorMission4Own" value="{$colors.colorMission4Own}"></td>
		</tr>
		<tr>
			<td>{$LNG.type_mission_5_own}</td>
			<td><input type="color" name="colorMission5Own" value="{$colors.colorMission5Own}"></td>
		</tr>
		<tr>
			<td>{$LNG.type_mission_6_own}</td>
			<td><input type="color" name="colorMission6Own" value="{$colors.colorMission6Own}"></td>
		</tr>
		<tr>
			<td>{$LNG.type_mission_7_own}</td>
			<td><input type="color" name="colorMission7Own" value="{$colors.colorMission7Own}"></td>
		</tr>
		<tr>
			<td>{$LNG.type_mission_7_own_return}</td>
			<td><input type="color" name="colorMission7OwnReturn" value="{$colors.colorMission7OwnReturn}"></td>
		<tr>
			<td>{$LNG.type_mission_8_own}</td>
			<td><input type="color" name="colorMission8Own" value="{$colors.colorMission8Own}"></td>
		</tr>
		<tr>
			<td>{$LNG.type_mission_9_own}</td>
			<td><input type="color" name="colorMission9Own" value="{$colors.colorMission9Own}"></td>
		</tr>
		<tr>
			<td>{$LNG.type_mission_10_own}</td>
			<td><input type="color" name="colorMission10Own" value="{$colors.colorMission10Own}"></td>
		</tr>
		<tr>
			<td>{$LNG.type_mission_15_own}</td>
			<td><input type="color" name="colorMission15Own" value="{$colors.colorMission15Own}"></td>
		</tr>
		<tr>
			<td>{$LNG.type_mission_16_own}</td>
			<td><input type="color" name="colorMission16Own" value="{$colors.colorMission16Own}"></td>
		</tr>
		<tr>
			<td>{$LNG.type_mission_17_own}</td>
			<td><input type="color" name="colorMission17Own" value="{$colors.colorMission17Own}"></td>
		</tr>
		<tr>
			<td>{$LNG.type_mission_return_own}</td>
			<td><input type="color" name="colorMissionReturnOwn" value="{$colors.colorMissionReturnOwn}"></td>
		</tr>
		<tr>
			<th colspan="2">{$LNG.foreign_colors}</th>
		</tr>
		<tr>
			<td>{$LNG.type_mission_1_foreign}</td>
			<td><input type="color" name="colorMission1Foreign" value="{$colors.colorMission1Foreign}"></td>
		</tr>
		<tr>
			<td>{$LNG.type_mission_2_foreign}</td>
			<td><input type="color" name="colorMission2Foreign" value="{$colors.colorMission2Foreign}"></td>
		</tr>
		<tr>
			<td>{$LNG.type_mission_3_foreign}</td>
			<td><input type="color" name="colorMission3Foreign" value="{$colors.colorMission3Foreign}"></td>
		</tr>
		<tr>
			<td>{$LNG.type_mission_4_foreign}</td>
			<td><input type="color" name="colorMission4Foreign" value="{$colors.colorMission4Foreign}"></td>
		</tr>
		<tr>
			<td>{$LNG.type_mission_5_foreign}</td>
			<td><input type="color" name="colorMission5Foreign" value="{$colors.colorMission5Foreign}"></td>
		</tr>
		<tr>
			<td>{$LNG.type_mission_6_foreign}</td>
			<td><input type="color" name="colorMission6Foreign" value="{$colors.colorMission6Foreign}"></td>
		</tr>
		<tr>
			<td>{$LNG.type_mission_7_foreign}</td>
			<td><input type="color" name="colorMission7Foreign" value="{$colors.colorMission7Foreign}"></td>
		</tr>
			<td>{$LNG.type_mission_8_foreign}</td>
			<td><input type="color" name="colorMission8Foreign" value="{$colors.colorMission8Foreign}"></td>
		</tr>
		<tr>
			<td>{$LNG.type_mission_9_foreign}</td>
			<td><input type="color" name="colorMission9Foreign" value="{$colors.colorMission9Foreign}"></td>
		</tr>
		<tr>
			<td>{$LNG.type_mission_10_foreign}</td>
			<td><input type="color" name="colorMission10Foreign" value="{$colors.colorMission10Foreign}"></td>
		</tr>
		<tr>
			<td>{$LNG.type_mission_15_foreign}</td>
			<td><input type="color" name="colorMission15Foreign" value="{$colors.colorMission15Foreign}"></td>
		</tr>
		<tr>
			<td>{$LNG.type_mission_16_foreign}</td>
			<td><input type="color" name="colorMission16Foreign" value="{$colors.colorMission16Foreign}"></td>
		</tr>
		<tr>
			<td>{$LNG.type_mission_17_foreign}</td>
			<td><input type="color" name="colorMission17Foreign" value="{$colors.colorMission17Foreign}"></td>
		</tr>
		<tr>
			<td>{$LNG.type_mission_return_foreign}</td>
			<td><input type="color" name="colorMissionReturnForeign" value="{$colors.colorMissionReturnForeign}"></td>
		</tr>
		<tr>
			<th colspan="2">{$LNG.general_colors}</th>
		</tr>
		<tr>
			<td>{$LNG.positiv}</td>
			<td><input type="color" name="colorPositive" value="{$colors.colorPositive}"></td>
		</tr>
		<tr>
			<td>{$LNG.negativ}</td>
			<td><input type="color" name="colorNegative" value="{$colors.colorNegative}"></td>
		</tr>
		<tr>
			<td>{$LNG.neutral}</td>
			<td><input type="color" name="colorNeutral" value="{$colors.colorNeutral}"></td>
		</tr>
		<tr>
			<td>{$LNG.StaticTimer}</td>
			<td><input type="color" name="colorStaticTimer" value="{$colors.colorStaticTimer}"></td>
		</tr>
		<tr>
			<th colspan="2">{$LNG.op_vacation_delete_mode}</th>
		</tr>
		<tr>
			<td><a title="{$LNG.op_activate_vacation_mode_descrip}">{$LNG.op_activate_vacation_mode}</a></td>
			<td><input name="vacation" type="checkbox" value="1"></td>
		</tr>
		<tr>
			<td><a title="{$LNG.op_dlte_account_descrip}">{$LNG.op_dlte_account}</a></td>
			<td><input name="delete" type="checkbox" value="1" {if $delete > 0}checked="checked"{/if}></td>
		</tr>
		{if isModuleAvailable($smarty.const.MODULE_BANNER)}
		<tr>
			<th colspan="3">{$LNG.ov_userbanner}</th>
		</tr>
		<tr>
			<td colspan="3"><img src="userpic.php?id={$userid}" alt="" width="590" height="95" id="userpic"><br><br><table><tr><td class="transparent">HTML:</td><td class="transparent"><input type="text" value='<a href="{$SELF_URL}{if $ref_active}index.php?ref={$userid}{/if}"><img src="{$SELF_URL}userpic.php?id={$userid}"></a>' readonly="readonly" style="width:450px;"></td></tr><tr><td class="transparent">BBCode:</td><td class="transparent"><input type="text" value="[url={$SELF_URL}{if $ref_active}index.php?ref={$userid}{/if}][img]{$SELF_URL}userpic.php?id={$userid}[/img][/url]" readonly="readonly" style="width:450px;"></td></tr></table></td>
		</tr>
		{/if}
		<tr>
			<td colspan="2"><input value="{$LNG.op_save_changes}" type="submit"></td>
		</tr>
		</tbody>
	</table>
	</form>
{/block}