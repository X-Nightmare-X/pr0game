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

# Actions not logged: Planet-Edit, Alliance-Edit

if (!allowedTo(str_replace([dirname(__FILE__), '\\', '/', '.php'], '', __FILE__))) {
    throw new Exception("Permission error!");
}

function ShowAccountEditorPage()
{
    $LNG =& Singleton()->LNG;
    $reslist =& Singleton()->reslist;
    $resource =& Singleton()->resource;
    $USER =& Singleton()->USER;
    $db = Database::get();
    $template = new template();
    $template->assign_vars([
        'signalColors'      => $USER['signalColors'],
    ]);
    if (!isset($_GET['edit'])) {
        $_GET['edit'] = '';
    }
    switch ($_GET['edit']) {
        case 'resources':
            $id = HTTP::_GP('id', 0);
            $metal = max(0, round(HTTP::_GP('metal', 0.0)));
            $cristal = max(0, round(HTTP::_GP('cristal', 0.0)));
            $deut = max(0, round(HTTP::_GP('deut', 0.0)));

            if ($_POST) {
                if (!empty($id)) {
                    $sql = "SELECT `metal`, `crystal`, `deuterium` FROM %%PLANETS%%
                        WHERE `id` = :planetID;";
                    $beforeRess = $db->selectSingle($sql, [
                        ':planetID' => $id,
                    ]);
                }
                if (isset($_POST['add'])) {
                    if (!empty($id)) {
                        $sql = "UPDATE %%PLANETS%% SET `metal` = `metal` + :metal, `crystal` = `crystal` + :cristal,
                            `deuterium` = `deuterium` + :deut
                            WHERE `id` = :id AND `universe` = :universe;";
                        $db->update($sql, [
                            ':metal' => $metal,
                            ':cristal' => $cristal,
                            ':deut' => $deut,
                            ':id' => $id,
                            ':universe' => Universe::getEmulated(),
                        ]);
                        $afterRess = [
                            'metal' => ($beforeRess['metal'] + $metal),
                            'crystal' => ($beforeRess['crystal'] + $cristal),
                            'deuterium' => ($beforeRess['deuterium'] + $deut),
                        ];
                    }
                } elseif (isset($_POST['delete'])) {
                    if (!empty($id)) {
                        $sql = "UPDATE %%PLANETS%% SET `metal` = `metal` - :metal, `crystal` = `crystal` - :cristal,
                            `deuterium` = `deuterium` - :deut
                            WHERE `id` = :id AND `universe` = :universe;";
                        $db->update($sql, [
                            ':metal' => $metal,
                            ':cristal' => $cristal,
                            ':deut' => $deut,
                            ':id' => $id,
                            ':universe' => Universe::getEmulated(),
                        ]);
                        $afterRess = [
                            'metal' => ($beforeRess['metal'] - $metal),
                            'crystal' => ($beforeRess['crystal'] - $cristal),
                            'deuterium' => ($beforeRess['deuterium'] - $deut),
                        ];
                    }
                }

                if (!empty($id)) {
                    $LOG = new Log(2);
                    $LOG->target = $id;
                    $LOG->universe = Universe::getEmulated();
                    $LOG->old = $beforeRess;
                    $LOG->new = $afterRess;
                    $LOG->save();
                }
                if (isset($_POST['add'])) {
                    $template->message($LNG['ad_add_res_sucess'], '?page=accounteditor&edit=resources');
                } elseif (isset($_POST['delete'])) {
                    $template->message($LNG['ad_delete_res_sucess'], '?page=accounteditor&edit=resources');
                }
                exit;
            }

            $template->show('AccountEditorPageResources.tpl');
            break;
        case 'ships':
            if ($_POST) {
                $id = HTTP::_GP('id', 0);
                foreach ($reslist['fleet'] as $ID) {
                    $fields[] = "`" . $resource[$ID] . "`";
                }
                $sql = "SELECT " + implode(", ", $fields) . " FROM %%PLANETS%% WHERE `id` = :planetID;";
                $beforeShips = $db->selectSingle($sql, [
                    ':planetID' => $id,
                ]);
                $afterShips = [];
                if (isset($_POST['add'])) {
                    $QryUpdate = [];
                    $sql = "UPDATE %%PLANETS%% SET `eco_hash` = '', ";
                    foreach ($reslist['fleet'] as $ID) {
                        $count = max(0, round(HTTP::_GP($resource[$ID], 0.0)));
                        if ($count > 0) {
                            $QryUpdate[] = "`" . $resource[$ID] . "` = `" . $resource[$ID] . "` + :" . $resource[$ID];
                            $params[':' . $resource[$ID]] = $count;
                            $afterShips[$ID] = $beforeShips[$ID] + $count;
                        }
                    }
                    if (!empty($QryUpdate)) {
                        $sql .= implode(", ", $QryUpdate);
                        $sql .= " WHERE `id` = :id AND `universe` = :universe;";
                        $db->update($sql, array_merge($params, [
                            ':id' => $id,
                            ':universe' => Universe::getEmulated(),
                        ]));
                    }
                } elseif (isset($_POST['delete'])) {
                    $QryUpdate = [];
                    $sql = "UPDATE %%PLANETS%% SET `eco_hash` = '', ";
                    foreach ($reslist['fleet'] as $ID) {
                        $count = max(0, round(HTTP::_GP($resource[$ID], 0.0)));
                        if ($count > 0) {
                            $QryUpdate[] = "`" . $resource[$ID] . "` = `" . $resource[$ID] . "` - :" . $resource[$ID];
                            $params[':' . $resource[$ID]] = $count;
                            $afterShips[$ID] = max(0, $beforeShips[$ID] - $count);
                        }
                    }
                    if (!empty($QryUpdate)) {
                        $sql .= implode(", ", $QryUpdate);
                        $sql .= " WHERE `id` = :id AND `universe` = :universe;";
                        $db->update($sql, array_merge($params, [
                            ':id' => $id,
                            ':universe' => Universe::getEmulated(),
                        ]));
                    }
                }

                if (!empty($QryUpdate)) {
                    $LOG = new Log(2);
                    $LOG->target = $id;
                    $LOG->universe = Universe::getEmulated();
                    $LOG->old = $beforeShips;
                    $LOG->new = $afterShips;
                    $LOG->save();
                }
                if (isset($_POST['add'])) {
                    $template->message($LNG['ad_add_ships_sucess'], '?page=accounteditor&edit=ships');
                } elseif (isset($_POST['delete'])) {
                    $template->message($LNG['ad_delete_ships_sucess'], '?page=accounteditor&edit=ships');
                }
                exit;
            }

            $parse['ships'] = "";
            foreach ($reslist['fleet'] as $ID) {
                $INPUT[$ID] = [
                    'type'  => $resource[$ID],
                ];
            }

            $template->assign_vars([
                'inputlist'     => $INPUT,
            ]);

            $template->show('AccountEditorPageShips.tpl');
            break;

        case 'defenses':
            if ($_POST) {
                $id = HTTP::_GP('id', 0);
                foreach ($reslist['defense'] as $ID) {
                    $fields[] = "`" . $resource[$ID] . "`";
                }
                $sql = "SELECT " + implode(", ", $fields) . " FROM %%PLANETS%% WHERE `id` = :planetID;";
                $beforeDef = $db->selectSingle($sql, [
                    ':planetID' => $id,
                ]);
                $afterDef = [];
                if (isset($_POST['add'])) {
                    $QryUpdate = [];
                    $sql = "UPDATE %%PLANETS%% SET ";
                    foreach ($reslist['defense'] as $ID) {
                        $count = max(0, round(HTTP::_GP($resource[$ID], 0.0)));
                        if ($count > 0) {
                            $QryUpdate[] = "`" . $resource[$ID] . "` = `" . $resource[$ID] . "` + :" . $resource[$ID];
                            $params[':' . $resource[$ID]] = $count;
                            $afterDef[$ID] = $beforeDef[$ID] + $count;
                        }
                    }
                    if (!empty($QryUpdate)) {
                        $sql .= implode(", ", $QryUpdate);
                        $sql .= " WHERE `id` = :id AND `universe` = :universe;";
                        $db->update($sql, array_merge($params, [
                            ':id' => $id,
                            ':universe' => Universe::getEmulated(),
                        ]));
                    }
                } elseif (isset($_POST['delete'])) {
                    $QryUpdate = [];
                    $sql = "UPDATE %%PLANETS%% SET ";
                    foreach ($reslist['defense'] as $ID) {
                        $count = max(0, round(HTTP::_GP($resource[$ID], 0.0)));
                        if ($count > 0) {
                            $QryUpdate[] = "`" . $resource[$ID] . "` = `" . $resource[$ID] . "` - :" . $resource[$ID];
                            $params[':' . $resource[$ID]] = $count;
                            $afterDef[$ID] = max(0, $beforeDef[$ID] - $count);
                        }
                    }
                    if (!empty($QryUpdate)) {
                        $sql .= implode(", ", $QryUpdate);
                        $sql .= " WHERE `id` = :id AND `universe` = :universe;";
                        $db->update($sql, array_merge($params, [
                            ':id' => $id,
                            ':universe' => Universe::getEmulated(),
                        ]));
                    }
                }

                if (!empty($QryUpdate)) {
                    $LOG = new Log(2);
                    $LOG->target = $id;
                    $LOG->universe = Universe::getEmulated();
                    $LOG->old = $beforeDef;
                    $LOG->new = $afterDef;
                    $LOG->save();
                }
                if (isset($_POST['add'])) {
                    $template->message($LNG['ad_add_defenses_success'], '?page=accounteditor&edit=defenses');
                } elseif (isset($_POST['delete'])) {
                    $template->message($LNG['ad_delete_defenses_success'], '?page=accounteditor&edit=defenses');
                }
                exit;
            }

            foreach ($reslist['defense'] as $ID) {
                $INPUT[$ID] = ['type'  => $resource[$ID]];
            }

            $template->assign_vars([
                'inputlist' => $INPUT,
            ]);

            $template->show('AccountEditorPageDefenses.tpl');
            break;
            break;

        case 'buildings':
            if ($_POST) {
                $id = HTTP::_GP('id', 0);
                $sql = "SELECT * FROM %%PLANETS%% WHERE `id` = :planetID;";
                $PlanetData = $db->selectSingle($sql, [
                    ':planetID' => $id,
                ]);
                if (!isset($PlanetData)) {
                    $template->message($LNG['ad_add_not_exist'], '?page=accounteditor&edit=buildings');
                }
                $beforeBuildings = [];
                $afterBuildings = [];
                foreach ($reslist['allow'][$PlanetData['planet_type']] as $ID) {
                    $beforeBuildings[$ID] = $PlanetData[$resource[$ID]];
                }
                if (isset($_POST['add'])) {
                    $QryUpdate = [];
                    $fields = 0;
                    $sql = "UPDATE %%PLANETS%% SET `eco_hash` = '', ";
                    foreach ($reslist['allow'][$PlanetData['planet_type']] as $ID) {
                        $count = max(0, round(HTTP::_GP($resource[$ID], 0.0)));
                        if ($count > 0) {
                            $QryUpdate[] = "`" . $resource[$ID] . "` = `" . $resource[$ID] . "` + :" . $resource[$ID];
                            $params[':' . $resource[$ID]] = $count;
                            $afterBuildings[$ID] = $beforeBuildings[$ID] + $count;
                            $fields += $count;
                        }
                    }
                    if (!empty($QryUpdate)) {
                        $sql .= implode(", ", $QryUpdate);
                        $sql .= ", `field_current` = `field_current` + :fields WHERE `id` = :id AND `universe` = :universe;";
                        $db->update($sql, array_merge($params, [
                            ':fields' => $fields,
                            ':id' => $id,
                            ':universe' => Universe::getEmulated(),
                        ]));
                    }
                } elseif (isset($_POST['delete'])) {
                    $QryUpdate = [];
                    $fields = 0;
                    $sql = "UPDATE %%PLANETS%% SET `eco_hash` = '', ";
                    foreach ($reslist['allow'][$PlanetData['planet_type']] as $ID) {
                        $count = max(0, round(HTTP::_GP($resource[$ID], 0.0)));
                        if ($count > 0) {
                            $QryUpdate[] = "`" . $resource[$ID] . "` = `" . $resource[$ID] . "` - :" . $resource[$ID];
                            $params[':' . $resource[$ID]] = $count;
                            $afterBuildings[$ID] = max($beforeBuildings[$ID] - $count, 0);
                            $fields += $count;
                        }
                    }
                    if (!empty($QryUpdate)) {
                        $sql .= implode(", ", $QryUpdate);
                        $sql .= ", `field_current` = `field_current` - :fields WHERE `id` = :id AND `universe` = :universe;";
                        $db->update($sql, array_merge($params, [
                            ':fields' => $fields,
                            ':id' => $id,
                            ':universe' => Universe::getEmulated(),
                        ]));
                    }
                }

                if (!empty($QryUpdate)) {
                    $LOG = new Log(2);
                    $LOG->target = $id;
                    $LOG->universe = Universe::getEmulated();
                    $LOG->old = $beforeBuildings;
                    $LOG->new = $afterBuildings;
                    $LOG->save();
                }

                if (isset($_POST['add'])) {
                    $template->message($LNG['ad_add_build_success'], '?page=accounteditor&edit=buildings');
                } elseif (isset($_POST['delete'])) {
                    $template->message($LNG['ad_delete_build_success'], '?page=accounteditor&edit=buildings');
                }
                exit;
            }

            foreach ($reslist['build'] as $ID) {
                $INPUT[$ID] = ['type'  => $resource[$ID]];
            }

            $template->assign_vars([
                'inputlist' => $INPUT,
            ]);

            $template->show('AccountEditorPageBuilds.tpl');
            break;

        case 'researchs':
            if ($_POST) {
                $id = HTTP::_GP('id', 0);
                foreach ($reslist['tech'] as $ID) {
                    $fields[] = "`" . $resource[$ID] . "`";
                }
                $sql = "SELECT " + implode(", ", $fields) . " FROM %%USERS%% WHERE `id` = :userID;";
                $beforeResearch = $db->selectSingle($sql, [
                    ':userID' => $id,
                ]);
                $afterResearch = [];
                if (isset($_POST['add'])) {
                    $QryUpdate = [];
                    $sql = "UPDATE %%USERS%% SET ";
                    foreach ($reslist['tech'] as $ID) {
                        $count = max(0, round(HTTP::_GP($resource[$ID], 0.0)));
                        if ($count > 0) {
                            $QryUpdate[] = "`" . $resource[$ID] . "` = `" . $resource[$ID] . "` + :" . $resource[$ID];
                            $params[':' . $resource[$ID]] = $count;
                            $afterResearch[$ID] = $beforeResearch[$ID] + $count;
                        }
                    }
                    if (!empty($QryUpdate)) {
                        $sql .= implode(", ", $QryUpdate);
                        $sql .= " WHERE `id` = :id AND `universe` = :universe;";
                        $db->update($sql, array_merge($params, [
                            ':id' => $id,
                            ':universe' => Universe::getEmulated(),
                        ]));
                    }
                } elseif (isset($_POST['delete'])) {
                    $QryUpdate = [];
                    $sql = "UPDATE %%USERS%% SET ";
                    foreach ($reslist['tech'] as $ID) {
                        $count = max(0, round(HTTP::_GP($resource[$ID], 0.0)));
                        if ($count > 0) {
                            $QryUpdate[] = "`" . $resource[$ID] . "` = `" . $resource[$ID] . "` - :" . $resource[$ID];
                            $params[':' . $resource[$ID]] = $count;
                            $afterResearch[$ID] = max(0, $beforeResearch[$ID] - $count);
                        }
                    }
                    if (!empty($QryUpdate)) {
                        $sql .= implode(", ", $QryUpdate);
                        $sql .= " WHERE `id` = :id AND `universe` = :universe;";
                        $db->update($sql, array_merge($params, [
                            ':id' => $id,
                            ':universe' => Universe::getEmulated(),
                        ]));
                    }
                }

                if (!empty($QryUpdate)) {
                    $LOG = new Log(1);
                    $LOG->target = $id;
                    $LOG->universe = Universe::getEmulated();
                    $LOG->old = $beforeResearch;
                    $LOG->new = $afterResearch;
                    $LOG->save();
                }
                if (isset($_POST['add'])) {
                    $template->message($LNG['ad_add_tech_success'], '?page=accounteditor&edit=researchs');
                } elseif (isset($_POST['delete'])) {
                    $template->message($LNG['ad_delete_tech_success'], '?page=accounteditor&edit=researchs');
                }
                exit;
            }

            foreach ($reslist['tech'] as $ID) {
                $INPUT[$ID] = ['type'  => $resource[$ID]];
            }

            $template->assign_vars([
                'inputlist'     => $INPUT,
            ]);

            $template->show('AccountEditorPageResearch.tpl');
            break;
        case 'personal':
            if ($_POST) {
                $id = HTTP::_GP('id', 0);
                $username = HTTP::_GP('username', '', UTF8_SUPPORT);
                $password = HTTP::_GP('password', '', true);
                $email = HTTP::_GP('email', '');
                $email_2 = HTTP::_GP('email_2', '');
                $vacation = HTTP::_GP('vacation', '');

                $sql = "SELECT `username`,`email`,`email_2`,`password`,`urlaubs_modus`,`urlaubs_until`,`universe`
                    FROM %%USERS%% WHERE `id` = :userID;";
                $beforePersonal = $db->selectSingle($sql, [
                    ':userID' => $id,
                ]);
                $afterPersonal = $beforePersonal;
                $QryUpdate = [];
                $params = [];

                if (!empty($username) && $id != ROOT_USER) {
                    $QryUpdate[] = "`username` = :username";
                    $param[':username'] = $username;
                    $afterPersonal['username'] = $username;
                }

                if (!empty($email) && $id != ROOT_USER) {
                    $QryUpdate[] = "`email` = :email";
                    $param[':email'] = $email;
                    $afterPersonal['email'] = $email;
                }

                if (!empty($email_2) && $id != ROOT_USER) {
                    $QryUpdate[] = "`email_2` = :email_2";
                    $param[':email_2'] = $email_2;
                    $afterPersonal['email_2'] = $email_2;
                }

                if (!empty($password) && $id != ROOT_USER) {
                    $QryUpdate[] = "`password` = :password";
                    $param[':password'] = PlayerUtil::cryptPassword($password);
                    $afterPersonal['password'] = (PlayerUtil::cryptPassword($password) != $beforePersonal['password']) ? 'CHANGED' : '';
                } else {
                    $afterPersonal['password'] = '';
                }
                $beforePersonal['password'] = '';

                if ($vacation == 'yes') {
                    $QryUpdate[] = "`urlaubs_modus` = :urlaubs_modus";
                    $param[':urlaubs_modus'] = 1;
                    $afterPersonal['urlaubs_modus'] = 1;
                    $d = HTTP::_GP('d', 0);
                    $h = HTTP::_GP('h', 0);
                    $m = HTTP::_GP('m', 0);
                    $s = HTTP::_GP('s', 0);
                    $TimeAns = TIMESTAMP + $d * 86400 + $h * 3600 + $m * 60 + $s;
                    $QryUpdate[] = "`urlaubs_until` = :urlaubs_until";
                    $param[':urlaubs_until'] = $TimeAns;
                    $afterPersonal['urlaubs_until'] = $TimeAns;
                }

                $sql = "UPDATE %%USERS%% SET ";
                $sql .= implode(", ", $QryUpdate);
                $sql .= " WHERE `id` = :id AND `universe` = :universe;";
                $db->update($sql, array_merge($params, [
                    ':id' => $id,
                    ':universe' => Universe::getEmulated(),
                ]));

                $LOG = new Log(1);
                $LOG->target = $id;
                $LOG->universe = Universe::getEmulated();
                $LOG->old = $beforePersonal;
                $LOG->new = $afterPersonal;
                $LOG->save();
                $template->message($LNG['ad_personal_succes'], '?page=accounteditor&edit=personal');
                exit;
            }

            $template->assign_vars([
                'Selector'      => ['' => $LNG['select_option'], 'yes' => $LNG['one_is_yes_1'], 'no' => $LNG['one_is_yes_0']],
            ]);

            $template->show('AccountEditorPagePersonal.tpl');
            break;

        case 'planets':
            if ($_POST) {
                $id = HTTP::_GP('id', 0);
                $name = HTTP::_GP('name', '', UTF8_SUPPORT);
                $diameter = HTTP::_GP('diameter', 0);
                $fields = HTTP::_GP('fields', 0);
                $buildings = HTTP::_GP('0_buildings', '');
                $ships = HTTP::_GP('0_ships', '');
                $defenses = HTTP::_GP('0_defenses', '');
                $c_hangar = HTTP::_GP('0_c_hangar', '');
                $c_buildings = HTTP::_GP('0_c_buildings', '');
                $change_pos = HTTP::_GP('change_position', '');
                $galaxy = HTTP::_GP('g', 0);
                $system = HTTP::_GP('s', 0);
                $planet = HTTP::_GP('p', 0);

                $sql = "SELECT * FROM %%PLANETS%% WHERE `id` = :planetID AND `universe` = :universe;";
                $beforePlanet = $db->selectSingle($sql, [
                    ':planetID' => $id,
                    ':universe' => Universe::getEmulated(),
                ]);

                if (!isset($beforePlanet)) {
                    $template->message($LNG['ad_pla_error_planets2'], '?page=accounteditor&edit=planets');
                    exit;
                }

                $afterPlanet = $beforePlanet;
                $QryUpdate = [];
                $params = [];

                if (!empty($name)) {
                    $QryUpdate[] = "`name` = :name";
                    $params[':name'] = $name;
                    $afterPlanet['name'] = $name;
                }

                if ($buildings == 'on') {
                    foreach ($reslist['build'] as $ID) {
                        $QryUpdate[] = "`" . $resource[$ID] . "` = '0'";
                        $afterPlanet[$ID] = 0;
                    }
                }

                if ($ships == 'on') {
                    foreach ($reslist['fleet'] as $ID) {
                        $QryUpdate[] = "`" . $resource[$ID] . "` = '0'";
                        $afterPlanet[$ID] = 0;
                    }
                }

                if ($defenses == 'on') {
                    foreach ($reslist['defense'] as $ID) {
                        $QryUpdate[] = "`" . $resource[$ID] . "` = '0'";
                        $afterPlanet[$ID] = 0;
                    }
                }

                if ($c_hangar == 'on') {
                    $QryUpdate[] = "`b_hangar` = 0";
                    $QryUpdate[] = "`b_hangar_plus` = 0";
                    $QryUpdate[] = "`b_hangar_id` = ''";
                    $afterPlanet['b_hangar'] = 0;
                    $afterPlanet['b_hangar_plus'] = 0;
                    $afterPlanet['b_hangar_id'] = '';
                }

                if ($c_buildings == 'on') {
                    $QryUpdate[] = "`b_building` = 0";
                    $QryUpdate[] = "`b_building_id` = ''";
                    $afterPlanet['b_building'] = 0;
                    $afterPlanet['b_building_id'] = '';
                }

                if (!empty($diameter)) {
                    $QryUpdate[] = "`diameter` = :diameter";
                    $params[':diameter'] = $diameter;
                    $afterPlanet['diameter'] = $diameter;
                }

                if (!empty($fields)) {
                    $QryUpdate[] = "`field_max` = :field_max";
                    $params[':field_max'] = $fields;
                    $afterPlanet['field_max'] = $fields;
                }

                $QryUpdate2 = [];
                $params2 = [];
                $beforePlanet2 = [];
                $afterPlanet2 = [];
                $QryUpdate3 = [];
                $params3 = [];
                $beforePlanet3 = [];
                $afterPlanet3 = [];
                if (
                    $change_pos == 'on' && $galaxy > 0 && $system > 0 && $planet > 0
                    && $galaxy <= Config::get(Universe::getEmulated())->max_galaxy
                    && $system <= Config::get(Universe::getEmulated())->max_system
                    && $planet <= Config::get(Universe::getEmulated())->max_planets
                ) {
                    if ($beforePlanet['planet_type'] == '1') {
                        if (
                            PlayerUtil::checkPosition(
                                Universe::getEmulated(),
                                $galaxy,
                                $system,
                                $planet,
                                $beforePlanet['planet_type']
                            )
                        ) {
                            $template->message($LNG['ad_pla_error_planets3'], '?page=accounteditor&edit=planets');
                            exit;
                        }

                        $QryUpdate[] = "`galaxy` = :galaxy";
                        $QryUpdate[] = "`system` = :system";
                        $QryUpdate[] = "`planet` = :planet";
                        $params[':galaxy'] = $galaxy;
                        $params[':system'] = $system;
                        $params[':planet'] = $planet;
                        $afterPlanet['galaxy'] = $galaxy;
                        $afterPlanet['system'] = $system;
                        $afterPlanet['planet'] = $planet;

                        if ($beforePlanet['id_luna'] > 0) {
                            $sql = "SELECT * FROM %%PLANETS%%
                                WHERE `id` = :id_luna AND `planet_type` = '3';";
                            $beforePlanet2 = $db->selectSingle($sql, [
                                ':id_luna' => $beforePlanet['id_luna'],
                            ]);
                            $afterPlanet2 = $beforePlanet2;

                            $QryUpdate2[] = "`galaxy` = :galaxy";
                            $QryUpdate2[] = "`system` = :system";
                            $QryUpdate2[] = "`planet` = :planet";
                            $params2[':galaxy'] = $galaxy;
                            $params2[':system'] = $system;
                            $params2[':planet'] = $planet;
                            $params2[':id'] = $beforePlanet2['id'];
                            $afterPlanet2['galaxy'] = $galaxy;
                            $afterPlanet2['system'] = $system;
                            $afterPlanet2['planet'] = $planet;
                        }
                    } else {
                        if (
                            !PlayerUtil::checkPosition(
                                Universe::getEmulated(),
                                $galaxy,
                                $system,
                                $planet,
                                $beforePlanet['planet_type']
                            )
                        ) {
                            $template->message($LNG['ad_pla_error_planets5'], '?page=accounteditor&edit=planets');
                            exit;
                        }

                        $sql = "SELECT * FROM %%PLANETS%%
                            WHERE `galaxy` = :galaxy AND `system` = :system
                            AND `planet` = :planet AND `planet_type` = '1'
                            AND `universe` = :universe;";
                        $beforePlanet2 = $db->selectSingle($sql, [
                            ':galaxy' => $galaxy,
                            ':system' => $system,
                            ':planet' => $planet,
                            ':universe' => Universe::getEmulated(),
                        ]);
                        $afterPlanet2 = $beforePlanet2;

                        if ($beforePlanet2['id_luna'] > 0) {
                            $template->message($LNG['ad_pla_error_planets4'], '?page=accounteditor&edit=planets');
                            exit;
                        }

                        $QryUpdate[] = "`galaxy` = :galaxy";
                        $QryUpdate[] = "`system` = :system";
                        $QryUpdate[] = "`planet` = :planet";
                        $params[':galaxy'] = $galaxy;
                        $params[':system'] = $system;
                        $params[':planet'] = $planet;
                        $afterPlanet['galaxy'] = $galaxy;
                        $afterPlanet['system'] = $system;
                        $afterPlanet['planet'] = $planet;

                        $QryUpdate2[] = "`id_luna` = :id_luna";
                        $params2[':id_luna'] = $beforePlanet['id'];
                        $params2[':id'] = $beforePlanet2['id'];
                        $afterPlanet2['id_luna'] = $beforePlanet['id'];

                        $sql = "SELECT * FROM %%PLANETS%%
                            WHERE `id_luna` = :id AND `planet_type` = '1';";
                        $beforePlanet3 = $db->selectSingle($sql, [
                            ':id' => $beforePlanet['id'],
                        ]);
                        $afterPlanet3 = $beforePlanet3;

                        $QryUpdate3[] = "`id_luna` = :id_luna";
                        $params3[':id_luna'] = 0;
                        $params3[':id'] = $beforePlanet3['id'];
                        $afterPlanet3['id_luna'] = 0;
                    }
                }

                $sql = "UPDATE %%PLANETS%% SET ";
                $sql .= implode(", ", $QryUpdate);
                $sql .= " WHERE `id` = :id AND `universe` = :universe;";
                $db->update($sql, array_merge($params, [
                    ':id' => $id,
                    ':universe' => Universe::getEmulated(),
                ]));

                $LOG = new Log(2);
                $LOG->target = $id;
                $LOG->universe = Universe::getEmulated();
                $LOG->old = $beforePlanet;
                $LOG->new = $afterPlanet;
                $LOG->save();
                if (isset($beforePlanet2)) {
                    $sql = "UPDATE %%PLANETS%% SET ";
                    $sql .= implode(", ", $QryUpdate2);
                    $sql .= " WHERE `id` = :id AND `universe` = :universe;";
                    $db->update($sql, array_merge($params2, [
                        ':universe' => Universe::getEmulated(),
                    ]));

                    $LOG = new Log(2);
                    $LOG->target = $beforePlanet2['id'];
                    $LOG->universe = Universe::getEmulated();
                    $LOG->old = $beforePlanet2;
                    $LOG->new = $afterPlanet2;
                    $LOG->save();
                }
                if (isset($beforePlanet3)) {
                    $sql = "UPDATE %%PLANETS%% SET ";
                    $sql .= implode(", ", $QryUpdate3);
                    $sql .= " WHERE `id` = :id AND `universe` = :universe;";
                    $db->update($sql, array_merge($params3, [
                        ':universe' => Universe::getEmulated(),
                    ]));

                    $LOG = new Log(2);
                    $LOG->target = $beforePlanet3['id'];
                    $LOG->universe = Universe::getEmulated();
                    $LOG->old = $beforePlanet3;
                    $LOG->new = $afterPlanet3;
                    $LOG->save();
                }

                $template->message($LNG['ad_pla_succes'], '?page=accounteditor&edit=planets');
                exit;
            }
            $template->show('AccountEditorPagePlanets.tpl');
            break;

        case 'alliances':
            if ($_POST) {
                $id = HTTP::_GP('id', 0);
                $name = HTTP::_GP('name', '', UTF8_SUPPORT);
                $changeleader = HTTP::_GP('changeleader', 0);
                $tag = HTTP::_GP('tag', '', UTF8_SUPPORT);
                $externo = HTTP::_GP('externo', '', true);
                $interno = HTTP::_GP('interno', '', true);
                $solicitud = HTTP::_GP('solicitud', '', true);
                $delete = HTTP::_GP('delete', '');
                $delete_u = HTTP::_GP('delete_u', '');

                $QryUpdate = [];
                $params = [];

                $sql = "SELECT * FROM %%ALLIANCE%% WHERE `id` = :allyId AND `ally_universe` = :universe;";
                $beforeAlliance = $db->selectSingle($sql, [
                    ':allyId' => $id,
                    ':universe' => Universe::getEmulated(),
                ]);

                if (!isset($beforeAlliance)) {
                    $template->message($LNG['ad_ally_not_exist'], '?page=accounteditor&edit=alliances');
                    exit;
                }

                if ($delete == 'on') {
                    $sql = "SELECT `id`, `username`, `ally_id`, `ally_register_time`, `ally_rank_id`
                        FROM %%USERS%% WHERE `ally_id` = :allyId AND `universe` = :universe;";
                    $beforeMembers = $db->select($sql, [
                        ':allyId' => $id,
                        ':universe' => Universe::getEmulated(),
                    ]);
                    foreach ($beforeMembers as $member) {
                        $afterMember = $member;
                        $sql = "UPDATE %%USERS%% SET `ally_id` = '0', `ally_rank_id` = '0',
                            `ally_register_time` = '0' WHERE `id` = :userId;";
                        $db->update($sql, [
                            ':id' => $member['id'],
                        ]);
                        $afterMember['ally_id'] = 0;
                        $afterMember['ally_rank_id'] = 0;
                        $afterMember['ally_register_time'] = 0;
                        $beforeAlliance['USERS'][$member['id']] = $member;
                        $afterAlliance['USERS'][$member['id']] = $afterMember;
                    }

                    $sql = "SELECT `id_owner`, `id_ally`, `stat_type` FROM %%STATPOINTS%% WHERE `id_ally` = :AllianceID;";
                    $beforeStats = $db->select($sql, [
                        ':AllianceID' => $id,
                    ]);
                    foreach ($beforeStats as $stats) {
                        $afterStats = $beforeStats;
                        $afterStats['id_ally'] = 0;
                        $beforeAlliance['STATPOINTS'][1][] = $stats;
                        $afterAlliance['STATPOINTS'][1][] = $afterStats;
                    }
                    $sql = "UPDATE %%STATPOINTS%% SET id_ally = '0' WHERE id_ally = :AllianceID;";
                    $db->update($sql, [
                        ':AllianceID' => $id,
                    ]);

                    $sql = "SELECT * FROM %%STATPOINTS%% WHERE `id_owner` = :AllianceID AND stat_type = 2;";
                    $beforeStats = $db->selectSingle($sql, [
                        ':AllianceID' => $id,
                    ]);
                    $sql = "DELETE FROM %%STATPOINTS%% WHERE id_owner = :AllianceID AND stat_type = 2;";
                    $db->delete($sql, [
                        ':AllianceID' => $id,
                    ]);
                    $beforeAlliance['STATPOINTS'][2] = $beforeStats;
                    $afterAlliance['STATPOINTS'][2] = [];

                    $sql = "DELETE FROM %%ALLIANCE%% WHERE `id` = :AllianceID;";
                    $db->delete($sql, [
                        ':AllianceID' => $id,
                    ]);

                    $sql = "SELECT * FROM %%ALLIANCE_REQUEST%% WHERE `allianceId` = :AllianceID;";
                    $beforeRequest = $db->selectSingle($sql, [
                        ':AllianceID' => $id,
                    ]);
                    $sql = "DELETE FROM %%ALLIANCE_REQUEST%% WHERE `allianceId` = :AllianceID;";
                    $db->delete($sql, [
                        ':AllianceID' => $id,
                    ]);
                    $beforeAlliance['ALLIANCE_REQUEST'] = $beforeRequest;
                    $afterAlliance['ALLIANCE_REQUEST'] = [];

                    $sql = "SELECT * FROM %%DIPLO%% WHERE `owner_1` = :AllianceID OR `owner_2` = :AllianceID;";
                    $beforeDiplos = $db->select($sql, [
                        ':AllianceID' => $id,
                    ]);
                    $sql = "DELETE FROM %%DIPLO%% WHERE `owner_1` = :AllianceID OR `owner_2` = :AllianceID;";
                    $db->delete($sql, [
                        ':AllianceID' => $id,
                    ]);
                    $beforeAlliance['DIPLO'] = $beforeDiplos;
                    $afterAlliance['DIPLO'] = [];

                    $LOG = new Log(6);
                    $LOG->target = $id;
                    $LOG->universe = Universe::getEmulated();
                    $LOG->old = $beforeAlliance;
                    $LOG->new = $afterAlliance;
                    $LOG->save();

                    $template->message($LNG['ad_ally_succes'], '?page=accounteditor&edit=alliances');
                    exit;
                }

                $afterAlliance = $beforeAlliance;

                if ($changeleader > 0) {
                    $sql = "SELECT `id`, `username`, `ally_rank_id` FROM %%USERS%% WHERE `id` = :newLeaderId AND `universe` = :universe;";
                    $beforeNewLeader = $db->selectSingle($sql, [
                        ':newLeaderId' => $changeleader,
                        ':universe' => Universe::getEmulated(),
                    ]);
                    $afterNewLeader = $beforeNewLeader;

                    if (!isset($beforeNewLeader)) {
                        $template->message($LNG['ad_ally_not_exist2'], '?page=accounteditor&edit=alliances');
                        exit;
                    }

                    $sql = "UPDATE %%USERS%% SET `ally_rank_id` = 0 WHERE `id` = :newLeaderId;";
                    $db->update($sql, [
                        ':newLeaderId' => $changeleader,
                    ]);
                    $afterNewLeader['ally_rank_id'] = 0;

                    $QryUpdate[] = "`ally_owner` = :newLeader";
                    $params[':newLeader'] = $changeleader;
                    $afterAlliance['ally_owner'] = $changeleader;
                    $beforeAlliance['USERS'][$changeleader] = $beforeNewLeader;
                    $afterAlliance['USERS'][$changeleader] = $afterNewLeader;
                }

                if (!empty($delete_u)) {
                    $sql = "SELECT `id`, `username`, `ally_id`, `ally_register_time`, `ally_rank_id`
                        FROM %%USERS%% WHERE `userId` = :userId AND `ally_id` = :allyId AND `universe` = :universe;";
                    $beforeMember = $db->selectSingle($sql, [
                        ':userId' => $delete_u,
                        ':allyId' => $id,
                        ':universe' => Universe::getEmulated(),
                    ]);
                    $afterMember = $beforeMember;

                    if (!isset($beforeMember)) {
                        $template->message($LNG['ad_ally_not_exist3'], '?page=accounteditor&edit=alliances');
                        exit;
                    }

                    $sql = "UPDATE %%USERS%% SET `ally_id` = '0', `ally_rank_id` = '0', `ally_register_time` = '0'
                        WHERE `id` = :userId AND `ally_id` = :allyId AND `universe` = :universe;";
                    $db->update($sql, [
                        ':userId' => $delete_u,
                        ':allyId' => $id,
                        ':universe' => Universe::getEmulated(),
                    ]);
                    $afterMember['ally_id'] = 0;
                    $afterMember['ally_rank_id'] = 0;
                    $afterMember['ally_register_time'] = 0;

                    $QryUpdate[] = "`ally_members` = ally_members - 1";
                    $afterAlliance['ally_members'] -= 1;
                    $beforeAlliance['USERS'][$delete_u] = $beforeMember;
                    $afterAlliance['USERS'][$delete_u] = $afterMember;
                }

                if (!empty($name)) {
                    $QryUpdate[] = "`ally_name` = :ally_name";
                    $params[':ally_name'] = $name;
                    $afterAlliance['ally_name'] = $name;
                }

                if (!empty($tag)) {
                    $QryUpdate[] = "`ally_tag` = :ally_tag";
                    $params[':ally_tag'] = $tag;
                    $afterAlliance['ally_tag'] = $tag;
                }

                if (!empty($externo)) {
                    $QryUpdate[] = "`ally_description` = :ally_description";
                    $params[':ally_description'] = $externo;
                    $afterAlliance['ally_description'] = $externo;
                }

                if (!empty($interno)) {
                    $QryUpdate[] = "`ally_text` = :ally_text";
                    $params[':ally_text'] = $interno;
                    $afterAlliance['ally_text'] = $interno;
                }

                if (!empty($solicitud)) {
                    $QryUpdate[] = "`ally_request` = :ally_request";
                    $params[':ally_request'] = $solicitud;
                    $afterAlliance['ally_request'] = $solicitud;
                }

                $sql = "UPDATE %%ALLIANCE%% SET ";
                $sql .= implode(", ", $QryUpdate);
                $sql .= " WHERE `id` = :id AND `ally_universe` = :universe;";
                $db->update($sql, array_merge($params, [
                    ':id' => $id,
                    ':universe' => Universe::getEmulated(),
                ]));

                $LOG = new Log(6);
                $LOG->target = $id;
                $LOG->universe = Universe::getEmulated();
                $LOG->old = $beforeAlliance;
                $LOG->new = $afterAlliance;
                $LOG->save();

                $template->message($LNG['ad_ally_succes'], '?page=accounteditor&edit=alliances');
                exit;
            }
            $template->show('AccountEditorPageAlliance.tpl');
            break;

        default:
            $template->show('AccountEditorPageMenu.tpl');
            break;
    }
}
