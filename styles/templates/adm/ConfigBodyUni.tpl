{include file="overall_header.tpl"}
<form action="" method="post">
	<input type="hidden" name="opt_save" value="1">
	<table width="70%" cellpadding="2" cellspacing="2">
		<tr>
			<th colspan="2">{$LNG.se_server_parameters}</th>
			<th colspan="1" width="5%">(?)</th>
		</tr>
		<tr>
			<td>{$LNG.se_uni_name}</td>
			<td><input name="uni_name" value="{$uni_name}" type="text" maxlength="60"></td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td>{$LNG.se_lang}</td>
			<td>{html_options name=lang options=$Selector.langs selected=$lang}</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td>{$LNG.se_building_speed}</td>
			<td><input name="building_speed" value="{$building_speed}" type="text" maxlength="5"></td>
			<td><img src="./styles/resource/images/admin/i.gif" width="16" height="16" alt="" class="tooltip" data-tooltip-content="{$LNG.se_normal_speed}"></td>
		</tr>
		<tr>
			<td>{$LNG.se_shipyard_speed}</td>
			<td><input name="shipyard_speed" value="{$shipyard_speed}" type="text" maxlength="5"></td>
			<td><img src="./styles/resource/images/admin/i.gif" width="16" height="16" alt="" class="tooltip" data-tooltip-content="{$LNG.se_normal_speed}"></td>
		</tr>
		<tr>
			<td>{$LNG.se_research_speed}</td>
			<td><input name="research_speed" value="{$research_speed}" type="text" maxlength="5"></td>
			<td><img src="./styles/resource/images/admin/i.gif" width="16" height="16" alt="" class="tooltip" data-tooltip-content="{$LNG.se_normal_speed}"></td>
		</tr>
		<tr>
			<td>{$LNG.se_fleet_speed}</td>
			<td><input name="fleet_speed" value="{$fleet_speed}" type="text" maxlength="5"></td>
			<td><img src="./styles/resource/images/admin/i.gif" width="16" height="16" alt="" class="tooltip" data-tooltip-content="{$LNG.se_normal_speed_fleet}"></td>
		</tr>
		<tr>
			<td>{$LNG.se_resources_producion_speed}</td>
			<td><input name="resource_multiplier" value="{$resource_multiplier}" type="text"></td>
			<td><img src="./styles/resource/images/admin/i.gif" width="16" height="16" alt="" class="tooltip" data-tooltip-content="{$LNG.se_normal_speed_resoruces}"></td>
		</tr>
		<tr>
			<td>{$LNG.se_storage_producion_speed}</td>
			<td><input name="storage_multiplier" value="{$storage_multiplier}" type="text"></td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td>{$LNG.se_max_overflow}</td>
			<td><input name="max_overflow" maxlength="3" size="3" value="{$max_overflow}" type="text"></td>
			<td><img src="./styles/resource/images/admin/i.gif" width="16" height="16" alt="" class="tooltip" data-tooltip-content="{$LNG.se_max_overflow_info}"></td>
		</tr>
		<tr>
			<td>{$LNG.se_expo_hold_multiplier}</td>
			<td><input name="expo_hold_multiplier" value="{$expo_hold_multiplier}" type="text"></td>
			<td><img src="./styles/resource/images/admin/i.gif" width="16" height="16" alt="" class="tooltip" data-tooltip-content="{$LNG.se_normal_speed_halt}"></td>
		</tr>
		<tr>
			<td>{$LNG.se_energy_speed}</td>
			<td><input name="energy_multiplier" value="{$energy_multiplier}" type="text"></td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td>{$LNG.se_del_user_automatic}</td>
			<td><input name="del_user_automatic" maxlength="3" size="2" value="{$del_user_automatic}" type="text"> {$LNG.se_days}</td>
			<td><img src="./styles/resource/images/admin/i.gif" width="16" height="16" alt="" class="tooltip" data-tooltip-content="{$LNG.se_del_user_automatic_info}"></td>
		</tr>
		<tr>
			<td>{$LNG.se_vmode_min_time}</td>
			<td><input name="vmode_min_time" maxlength="11" size="11" value="{$vmode_min_time}" type="text"></td>
			<td><img src="./styles/resource/images/admin/i.gif" width="16" height="16" alt="" class="tooltip" data-tooltip-content="{$LNG.se_vmode_min_time_info}"></td>
		</tr>
		<tr>
			<td>{$LNG.se_alliance_create_min_points}</td>
			<td><input name="alliance_create_min_points" maxlength="20" size="25" value="{$alliance_create_min_points}" type="text"></td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td>{$LNG.se_debug_mode}</td>
			<td><input name="debug" {if $debug} checked="checked" {/if} type="checkbox"></td>
			<td><img src="./styles/resource/images/admin/i.gif" width="16" height="16" alt="" class="tooltip" data-tooltip-content="{$LNG.se_debug_message}"></td>
		</tr>
		<tr>
			<td>{$LNG.se_uni_status}</td>
			<td>{html_options name=uni_status options=$Selector.uni_status selected=$uni_status}</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td>{$LNG.se_server_status_message}<br></td>
			<td><textarea name="close_reason" cols="80" rows="5">{$close_reason}</textarea></td>
			<td>&nbsp;</td>
		</tr>


		<tr>
			<th colspan="2">{$LNG.se_buildlist}</th>
			<th>&nbsp;</th>
		</tr>
		<tr>
			<td>{$LNG.se_max_elements_build}</td>
			<td><input name="max_elements_build" maxlength="3" size="3" value="{$max_elements_build}" type="text"></td>
			<td><img src="./styles/resource/images/admin/i.gif" width="16" height="16" alt="" class="tooltip" data-tooltip-content="{$LNG.se_max_elements_build_info}"></td>
		</tr>
		<tr>
			<td>{$LNG.se_max_elements_tech}</td>
			<td><input name="max_elements_tech" maxlength="3" size="3" value="{$max_elements_tech}" type="text"></td>
			<td><img src="./styles/resource/images/admin/i.gif" width="16" height="16" alt="" class="tooltip" data-tooltip-content="{$LNG.se_max_elements_tech_info}"></td>
		</tr>
		<tr>
			<td>{$LNG.se_max_elements_ships}</td>
			<td><input name="max_elements_ships" maxlength="3" size="3" value="{$max_elements_ships}" type="text"></td>
			<td><img src="./styles/resource/images/admin/i.gif" width="16" height="16" alt="" class="tooltip" data-tooltip-content="{$LNG.se_max_elements_ships_info}"></td>
		</tr>
		<tr>
			<td>{$LNG.se_max_fleet_per_build}</td>
			<td><input name="max_fleet_per_build" maxlength="20" size="15" value="{$max_fleet_per_build}" type="text"></td>
			<td><img src="./styles/resource/images/admin/i.gif" width="16" height="16" alt="" class="tooltip" data-tooltip-content="{$LNG.se_max_fleet_per_build_info}"></td>
		</tr>
		<tr>
			<td>{$LNG.se_min_build_time}</td>
			<td><input name="min_build_time" maxlength="2" size="5" value="{$min_build_time}" type="text"></td>
			<td><img src="./styles/resource/images/admin/i.gif" width="16" height="16" alt="" class="tooltip" data-tooltip-content="{$LNG.se_min_build_time_info}" /></td>
		</tr>


		<tr>
			<th colspan="2">{$LNG.se_ref}</th>
			<th>&nbsp;</th>
		</tr>
		<tr>
			<td>{$LNG.se_ref_active}</td>
			<td><input name="ref_active" {if $ref_active} checked="checked" {/if} type="checkbox"></td>
			<td><img src="./styles/resource/images/admin/i.gif" width="16" height="16" alt="" class="tooltip" data-tooltip-content="{$LNG.se_ref_active_info}"></td>
		</tr>
		<tr>
			<td>{$LNG.se_ref_bonus}</td>
			<td><input name="ref_bonus" maxlength="6" size="8" value="{$ref_bonus}" type="text"></td>
			<td><img src="./styles/resource/images/admin/i.gif" width="16" height="16" alt="" class="tooltip" data-tooltip-content="{$LNG.se_ref_bonus_info}"></td>
		</tr>
		<tr>
			<td>{$LNG.se_ref_minpoints}</td>
			<td><input name="ref_minpoints" maxlength="20" size="25" value="{$ref_minpoints}" type="text"></td>
			<td><img src="./styles/resource/images/admin/i.gif" width="16" height="16" alt="" class="tooltip" data-tooltip-content="{$LNG.se_ref_minpoints_info}"></td>
		</tr>
		<tr>
			<td>{$LNG.se_ref_max_referals}</td>
			<td><input name="ref_max_referals" maxlength="6" size="8" value="{$ref_max_referals}" type="text"></td>
			<td><img src="./styles/resource/images/admin/i.gif" width="16" height="16" alt="" class="tooltip" data-tooltip-content="{$LNG.se_ref_max_referals_info}"></td>
		</tr>


		<tr>
			<th colspan="2">{$LNG.se_galaxy_parameters}</th>
			<th>&nbsp;</th>
		</tr>
		<tr>
			<td>{$LNG.se_deuterium_cost_galaxy}</td>
			<td><input name="deuterium_cost_galaxy" maxlength="11" size="11" value="{$deuterium_cost_galaxy}" type="text"> {$LNG.tech.903}</td>
			<td><img src="./styles/resource/images/admin/i.gif" width="16" height="16" alt="" class="tooltip" data-tooltip-content="{$LNG.se_deuterium_cost_galaxy_info}"></td>
		</tr>
		<tr>
			<td>{$LNG.se_max_galaxy}</td>
			<td><input name="max_galaxy" maxlength="3" size="3" value="{$max_galaxy}" type="text"></td>
			<td><img src="./styles/resource/images/admin/i.gif" width="16" height="16" alt="" class="tooltip" data-tooltip-content="{$LNG.se_max_galaxy_info}"></td>
		</tr>
		<tr>
			<td>{$LNG.se_max_system}</td>
			<td><input name="max_system" maxlength="5" size="5" value="{$max_system}" type="text"></td>
			<td><img src="./styles/resource/images/admin/i.gif" width="16" height="16" alt="" class="tooltip" data-tooltip-content="{$LNG.se_max_system_info}"></td>
		</tr>
		<tr>
			<td>{$LNG.se_max_planets}</td>
			<td><input name="max_planets" maxlength="3" size="3" value="{$max_planets}" type="text"></td>
			<td><img src="./styles/resource/images/admin/i.gif" width="16" height="16" alt="" class="tooltip" data-tooltip-content="{$LNG.se_max_planets_info}"></td>
		</tr>
		<tr>
			<td>{$LNG.se_uni_type}</td>
			<td>{html_options name=uni_type options=$Selector.uni_types selected=$uni_type}</td>
			<td><img src="./styles/resource/images/admin/i.gif" width="16" height="16" alt="" class="tooltip" data-tooltip-content="{$LNG.se_uni_type_info}"></td>
		</tr>
		<tr>
			<td>{$LNG.se_galaxy_type}</td>
			<td>{html_options name=galaxy_type options=$Selector.galaxy_types selected=$galaxy_type}</td>
			<td><img src="./styles/resource/images/admin/i.gif" width="16" height="16" alt="" class="tooltip" data-tooltip-content="{$LNG.se_galaxy_type_info}"></td>
		</tr>
		<tr>
			<td>{$LNG.se_planet_creation}</td>
			<td>{html_options name=planet_creation options=$Selector.planet_creations selected=$planet_creation}</td>
			<td><img src="./styles/resource/images/admin/i.gif" width="16" height="16" alt="" class="tooltip" data-tooltip-content="{$LNG.se_planet_creation_info}"></td>
		</tr>


		<tr>
			<th colspan="2">{$LNG.se_server_colonisation_config}</th>
			<th>&nbsp;</th>
		</tr>
		<tr>
			<td>{$LNG.se_planets_min}</td>
			<td><input name="max_initial_planets" maxlength="11" size="11" value="{$max_initial_planets}" type="text"></td>
			<td><img src="./styles/resource/images/admin/i.gif" width="16" height="16" alt="" class="tooltip" data-tooltip-content="{$LNG.se_planets_min_info}"></td>
		</tr>
		<tr>
			<td>{$LNG.se_max_additional_planets}</td>
			<td><input name="max_additional_planets" maxlength="11" size="11" value="{$max_additional_planets}" type="text"></td>
			<td><img src="./styles/resource/images/admin/i.gif" width="16" height="16" alt="" class="tooltip" data-tooltip-content="{$LNG.se_max_additional_planets_info}"></td>
		</tr>
		<tr>
			<td>{$LNG.se_planets_per_tech}</td>
			<td><input name="planets_per_tech" maxlength="11" size="11" value="{$planets_per_tech}" type="text"></td>
			<td><img src="./styles/resource/images/admin/i.gif" width="16" height="16" alt="" class="tooltip" data-tooltip-content="{$LNG.se_planets_per_tech_info}"></td>
		</tr>
		<tr>
			<td>{$LNG.se_planet_size_factor}</td>
			<td><input name="planet_size_factor" maxlength="3" size="3" value="{$planet_size_factor}" type="text"></td>
			<td><img src="./styles/resource/images/admin/i.gif" width="16" height="16" alt="" class="tooltip" data-tooltip-content="{$LNG.se_planet_size_factor_info}"></td>
		</tr>
		<tr>
			<td>{$LNG.se_all_planet_pictures}</td>
			<td><input name="all_planet_pictures" {if $all_planet_pictures} checked="checked" {/if} type="checkbox"></td>
			<td><img src="./styles/resource/images/admin/i.gif" width="16" height="16" alt="" class="tooltip" data-tooltip-content="{$LNG.se_all_planet_pictures_info}"></td>
		</tr>


		<tr>
			<th colspan="2">{$LNG.se_server_planet_parameters}</th>
			<th>&nbsp;</th>
		</tr>
		<tr>
			<td>{$LNG.se_metal_start}</td>
			<td><input name="metal_start" maxlength="11" size="11" value="{$metal_start}" type="text"></td>
			<td><img src="./styles/resource/images/admin/i.gif" width="16" height="16" alt="" class="tooltip" data-tooltip-content="{$LNG.se_metal_start_info}"></td>
		</tr>
		<tr>
			<td>{$LNG.se_crystal_start}</td>
			<td><input name="crystal_start" maxlength="11" size="11" value="{$crystal_start}" type="text"></td>
			<td><img src="./styles/resource/images/admin/i.gif" width="16" height="16" alt="" class="tooltip" data-tooltip-content="{$LNG.se_crystal_start_info}"></td>
		</tr>
		<tr>
			<td>{$LNG.se_deuterium_start}</td>
			<td><input name="deuterium_start" maxlength="11" size="11" value="{$deuterium_start}" type="text"></td>
			<td><img src="./styles/resource/images/admin/i.gif" width="16" height="16" alt="" class="tooltip" data-tooltip-content="{$LNG.se_deuterium_start_info}"></td>
		</tr>
		<tr>
			<td>{$LNG.se_initial_fields}</td>
			<td><input name="initial_fields" maxlength="10" size="10" value="{$initial_fields}" type="text"> {$LNG.se_fields} </td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td>{$LNG.se_initial_temp}</td>
			<td><input name="initial_temp" maxlength="10" size="10" value="{$initial_temp}" type="text"> {$LNG.se_temp} </td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td>{$LNG.se_metal_production}</td>
			<td><input name="metal_basic_income" maxlength="10" size="10" value="{$metal_basic_income}" type="text"> {$LNG.se_per_hour}</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td>{$LNG.se_crystal_production}</td>
			<td><input name="crystal_basic_income" maxlength="10" size="10" value="{$crystal_basic_income}" type="text"> {$LNG.se_per_hour}</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td>{$LNG.se_deuterium_production}</td>
			<td><input name="deuterium_basic_income" maxlength="10" size="10" value="{$deuterium_basic_income}" type="text"> {$LNG.se_per_hour}</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td>{$LNG.se_energy_production}</td>
			<td><input name="energy_basic_income" maxlength="10" size="10" value="{$energy_basic_income}" type="text"> {$LNG.se_per_hour}</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td>{$LNG.se_moon_factor}</td>
			<td><input name="moon_factor" maxlength="3" size="3" value="{$moon_factor}" type="text"></td>
			<td><img src="./styles/resource/images/admin/i.gif" width="16" height="16" alt="" class="tooltip" data-tooltip-content="{$LNG.se_moon_factor_info}"></td>
		</tr>
		<tr>
			<td>{$LNG.se_moon_chance}</td>
			<td><input name="moon_chance" maxlength="3" size="3" value="{$moon_chance}" type="text"></td>
			<td><img src="./styles/resource/images/admin/i.gif" width="16" height="16" alt="" class="tooltip" data-tooltip-content="{$LNG.se_moon_chance_info}"></td>
		</tr>
		<tr>
			<td>{$LNG.se_debris_moon}</td>
			<td><input name="debris_moon" {if $debris_moon} checked="checked" {/if} type="checkbox"></td>
			<td><img src="./styles/resource/images/admin/i.gif" width="16" height="16" alt="" class="tooltip" data-tooltip-content="{$LNG.se_debris_moon_info}"></td>
		</tr>
		<tr>
			<td>{$LNG.se_moon_size_factor}</td>
			<td><input name="moon_size_factor" maxlength="11" size="11" type="text" value="{$moon_size_factor}"></td>
			<td><img src="./styles/resource/images/admin/i.gif" width="16" height="16" alt="" class="tooltip" data-tooltip-content="{$LNG.se_moon_size_factor_info}"></td>
		</tr>
		<tr>
			<td>{$LNG.se_cascading_moon_chance}</td>
			<td><input name="cascading_moon_chance" maxlength="11" size="11" type="text" value="{$cascading_moon_chance}"> %</td>
			<td><img src="./styles/resource/images/admin/i.gif" width="16" height="16" alt="" class="tooltip" data-tooltip-content="{$LNG.se_cascading_moon_chance_info}"></td>
		</tr>


		<tr>
			<th colspan="2">{$LNG.se_buildings}</th>
			<th>&nbsp;</th>
		</tr>
		<tr>
			<td>{$LNG.se_factor_university}</td>
			<td><input name="factor_university" maxlength="3" size="3" value="{$factor_university}" type="text"></td>
			<td><img src="./styles/resource/images/admin/i.gif" width="16" height="16" alt="" class="tooltip" data-tooltip-content="{$LNG.se_factor_university_info}"></td>
		</tr>
		<tr>
			<td>{$LNG.se_silo_factor}</td>
			<td><input name="silo_factor" maxlength="2" size="2" value="{$silo_factor}" type="text"></td>
			<td><img src="./styles/resource/images/admin/i.gif" width="16" height="16" alt="" class="tooltip" data-tooltip-content="{$LNG.se_silo_factor_info}"></td>
		</tr>
		<tr>
			<td>{$LNG.se_jumpgate_factor}</td>
			<td><input name="jumpgate_factor" maxlength="11" size="11" value="{$jumpgate_factor}" type="text"></td>
			<td><img src="./styles/resource/images/admin/i.gif" width="16" height="16" alt="" class="tooltip" data-tooltip-content="{$LNG.se_jumpgate_factor_info}"></td>
		</tr>


		<tr>
			<th colspan="2">{$LNG.se_expedition_parameters}</th>
			<th>&nbsp;</th>
		</tr>
		<tr>
			<td>{$LNG.se_ress_chance}</td>
			<td>
				<div class="slidecontainer">
					<input name="expo_ress_met_chance" type="range" min="0" max="100" value="{$expo_ress_met_chance}" class="slider" id="expoMetal"> {$LNG.tech.901}: <span id="expoMetalValue"></span><br>
					<input name="expo_ress_crys_chance" type="range" min="0" max="100" value="{$expo_ress_crys_chance}" class="slider" id="expoCrystal"> {$LNG.tech.902}: <span id="expoCrystalValue"></span><br>
					<input name="expo_ress_deut_chance" type="range" min="0" max="100" value="{$expo_ress_deut_chance}" class="slider" id="expoDeut"> {$LNG.tech.903}: <span id="expoDeutValue"></span>
				</div>
			</td>
			<td>&nbsp;</td>
		</tr>

		<tr>
			<th colspan="2">{$LNG.se_attacks}</th>
			<th>&nbsp;</th>
		</tr>
		<tr>
			<td>{$LNG.se_ships_cdr}</td>
			<td><input name="fleet_debris_percentage" maxlength="3" size="3" value="{$shiips}" type="text"> %</td>
			<td><img src="./styles/resource/images/admin/i.gif" width="16" height="16" alt="" class="tooltip" data-tooltip-content="{$LNG.se_ships_cdr_message}"></td>
		</tr>
		<tr>
			<td>{$LNG.se_def_cdr}</td>
			<td><input name="def_debris_percentage" maxlength="3" size="3" value="{$defenses}" type="text"> %</td>
			<td><img src="./styles/resource/images/admin/i.gif" width="16" height="16" alt="" class="tooltip" data-tooltip-content="{$LNG.se_def_cdr_message}"></td>
		</tr>
		<tr>
			<td>{$LNG.se_max_fleets_per_acs}</td>
			<td><input name="max_fleets_per_acs" maxlength="3" size="3" value="{$max_fleets_per_acs}" type="text"></td>
			<td><img src="./styles/resource/images/admin/i.gif" width="16" height="16" alt="" class="tooltip" data-tooltip-content="{$LNG.se_max_fleets_per_acs_info}"></td>
		</tr>
		<tr>
			<td>{$LNG.se_max_participants_per_acs}</td>
			<td><input name="max_participants_per_acs" maxlength="3" size="3" value="{$max_participants_per_acs}" type="text"></td>
			<td><img src="./styles/resource/images/admin/i.gif" width="16" height="16" alt="" class="tooltip" data-tooltip-content="{$LNG.se_max_participants_per_acs_info}"></td>
		</tr>
		<tr>
			<td>{$LNG.se_noob_protect}</td>
			<td><input name="noob_protection" {if $noobprot} checked="checked" {/if} type="checkbox"></td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td>{$LNG.se_noob_protect2}</td>
			<td><input name="noob_protection_time" value="{$noobprot2}" type="text"></td>
			<td><img src="./styles/resource/images/admin/i.gif" width="16" height="16" alt="" class="tooltip" data-tooltip-content="{$LNG.se_noob_protect_e2}"></td>
		</tr>
		<tr>
			<td>{$LNG.se_noob_protect3}</td>
			<td><input name="noob_protection_multi" value="{$noobprot3}" type="text"></td>
			<td><img src="./styles/resource/images/admin/i.gif" width="16" height="16" alt="" class="tooltip" data-tooltip-content="{$LNG.se_noob_protect_e3}"></td>
		</tr>
		<tr>
			<td>{$LNG.se_admin_protection}</td>
			<td><input name="adm_attack" {if $adm_attack} checked="checked" {/if} type="checkbox"></td>
			<td><img src="./styles/resource/images/admin/i.gif" width="16" height="16" alt="" class="tooltip" data-tooltip-content="{$LNG.se_title_admins_protection}" /></td>
		</tr>

		
		<tr>
			<th colspan="2">{$LNG.se_several_parameters}</th>
			<th>&nbsp;</th>
		</tr>


		<tr>
			<th colspan="2">{$LNG.se_trader_head}</th>
			<th>&nbsp;</th>
		</tr>
		<tr>
			<td>{$LNG.se_trader_ships}</td>
			<td><input name="trade_allowed_ships" maxlength="255" size="60" value="{$trade_allowed_ships}" type="text"></td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td>{$LNG.se_trader_charge}</td>
			<td><input name="trade_charge" maxlength="5" size="10" value="{$trade_charge}" type="text"></td>
			<td></td>
		</tr>


		<tr>
			<th colspan="2">{$LNG.se_news_head}</th>
			<th>&nbsp;</th>
		</tr>
		<tr>
			<td>{$LNG.se_news_active}</td>
			<td><input name="newsframe" {if $newsframe} checked="checked" {/if} type="checkbox"></td>
			<td><img src="./styles/resource/images/admin/i.gif" width="16" height="16" alt="" class="tooltip" data-tooltip-content="{$LNG.se_news_info}"></td>
		</tr>
		<tr>
			<td>{$LNG.se_news}</td>
			<td><textarea name="NewsText" cols="80" rows="5">{$NewsTextVal}</textarea></td>
			<td><img src="./styles/resource/images/admin/i.gif" width="16" height="16" alt="" class="tooltip" data-tooltip-content="{$LNG.se_news_limit}"></td>
		</tr>
		<tr>
			<td colspan="3"><input value="{$LNG.se_save_parameters}" type="submit"></td>
		</tr>
	</table>
</form>
<script src="./scripts/admin/Percentslider.js"></script>
<script>
	addpercentsliderstuff(["expoMetal", "expoCrystal", "expoDeut"])
</script>

{include file="overall_footer.tpl"}