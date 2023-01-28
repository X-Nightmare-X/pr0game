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

    switch ($edit) {
        case 'planet':
            $DataIDs = array_merge($reslist['fleet'], $reslist['build'], $reslist['defense']);
            $SpecifyItemsPQ = "";

            foreach ($DataIDs as $ID) {
                $SpecifyItemsPQ .= "`" . $resource[$ID] . "`,";
            }
            $PlanetData = $GLOBALS['DATABASE']->getFirstRow(
                "SELECT " . $SpecifyItemsPQ . " `name`, `id_owner`, `planet_type`, `galaxy`, `system`, `planet`,"
                . " `destruyed`, `diameter`, `field_current`, `field_max`, `temp_min`, `temp_max`, `metal`, `crystal`,"
                . " `deuterium` FROM " . PLANETS . " WHERE `id` = '" . $id . "';"
            );

            if ($action == 'send') {
                $SQL = "UPDATE " . PLANETS . " SET ";
                $Fields = $PlanetData['field_current'];
                foreach ($DataIDs as $ID) {
                    $level = min(
                        max(0, round(HTTP::_GP($resource[$ID], 0.0))),
                        (in_array($ID, $reslist['build']) ? 255 : 18446744073709551615)
                    );

                    if (in_array($ID, $reslist['allow'][$PlanetData['planet_type']])) {
                        $Fields += $level - $PlanetData[$resource[$ID]];
                    }

                    $SQL .= "`" . $resource[$ID] . "` = " . $level . ", ";
                }

                $SQL .= "`metal` = " . max(0, round(HTTP::_GP('metal', 0.0))) . ", ";
                $SQL .= "`crystal` = " . max(0, round(HTTP::_GP('crystal', 0.0))) . ", ";
                $SQL .= "`deuterium` = " . max(0, round(HTTP::_GP('deuterium', 0.0))) . ", ";
                $SQL .= "`field_current` = '" . $Fields . "', ";
                $SQL .= "`field_max` = '" . HTTP::_GP('field_max', 0) . "', ";
                $SQL .= "`name` = '" . $GLOBALS['DATABASE']->sql_escape(HTTP::_GP('name', '', UTF8_SUPPORT)) . "', ";
                $SQL .= "`eco_hash` = '' ";
                $SQL .= "WHERE `id` = '" . $id . "' AND `universe` = '" . Universe::getEmulated() . "';";

                $GLOBALS['DATABASE']->query($SQL);

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
            $UserInfo = $GLOBALS['DATABASE']->getFirstRow(
                "SELECT `username` FROM " . USERS . " WHERE `id` = '" . $PlanetData['id_owner'] . "' AND `universe` = '"
                . Universe::getEmulated() . "';"
            );

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
            $UserData = $GLOBALS['DATABASE']->getFirstRow(
                "SELECT " . $SpecifyItemsPQ . " `username`, `authlevel`, `galaxy`, `system`, `planet`, `id_planet`,"
                . " `authattack`, `authlevel` FROM " . USERS . " WHERE `id` = '" . $id . "';"
            );
            $ChangePW = $USER['id'] == ROOT_USER || ($id != ROOT_USER && $USER['authlevel'] > $UserData['authlevel']);

            if ($action == 'send') {
                $SQL = "UPDATE " . USERS . " SET ";
                foreach ($DataIDs as $ID) {
                    $SQL .= "`" . $resource[$ID] . "` = " . min(abs(HTTP::_GP($resource[$ID], 0)), 255) . ", ";
                }
                if (!empty($_POST['password']) && $ChangePW) {
                    $SQL .= "`password` = '" . PlayerUtil::cryptPassword(HTTP::_GP('password', '', true)) . "', ";
                }

                $SQL .= "`username` = '" . $GLOBALS['DATABASE']->sql_escape(HTTP::_GP('name', '', UTF8_SUPPORT))
                    . "', ";
                $SQL .= "`authattack` = '"
                    . ($UserData['authlevel'] != AUTH_USR
                    && HTTP::_GP('authattack', '') == 'on' ? $UserData['authlevel'] : 0) . "' ";
                $SQL .= "WHERE `id` = '" . $id . "' AND `universe` = '" . Universe::getEmulated() . "';";
                $GLOBALS['DATABASE']->query($SQL);

                $old = [];
                $new = [];
                $multi =  HTTP::_GP('multi', 0);
                foreach ($DataIDs as $IDs) {
                    $old[$IDs] = $UserData[$resource[$IDs]];
                    $new[$IDs] = abs(HTTP::_GP($resource[$IDs], 0));
                }
                $old['username'] = $UserData['username'];
                $new['username'] = $GLOBALS['DATABASE']->sql_escape(HTTP::_GP('name', '', UTF8_SUPPORT));
                $old['authattack'] = $UserData['authattack'];
                $new['authattack'] = ($UserData['authlevel'] != AUTH_USR
                    && HTTP::_GP('authattack', '') == 'on' ? $UserData['authlevel'] : 0);
                $old['multi'] = $GLOBALS['DATABASE']->getFirstCell(
                    "SELECT COUNT(*) FROM " . MULTI . " WHERE userID = " . $id . ";"
                );
                $new['authattack'] = $multi;

                if ($old['multi'] != $multi) {
                    if ($multi == 0) {
                        $GLOBALS['DATABASE']->query("DELETE FROM " . MULTI . " WHERE userID = " . ((int) $id) . ";");
                    } elseif ($multi == 1) {
                        $GLOBALS['DATABASE']->query("INSERT INTO " . MULTI . " SET userID = " . ((int) $id) . ";");
                    }
                }

                $LOG = new Log(1);
                $LOG->target = $id;
                $LOG->old = $old;
                $LOG->new = $new;
                $LOG->save();

                exit(sprintf($LNG['qe_edit_player_sucess'], $UserData['username'], $id));
            }
            $PlanetInfo = $GLOBALS['DATABASE']->getFirstRow(
                "SELECT `name` FROM " . PLANETS . " WHERE `id` = '" . $UserData['id_planet'] . "' AND `universe` = '"
                . Universe::getEmulated() . "';"
            );

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
                'multi'         => $GLOBALS['DATABASE']->getFirstCell(
                    "SELECT COUNT(*) FROM " . MULTI . " WHERE userID = " . $id . ";"
                ),
                'ChangePW'      => $ChangePW,
                'yesorno'       => [1 => $LNG['one_is_yes_1'], 0 => $LNG['one_is_yes_0']],
                'signalColors'  => $USER['signalColors']
            ]);
            $template->show('QuickEditorUser.tpl');
            break;
    }
}
