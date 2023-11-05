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

function ShowQuickEditorPage()
{
    $USER =& Singleton()->USER;
    $LNG =& Singleton()->LNG;
    $reslist =& Singleton()->reslist;
    $resource =& Singleton()->resource;
    $action = HTTP::_GP('action', '');
    $edit = HTTP::_GP('edit', '');
    $id = HTTP::_GP('id', 0);

    $db = Database::get();

    switch ($edit) {
        case 'planet':
            $DataIDs = array_merge($reslist['fleet'], $reslist['build'], $reslist['defense']);
            $SpecifyItemsPQ = "";

            foreach ($DataIDs as $ID) {
                $SpecifyItemsPQ .= "`" . $resource[$ID] . "`,";
            }
            $sql = "SELECT " . $SpecifyItemsPQ . " `name`, `id_owner`, `planet_type`, `galaxy`, `system`, `planet`, `destruyed`,
                `diameter`, `field_current`, `field_max`, `temp_min`, `temp_max`, `metal`, `crystal`, `deuterium`
                FROM %%PLANETS%% WHERE `id` = :planetID;";
            $PlanetData = $db->selectSingle($sql, [
                ':planetID' => $id,
            ]);

            if ($action == 'send') {
                $sql = "UPDATE %%PLANETS%% SET ";
                $Fields = $PlanetData['field_current'];
                foreach ($DataIDs as $ID) {
                    $level = min(
                        max(0, round(HTTP::_GP($resource[$ID], 0.0))),
                        (in_array($ID, $reslist['build']) ? 255 : 18446744073709551615)
                    );

                    if (in_array($ID, $reslist['allow'][$PlanetData['planet_type']])) {
                        $Fields += $level - $PlanetData[$resource[$ID]];
                    }

                    $sql .= "`" . $resource[$ID] . "` = " . $level . ", ";
                }

                $sql .= "`metal` = :metal, ";
                $sql .= "`crystal` = :crystal, ";
                $sql .= "`deuterium` = :deuterium, ";
                $sql .= "`field_current` = :fields, ";
                $sql .= "`field_max` = :field_max, ";
                $sql .= "`name` = :name, ";
                $sql .= "`eco_hash` = '' ";
                $sql .= "WHERE `id` = :planetID AND `universe` = :universe;";

                $db->update($sql, [
                    ':metal' => max(0, round(HTTP::_GP('metal', 0.0))),
                    ':crystal' => max(0, round(HTTP::_GP('crystal', 0.0))),
                    ':deuterium' => max(0, round(HTTP::_GP('deuterium', 0.0))),
                    ':fields' => $Fields,
                    ':field_max' => HTTP::_GP('field_max', 0),
                    ':name' => HTTP::_GP('name', '', UTF8_SUPPORT),
                    ':planetID' => $id,
                    ':universe' => Universe::getEmulated(),
                ]);

                $old = [];
                $new = [];
                foreach (array_merge($DataIDs, $reslist['resstype'][1]) as $IDs) {
                    $old[$IDs] = $PlanetData[$resource[$IDs]];
                    $new[$IDs] = max(0, round(HTTP::_GP($resource[$IDs], 0.0)));
                }
                $old['field_max'] = $PlanetData['field_max'];
                $new['field_max'] = HTTP::_GP('field_max', 0);
                $LOG = new Log(2);
                $LOG->target = $id;
                $LOG->old = $old;
                $LOG->new = $new;
                $LOG->save();

                exit(
                    sprintf(
                        $LNG['qe_edit_planet_sucess'],
                        $PlanetData['name'],
                        $PlanetData['galaxy'],
                        $PlanetData['system'],
                        $PlanetData['planet']
                    )
                );
            }

            $sql = "SELECT `username` FROM %%USERS%%
                WHERE `id` = :userID AND universe = :universe;";
            $UserInfo = $db->selectSingle($sql, [
                ':userID' => $PlanetData['id_owner'],
                ':universe' => Universe::getEmulated(),
            ]);

            $build = $defense = $fleet = [];

            foreach ($reslist['allow'][$PlanetData['planet_type']] as $ID) {
                $build[] = [
                    'type'  => $resource[$ID],
                    'name'  => $LNG['tech'][$ID],
                    'count' => pretty_number($PlanetData[$resource[$ID]]),
                    'input' => $PlanetData[$resource[$ID]],
                ];
            }

            foreach ($reslist['fleet'] as $ID) {
                $fleet[] = [
                    'type'  => $resource[$ID],
                    'name'  => $LNG['tech'][$ID],
                    'count' => pretty_number($PlanetData[$resource[$ID]]),
                    'input' => $PlanetData[$resource[$ID]],
                ];
            }

            foreach ($reslist['defense'] as $ID) {
                $defense[] = [
                    'type'  => $resource[$ID],
                    'name'  => $LNG['tech'][$ID],
                    'count' => pretty_number($PlanetData[$resource[$ID]]),
                    'input' => $PlanetData[$resource[$ID]],
                ];
            }

            $template = new template();
            $template->assign_vars([
                'build'         => $build,
                'fleet'         => $fleet,
                'defense'       => $defense,
                'id'            => $id,
                'ownerid'       => $PlanetData['id_owner'],
                'ownername'     => $UserInfo['username'],
                'name'          => $PlanetData['name'],
                'galaxy'        => $PlanetData['galaxy'],
                'system'        => $PlanetData['system'],
                'planet'        => $PlanetData['planet'],
                'field_min'     => $PlanetData['field_current'],
                'field_max'     => $PlanetData['field_max'],
                'temp_min'      => $PlanetData['temp_min'],
                'temp_max'      => $PlanetData['temp_max'],
                'metal'         => floatToString($PlanetData['metal']),
                'crystal'       => floatToString($PlanetData['crystal']),
                'deuterium'     => floatToString($PlanetData['deuterium']),
                'metal_c'       => pretty_number($PlanetData['metal']),
                'crystal_c'     => pretty_number($PlanetData['crystal']),
                'deuterium_c'   => pretty_number($PlanetData['deuterium']),
                'signalColors'  => $USER['signalColors']
            ]);
            $template->show('QuickEditorPlanet.tpl');
            break;
        case 'player':
            $DataIDs = $reslist['tech'];
            $SpecifyItemsPQ = "";

            foreach ($DataIDs as $ID) {
                $SpecifyItemsPQ .= "`" . $resource[$ID] . "`,";
            }
            $sql = "SELECT " . $SpecifyItemsPQ . " `username`, `authlevel`, `galaxy`, `system`, `planet`, `id_planet`, `authattack`, `authlevel`
                FROM %%USERS%%
                WHERE `id` = :userID;";
            $UserData = $db->selectSingle($sql, [':userID' => $id]);
            $ChangePW = $USER['id'] == ROOT_USER || ($id != ROOT_USER && $USER['authlevel'] > $UserData['authlevel']);

            if ($action == 'send') {
                $sql = "UPDATE %%USERS%% SET ";
                foreach ($DataIDs as $ID) {
                    $sql .= "`" . $resource[$ID] . "` = " . min(abs(HTTP::_GP($resource[$ID], 0)), 255) . ", ";
                }
                if (!empty($_POST['password']) && $ChangePW) {
                    $sql .= "`password` = '" . PlayerUtil::cryptPassword(HTTP::_GP('password', '', true)) . "', ";
                }

                $sql .= "`username` = :username, ";
                $sql .= "`authattack` = :authattack ";
                $sql .= "WHERE `id` = :userID AND `universe` = :universe;";
                $db->update($sql, [
                    ':username' => HTTP::_GP('name', '', UTF8_SUPPORT),
                    ':authattack' => ($UserData['authlevel'] != AUTH_USR && HTTP::_GP('authattack', '') == 'on' ? $UserData['authlevel'] : 0),
                    ':userID' => $id,
                    ':universe' => Universe::getEmulated(),
                ]);

                $old = [];
                $new = [];
                foreach ($DataIDs as $IDs) {
                    $old[$IDs] = $UserData[$resource[$IDs]];
                    $new[$IDs] = abs(HTTP::_GP($resource[$IDs], 0));
                }
                $old['username'] = $UserData['username'];
                $new['username'] = HTTP::_GP('name', '', UTF8_SUPPORT);
                $old['authattack'] = $UserData['authattack'];
                $new['authattack'] = ($UserData['authlevel'] != AUTH_USR
                    && HTTP::_GP('authattack', '') == 'on' ? $UserData['authlevel'] : 0);

                $LOG = new Log(1);
                $LOG->target = $id;
                $LOG->old = $old;
                $LOG->new = $new;
                $LOG->save();

                exit(sprintf($LNG['qe_edit_player_sucess'], $UserData['username'], $id));
            }
            $sql = "SELECT `name` FROM %%PLANETS%% WHERE `id` = :planetID AND `universe` = :universe;";
            $PlanetInfo = $db->selectSingle($sql, [
                ':planetID' => $UserData['id_planet'],
                ':universe' => Universe::getEmulated(),
            ]);

            $tech = [];

            foreach ($reslist['tech'] as $ID) {
                $tech[] = [
                    'type'  => $resource[$ID],
                    'name'  => $LNG['tech'][$ID],
                    'count' => pretty_number($UserData[$resource[$ID]]),
                    'input' => $UserData[$resource[$ID]]
                ];
            }

            $template = new template();
            $template->assign_vars([
                'tech'          => $tech,
                'id'            => $id,
                'planetid'      => $UserData['id_planet'],
                'planetname'    => $PlanetInfo['name'],
                'name'          => $UserData['username'],
                'galaxy'        => $UserData['galaxy'],
                'system'        => $UserData['system'],
                'planet'        => $UserData['planet'],
                'authlevel'     => $UserData['authlevel'],
                'qe_change'     => '',
                'authattack'    => $UserData['authattack'],
                'ChangePW'      => $ChangePW,
                'yesorno'       => [1 => $LNG['one_is_yes_1'], 0 => $LNG['one_is_yes_0']],
                'signalColors'  => $USER['signalColors']
            ]);
            $template->show('QuickEditorUser.tpl');
            break;
    }
}
