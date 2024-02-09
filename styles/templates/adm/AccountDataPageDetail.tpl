{include file="overall_header.tpl"}
<style type="text/css">
a.link{
font-size:14px;font-variant:small-caps;margin-left:120px;
}
span.no_moon{
font-size:14px;font-variant:small-caps;margin-left:120px;font-family: Arial, Helvetica, sans-serif;
}
span.no_moon:hover{
font-size:14px;font-variant:small-caps;margin-left:120px;color:#FF0000;cursor:default;font-family: Arial, Helvetica, sans-serif;
}
a.ccc{
font-size:15px;
}
a.ccc:hover{
font-size:15px;color:aqua
;}
table.tableunique{
border:0px;background:url(./styles/resource/images/admin/blank.gif);width:100%;
}
td.unico{
border:0px;text-align:left;
}
td.unico2{
border:0px;text-align:center;
}
td{
color:#FFFFFF;font-size:10px;font-variant:normal;
}
td.blank{
border:0px;background:url(./styles/resource/images/admin/blank.gif);text-align:right;padding-right:80px;font-size:15px;
}
</style>


<table class="tableunique">
	<tr>
		<td class="blank">
			<a class="tooltip" data-tooltip-content="{$LNG.ac_note_k}">
				{$LNG.ac_leyend}&nbsp; <img src="./styles/resource/images/admin/i.gif" height="12" width="12">
			</a>
		</td>
	</tr>
	<tr>
		<td class="unico transparent">
			<a href="#" onclick="$('#datos').slideToggle();return false" class="link">
				<img src="./styles/resource/images/admin/arrowright.png" width="16" height="10"> {$LNG.ac_account_data}
			</a>
		</td>
	</tr>
	<tr>
		<td class="unico transparent">
			<div id="datos">
				<table align="center" width="60%">
					<tr><th colspan="2">&nbsp;</th></tr>
					<tr><td height="22px">{$LNG.input_id}</td><td>{$id}</td></tr>
					<tr><td height="22px">{$LNG.ac_name}</td><td>{$nombre}</td></tr>
					<tr><td height="22px">{$LNG.ac_mail}</td><td>{$email_1}</td></tr>
					<tr><td height="22px">{$LNG.ac_perm_mail}</td><td>{$email_2}</td></tr>
					<tr><td height="22px">{$LNG.ac_auth_level}</td><td>{$nivel}</td></tr>
					<tr><td height="22px">{$LNG.ac_on_vacation}</td><td>{$vacas}</td></tr>
					<tr><td height="22px">{$LNG.ac_banned}</td><td>{$suspen} {$mas}</td></tr>
					<tr><td height="22px">{$LNG.ac_alliance}</td><td>{if !empty($allianceData)}{$allianceData.name} ({$LNG.ac_ali_idid} {$allianceData.id}){else}{$LNG.ac_no_ally}{/if}</td></tr>
					<tr><td height="22px">{$LNG.ac_reg_ip}</td><td>{$ip}</td></tr>
					<tr><td height="22px">{$LNG.ac_last_ip}</td><td>{$ip2}</td></tr>
					<tr><td height="22px">{$LNG.ac_checkip_title}</td><td>{$ipcheck}</td></tr>
					<tr><td height="22px">{$LNG.ac_register_time}</td><td>{$reg_time}</td></tr>
					<tr><td height="22px">{$LNG.ac_act_time}</td><td>{$onlinetime}</td></tr>
					<tr><td height="22px">{$LNG.ac_home_planet_id}</td><td>{$id_p}</td></tr>
					<tr><td height="22px">{$LNG.ac_home_planet_coord}</td><td>[{$g}:{$s}:{$p}]</td></tr>
					{if $info}
						<tr><td height="22px">{$LNG.ac_user_system}</td><td>{$info}</td></tr>
					{/if}
					<tr><td height="22px">{$LNG.ac_ranking}</td><td><a href="#" onclick="$('#puntaje').slideToggle();return false">{$LNG.ac_see_ranking}</a></td></tr>
				</table>
				<br>

				<!-- USER SCORE -->
				<div id="puntaje" style="display:none">
					<table align="center" width="60%">
						<tr><th colspan="3" class="centrado2">{$LNG.ac_user_ranking}</th></tr>
						<tr><td width="15%"></td><td width="40%" class="centrado">{$LNG.ac_points_count}</td><td width="5%" class="centrado">{$LNG.ac_ranking}</td></tr>
						<tr><td width="15%" class="centrado">{$LNG.researchs_title}</td><td width="40%">{$point_tecno} ({$count_tecno} {$LNG.researchs_title})</td><td width="5%" class="ranking"># {$ranking_tecno}</td></tr>
						<tr><td width="15%" class="centrado">{$LNG.defenses_title}</td><td width="40%">{$point_def} ({$count_def} {$LNG.defenses_title})</td><td width="5%" class="ranking"># {$ranking_def}</td></tr>
						<tr><td width="15%" class="centrado">{$LNG.ships_title}</td><td width="40%">{$point_fleet} ({$count_fleet} {$LNG.ships_title})</td><td width="5%" class="ranking"># {$ranking_fleet}</td></tr>
						<tr><td width="15%" class="centrado">{$LNG.buildings_title}</td><td width="40%">{$point_builds} ({$count_builds} {$LNG.buildings_title})</td><td width="5%" class="ranking"># {$ranking_builds}</td></tr>
						<tr><td colspan="3" class="total">{$LNG.ac_total_points}<span class="colorNegative">{$total_points}</span></td></tr>
					</table>
					<br>
				</div>

				<div id="banned" style="display:none">
					<table align="center" width="60%">
						<tr><th colspan="4">{$LNG.ac_suspended_title}</th></tr>
						<tr><td>{$LNG.ac_suspended_time}</td><td>{$sus_time}</td></tr>
						<tr><td>{$LNG.ac_suspended_longer}</td><td>{$sus_longer}</td></tr>
						<tr><td>{$LNG.ac_suspended_reason}</td><td>{$sus_reason}</td></tr>
						<tr><td>{$LNG.ac_suspended_autor}</td><td>{$sus_author}</td></tr>
					</table>
				</div>
			</div>
		</td>
	</tr>

	<!-- ALLIANCE -->
	<tr>
		<td class="unico transparent">
			{if !empty($allianceData)}
				<a href="#" onclick="$('#alianza').slideToggle();return false" class="link">
					<img src="./styles/resource/images/admin/arrowright.png" width="16" height="10"> {$LNG.ac_alliance}
				</a>
			{else}
				<span class="no_moon">
					<img src="./styles/resource/images/admin/arrowright.png" width="16" height="10">
					{$LNG.ac_alliance}&nbsp;{$LNG.ac_no_alliance}
				</span>
			{/if}
		</td>
	</tr>
	{if !empty($allianceData)}
		<tr>
			<td class="unico transparent">
				<div id="alianza" style="display:none">
					<table align="center" width="60%">
						<tr><th colspan="2">{$LNG.ac_info_ally}</th></tr>
						<tr><td width="25%" align="center" >{$LNG.input_id}</td><td>{$allianceData.id}</td></tr>
						<tr><td>{$LNG.ac_leader}</td><td>{$allianceData.leader}</td></tr>
						<tr><td>{$LNG.ac_tag}</td><td>{$allianceData.tag}</td></tr>
						<tr><td>{$LNG.ac_name_ali}</td><td>{$allianceData.name}</td></tr>
						<tr><td>{$LNG.ac_ext_text}</td><td>{if !$allianceData.externalText}{$LNG.ac_no_text_ext}{else}<a href="#" onclick="$('#externo').slideToggle();return false">{$LNG.ac_view_text_ext}{/if}</td></tr>
						<tr><td>{$LNG.ac_int_text}</td><td>{if !$allianceData.internalText}{$LNG.ac_no_text_int}{else}<a href="#" onclick="$('#interno').slideToggle();return false">{$LNG.ac_view_text_int}{/if}</td></tr>
						<tr><td>{$LNG.ac_sol_text}</td><td>{if !$allianceData.applyText}{$LNG.ac_no_text_sol}{else}<a href="#" onclick="$('#solicitud').slideToggle();return false">{$LNG.ac_view_text_sol}{/if}</td></tr>
						<tr><td>{$LNG.ac_image}</td><td>{if !$allianceData.logo}{$LNG.ac_no_img}{else}<a href="#" onclick="$('#imagen').slideToggle();return false">{$LNG.ac_view_image2}{/if}</td></tr>
						<tr><td>{$LNG.ac_ally_web}</td><td>"<a href="{$allianceData.website}" target=_blank>{$allianceData.website}</a></td></tr>
						<tr><td>{$LNG.ac_register_ally_time}</td><td>{$allianceData.foundationDate}</td></tr>
						<tr><td>{$LNG.ac_total_members}</td><td>{$allianceData.memberAmount}</td></tr>
						<tr><td>{$LNG.ac_ranking}</td><td><a href="#" onclick="$('#puntaje_ali').slideToggle();return false">{$LNG.ac_see_ranking}</a></td></tr>
					</table>
					<br>

					<div id="imagen" style="display:none">
						<table align="center" width="60%">
							<tr><th>{$LNG.ac_ali_logo_11}</th></tr>
							<tr><td width="60%"><img src="{$allianceData.logo}" class="image"></td></tr>
							<tr><td><a href="{$allianceData.logo}" target="_blank">{$LNG.ac_view_image}</a></td></tr>
							<tr><td>{$LNG.ac_urlnow} <input type="text" size="50" value="{$allianceData.logo}"></td></tr>
						</table>
						<br>
					</div>

					<div id="externo" style="display:none">
						<table align="center" width="60%">
							<tr><th>{$LNG.ac_ali_text_11}</th></tr>
							<tr><td width="60%">{$allianceData.externalText}</td></tr>
						</table>
						<br>
					</div>

					<div id="interno" style="display:none">
						<table align="center" width="60%">
							<tr><td class="c">{$LNG.ac_ali_text_22}</td></tr>
							<tr><td width="60%">{$allianceData.internalText}</td></tr>
						</table>
						<br>
					</div>

					<div id="solicitud" style="display:none">
						<table align="center" width="60%">
							<tr><th>{$LNG.ac_ali_text_33}</th></tr>
							<tr><td width="60%">{$allianceData.applyText}</td></tr>
						</table>
						<br>
					</div>

					<!-- USER ALLIANCE SCORE -->
					<div id="puntaje_ali" style="display:none">
						<table align="center" width="60%">
							<tr><td class="c" colspan="3">{$LNG.ac_ally_ranking}</td></tr>
							<tr><td width="15%"></td><td width="40%">{$LNG.ac_points_count}</td><td width="5%" class="centrado">{$LNG.ac_ranking}</td></tr>
							<tr><td width="15%">{$LNG.researchs_title}</td><td width="40%">{pretty_number($allianceStats.tech_points)} ({pretty_number($allianceStats.tech_count)} {$LNG.researchs_title})</td><td width="5%"># {pretty_number($allianceStats.tech_rank)}</td></tr>
							<tr><td width="15%">{$LNG.defenses_title}</td><td width="40%">{pretty_number($allianceStats.defs_points)} ({pretty_number($allianceStats.defs_count)} {$LNG.defenses_title})</td><td width="5%"># {pretty_number($allianceStats.defs_rank)}</td></tr>
							<tr><td width="15%">{$LNG.ships_title}</td><td width="40%">{pretty_number($allianceStats.fleet_points)} ({pretty_number($allianceStats.fleet_count)} {$LNG.ships_title})</td><td width="5%"># {pretty_number($allianceStats.fleet_rank)}</td></tr>
							<tr><td width="15%">{$LNG.buildings_title}</td><td width="40%">{pretty_number($allianceStats.build_points)} ({pretty_number($allianceStats.build_count)} {$LNG.buildings_title})</td><td width="5%"># {pretty_number($allianceStats.build_rank)}</td></tr>
							<tr><td colspan="3">{$LNG.ac_total_points}<span class="colorNegative">{pretty_number($allianceStats.total_points)}</span></td></tr>
						</table>
						<br>
					</div>
				</div>
			</td>
		</tr>
	{/if}

	<!-- PLANETS & MOONS -->
	<tr>
		<td class="unico transparent">
			<a href="#" onclick="$('#pla').slideToggle();return false" class="link">
				<img src="./styles/resource/images/admin/arrowright.png" width="16" height="10"> {$LNG.ac_id_names_coords}
			</a>
		</td>
	</tr>
	<tr>
		<td class="unico transparent">
			<div id="pla" style="display:none">
				<table width="70%" align="center">
					<tr>
						<th>{$LNG.ac_name}</th>
						<th>{$LNG.input_id}</th>
						<th>{$LNG.ac_diameter}</th>
						<th>{$LNG.ac_fields}</th>
						<th>{$LNG.ac_temperature}</th>
						{if $canedit == 1}<th>{$LNG.se_search_edit}</th>{/if}
					</tr>
					{$planetsMoonsTableRows}
				</table>
				<br>
			</div>
		</td>
	</tr>

	<!-- RESOURCES -->
	<tr>
		<td class="unico transparent">
			<a href="#" onclick="$('#recursos').slideToggle();return false" class="link">
				<img src="./styles/resource/images/admin/arrowright.png" width="16" height="10"> {$LNG.resources_title}
			</a>
		</td>
	</tr>
	<tr>
		<td class="unico transparent">
			<div id="recursos" style="display:none">
				<table width="70%" align="center">
					<tr>
						<th>{$LNG.ac_name}</th>
						<th>{$Metal}</th>
						<th>{$Crystal}</th>
						<th>{$Deuterium}</th>
						<th>{$Energy}</th>
					</tr>
					{$resourcesTableRows}
				</table>
				<br>
			</div>
		</td>
	</tr>

	<!-- BUILDINGS -->
	<tr>
		<td class="unico transparent">
			<a href="#" onclick="$('#edificios').slideToggle();return false" class="link">
				<img src="./styles/resource/images/admin/arrowright.png" width="16" height="10"> {$LNG.buildings_title}
			</a>
		</td>
	</tr>
	<tr>
		<td class="unico transparent">
			<div id="edificios" style="display:none">
				<table width="100%" align="center">
					{$planetNames}
					{$buildingsTableRows}
				</table>
				<br>
			</div>
		</td>
	</tr>

	<!-- FLEET -->
	<tr>
		<td class="unico transparent">
			<a href="#" onclick="$('#naves').slideToggle();return false" class="link">
				<img src="./styles/resource/images/admin/arrowright.png" width="16" height="10"> {$LNG.ships_title}
			</a>
		</td>
	</tr>
	<tr>
		<td class="unico transparent">
			<div id="naves" style="display:none">
				<table align="center" width="100%">
					{$planetNames}
					{$fleetTableRows}
				</table>
				<br>
			</div>
		</td>
	</tr>

	<!-- DEFENCE -->
	<tr>
		<td class="unico transparent">
			<a href="#" onclick="$('#defensa').slideToggle();return false" class="link">
				<img src="./styles/resource/images/admin/arrowright.png" width="16" height="10"> {$LNG.defenses_title}
			</a>
		</td>
	</tr>
	<tr>
		<td class="unico transparent">
			<div id="defensa" style="display:none">
				<table align="center" width="100%">
					{$planetNames}
					{$defenseTableRows}
				</table>
				<br>
			</div>
		</td>
	</tr>
	
	<!-- RESEARCH -->
	<tr>
		<td class="unico transparent">
			<a href="#" onclick="$('#inves').slideToggle();return false" class="link">
				<img src="./styles/resource/images/admin/arrowright.png" width="16" height="10"> {$LNG.ac_research}
			</a>
		</td>
	</tr>
	<tr>
		<td class="unico transparent">
			<div id="inves" style="display:none">
				<table align="center" width="60%">
					<tr><th width="50%">{$LNG.researchs_title}</th></tr>
					{$technologyTableRows}
				</table>
				<br>
			</div>
		</td>
	</tr>

	<!-- RECENTLY DESTROYED PLANETS -->
	<tr>
		<td class="unico transparent">
			{if $hasDestroyedPlanets}
				<a href="#" onclick="$('#destr').slideToggle();return false" class="link">
					<img src="./styles/resource/images/admin/arrowright.png" width="16" height="10"> {$LNG.ac_recent_destroyed_planets}
				</a>
			{else}
				<span class="no_moon">
					<img src="./styles/resource/images/admin/arrowright.png" width="16" height="10">
					{$LNG.ac_recent_destroyed_planets}&nbsp;{$LNG.ac_isnodestruyed}
				</span>
			{/if}
		</td>
	</tr>
	<tr>
		<td class="unico transparent">
			<div id="destr" style="display:none">
				<table align="center" width="60%">
					<tr>
						<th>{$LNG.ac_name}</th>
						<th>{$LNG.input_id}</th>
						<th>{$LNG.ac_coords}</th>
						<th>{$LNG.ac_time_destruyed}</th>
					</tr>
					{$destroyedPlanetsTableRows}
				</table>
				<br>
			</div>
		</td>
	</tr>
</table>
{include file="overall_footer.tpl"}
