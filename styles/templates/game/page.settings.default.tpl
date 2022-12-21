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
	    <td width="50%" style="height:22px;">{if $changeNickTime < 0}<input name="username" size="20" value="{$username}" type="text" maxlength="32">{else}{$username}{/if}</td>
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
	    <td><input name="email" maxlength="64" size="20" value="{$email}" type="text" maxlength="64"></td>
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
			<td>
				<input type="color" name="colorMission1Own" value="{$colors.colorMission1Own}" basecolor="{$defaultColors.colorMission1Own}" style="vertical-align:middle;">
				<button onclick="let inpt=this.parentNode.getElementsByTagName('input')[0];inpt.value=inpt.getAttribute('basecolor');" type="button" style="vertical-align:middle;">{$LNG['nt_reset']}</button>
			</td>
		</tr>
		<tr>
			<td>{$LNG.type_mission_2_own}</td>
			<td>
				<input type="color" name="colorMission2Own" value="{$colors.colorMission2Own}" basecolor="{$defaultColors.colorMission2Own}" style="vertical-align:middle;">
				<button onclick="let inpt=this.parentNode.getElementsByTagName('input')[0];inpt.value=inpt.getAttribute('basecolor');" type="button" style="vertical-align:middle;">{$LNG['nt_reset']}</button>
			</td>
		</tr>
		<tr>
			<td>{$LNG.type_mission_2_friend}</td>
			<td>
				<input type="color" name="colorMission2friend" value="{$colors.colorMission2friend}" basecolor="{$defaultColors.colorMission2friend}" style="vertical-align:middle;">
				<button onclick="let inpt=this.parentNode.getElementsByTagName('input')[0];inpt.value=inpt.getAttribute('basecolor');" type="button" style="vertical-align:middle;">{$LNG['nt_reset']}</button>
			</td>
		</tr>
		<tr>
			<td>{$LNG.type_mission_3_own}</td>
			<td>
				<input type="color" name="colorMission3Own" value="{$colors.colorMission3Own}" basecolor="{$defaultColors.colorMission3Own}" style="vertical-align:middle;">
				<button onclick="let inpt=this.parentNode.getElementsByTagName('input')[0];inpt.value=inpt.getAttribute('basecolor');" type="button" style="vertical-align:middle;">{$LNG['nt_reset']}</button>
			</td>
		</tr>
		<tr>
			<td>{$LNG.type_mission_4_own}</td>
			<td>
				<input type="color" name="colorMission4Own" value="{$colors.colorMission4Own}" basecolor="{$defaultColors.colorMission4Own}" style="vertical-align:middle;">
				<button onclick="let inpt=this.parentNode.getElementsByTagName('input')[0];inpt.value=inpt.getAttribute('basecolor');" type="button" style="vertical-align:middle;">{$LNG['nt_reset']}</button>
			</td>
		</tr>
		<tr>
			<td>{$LNG.type_mission_5_own}</td>
			<td>
				<input type="color" name="colorMission5Own" value="{$colors.colorMission5Own}" basecolor="{$defaultColors.colorMission5Own}" style="vertical-align:middle;">
				<button onclick="let inpt=this.parentNode.getElementsByTagName('input')[0];inpt.value=inpt.getAttribute('basecolor');" type="button" style="vertical-align:middle;">{$LNG['nt_reset']}</button>
			</td>
		</tr>
		<tr>
			<td>{$LNG.type_mission_6_own}</td>
			<td>
				<input type="color" name="colorMission6Own" value="{$colors.colorMission6Own}" basecolor="{$defaultColors.colorMission6Own}" style="vertical-align:middle;">
				<button onclick="let inpt=this.parentNode.getElementsByTagName('input')[0];inpt.value=inpt.getAttribute('basecolor');" type="button" style="vertical-align:middle;">{$LNG['nt_reset']}</button>
			</td>
		</tr>
		<tr>
			<td>{$LNG.type_mission_7_own}</td>
			<td>
				<input type="color" name="colorMission7Own" value="{$colors.colorMission7Own}" basecolor="{$defaultColors.colorMission7Own}" style="vertical-align:middle;">
				<button onclick="let inpt=this.parentNode.getElementsByTagName('input')[0];inpt.value=inpt.getAttribute('basecolor');" type="button" style="vertical-align:middle;">{$LNG['nt_reset']}</button>
			</td>
		</tr>
		<tr>
			<td>{$LNG.type_mission_7_own_return}</td>
			<td>
				<input type="color" name="colorMission7OwnReturn" value="{$colors.colorMission7OwnReturn}" basecolor="{$defaultColors.colorMission7OwnReturn}" style="vertical-align:middle;">
				<button onclick="let inpt=this.parentNode.getElementsByTagName('input')[0];inpt.value=inpt.getAttribute('basecolor');" type="button" style="vertical-align:middle;">{$LNG['nt_reset']}</button>
			</td>
		<tr>
			<td>{$LNG.type_mission_8_own}</td>
			<td>
				<input type="color" name="colorMission8Own" value="{$colors.colorMission8Own}" basecolor="{$defaultColors.colorMission8Own}" style="vertical-align:middle;">
				<button onclick="let inpt=this.parentNode.getElementsByTagName('input')[0];inpt.value=inpt.getAttribute('basecolor');" type="button" style="vertical-align:middle;">{$LNG['nt_reset']}</button>
			</td>
		</tr>
		<tr>
			<td>{$LNG.type_mission_9_own}</td>
			<td>
				<input type="color" name="colorMission9Own" value="{$colors.colorMission9Own}" basecolor="{$defaultColors.colorMission9Own}" style="vertical-align:middle;">
				<button onclick="let inpt=this.parentNode.getElementsByTagName('input')[0];inpt.value=inpt.getAttribute('basecolor');" type="button" style="vertical-align:middle;">{$LNG['nt_reset']}</button>
			</td>
		</tr>
		<tr>
			<td>{$LNG.type_mission_10_own}</td>
			<td>
				<input type="color" name="colorMission10Own" value="{$colors.colorMission10Own}" basecolor="{$defaultColors.colorMission10Own}" style="vertical-align:middle;">
				<button onclick="let inpt=this.parentNode.getElementsByTagName('input')[0];inpt.value=inpt.getAttribute('basecolor');" type="button" style="vertical-align:middle;">{$LNG['nt_reset']}</button>
			</td>
		</tr>
		<tr>
			<td>{$LNG.type_mission_15_own}</td>
			<td>
				<input type="color" name="colorMission15Own" value="{$colors.colorMission15Own}" basecolor="{$defaultColors.colorMission15Own}" style="vertical-align:middle;">
				<button onclick="let inpt=this.parentNode.getElementsByTagName('input')[0];inpt.value=inpt.getAttribute('basecolor');" type="button" style="vertical-align:middle;">{$LNG['nt_reset']}</button>
			</td>
		</tr>
		<tr>
			<td>{$LNG.type_mission_16_own}</td>
			<td>
				<input type="color" name="colorMission16Own" value="{$colors.colorMission16Own}" basecolor="{$defaultColors.colorMission16Own}" style="vertical-align:middle;">
				<button onclick="let inpt=this.parentNode.getElementsByTagName('input')[0];inpt.value=inpt.getAttribute('basecolor');" type="button" style="vertical-align:middle;">{$LNG['nt_reset']}</button>
			</td>
		</tr>
		<tr>
			<td>{$LNG.type_mission_17_own}</td>
			<td>
				<input type="color" name="colorMission17Own" value="{$colors.colorMission17Own}" basecolor="{$defaultColors.colorMission17Own}" style="vertical-align:middle;">
				<button onclick="let inpt=this.parentNode.getElementsByTagName('input')[0];inpt.value=inpt.getAttribute('basecolor');" type="button" style="vertical-align:middle;">{$LNG['nt_reset']}</button>
			</td>
		</tr>
		<tr>
			<td>{$LNG.type_mission_return_own}</td>
			<td>
				<input type="color" name="colorMissionReturnOwn" value="{$colors.colorMissionReturnOwn}" basecolor="{$defaultColors.colorMissionReturnOwn}" style="vertical-align:middle;">
				<button onclick="let inpt=this.parentNode.getElementsByTagName('input')[0];inpt.value=inpt.getAttribute('basecolor');" type="button" style="vertical-align:middle;">{$LNG['nt_reset']}</button>
			</td>
		</tr>
		<tr>
			<th colspan="2">{$LNG.foreign_colors}</th>
		</tr>
		<tr>
			<td>{$LNG.type_mission_1_foreign}</td>
			<td>
				<input type="color" name="colorMission1Foreign" value="{$colors.colorMission1Foreign}" basecolor="{$defaultColors.colorMission1Foreign}" style="vertical-align:middle;">
				<button onclick="let inpt=this.parentNode.getElementsByTagName('input')[0];inpt.value=inpt.getAttribute('basecolor');" type="button" style="vertical-align:middle;">{$LNG['nt_reset']}</button>
			</td>
		</tr>
		<tr>
			<td>{$LNG.type_mission_2_foreign}</td>
			<td>
				<input type="color" name="colorMission2Foreign" value="{$colors.colorMission2Foreign}" basecolor="{$defaultColors.colorMission2Foreign}" style="vertical-align:middle;">
				<button onclick="let inpt=this.parentNode.getElementsByTagName('input')[0];inpt.value=inpt.getAttribute('basecolor');" type="button" style="vertical-align:middle;">{$LNG['nt_reset']}</button>
			</td>
		</tr>
		<tr>
			<td>{$LNG.type_mission_3_foreign}</td>
			<td>
				<input type="color" name="colorMission3Foreign" value="{$colors.colorMission3Foreign}" basecolor="{$defaultColors.colorMission3Foreign}" style="vertical-align:middle;">
				<button onclick="let inpt=this.parentNode.getElementsByTagName('input')[0];inpt.value=inpt.getAttribute('basecolor');" type="button" style="vertical-align:middle;">{$LNG['nt_reset']}</button>
			</td>
		</tr>
		<tr>
			<td>{$LNG.type_mission_4_foreign}</td>
			<td>
				<input type="color" name="colorMission4Foreign" value="{$colors.colorMission4Foreign}" basecolor="{$defaultColors.colorMission4Foreign}" style="vertical-align:middle;">
				<button onclick="let inpt=this.parentNode.getElementsByTagName('input')[0];inpt.value=inpt.getAttribute('basecolor');" type="button" style="vertical-align:middle;">{$LNG['nt_reset']}</button>
			</td>
		</tr>
		<tr>
			<td>{$LNG.type_mission_5_foreign}</td>
			<td>
				<input type="color" name="colorMission5Foreign" value="{$colors.colorMission5Foreign}" basecolor="{$defaultColors.colorMission5Foreign}" style="vertical-align:middle;">
				<button onclick="let inpt=this.parentNode.getElementsByTagName('input')[0];inpt.value=inpt.getAttribute('basecolor');" type="button" style="vertical-align:middle;">{$LNG['nt_reset']}</button>
			</td>
		</tr>
		<tr>
			<td>{$LNG.type_mission_6_foreign}</td>
			<td>
				<input type="color" name="colorMission6Foreign" value="{$colors.colorMission6Foreign}" basecolor="{$defaultColors.colorMission6Foreign}" style="vertical-align:middle;">
				<button onclick="let inpt=this.parentNode.getElementsByTagName('input')[0];inpt.value=inpt.getAttribute('basecolor');" type="button" style="vertical-align:middle;">{$LNG['nt_reset']}</button>
			</td>
		</tr>
		<tr>
			<td>{$LNG.type_mission_7_foreign}</td>
			<td>
				<input type="color" name="colorMission7Foreign" value="{$colors.colorMission7Foreign}" basecolor="{$defaultColors.colorMission7Foreign}" style="vertical-align:middle;">
				<button onclick="let inpt=this.parentNode.getElementsByTagName('input')[0];inpt.value=inpt.getAttribute('basecolor');" type="button" style="vertical-align:middle;">{$LNG['nt_reset']}</button>
			</td>
		</tr>
			<td>{$LNG.type_mission_8_foreign}</td>
			<td>
				<input type="color" name="colorMission8Foreign" value="{$colors.colorMission8Foreign}" basecolor="{$defaultColors.colorMission8Foreign}" style="vertical-align:middle;">
				<button onclick="let inpt=this.parentNode.getElementsByTagName('input')[0];inpt.value=inpt.getAttribute('basecolor');" type="button" style="vertical-align:middle;">{$LNG['nt_reset']}</button>
			</td>
		</tr>
		<tr>
			<td>{$LNG.type_mission_9_foreign}</td>
			<td>
				<input type="color" name="colorMission9Foreign" value="{$colors.colorMission9Foreign}" basecolor="{$defaultColors.colorMission9Foreign}" style="vertical-align:middle;">
				<button onclick="let inpt=this.parentNode.getElementsByTagName('input')[0];inpt.value=inpt.getAttribute('basecolor');" type="button" style="vertical-align:middle;">{$LNG['nt_reset']}</button>
			</td>
		</tr>
		<tr>
			<td>{$LNG.type_mission_10_foreign}</td>
			<td>
				<input type="color" name="colorMission10Foreign" value="{$colors.colorMission10Foreign}" basecolor="{$defaultColors.colorMission10Foreign}" style="vertical-align:middle;">
				<button onclick="let inpt=this.parentNode.getElementsByTagName('input')[0];inpt.value=inpt.getAttribute('basecolor');" type="button" style="vertical-align:middle;">{$LNG['nt_reset']}</button>
			</td>
		</tr>
		<tr>
			<td>{$LNG.type_mission_15_foreign}</td>
			<td>
				<input type="color" name="colorMission15Foreign" value="{$colors.colorMission15Foreign}" basecolor="{$defaultColors.colorMission15Foreign}" style="vertical-align:middle;">
				<button onclick="let inpt=this.parentNode.getElementsByTagName('input')[0];inpt.value=inpt.getAttribute('basecolor');" type="button" style="vertical-align:middle;">{$LNG['nt_reset']}</button>
			</td>
		</tr>
		<tr>
			<td>{$LNG.type_mission_16_foreign}</td>
			<td>
				<input type="color" name="colorMission16Foreign" value="{$colors.colorMission16Foreign}" basecolor="{$defaultColors.colorMission16Foreign}" style="vertical-align:middle;">
				<button onclick="let inpt=this.parentNode.getElementsByTagName('input')[0];inpt.value=inpt.getAttribute('basecolor');" type="button" style="vertical-align:middle;">{$LNG['nt_reset']}</button>
			</td>
		</tr>
		<tr>
			<td>{$LNG.type_mission_17_foreign}</td>
			<td>
				<input type="color" name="colorMission17Foreign" value="{$colors.colorMission17Foreign}" basecolor="{$defaultColors.colorMission17Foreign}" style="vertical-align:middle;">
				<button onclick="let inpt=this.parentNode.getElementsByTagName('input')[0];inpt.value=inpt.getAttribute('basecolor');" type="button" style="vertical-align:middle;">{$LNG['nt_reset']}</button>
			</td>
		</tr>
		<tr>
			<td>{$LNG.type_mission_return_foreign}</td>
			<td>
				<input type="color" name="colorMissionReturnForeign" value="{$colors.colorMissionReturnForeign}" basecolor="{$defaultColors.colorMissionReturnForeign}" style="vertical-align:middle;">
				<button onclick="let inpt=this.parentNode.getElementsByTagName('input')[0];inpt.value=inpt.getAttribute('basecolor');" type="button" style="vertical-align:middle;">{$LNG['nt_reset']}</button>
			</td>
		</tr>
		<tr>
			<th colspan="2">{$LNG.general_colors}</th>
		</tr>
		<tr>
			<td>{$LNG.positiv}</td>
			<td>
				<input type="color" name="colorPositive" value="{$signalColors.colorPositive}" basecolor="{$defaultSignalColors.colorPositive}" style="vertical-align:middle;">
				<button onclick="let inpt=this.parentNode.getElementsByTagName('input')[0];inpt.value=inpt.getAttribute('basecolor');" type="button" style="vertical-align:middle;">{$LNG['nt_reset']}</button>
			</td>
		</tr>
		<tr>
			<td>{$LNG.negativ}</td>
			<td>
				<input type="color" name="colorNegative" value="{$signalColors.colorNegative}" basecolor="{$defaultSignalColors.colorNegative}" style="vertical-align:middle;">
				<button onclick="let inpt=this.parentNode.getElementsByTagName('input')[0];inpt.value=inpt.getAttribute('basecolor');" type="button" style="vertical-align:middle;">{$LNG['nt_reset']}</button>
			</td>
		</tr>
		<tr>
			<td>{$LNG.neutral}</td>
			<td>
				<input type="color" name="colorNeutral" value="{$signalColors.colorNeutral}" basecolor="{$defaultSignalColors.colorNeutral}" style="vertical-align:middle;">
				<button onclick="let inpt=this.parentNode.getElementsByTagName('input')[0];inpt.value=inpt.getAttribute('basecolor');" type="button" style="vertical-align:middle;">{$LNG['nt_reset']}</button>
			</td>
		</tr>
		<tr>
			<td>{$LNG.StaticTimer}</td>
			<td>
				<input type="color" name="colorStaticTimer" value="{$colors.colorStaticTimer}" basecolor="{$defaultColors.colorStaticTimer}" style="vertical-align:middle;">
				<button onclick="let inpt=this.parentNode.getElementsByTagName('input')[0];inpt.value=inpt.getAttribute('basecolor');" type="button" style="vertical-align:middle;">{$LNG['nt_reset']}</button>
			</td>
		</tr>
    <tr><th colspan="2">{$LNG.fleetPrio}</th></tr>

    {foreach $missionPrios as $ID => $score}
		<tr>
	  		<td>{$LNG[$ID]}</td>
	  		<td><input min="0" max="99" type="number" name="{$ID}" value="{$score}"></td>
		</tr>
    {/foreach}

		<tr>
			<th colspan="2">Scavengers Toolbox</th>
		</tr>
		<tr>
			<td>Scavengers Toolbox Aktiviert</td>
			<td>
				<input name="stb_enabled" type="checkbox" value="1" {if $stb_enabled == 1}checked="checked"{/if}>
			</td>
		</tr>
		<tr>
			<td>
				<a title="Wenn weniger Ressoucen als eingegeben auf dem Planeten sind, wird der der Spionagebericht hellrot (schlecht) gekennzeichnet.">Geringe Ressourcen auf dem Zielplaneten vorhanden</a>
			</td>
			<td>
				<input name="stb_small_ress" size="{$stb_small_ress|count_characters + 3}" value="{$stb_small_ress}" type="int">
			</td>
		</tr>
		<tr>
			<td>
				<a title="Wenn weniger Ressoucen als eingegeben auf dem Planeten sind, wird der der Spionagebericht hellorange (mittel) gekennzeichnet.">Mittlere Ressourcen auf dem Zielplaneten vorhanden</a>
			</td>
			<td>
				<input name="stb_med_ress" size="{$stb_med_ress|count_characters + 3}" value="{$stb_med_ress}" type="int">
			</td>
		</tr>
		<tr>
			<td>
				<a title="Wenn weniger Ressoucen als eingegeben auf dem Planeten sind, wird der der Spionagebericht hellgrün (gut) gekennzeichnet. Spionageberichte mit Ressourcen oberhalb dieses Wertes werden hellblau (optimal) angezeigt.">Viele Ressourcen auf dem Zielplaneten vorhanden</a>
			</td>
			<td>
				<input name="stb_big_ress" size="{$stb_big_ress|count_characters + 3}" value="{$stb_big_ress}" type="int">
			</td>
		</tr>
		<tr>
			<td>
				<a title="Irgendwas">kleine ressourcen</a>
			</td>
			<td>
				<input name="stb_small_time" size="{$stb_small_time|count_characters + 3}" value="{$stb_small_time}" type="int">
			</td>
		</tr>
		<tr>
			<td>
				<a title="Irgendwas">kleine ressourcen</a>
			</td>
			<td>
				<input name="stb_med_time" size="{$stb_med_time|count_characters + 3}" value="{$stb_med_time}" type="int">
			</td>
		</tr>
		<tr>
			<td>
				<a title="Irgendwas">kleine ressourcen</a>
			</td>
			<td>
				<input name="stb_big_time" size="{$stb_big_time|count_characters + 3}" value="{$stb_big_time}" type="int">
			</td>
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
