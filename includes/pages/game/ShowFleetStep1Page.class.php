<?php

/**
 *  2Moons
 *   by Jan-Otto Kröpke 2009-2016
 * For the full copyright and license information, please view the LICENSE
 * @package 2Moons
 * @author Jan-Otto Kröpke <slaver7@gmail.com>
 * @copyright 2009 Lucky
 * @copyright 2016 Jan-Otto Kröpke <slaver7@gmail.com>
 * @licence MIT
 * @version 1.8.0
 * @link https://github.com/jkroepke/2Moons
 */

class ShowFleetStep1Page extends AbstractGamePage
{
    public static int $requireModule = MODULE_FLEET_TABLE;

    public function __construct()
    {
        parent::__construct();
    }

    public function show(): void
    {
        $USER =& Singleton()->USER;
        $PLANET =& Singleton()->PLANET;
        $pricelist =& Singleton()->pricelist;
        $reslist =& Singleton()->reslist;
        $LNG =& Singleton()->LNG;
        $resource =& Singleton()->resource;
        $targetGalaxy = HTTP::_GP('galaxy', (int) $PLANET['galaxy']);
        $targetSystem = HTTP::_GP('system', (int) $PLANET['system']);
        $targetPlanet = HTTP::_GP('planet', (int) $PLANET['planet']);
        $targetType = HTTP::_GP('type', (int) $PLANET['planet_type']);

        $mission = HTTP::_GP('target_mission', -1);

        $Fleet = [];
        $FleetRoom = 0;
        foreach ($reslist['fleet'] as $id => $ShipID) {
            $amount = max(0, round(HTTP::_GP('ship' . $ShipID, 0.0, 0.0)));

            if ($amount < 1 || $ShipID == 212) {
                continue;
            }

            $Fleet[$ShipID] = $amount;
            $FleetRoom += $pricelist[$ShipID]['capacity'] * $amount;
        }

        if (empty($Fleet)) {
            FleetFunctions::gotoFleetPage();
        }

        $FleetData = [
            'fleetroom'         => floatToString($FleetRoom),
            'gamespeed'         => FleetFunctions::getGameSpeedFactor(),
            'fleetspeedfactor'  => 1,
            'planet'            => [
                'galaxy' => $PLANET['galaxy'],
                'system' => $PLANET['system'],
                'planet' => $PLANET['planet'],
                'planet_type' => $PLANET['planet_type']
            ],
            'maxspeed'          => FleetFunctions::getFleetMaxSpeed($Fleet, $USER),
            'ships'             => FleetFunctions::getFleetShipInfo($Fleet, $USER),
            'fleetMinDuration'  => MIN_FLEET_TIME
        ];

        $token = getRandomString();
        $session = Session::load();
        $fleet = $session->fleet;
        $fleet[$token] = [
            'time'      => TIMESTAMP,
            'fleet'     => $Fleet,
            'fleetRoom' => $FleetRoom,
            'predefinedResources'=>[
                'met'=>  HTTP::_GP('met_storage', ""),
                'krist'=>  HTTP::_GP('krist_storage', ""),
                'deut'=>  HTTP::_GP('deut_storage', "")
            ]
        ];
        $session->fleet = $fleet;
        $session->save();

        $shortcutList = $this->GetUserShotcut();
        $colonyList = $this->GetColonyList();
        $ACSList = $this->GetAvalibleACS();

        if (!empty($shortcutList)) {
            $shortcutAmount = max(array_keys($shortcutList));
        } else {
            $shortcutAmount = 0;
        }

        $this->tplObj->loadscript('flotten.js');
        $this->tplObj->execscript('updateVars();FleetTime();var relativeTime3 = Math.floor(Date.now() / 1000);'
            . 'window.setInterval(function() {if(relativeTime3 < Math.floor(Date.now() / 1000)) {FleetTime();'
            . 'relativeTime3++;}}, 25);');



        $this->assign([
            'token'         => $token,
            'mission'       => $mission,
            'shortcutList'  => $shortcutList,
            'shortcutMax'   => $shortcutAmount,
            'colonyList'    => $colonyList,
            'ACSList'       => $ACSList,
            'galaxy'        => $targetGalaxy,
            'system'        => $targetSystem,
            'planet'        => $targetPlanet,
            'type'          => $targetType,
            'speedSelect'   => FleetFunctions::$allowedSpeed,
            'typeSelect'    => [1 => $LNG['type_planet_1'], 2 => $LNG['type_planet_2'], 3 => $LNG['type_planet_3']],
            'fleetdata'     => $FleetData,
            'config'        => [
                'uni_type'     => Config::get()->uni_type,
                'galaxy_type'  => Config::get()->galaxy_type,
                'max_galaxy'    => Config::get()->max_galaxy,
                'max_system'    => Config::get()->max_system,
                'max_planets'    => Config::get()->max_planets,
            ]
        ]);

        //check if all ships are available
        foreach ($Fleet as $Ship => $Count) {
            if ($Count > $PLANET[$resource[$Ship]]) {
                $this->printMessage($LNG['fl_not_all_ship_avalible'], [[
                    'label' => $LNG['sys_back'],
                    'url'   => 'game.php?page=fleetTable',
                ]]);
            }
        }
        $this->display('page.fleetStep1.default.tpl');
    }

    public function saveShortcuts(): void
    {
        $USER =& Singleton()->USER;
        $LNG =& Singleton()->LNG;
        if (!isset($_REQUEST['shortcut'])) {
            $this->sendJSON($LNG['fl_shortcut_saved']);
        }

        $db = Database::get();

        $ShortcutData = $_REQUEST['shortcut'];
        $ShortcutUser = $this->GetUserShotcut();
        foreach ($ShortcutData as $ID => $planetData) {
            if (!isset($ShortcutUser[$ID])) {
                if (
                    empty($planetData['name']) || empty($planetData['galaxy']) || empty($planetData['system'])
                    || empty($planetData['planet'])
                ) {
                    continue;
                }

                $sql = "INSERT INTO %%SHORTCUTS%% SET ownerID = :userID, name = :name, galaxy = :galaxy,"
                    . " system = :system, planet = :planet, type = :type;";
                $db->insert($sql, [
                    ':userID' => $USER['id'],
                    ':name' => substr($planetData['name'], 0, 32),
                    ':galaxy' => $planetData['galaxy'],
                    ':system' => $planetData['system'],
                    ':planet' => $planetData['planet'],
                    ':type' => $planetData['type'],
                ]);
            } elseif (empty($planetData['name'])) {
                $sql = "DELETE FROM %%SHORTCUTS%% WHERE shortcutID = :shortcutID AND ownerID = :userID;";
                $db->delete($sql, [
                    ':shortcutID' => $ID,
                    ':userID' => $USER['id'],
                ]);
            } else {
                $planetData['ownerID'] = $USER['id'];
                $planetData['shortcutID'] = $ID;
                if ($planetData != $ShortcutUser[$ID]) {
                    $sql = "UPDATE %%SHORTCUTS%% SET name = :name, galaxy = :galaxy, system = :system,"
                        . " planet = :planet, type = :type WHERE shortcutID = :shortcutID AND ownerID = :userID;";
                    $db->update($sql, [
                        ':userID' => $USER['id'],
                        ':name' => substr($planetData['name'], 0, 32),
                        ':galaxy' => $planetData['galaxy'],
                        ':system' => $planetData['system'],
                        ':planet' => $planetData['planet'],
                        ':type' => $planetData['type'],
                        ':shortcutID' => $ID,
                    ]);
                }
            }
        }

        $this->sendJSON($LNG['fl_shortcut_saved']);
    }

    private function GetColonyList()
    {
        $PLANET =& Singleton()->PLANET;
        $USER =& Singleton()->USER;
        $ColonyList = [];

        foreach ($USER['PLANETS'] as $CurPlanetID => $CurPlanet) {
            if ($PLANET['id'] == $CurPlanet['id']) {
                continue;
            }

            $ColonyList[] = [
                'name'      => $CurPlanet['name'],
                'galaxy'    => $CurPlanet['galaxy'],
                'system'    => $CurPlanet['system'],
                'planet'    => $CurPlanet['planet'],
                'type'      => $CurPlanet['planet_type'],
            ];
        }

        return $ColonyList;
    }

    private function GetUserShotcut()
    {
        $USER =& Singleton()->USER;

        if (!isModuleAvailable(MODULE_SHORTCUTS)) {
            return [];
        }

        $db = Database::get();

        $sql = "SELECT * FROM %%SHORTCUTS%% WHERE ownerID = :userID;";
        $ShortcutResult = $db->select($sql, [':userID'   => $USER['id']]);

        $ShortcutList = [];

        foreach ($ShortcutResult as $ShortcutRow) {
            $ShortcutList[$ShortcutRow['shortcutID']] = $ShortcutRow;
        }

        return $ShortcutList;
    }

    private function GetAvalibleACS()
    {
        $USER =& Singleton()->USER;

        $db = Database::get();

        $ACSList = [];

        $sql = "SELECT acs.id, acs.name, planet.galaxy, planet.system, planet.planet, planet.planet_type
		FROM %%USERS_ACS%%
		INNER JOIN %%AKS%% acs ON acsID = acs.id
		INNER JOIN %%PLANETS%% planet ON planet.id = acs.target
		WHERE userID = :userID AND :maxFleets > (SELECT COUNT(*) FROM %%FLEETS%% WHERE fleet_group = acsID);";
        $ACSResult = $db->select($sql, [
            ':userID' => $USER['id'],
            ':maxFleets' => Config::get()->max_fleets_per_acs,
        ]);

        if (!empty($ACSResult)) {
            foreach ($ACSResult as $ACSRow) {
                $sql = "SELECT acsID
                FROM %%USERS_ACS%%
                WHERE userID = :userID AND acsID = :aksID
                AND ( :maxParticipants > (SELECT COUNT(DISTINCT fleet_owner) FROM %%FLEETS%% WHERE fleet_group = :aksID)
                OR 1 = (SELECT COUNT(DISTINCT fleet_owner) FROM %%FLEETS%% WHERE fleet_group = :aksID AND fleet_owner = :userID) );";
                $ACSResult2 = $db->select($sql, [
                    ':userID' => $USER['id'],
                    ':aksID' => $ACSRow['id'],
                    ':maxParticipants' => Config::get()->max_participants_per_acs,
                ]);

                if (!empty($ACSResult2)) {
                    $ACSList[] = $ACSRow;
                }
            }
        }

        return $ACSList;
    }

    public function checkTarget(): void
    {
        $PLANET =& Singleton()->PLANET;
        $LNG =& Singleton()->LNG;
        $USER =& Singleton()->USER;
        $resource =& Singleton()->resource;
        $targetGalaxy = HTTP::_GP('galaxy', 0);
        $targetSystem = HTTP::_GP('system', 0);
        $targetPlanet = HTTP::_GP('planet', 0);
        $targetPlanetType = HTTP::_GP('planet_type', 1);
        $koloShipInFleet = HTTP::_GP('kolo', 0);

        if (
            $targetGalaxy == $PLANET['galaxy'] && $targetSystem == $PLANET['system']
            && $targetPlanet == $PLANET['planet'] && $targetPlanetType == $PLANET['planet_type']
        ) {
            $this->sendJSON($LNG['fl_error_same_planet']);
        }

        // If target is not expedition
        if ($targetPlanet != Config::get()->max_planets + 1) {
            $db = Database::get();
            $sql = "SELECT u.id, u.urlaubs_modus, u.user_lastip, u.authattack,
            	p.destruyed, p.der_metal, p.der_crystal, p.tf_active, p.destruyed
                FROM %%USERS%% as u, %%PLANETS%% as p WHERE
                p.universe = :universe AND
                p.galaxy = :targetGalaxy AND
                p.system = :targetSystem AND
                p.planet = :targetPlanet  AND
                p.planet_type = :targetType AND
                u.id = p.id_owner;";

            $planetData = $db->selectSingle($sql, [
                ':universe' => Universe::current(),
                ':targetGalaxy' => $targetGalaxy,
                ':targetSystem' => $targetSystem,
                ':targetPlanet' => $targetPlanet,
                ':targetType' => (($targetPlanetType == 2) ? 1 : $targetPlanetType),
            ]);

            if ($targetPlanetType == 3 && !isset($planetData)) {
                $this->sendJSON($LNG['fl_error_no_moon']);
            }

            if ($targetPlanetType == 1 && !isset($planetData) && !$koloShipInFleet) {
                $this->sendJSON($LNG['fl_error_not_avalible']);
            }

            if ($targetPlanetType != 2 && !empty($planetData['urlaubs_modus'])) {
                $this->sendJSON($LNG['fl_in_vacation_player']);
            }

            if (!empty($planetData)) {
                if (
                    $planetData['id'] != $USER['id'] && Config::get()->adm_attack == 1
                    && $planetData['authattack'] > $USER['authlevel']
                ) {
                    $this->sendJSON($LNG['fl_admin_attack']);
                }
            }

            if (!empty($planetData)) {
                if ($planetData['destruyed'] != 0 && $targetPlanetType != 2) {
                    $this->sendJSON($LNG['fl_error_not_avalible']);
                }
            }

            if ($targetPlanetType == 2 && (empty($planetData) || ($planetData['der_metal'] + $planetData['der_crystal']) == 0 &&
                $planetData['tf_active'] == 0)) {
                $this->sendJSON($LNG['fl_error_empty_derbis']);
            }

            // $sql = 'SELECT (
			// 	(SELECT COUNT(*) FROM %%MULTI%% WHERE userID = :userID) +
			// 	(SELECT COUNT(*) FROM %%MULTI%% WHERE userID = :dataID)
			// ) as count;';

            // if (!empty($planetData)) {
            //     $multiCount = $db->selectSingle($sql, [
            //         ':userID' => $USER['id'],
            //         ':dataID' => $planetData['id'],
            //     ], 'count');
            // }

            // if (
            //     ENABLE_MULTIALERT && $USER['id'] != $planetData['id'] && $USER['authlevel'] != AUTH_ADM
            //     && $USER['user_lastip'] == $planetData['user_lastip'] && $multiCount != 2
            // ) {
            //     $this->sendJSON($LNG['fl_multi_alarm']);
            // }
        } else {
            if ($USER[$resource[124]] == 0) {
                $this->sendJSON($LNG['fl_expedition_tech_required']);
            }

            $activeExpedition = FleetFunctions::getCurrentFleets($USER['id'], 15, true);

            if ($activeExpedition >= FleetFunctions::getExpeditionLimit($USER)) {
                $this->sendJSON($LNG['fl_no_expedition_slot']);
            }
        }

        $this->sendJSON('OK');
    }
}
