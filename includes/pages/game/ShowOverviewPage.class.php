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

class ShowOverviewPage extends AbstractGamePage
{
    public static $requireModule = 0;

    public function __construct()
    {
        parent::__construct();
    }

    private function getFleets()
    {
        global $USER, $PLANET;
        require 'includes/classes/class.FlyingFleetsTable.php';
        $fleetTableObj = new FlyingFleetsTable();
        $fleetTableObj->setUser($USER['id']);
        $fleetTableObj->setPlanet($PLANET['id']);
        return $fleetTableObj->renderTable();
    }

    // unused?
    public function savePlanetAction()
    {
        global $USER, $PLANET, $LNG;
        $password = HTTP::_GP('password', '', true);
        if (!empty($password)) {
            $db = Database::get();
            $sql = "SELECT COUNT(*) as state FROM %%FLEETS%% WHERE
                      (fleet_owner = :userID AND (fleet_start_id = :planetID OR fleet_start_id = :lunaID)) OR
                      (fleet_target_owner = :userID AND (fleet_end_id = :planetID OR fleet_end_id = :lunaID));";
            $IfFleets = $db->selectSingle($sql, [
                ':userID'   => $USER['id'],
                ':planetID' => $PLANET['id'],
                ':lunaID'   => $PLANET['id_luna'],
            ], 'state');

            if ($IfFleets > 0) {
                exit(json_encode(['message' => $LNG['ov_abandon_planet_not_possible']]));
            } elseif ($USER['id_planet'] == $PLANET['id']) {
                exit(json_encode(['message' => $LNG['ov_principal_planet_cant_abanone']]));
            } elseif (PlayerUtil::cryptPassword($password) != $USER['password']) {
                exit(json_encode(['message' => $LNG['ov_wrong_pass']]));
            } else {
                if ($PLANET['planet_type'] == 1) {
                    $sql = "UPDATE %%PLANETS%% SET destruyed = :time WHERE id = :planetID;";
                    $db->update($sql, [
                        ':time'   => TIMESTAMP + 86400,
                        ':planetID' => $PLANET['id'],
                    ]);
                    $sql = "DELETE FROM %%PLANETS%% WHERE id = :lunaID;";
                    $db->delete($sql, [':lunaID' => $PLANET['id_luna']]);
                } else {
                    $sql = "UPDATE %%PLANETS%% SET id_luna = 0 WHERE id_luna = :planetID;";
                    $db->update($sql, [':planetID' => $PLANET['id']]);
                    $sql = "DELETE FROM %%PLANETS%% WHERE id = :planetID;";
                    $db->delete($sql, [':planetID' => $PLANET['id']]);
                }

                $PLANET['id']   = $USER['id_planet'];
                exit(json_encode(['ok' => true, 'message' => $LNG['ov_planet_abandoned']]));
            }
        }
    }

    public function show()
    {
        global $LNG, $PLANET, $USER;

        $AdminsOnline = [];
        $AllPlanets = [];
        $Moon = [];
        $RefLinks = [];

        $db = Database::get();

        foreach ($USER['PLANETS'] as $ID => $CPLANET) {
            if ($ID == $PLANET['id'] || $CPLANET['planet_type'] == 3) {
                continue;
            }

            if (!empty($CPLANET['b_building']) && $CPLANET['b_building'] > TIMESTAMP) {
                $Queue = unserialize($CPLANET['b_building_id']);
                $BuildPlanet = $LNG['tech'][$Queue[0][0]] . " (" . $Queue[0][1]
                    . ")<br><span style=\"color:#7F7F7F;\">(" . pretty_time($Queue[0][3] - TIMESTAMP) . ")</span>";
            } else {
                $BuildPlanet = $LNG['ov_free'];
            }

            $AllPlanets[] = [
                'id'    => $CPLANET['id'],
                'name'  => $CPLANET['name'],
                'image' => $CPLANET['image'],
                'build' => $BuildPlanet,
            ];
        }

        if ($PLANET['id_luna'] != 0) {
            $sql = "SELECT id, name FROM %%PLANETS%% WHERE id = :lunaID;";
            $Moon = $db->selectSingle($sql, [':lunaID'   => $PLANET['id_luna']]);
        }

        if ($PLANET['b_building'] - TIMESTAMP > 0) {
            $Queue          = unserialize($PLANET['b_building_id']);
            $buildInfo['buildings'] = [
                'id'        => $Queue[0][0],
                'level'     => $Queue[0][1],
                'timeleft'  => $PLANET['b_building'] - TIMESTAMP,
                'time'      => $PLANET['b_building'],
                'starttime' => pretty_time($PLANET['b_building'] - TIMESTAMP),
            ];
        } else {
            $buildInfo['buildings'] = false;
        }

        if (!empty($PLANET['b_hangar_id'])) {
            $Queue  = unserialize($PLANET['b_hangar_id']);
            $time   = BuildFunctions::getBuildingTime($USER, $PLANET, $Queue[0][0]) * $Queue[0][1];
            $buildInfo['fleet'] = [
                'id'        => $Queue[0][0],
                'level'     => $Queue[0][1],
                'timeleft'  => $time - $PLANET['b_hangar'],
                'time'      => $time,
                'starttime' => pretty_time($time - $PLANET['b_hangar']),
            ];
        } else {
            $buildInfo['fleet'] = false;
        }

        if ($USER['b_tech'] - TIMESTAMP > 0) {
            $Queue = unserialize($USER['b_tech_queue']);
            $buildInfo['tech'] = [
                'id'        => $Queue[0][0],
                'level'     => $Queue[0][1],
                'timeleft'  => $USER['b_tech'] - TIMESTAMP,
                'time'      => $USER['b_tech'],
                'starttime' => pretty_time($USER['b_tech'] - TIMESTAMP),
            ];
        } else {
            $buildInfo['tech']  = false;
        }


        $sql = "SELECT id,username FROM %%USERS%% WHERE universe = :universe AND onlinetime >= :onlinetime"
            . " AND authlevel > :authlevel;";
        $onlineAdmins = $db->select($sql, [
            ':universe'     => Universe::current(),
            ':onlinetime'   => TIMESTAMP - 10 * 60,
            ':authlevel'    => AUTH_USR
        ]);

        foreach ($onlineAdmins as $AdminRow) {
            $AdminsOnline[$AdminRow['id']]  = $AdminRow['username'];
        }

        $Messages       = $USER['messages'];

        // Fehler: Wenn Spieler gelöscht werden, werden sie nicht mehr in der Tabelle angezeigt.
        $sql = "SELECT u.id, u.username, s.total_points FROM %%USERS%% as u
		LEFT JOIN %%STATPOINTS%% as s ON s.id_owner = u.id AND s.stat_type = '1' WHERE ref_id = :userID;";
        $RefLinksRAW = $db->select($sql, [':userID'   => $USER['id']]);

        $config = Config::get();

        if ($config->ref_active) {
            foreach ($RefLinksRAW as $RefRow) {
                $RefLinks[$RefRow['id']] = [
                    'username'  => $RefRow['username'],
                    'points'    => min($RefRow['total_points'], $config->ref_minpoints),
                ];
            }
        }

        $sql    = 'SELECT total_points, total_rank
		FROM %%STATPOINTS%%
		WHERE id_owner = :userId AND stat_type = :statType';

        $statData   = Database::get()->selectSingle($sql, [
            ':userId'   => $USER['id'],
            ':statType' => 1
        ]);

        if (($statData['total_rank'] ?? 0) == 0) {
            $rankInfo   = "-";
        } else {
            $rankInfo   = sprintf(
                $LNG['ov_userrank_info'],
                pretty_number($statData['total_points']),
                $LNG['ov_place'],
                $statData['total_rank'],
                $statData['total_rank'],
                $LNG['ov_of'],
                $config->users_amount
            );
        }

        $usersOnline = Database::get()->selectSingle(
            'SELECT COUNT(*)
			FROM %%USERS%% WHERE onlinetime >= UNIX_TIMESTAMP(NOW() - INTERVAL 15 MINUTE)'
        )['COUNT(*)'];

        $fleetsOnline = Database::get()->selectSingle(
            'SELECT COUNT(*)
			FROM %%FLEETS%%'
        )['COUNT(*)'];

        $this->assign([
            'rankInfo'                  => $rankInfo,
            'is_news'                   => $config->OverviewNewsFrame,
            'news'                      => makebr($config->OverviewNewsText),
            'usersOnline'               => $usersOnline,
            'fleetsOnline'              => $fleetsOnline,
            'planetname'                => $PLANET['name'],
            'planetimage'               => $PLANET['image'],
            'galaxy'                    => $PLANET['galaxy'],
            'system'                    => $PLANET['system'],
            'planet'                    => $PLANET['planet'],
            'planet_type'               => $PLANET['planet_type'],
            'username'                  => $USER['username'],
            'userid'                    => $USER['id'],
            'buildInfo'                 => $buildInfo,
            'Moon'                      => $Moon,
            'fleets'                    => $this->getFleets(),
            'AllPlanets'                => $AllPlanets,
            'AdminsOnline'              => $AdminsOnline,
            'messages'                  => ($Messages > 0) ? (
                ($Messages == 1) ? $LNG['ov_have_new_message']
                    : sprintf($LNG['ov_have_new_messages'], pretty_number($Messages))
            ) : false,
            'planet_diameter'           => pretty_number($PLANET['diameter']),
            'planet_field_current'      => $PLANET['field_current'],
            'planet_field_max'          => CalculateMaxPlanetFields($PLANET),
            'planet_temp_min'           => $PLANET['temp_min'],
            'planet_temp_max'           => $PLANET['temp_max'],
            'ref_active'                => $config->ref_active,
            'ref_minpoints'             => $config->ref_minpoints,
            'RefLinks'                  => $RefLinks,
            'servertime'                => _date("M D d H:i:s", TIMESTAMP, $USER['timezone']),
            'path'                      => HTTP_PATH,
        ]);

        $this->display('page.overview.default.tpl');
    }

    public function actions()
    {
        global $LNG, $PLANET;

        $this->initTemplate();
        $this->setWindow('popup');

        $this->assign([
            'ov_security_confirm' => sprintf($LNG['ov_security_confirm'], $PLANET['name'] . ' ['
                . $PLANET['galaxy'] . ':' . $PLANET['system'] . ':' . $PLANET['planet'] . ']'),
        ]);
        $this->display('page.overview.actions.tpl');
    }

    public function rename()
    {
        global $LNG, $PLANET;

        $newname        = HTTP::_GP('name', '', UTF8_SUPPORT);
        if (!empty($newname)) {
            if (!PlayerUtil::isNameValid($newname)) {
                $this->sendJSON(['message' => $LNG['ov_newname_specialchar'], 'error' => true]);
            } else {
                $db = Database::get();
                $sql = "UPDATE %%PLANETS%% SET name = :newName WHERE id = :planetID;";
                $db->update($sql, [
                    ':newName'  => $newname,
                    ':planetID' => $PLANET['id']
                ]);

                $this->sendJSON(['message' => $LNG['ov_newname_done'], 'error' => false]);
            }
        }
    }

    public function delete()
    {
        global $LNG, $PLANET, $USER;
        $password   = HTTP::_GP('password', '', true);

        if (!empty($password)) {
            $db = Database::get();
            $sql = "SELECT COUNT(*) as state FROM %%FLEETS%% WHERE
                      (fleet_owner = :userID AND (fleet_start_id = :planetID OR fleet_start_id = :lunaID)) OR
                      (fleet_target_owner = :userID AND (fleet_end_id = :planetID OR fleet_end_id = :lunaID));";
            $IfFleets = $db->selectSingle($sql, [
                ':userID'   => $USER['id'],
                ':planetID' => $PLANET['id'],
                ':lunaID'   => $PLANET['id_luna'],
            ], 'state');

            if ($USER['b_tech_planet'] == $PLANET['id'] && !empty($USER['b_tech_queue'])) {
                $TechQueue = unserialize($USER['b_tech_queue']);
                $NewCurrentQueue = [];
                foreach ($TechQueue as $ID => $ListIDArray) {
                    if ($ListIDArray[4] == $PLANET['id']) {
                        $ListIDArray[4] = $USER['id_planet'];
                        $NewCurrentQueue[] = $ListIDArray;
                    }
                }

                $USER['b_tech_planet'] = $USER['id_planet'];
                $USER['b_tech_queue'] = serialize($NewCurrentQueue);
            }

            if ($IfFleets > 0) {
                $this->sendJSON(['message' => $LNG['ov_abandon_planet_not_possible']]);
            } elseif ($USER['id_planet'] == $PLANET['id']) {
                $this->sendJSON(['message' => $LNG['ov_principal_planet_cant_abanone']]);
            // } elseif (PlayerUtil::cryptPassword($password) != $USER['password']) {
            } elseif ($password != $PLANET['name']) {
                $this->sendJSON(['message' => $LNG['ov_wrong_name']]);
            } else {
                if ($PLANET['planet_type'] == 1) {
                    $sql = "UPDATE %%PLANETS%% SET destruyed = :time WHERE id = :planetID;";
                    $db->update($sql, [
                        ':time'   => TIMESTAMP + 86400,
                        ':planetID' => $PLANET['id'],
                    ]);
                    $sql = "DELETE FROM %%PLANETS%% WHERE id = :lunaID;";
                    $db->delete($sql, [
                        ':lunaID' => $PLANET['id_luna']
                    ]);
                } else {
                    $sql = "UPDATE %%PLANETS%% SET id_luna = 0 WHERE id_luna = :planetID;";
                    $db->update($sql, [
                        ':planetID' => $PLANET['id'],
                    ]);
                    $sql = "DELETE FROM %%PLANETS%% WHERE id = :planetID;";
                    $db->delete($sql, [
                        ':planetID' => $PLANET['id'],
                    ]);
                }

                Session::load()->planetId = $USER['id_planet'];
                $this->sendJSON(['ok' => true, 'message' => $LNG['ov_planet_abandoned']]);
            }
        }
    }
}
