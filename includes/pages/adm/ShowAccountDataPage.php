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
    $db = Database::get();
    $template = new template();
    $template->assign_vars([
        'signalColors'  => $USER['signalColors']
    ]);
    $id_u = HTTP::_GP('id_u', 0);
    if (!empty($id_u)) {
        $sql = "SELECT `id`, `authlevel` FROM %%USERS%% WHERE `id` = :userID;";
        $OnlyQueryLogin = $db->selectSingle($sql, [
            ':userID' => $id_u,
        ]);
        if (!isset($OnlyQueryLogin)) {
            $template->message($LNG['ac_username_doesnt'], '?page=accoutdata');
        } else {
            // User data
            $sql = "SELECT u.`id`, u.`username`, u.`email`, u.`email_2`, u.`authlevel`, u.`id_planet`, u.`galaxy`,
                u.`system`, u.`planet`, u.`user_lastip`, u.`ip_at_reg`, u.`register_time`, u.`onlinetime`,
                u.`urlaubs_modus`, u.`urlaubs_until`, u.`ally_id`, u.`ally_register_time`,
                u.`ally_rank_id`, u.`bana`, u.`banaday`, ";
            foreach ($reslist['tech'] as $ID) {
                $sql .= " u.`" . $resource[$ID] . "`, ";
            }
            $sql = rtrim($sql, ', ') . " FROM %%USERS%% as u
                WHERE u.`id` = :userID;";
            $UserQuery = $db->selectSingle($sql, [
                ':userID' => $id_u,
            ]);

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
            
            $mas = 0;
            $sus_time = 0;
            $sus_longer = 0;
            $sus_reason = '';
            $sus_author = 0;
            if ($UserQuery['bana'] != 0) {
                $mas = '<a ref="#" onclick="$(\'#banned\').slideToggle();return false"> ' . $LNG['ac_more'] . '</a>';

                $sql = "SELECT `theme`, `time`, `longer`, `author` FROM %%BANNED%%
                    WHERE `who` = :username;";
                $BannedQuery = $db->selectSingle($sql, [
                    ':username' => $nombre,
                ]);

                $sus_longer = _date($LNG['php_tdformat'], $BannedQuery['longer'], $USER['timezone']);
                $sus_time = _date($LNG['php_tdformat'], $BannedQuery['time'], $USER['timezone']);
                $sus_reason = $BannedQuery['theme'];
                $sus_author = $BannedQuery['author'];
            }

            // Stats data
            $sql = "SELECT `tech_count`, `defs_count`, `fleet_count`, `build_count`, `build_points`, `tech_points`,
                `defs_points`, `fleet_points`, `tech_rank`, `build_rank`, `defs_rank`, `fleet_rank`, `total_points`,
                `stat_type`
                FROM %%STATPOINTS%% WHERE `id_owner` = :userID AND `stat_type` = 1";
            $StatQuery = $db->selectSingle($sql, [
                ':userID' => $id_u,
            ]);

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

            // Alliance data
            $allianceData = [];
            $allianceStats = [];

            $allyID = $UserQuery['ally_id'];

            if ($allyID != 0) {
                $sql = "SELECT a.`id`, a.`ally_owner`, u.`username`, a.`ally_tag`, a.`ally_name`, a.`ally_web`, a.`ally_description`, a.`ally_text`,
                    a.`ally_request`, a.`ally_image`, a.`ally_members`, a.`ally_register_time`
                    FROM %%ALLIANCE%% a
                    LEFT JOIN %%USERS%% u ON u.`id` = a.`ally_owner`
                    WHERE a.`id` = :allyID;";
                $AllianceQuery = $db->selectSingle($sql, [
                    ':allyID' => $allyID,
                ]);
                
                $allianceData['id'] = $UserQuery['ally_id'];
                $allianceData['leader'] = $AllianceQuery['username'] . ' (' . $AllianceQuery['ally_owner'] . ')';
                $allianceData['tag'] = $AllianceQuery['ally_tag'];
                $allianceData['name'] = $AllianceQuery['ally_name'];
                $allianceData['externalText'] = empty($AllianceQuery['ally_description']) ? false : nl2br($AllianceQuery['ally_description']);
                $allianceData['internalText'] = empty($AllianceQuery['ally_text']) ? false : nl2br($AllianceQuery['ally_text']);
                $allianceData['applyText'] = empty($AllianceQuery['ally_request']) ? false : nl2br($AllianceQuery['ally_request']);
                $allianceData['logo'] = empty($AllianceQuery['ally_image']) ? false : $AllianceQuery['ally_image'];
                $allianceData['website'] = empty($AllianceQuery['ally_web']) ? false : $AllianceQuery['ally_web'];
                $allianceData['foundationDate'] = _date($LNG['php_tdformat'], $AllianceQuery['ally_register_time'], $USER['timezone']);
                $allianceData['memberAmount'] = $AllianceQuery['ally_members'];

                $sql = "SELECT `tech_count`, `defs_count`, `fleet_count`, `build_count`, `build_points`, `tech_points`,
                    `defs_points`, `fleet_points`, `tech_rank`, `build_rank`, `defs_rank`, `fleet_rank`, `total_points`,
                    `stat_type`
                    FROM %%STATPOINTS%% WHERE `id_owner` = :allyID AND `stat_type` = 2";
                $allianceStats = $db->selectSingle($sql, [
                    ':allyID' => $allyID,
                ]);
            }
            $planetNames = "<tr><th class=\"center\" width=\"150\">&nbsp;</th>";

            // Planet data
            $sql = "SELECT `planet_type`, `id`, `name`, `galaxy`, `system`, `planet`, `destruyed`, `diameter`, `field_current`, `field_max`,
                `temp_min`, `temp_max`, `metal`, `crystal`, `deuterium`, `energy`, `energy_used`, ";
            foreach (array_merge($reslist['fleet'], $reslist['build'], $reslist['defense']) as $ID) {
                $sql .= "`" . $resource[$ID] . "`, ";
                $RES[$resource[$ID]] = "<tr><td width=\"150\">" . $LNG['tech'][$ID] . "</td>";
            }
            $sql = rtrim($sql, ', ') . "FROM %%PLANETS%% WHERE `id_owner` = :userID;";
            $PlanetsQuery = $db->select($sql, [
                ':userID' => $id_u,
            ]);

            $planetsMoonsTableRows = '';
            $resourcesTableRows = '';
            $destroyedPlanetsTableRows = 0;
            foreach ($PlanetsQuery as $planetData) {
                if ($planetData['planet_type'] == 3) {
                    $planetName = sprintf(
                        "%s&nbsp;(%s)<br><font color=\"aqua\">[%d:%d:%d]</font>",
                        $planetData['name'],
                        $LNG['ac_moon'],
                        $planetData['galaxy'],
                        $planetData['system'],
                        $planetData['planet']
                    );
                } else {
                    $planetName = sprintf(
                        "%s<br><font color=aqua>[%d:%d:%d]</font>",
                        $planetData['name'],
                        $planetData['galaxy'],
                        $planetData['system'],
                        $planetData['planet']
                    );
                }

                if ($planetData["destruyed"] == 0) {
                    $planetsMoonsTableRows .= "
					<tr>
						<td>" . $planetName . "</td>
						<td>" . $planetData['id'] . "</td>
						<td>" . pretty_number($planetData['diameter']) . "</td>
						<td>" . pretty_number($planetData['field_current']) . " / "
                        . pretty_number(CalculateMaxPlanetFields($planetData)) . " ("
                        . pretty_number($planetData['field_current']) . " / "
                        . pretty_number($planetData['field_max']) . ")</td>
						<td>" . pretty_number($planetData['temp_min']) . " / "
                        . pretty_number($planetData['temp_max']) . "</td>"
                        . (allowedTo('ShowQuickEditorPage') ? "<td><a href=\"javascript:openEdit('"
                            . $planetData['id'] . "', 'planet');\" border=\"0\"><img"
                            . " src=\"./styles/resource/images/admin/GO.png\" title=" . $LNG['se_search_edit']
                            . "></a></td>" : "") . "</tr>";


                    $SumOfEnergy = ($planetData['energy'] + $planetData['energy_used']);

                    if ($SumOfEnergy < 0) {
                        $Color = "<font color=#FF6600>" . shortly_number($SumOfEnergy) . "</font>";
                    } elseif ($SumOfEnergy > 0) {
                        $Color = "<font class=\"colorPositive\">" . shortly_number($SumOfEnergy) . "</font>";
                    } else {
                        $Color = shortly_number($SumOfEnergy);
                    }


                    $resourcesTableRows .= "
					<tr>
						<td>" . $planetName . "</td>
						<td><a title=\"" . pretty_number($planetData['metal']) . "\">"
                        . shortly_number($planetData['metal']) . "</a></td>
						<td><a title=\"" . pretty_number($planetData['crystal']) . "\">"
                        . shortly_number($planetData['crystal']) . "</a></td>
						<td><a title=\"" . pretty_number($planetData['deuterium']) . "\">"
                        . shortly_number($planetData['deuterium']) . "</a></td>
						<td><a title=\"" . pretty_number($SumOfEnergy) . "\">" . $Color . "</a>/<a title=\""
                        . pretty_number($planetData['energy']) . "\">" . shortly_number($planetData['energy'])
                        . "</a></td>
					</tr>";
                    $planetNames .= "<th class=\"center\" width=\"60\">" . $planetName . "</th>";
                    foreach (array_merge($reslist['fleet'], $reslist['build'], $reslist['defense']) as $ID) {
                        $RES[$resource[$ID]] .= "<td width=\"60\"><a title=\""
                            . pretty_number($planetData[$resource[$ID]]) . "\">"
                            . shortly_number($planetData[$resource[$ID]]) . "</a></td>";
                    }
                }

                $hasDestroyedPlanets = false;
                if ($planetData["destruyed"] > 0) {
                    $destroyedPlanetsTableRows .= sprintf(
                        "<tr><td>%s</td><td>%d</td><td>[%d:%d:%d]</td><td>%s</td></tr>",
                        $planetData['name'],
                        $planetData['id'],
                        $planetData['galaxy'],
                        $planetData['system'],
                        $planetData['planet'],
                        date("d-m-Y   H:i:s", $planetData['destruyed'])
                    );
                    $hasDestroyedPlanets = true;
                }
            }
            $planetNames .= "</tr>";
            foreach (array_merge($reslist['fleet'], $reslist['build'], $reslist['defense']) as $ID) {
                $RES[$resource[$ID]] .= "</tr>";
            }
            $buildingsTableRows = '';
            foreach ($reslist['build'] as $ID) {
                $buildingsTableRows .= $RES[$resource[$ID]];
            }
            $fleetTableRows = '';
            foreach ($reslist['fleet'] as $ID) {
                $fleetTableRows .= $RES[$resource[$ID]];
            }
            $defenseTableRows = '';
            foreach ($reslist['defense'] as $ID) {
                $defenseTableRows .= $RES[$resource[$ID]];
            }

            $template->assign_vars([
                'planetsMoonsTableRows'         => $planetsMoonsTableRows,
                'planetNames'                   => $planetNames,
                'resourcesTableRows'            => $resourcesTableRows,
                'buildingsTableRows'            => $buildingsTableRows,
                'fleetTableRows'                => $fleetTableRows,
                'defenseTableRows'              => $defenseTableRows,
                'technologyTableRows'           => $technologyTableRows,
                'hasDestroyedPlanets'           => $hasDestroyedPlanets,
                'destroyedPlanetsTableRows'     => $destroyedPlanetsTableRows,
                'allianceData'                  => $allianceData,
                'allianceStats'                 => $allianceStats,

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
                'id'                            => $id,
                'nombre'                        => $nombre,
                'nivel'                         => $nivel,
                'vacas'                         => $vacas,
                'suspen'                        => $suspen,
                'mas'                           => $mas,
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
                'canedit'                       => allowedTo('ShowQuickEditorPage'),
                'Metal'                         => $LNG['tech'][901],
                'Crystal'                       => $LNG['tech'][902],
                'Deuterium'                     => $LNG['tech'][903],
                'Energy'                        => $LNG['tech'][911],
            ]);
            $template->show('AccountDataPageDetail.tpl');
        }
        exit;
    }
    $sql = "SELECT `id`, `username`, `authlevel` FROM %%USERS%%
        WHERE `authlevel` <= :userLevel AND `universe` = :universe
        ORDER BY `username` ASC;";
    $UserQuerry = $db->select($sql, [
        ':userLevel' => $USER['authlevel'],
        ':universe' => Universe::getEmulated(),
    ]);

    $Userlist = "";
    foreach ($UserQuerry as $User) {
        $Userlist .= sprintf(
            "<option value=\"%d\">%s&nbsp;&nbsp;(%s)</option>",
            $User['id'],
            $User['username'],
            $LNG['rank_' . $User['authlevel']]
        );
    }

    $template->loadscript('filterlist.js');
    $template->assign_vars([
        'Userlist'          => $Userlist,
        'ac_enter_user_id'  => $LNG['ac_enter_user_id'],
        'bo_select_title'   => $LNG['bo_select_title'],
        'button_filter'     => $LNG['button_filter'],
        'button_deselect'   => $LNG['button_deselect'],
        'ac_select_id_num'  => $LNG['ac_select_id_num'],
        'button_submit'     => $LNG['button_submit'],
    ]);
    $template->show('AccountDataPageIntro.tpl');
}
