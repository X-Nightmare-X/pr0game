<?php

/**
 *  2Moons
 *   by Jan-Otto Kröpke 2009-2016
 *
 * For the full copyright and license information, please view the LICENSE
 *
 * @package 2Moons
 * @author Jan-Otto Kröpke <slaver7@gmail.com>
 * @copyright 2009 Lucky
 * @copyright 2016 Jan-Otto Kröpke <slaver7@gmail.com>
 * @licence MIT
 * @version 1.8.0
 * @link https://github.com/jkroepke/2Moons
 */

if (!allowedTo(str_replace([dirname(__FILE__), '\\', '/', '.php'], '', __FILE__))) {
    throw new Exception("Permission error!");
}

function ShowAccountDataPage()
{
    $USER =& Singleton()->USER;
    $reslist =& Singleton()->reslist;
    $resource =& Singleton()->resource;
    $LNG =& Singleton()->LNG;
    $template = new template();
    $template->assign_vars([
        'signalColors'  => $USER['signalColors']
    ]);
    $id_u = HTTP::_GP('id_u', 0);
    if (!empty($id_u)) {
        $OnlyQueryLogin = $GLOBALS['DATABASE']->getFirstRow(
            "SELECT `id`, `authlevel` FROM " . USERS . " WHERE `id` = '" . $id_u . "' AND"
            . " `universe` = '" . Universe::getEmulated() . "';"
        );
        $SpecifyItemsUQ = "";
        if (!isset($OnlyQueryLogin)) {
            $template->message($LNG['ac_username_doesnt'], '?page=accoutdata');
        } else {
            foreach ($reslist['tech'] as $ID) {
                $SpecifyItemsUQ .= "u.`" . $resource[$ID] . "`,";
            }

            // COMIENZA SAQUEO DE DATOS DE LA TABLA DE USUARIOS
            $SpecifyItemsU = "u.id,u.username,u.email,u.email_2,u.authlevel,u.id_planet,u.galaxy,u.system,u.planet,"
                . "u.user_lastip,u.ip_at_reg,u.register_time,u.onlinetime,u.urlaubs_modus,u.urlaubs_until,"
                . "u.ally_id,a.ally_name," . $SpecifyItemsUQ . "u.ally_register_time,u.ally_rank_id,u.bana,u.banaday";

            $UserQuery = $GLOBALS['DATABASE']->getFirstRow(
                "SELECT " . $SpecifyItemsU . " FROM " . USERS . " as u LEFT JOIN " . ALLIANCE . " a ON a.id = u.ally_id"
                . " WHERE u.`id` = '" . $id_u . "';"
            );
            if (!isset($UserQuery['user_ua'])) {
                $UserQuery['user_ua'] = '';
            }

            $reg_time = _date($LNG['php_tdformat'], $UserQuery['register_time'], $USER['timezone']);
            $onlinetime = _date($LNG['php_tdformat'], $UserQuery['onlinetime'], $USER['timezone']);
            $id = $UserQuery['id'];
            $nombre = $UserQuery['username'];
            $email_1 = $UserQuery['email'];
            $email_2 = $UserQuery['email_2'];
            $ip = $UserQuery['ip_at_reg'];
            $ip2 = $UserQuery['user_lastip'];
            $id_p = $UserQuery['id_planet'];
            $g = $UserQuery['galaxy'];
            $s = $UserQuery['system'];
            $p = $UserQuery['planet'];
            $info = $UserQuery['user_ua'];
            $alianza = $UserQuery['ally_name'];
            $nivel = $LNG['rank_' . $UserQuery['authlevel']];
            $vacas = $LNG['one_is_yes_' . $UserQuery['urlaubs_modus']];
            $suspen = $LNG['one_is_yes_' . $UserQuery['bana']];

            foreach ($reslist['tech'] as $ID) {
                $techno[] = $ID;
            }
            $technologyTableRows = "";
            for ($i = 0; $i < count($reslist['tech']); $i++) {
                if (isset($techno[$i])) {
                    $technologyTableRows .= sprintf(
                        "<tr><td>%s: <font color=aqua>%s</font></td>",
                        $LNG['tech'][$techno[$i]],
                        $UserQuery[$resource[$techno[$i]]]
                    );
                } else {
                    $technologyTableRows .= "<tr><td>&nbsp;</td>";
                }
            }

            if ($UserQuery['bana'] != 0) {
                $mas = '<a ref="#" onclick="$(\'#banned\').slideToggle();return false"> ' . $LNG['ac_more'] . '</a>';

                $BannedQuery = $GLOBALS['DATABASE']->getFirstRow(
                    "SELECT theme,time,longer,author FROM " . BANNED
                    . " WHERE `who` = '" . $UserQuery['username'] . "';"
                );


                $sus_longer = _date($LNG['php_tdformat'], $BannedQuery['longer'], $USER['timezone']);
                $sus_time = _date($LNG['php_tdformat'], $BannedQuery['time'], $USER['timezone']);
                $sus_reason = $BannedQuery['theme'];
                $sus_author = $BannedQuery['author'];
            }


            // COMIENZA EL SAQUEO DE DATOS DE LA TABLA DE PUNTAJE
            $SpecifyItemsS = "tech_count,defs_count,fleet_count,build_count,build_points,tech_points,defs_points,"
                . "fleet_points,tech_rank,build_rank,defs_rank,fleet_rank,total_points,stat_type";

            $StatQuery = $GLOBALS['DATABASE']->getFirstRow(
                "SELECT " . $SpecifyItemsS . " FROM " . STATPOINTS
                . " WHERE `id_owner` = '" . $id_u . "' AND `stat_type` = '1';"
            );

            $count_tecno = pretty_number($StatQuery['tech_count']);
            $count_def = pretty_number($StatQuery['defs_count']);
            $count_fleet = pretty_number($StatQuery['fleet_count']);
            $count_builds = pretty_number($StatQuery['build_count']);

            $point_builds = pretty_number($StatQuery['build_points']);
            $point_tecno = pretty_number($StatQuery['tech_points']);
            $point_def = pretty_number($StatQuery['defs_points']);
            $point_fleet = pretty_number($StatQuery['fleet_points']);


            $ranking_tecno = $StatQuery['tech_rank'];
            $ranking_builds = $StatQuery['build_rank'];
            $ranking_def = $StatQuery['defs_rank'];
            $ranking_fleet = $StatQuery['fleet_rank'];

            $total_points = pretty_number($StatQuery['total_points']);



            // COMIENZA EL SAQUEO DE DATOS DE LA ALIANZA
            $AliID = $UserQuery['ally_id'];


            if ($alianza == 0 && $AliID == 0) {
                $alianza = $LNG['ac_no_ally'];
                $AllianceHave = sprintf(
                    "<span class=\"no_moon\"><img src=\"./styles/resource/images/admin/arrowright.png\""
                    . " width=\"16\" height=\"10\"/>%s&nbsp;%s</span>",
                    $LNG['ac_alliance'],
                    $LNG['ac_no_alliance']
                );
            } elseif ($alianza != null && $AliID != 0) {
                $AllianceHave = sprintf(
                    '<a href="#" onclick="$(\'#alianza\').slideToggle();return false" class="link"><img'
                    . ' src="./styles/resource/images/admin/arrowright.png" width="16" height="10">&nbsp;%s</a>',
                    $LNG['ac_alliance']
                );



                $SpecifyItemsA = "ally_owner,id,ally_tag,ally_name,ally_web,ally_description,ally_text,ally_request,"
                    . "ally_image,ally_members,ally_register_time";

                $AllianceQuery = $GLOBALS['DATABASE']->getFirstRow(
                    "SELECT " . $SpecifyItemsA . " FROM " . ALLIANCE . " WHERE `ally_name` = '" . $alianza . "';"
                );


                $alianza = $alianza;
                $id_ali = " (" . $LNG['ac_ali_idid'] . "&nbsp;" . $AliID . ")";
                $id_aliz = $AllianceQuery['id'];
                $tag = $AllianceQuery['ally_tag'];
                $ali_nom = $AllianceQuery['ally_name'];
                $ali_cant = $AllianceQuery['ally_members'];
                $ally_register_time = _date(
                    $LNG['php_tdformat'],
                    $AllianceQuery['ally_register_time'],
                    $USER['timezone']
                );
                $ali_lider = $AllianceQuery['ally_owner'];
                if ($AllianceQuery['ally_web'] != null) {
                    $ali_web = sprintf(
                        "<a href=\"%s\" target=_blank>%s</a>",
                        $AllianceQuery['ally_web'],
                        $AllianceQuery['ally_web']
                    );
                } else {
                    $ali_web = $LNG['ac_no_web'];
                }

                if ($AllianceQuery['ally_description'] != null) {
                    $ali_ext2 = nl2br($AllianceQuery['ally_description']);
                    $ali_ext = "<a href=\"#\" rel=\"toggle[externo]\">" . $LNG['ac_view_text_ext'] . "</a>";
                } else {
                    $ali_ext = $LNG['ac_no_text_ext'];
                }


                if ($AllianceQuery['ally_text'] != null) {
                    $ali_int2 = nl2br($AllianceQuery['ally_text']);
                    $ali_int = "<a href=\"#\" rel=\"toggle[interno]\">" . $LNG['ac_view_text_int'] . "</a>";
                } else {
                    $ali_int = $LNG['ac_no_text_int'];
                }


                if ($AllianceQuery['ally_request'] != null) {
                    $ali_sol2 = nl2br($AllianceQuery['ally_request']);
                    $ali_sol = "<a href=\"#\" rel=\"toggle[solicitud]\">" . $LNG['ac_view_text_sol'] . "</a>";
                } else {
                    $ali_sol = $LNG['ac_no_text_sol'];
                }


                if ($AllianceQuery['ally_image'] != null) {
                    $ali_logo2 = $AllianceQuery['ally_image'];
                    $ali_logo = "<a href=\"#\" rel=\"toggle[imagen]\">" . $LNG['ac_view_image2'] . "</a>";
                } else {
                    $ali_logo = $LNG['ac_no_img'];
                }

                $StatQueryAlly = $GLOBALS['DATABASE']->getFirstRow(
                    "SELECT " . $SpecifyItemsS . " FROM " . STATPOINTS
                    . " WHERE `id_owner` = '" . $ali_lider . "' AND `stat_type` = '2';"
                );

                $SearchLeader = $GLOBALS['DATABASE']->getFirstRow(
                    "SELECT `username` FROM " . USERS . " WHERE `id` = '" . $ali_lider . "';"
                );
                $ali_lider = $SearchLeader['username'];

                if (!empty($StatQueryAlly)) {
                    $count_tecno_ali = pretty_number($StatQueryAlly['tech_count']);
                    $count_def_ali = pretty_number($StatQueryAlly['defs_count']);
                    $count_fleet_ali = pretty_number($StatQueryAlly['fleet_count']);
                    $count_builds_ali = pretty_number($StatQueryAlly['build_count']);

                    $point_builds_ali = pretty_number($StatQueryAlly['build_points']);
                    $point_tecno_ali = pretty_number($StatQueryAlly['tech_points']);
                    $point_def_ali = pretty_number($StatQueryAlly['defs_points']);
                    $point_fleet_ali = pretty_number($StatQueryAlly['fleet_points']);


                    $ranking_tecno_ali = pretty_number($StatQueryAlly['tech_rank']);
                    $ranking_builds_ali = pretty_number($StatQueryAlly['build_rank']);
                    $ranking_def_ali = pretty_number($StatQueryAlly['defs_rank']);
                    $ranking_fleet_ali = pretty_number($StatQueryAlly['fleet_rank']);

                    $total_points_ali = pretty_number($StatQueryAlly['total_points']);
                }
            }
            $SpecifyItemsPQ = '';
            foreach (array_merge($reslist['fleet'], $reslist['build'], $reslist['defense']) as $ID) {
                $SpecifyItemsPQ .= $resource[$ID] . ",";
                $RES[$resource[$ID]] = "<tr><td width=\"150\">" . $LNG['tech'][$ID] . "</td>";
            }
            $names = "<tr><th class=\"center\" width=\"150\">&nbsp;</th>";

            // COMIENZA EL SAQUEO DE DATOS DE LOS PLANETAS
            $SpecifyItemsP = "planet_type,id,name,galaxy,`system`,planet,destruyed,diameter,field_current,field_max,"
                . "temp_min,temp_max,metal,crystal,deuterium,energy," . $SpecifyItemsPQ . "energy_used";

            $PlanetsQuery = $GLOBALS['DATABASE']->query(
                "SELECT " . $SpecifyItemsP . " FROM " . PLANETS . " WHERE `id_owner` = '" . $id_u . "';"
            );
            $planets_moons = '';
            $resources = '';
            $MoonZ = 0;
            while ($PlanetsWhile = $GLOBALS['DATABASE']->fetch_array($PlanetsQuery)) {
                if ($PlanetsWhile['planet_type'] == 3) {
                    $Planettt = sprintf(
                        "%s&nbsp;(%s)<br><font color=\"aqua\">[%d:%d:%d]</font>",
                        $PlanetsWhile['name'],
                        $LNG['ac_moon'],
                        $PlanetsWhile['galaxy'],
                        $PlanetsWhile['system'],
                        $PlanetsWhile['planet']
                    );

                    $MoonZ = 0;
                    $Moons = sprintf(
                        "%s&nbsp;(%s)<br><font color=aqua>[%d:%d:%d]</font>",
                        $PlanetsWhile['name'],
                        $LNG['ac_moon'],
                        $PlanetsWhile['galaxy'],
                        $PlanetsWhile['system'],
                        $PlanetsWhile['planet']
                    );
                    $MoonZ++;
                } else {
                    $Planettt = sprintf(
                        "%s<br><font color=aqua>[%d:%d:%d]</font>",
                        $PlanetsWhile['name'],
                        $PlanetsWhile['galaxy'],
                        $PlanetsWhile['system'],
                        $PlanetsWhile['planet']
                    );
                }

                if ($PlanetsWhile["destruyed"] == 0) {
                    $planets_moons .= "
					<tr>
						<td>" . $Planettt . "</td>
						<td>" . $PlanetsWhile['id'] . "</td>
						<td>" . pretty_number($PlanetsWhile['diameter']) . "</td>
						<td>" . pretty_number($PlanetsWhile['field_current']) . " / "
                        . pretty_number(CalculateMaxPlanetFields($PlanetsWhile)) . " ("
                        . pretty_number($PlanetsWhile['field_current']) . " / "
                        . pretty_number($PlanetsWhile['field_max']) . ")</td>
						<td>" . pretty_number($PlanetsWhile['temp_min']) . " / "
                        . pretty_number($PlanetsWhile['temp_max']) . "</td>"
                        . (allowedTo('ShowQuickEditorPage') ? "<td><a href=\"javascript:openEdit('"
                            . $PlanetsWhile['id'] . "', 'planet');\" border=\"0\"><img"
                            . " src=\"./styles/resource/images/admin/GO.png\" title=" . $LNG['se_search_edit']
                            . "></a></td>" : "") . "</tr>";


                    $SumOfEnergy = ($PlanetsWhile['energy'] + $PlanetsWhile['energy_used']);

                    if ($SumOfEnergy < 0) {
                        $Color = "<font color=#FF6600>" . shortly_number($SumOfEnergy) . "</font>";
                    } elseif ($SumOfEnergy > 0) {
                        $Color = "<font class=\"colorPositive\">" . shortly_number($SumOfEnergy) . "</font>";
                    } else {
                        $Color = shortly_number($SumOfEnergy);
                    }


                    $resources .= "
					<tr>
						<td>" . $Planettt . "</td>
						<td><a title=\"" . pretty_number($PlanetsWhile['metal']) . "\">"
                        . shortly_number($PlanetsWhile['metal']) . "</a></td>
						<td><a title=\"" . pretty_number($PlanetsWhile['crystal']) . "\">"
                        . shortly_number($PlanetsWhile['crystal']) . "</a></td>
						<td><a title=\"" . pretty_number($PlanetsWhile['deuterium']) . "\">"
                        . shortly_number($PlanetsWhile['deuterium']) . "</a></td>
						<td><a title=\"" . pretty_number($SumOfEnergy) . "\">" . $Color . "</a>/<a title=\""
                        . pretty_number($PlanetsWhile['energy']) . "\">" . shortly_number($PlanetsWhile['energy'])
                        . "</a></td>
					</tr>";
                    $names .= "<th class=\"center\" width=\"60\">" . $Planettt . "</th>";
                    foreach (array_merge($reslist['fleet'], $reslist['build'], $reslist['defense']) as $ID) {
                        $RES[$resource[$ID]] .= "<td width=\"60\"><a title=\""
                            . pretty_number($PlanetsWhile[$resource[$ID]]) . "\">"
                            . shortly_number($PlanetsWhile[$resource[$ID]]) . "</a></td>";
                    }

                    if ($MoonZ != 0) {
                        $MoonHave = '<a href="#" onclick="$(\'#especiales\').slideToggle();return false" class="link">'
                            . '<img src="./styles/resource/images/admin/arrowright.png" width="16" height="10"/> '
                            . $LNG['moon_build'] . "</a>";
                    } else {
                        $MoonHave = "<span class=\"no_moon\"><img src=\"./styles/resource/images/admin/arrowright.png\"'
                            . ' width=\"16\" height=\"10\"/>" . $LNG['moon_build'] . "&nbsp;"
                            . $LNG['ac_moons_no'] . "</span>";
                    }
                }

                $DestruyeD = 0;
                if ($PlanetsWhile["destruyed"] > 0) {
                    $destroyed .= sprintf(
                        "<tr><td>%s</td><td>%d</td><td>[%d:%d:%d]</td><td>%s</td></tr>",
                        $PlanetsWhile['name'],
                        $PlanetsWhile['id'],
                        $PlanetsWhile['galaxy'],
                        $PlanetsWhile['system'],
                        $PlanetsWhile['planet'],
                        date("d-m-Y   H:i:s", $PlanetsWhile['destruyed'])
                    );
                    $DestruyeD++;
                }
            }
            $names .= "</tr>";
            foreach (array_merge($reslist['fleet'], $reslist['build'], $reslist['defense']) as $ID) {
                $RES[$resource[$ID]] .= "</tr>";
            }
            $build = '';
            foreach ($reslist['build'] as $ID) {
                $build .= $RES[$resource[$ID]];
            }
            $fleet = '';
            foreach ($reslist['fleet'] as $ID) {
                $fleet .= $RES[$resource[$ID]];
            }
            $defense = '';
            foreach ($reslist['defense'] as $ID) {
                $defense .= $RES[$resource[$ID]];
            }

            // TODO: It was previously undefined, but assigned, remove it maybe?
            $input_id = null;
            if (!isset($destroyed)) {
                $destroyed = 0;
            }
            if (!isset($ali_lider)) {
                $ali_lider = 0;
            }
            if (!isset($point_tecno_ali)) {
                $point_tecno_ali = 0;
                $count_tecno_ali = 0;
                $ranking_tecno_ali = 0;
                $point_def_ali = 0;
                $count_def_ali = 0;
                $ranking_def_ali = 0;
                $point_fleet_ali = 0;
                $count_fleet_ali = 0;
                $ranking_fleet_ali = 0;
                $point_builds_ali = 0;
                $count_builds_ali = 0;
                $ranking_builds_ali = 0;
                $total_points_ali = 0;
                $id_aliz = 0;
                $tag = 0;
                $ali_nom = 0;
                $ali_ext = 0;
                $ali_ext2 = 0;
                $ali_int = 0;
                $ali_int2 = 0;
                $ali_sol2 = 0;
                $ali_sol = 0;
                $ali_logo = 0;
                $ali_logo2 = 0;
                $ali_web = 0;
                $ally_register_time = 0;
                $ali_cant = 0;
                $alianza = 0;
                $id_ali = 0;
                $mas = 0;
                $sus_time = 0;
                $sus_longer = 0;
                $sus_reason = '';
                $sus_author = 0;
            }
            $template->assign_vars([
                'DestruyeD'                     => $DestruyeD,
                'destroyed'                     => $destroyed,
                'resources'                     => $resources,
                'names'                         => $names,
                'build'                         => $build,
                'fleet'                         => $fleet,
                'defense'                       => $defense,
                'planets_moons'                 => $planets_moons,
                'ali_lider'                     => $ali_lider,
                'AllianceHave'                  => $AllianceHave,
                'point_tecno'                   => $point_tecno,
                'count_tecno'                   => $count_tecno,
                'ranking_tecno'                 => $ranking_tecno,
                'point_def'                     => $point_def,
                'count_def'                     => $count_def,
                'ranking_def'                   => $ranking_def,
                'point_fleet'                   => $point_fleet,
                'count_fleet'                   => $count_fleet,
                'ranking_fleet'                 => $ranking_fleet,
                'point_builds'                  => $point_builds,
                'count_builds'                  => $count_builds,
                'ranking_builds'                => $ranking_builds,
                'total_points'                  => $total_points,
                'point_tecno_ali'               => $point_tecno_ali,
                'count_tecno_ali'               => $count_tecno_ali,
                'ranking_tecno_ali'             => $ranking_tecno_ali,
                'point_def_ali'                 => $point_def_ali,
                'count_def_ali'                 => $count_def_ali,
                'ranking_def_ali'               => $ranking_def_ali,
                'point_fleet_ali'               => $point_fleet_ali,
                'count_fleet_ali'               => $count_fleet_ali,
                'ranking_fleet_ali'             => $ranking_fleet_ali,
                'point_builds_ali'              => $point_builds_ali,
                'count_builds_ali'              => $count_builds_ali,
                'ranking_builds_ali'            => $ranking_builds_ali,
                'total_points_ali'              => $total_points_ali,
                'input_id'                      => $input_id,
                'id_aliz'                       => $id_aliz,
                'tag'                           => $tag,
                'ali_nom'                       => $ali_nom,
                'ali_ext'                       => $ali_ext,
                'ali_ext'                       => $ali_ext2,
                'ali_int'                       => $ali_int,
                'ali_int'                       => $ali_int2,
                'ali_sol2'                      => $ali_sol2,
                'ali_sol'                       => $ali_sol,
                'ali_logo'                      => $ali_logo,
                'ali_logo2'                     => $ali_logo2,
                'ali_web'                       => $ali_web,
                'ally_register_time'            => $ally_register_time,
                'ali_cant'                      => $ali_cant,
                'alianza'                       => $alianza,
                'input_id'                      => $input_id,
                'id'                            => $id,
                'nombre'                        => $nombre,
                'nivel'                         => $nivel,
                'vacas'                         => $vacas,
                'suspen'                        => $suspen,
                'mas'                           => $mas,
                'id_ali'                        => $id_ali,
                'ip'                            => $ip,
                'ip2'                           => $ip2,
                'ipcheck'                       => $LNG['one_is_yes_1'],
                'reg_time'                      => $reg_time,
                'onlinetime'                    => $onlinetime,
                'id_p'                          => $id_p,
                'g'                             => $g,
                's'                             => $s,
                'p'                             => $p,
                'info'                          => $info,
                'email_1'                       => $email_1,
                'email_2'                       => $email_2,
                'sus_time'                      => $sus_time,
                'sus_longer'                    => $sus_longer,
                'sus_reason'                    => $sus_reason,
                'sus_author'                    => $sus_author,
                'technology_table_rows'         => $technologyTableRows,
                'canedit'                       => allowedTo('ShowQuickEditorPage'),
                'buildings_title'               => $LNG['buildings_title'],
                'researchs_title	'           => $LNG['researchs_title'],
                'ships_title'                   => $LNG['ships_title'],
                'defenses_title'                => $LNG['defenses_title'],
                'ac_recent_destroyed_planets'   => $LNG['ac_recent_destroyed_planets'],
                'ac_isnodestruyed'              => $LNG['ac_isnodestruyed'],
                'ac_note_k'                     => $LNG['ac_note_k'],
                'ac_leyend'                     => $LNG['ac_leyend'],
                'ac_account_data'               => $LNG['ac_account_data'],
                'ac_name'                       => $LNG['ac_name'],
                'ac_mail'                       => $LNG['ac_mail'],
                'ac_perm_mail'                  => $LNG['ac_perm_mail'],
                'ac_auth_level'                 => $LNG['ac_auth_level'],
                'ac_on_vacation'                => $LNG['ac_on_vacation'],
                'ac_banned'                     => $LNG['ac_banned'],
                'ac_alliance'                   => $LNG['ac_alliance'],
                'ac_reg_ip'                     => $LNG['ac_reg_ip'],
                'ac_last_ip'                    => $LNG['ac_last_ip'],
                'ac_checkip_title'              => $LNG['ac_checkip_title'],
                'ac_register_time'              => $LNG['ac_register_time'],
                'ac_act_time'                   => $LNG['ac_act_time'],
                'ac_home_planet_id'             => $LNG['ac_home_planet_id'],
                'ac_home_planet_coord'          => $LNG['ac_home_planet_coord'],
                'ac_user_system'                => $LNG['ac_user_system'],
                'ac_ranking'                    => $LNG['ac_ranking'],
                'ac_see_ranking'                => $LNG['ac_see_ranking'],
                'ac_user_ranking'               => $LNG['ac_user_ranking'],
                'ac_points_count'               => $LNG['ac_points_count'],
                'ac_total_points'               => $LNG['ac_total_points'],
                'ac_suspended_title'            => $LNG['ac_suspended_title'],
                'ac_suspended_time'             => $LNG['ac_suspended_time'],
                'ac_suspended_longer'           => $LNG['ac_suspended_longer'],
                'ac_suspended_reason'           => $LNG['ac_suspended_reason'],
                'ac_suspended_autor'            => $LNG['ac_suspended_autor'],
                'ac_info_ally'                  => $LNG['ac_info_ally'],
                'ac_leader'                     => $LNG['ac_leader'],
                'ac_tag'                        => $LNG['ac_tag'],
                'ac_name_ali'                   => $LNG['ac_name_ali'],
                'ac_ext_text		'           => $LNG['ac_ext_text'],
                'ac_int_text'                   => $LNG['ac_int_text'],
                'ac_sol_text'                   => $LNG['ac_sol_text'],
                'ac_image'                      => $LNG['ac_image'],
                'ac_ally_web'                   => $LNG['ac_ally_web'],
                'ac_total_members'              => $LNG['ac_total_members'],
                'ac_view_image'                 => $LNG['ac_view_image'],
                'ac_urlnow'                     => $LNG['ac_urlnow'],
                'ac_ally_ranking'               => $LNG['ac_ally_ranking'],
                'ac_id_names_coords'            => $LNG['ac_id_names_coords'],
                'ac_diameter'                   => $LNG['ac_diameter'],
                'ac_fields'                     => $LNG['ac_fields'],
                'ac_temperature'                => $LNG['ac_temperature'],
                'se_search_edit'                => $LNG['se_search_edit'],
                'resources_title'               => $LNG['resources_title'],
                'Metal'                         => $LNG['tech'][901],
                'Crystal'                       => $LNG['tech'][902],
                'Deuterium'                     => $LNG['tech'][903],
                'Energy'                        => $LNG['tech'][911],
                'ac_research'                   => $LNG['ac_research'],
                'researchs_title'               => $LNG['researchs_title'],
                'ac_coords'                     => $LNG['ac_coords'],
                'ac_time_destruyed'             => $LNG['ac_time_destruyed'],
                'ac_ext_text'                   => '',
                'ac_register_ally_time'         => 0,
                'ac_ali_logo_11'                => '',
                'ac_ali_text_11'                => '',
                'ali_ext2'                      => '',
                'ac_ali_text_22'                => '',
                'ali_int2'                      => '',
                'ac_ali_text_33'                => '',
            ]);
            $template->show('AccountDataPageDetail.tpl');
        }
        exit;
    }
    $Userlist = "";
    $UserWhileLogin = $GLOBALS['DATABASE']->query(
        "SELECT `id`, `username`, `authlevel` FROM " . USERS . " WHERE `authlevel` <= '" . $USER['authlevel']
        . "' AND `universe` = '" . Universe::getEmulated() . "' ORDER BY `username` ASC;"
    );
    while ($UserList = $GLOBALS['DATABASE']->fetch_array($UserWhileLogin)) {
        $Userlist .= sprintf(
            "<option value=\"%d\">%s&nbsp;&nbsp;(%s)</option>",
            $UserList['id'],
            $UserList['username'],
            $LNG['rank_' . $UserList['authlevel']]
        );
    }

    $template->loadscript('filterlist.js');
    $template->assign_vars(array(
        'Userlist'          => $Userlist,
        'ac_enter_user_id'  => $LNG['ac_enter_user_id'],
        'bo_select_title'   => $LNG['bo_select_title'],
        'button_filter'     => $LNG['button_filter'],
        'button_deselect'   => $LNG['button_deselect'],
        'ac_select_id_num'  => $LNG['ac_select_id_num'],
        'button_submit'     => $LNG['button_submit'],
    ));
    $template->show('AccountDataPageIntro.tpl');
}
