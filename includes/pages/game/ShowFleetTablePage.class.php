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


class ShowFleetTablePage extends AbstractGamePage
{
    public static $requireModule = MODULE_FLEET_TABLE;

    public function __construct()
    {
        parent::__construct();
    }

    public function createACS($fleetID, $fleetData)
    {
        $USER =& Singleton()->USER;

        $rand = mt_rand(100000, 999999999);
        $acsName = 'AG' . $rand;
        $acsCreator = $USER['id'];

        $db = Database::get();
        $sql = "INSERT INTO %%AKS%% SET name = :acsName, ankunft = :time, target = :target;";
        $db->insert($sql, [
            ':acsName' => $acsName,
            ':time' => $fleetData['fleet_start_time'],
            ':target' => $fleetData['fleet_end_id'],
        ]);

        // $sql = "SELECT `id` FROM %%AKS%% WHERE `name` = :acsName AND `target` = :target";
        // $acsID = $db->selectSingle($sql, [
        //     ':acsName' => $acsName,
        //     ':target' => $fleetData['fleet_end_id'],
        // ], 'id');

        $acsID = $db->lastInsertId();

        $sql = "INSERT INTO %%USERS_ACS%% SET acsID = :acsID, userID = :userID;";
        $db->insert($sql, [
            ':acsID' => $acsID,
            ':userID' => $acsCreator,
        ]);

        $sql = "UPDATE %%FLEETS%% SET fleet_group = :acsID WHERE fleet_id = :fleetID;";
        $db->update($sql, [
            ':acsID' => $acsID,
            ':fleetID' => $fleetID,
        ]);

        $sql = "UPDATE %%LOG_FLEETS%% SET fleet_group = :acsID WHERE fleet_id = :fleetID;";
        $db->update($sql, [
            ':acsID' => $acsID,
            ':fleetID' => $fleetID,
        ]);

        return [
            'name' => $acsName,
            'id' => $acsID,
        ];
    }

    public function loadACS($fleetData)
    {
        $USER =& Singleton()->USER;

        $db = Database::get();
        $sql = "SELECT id, name FROM %%USERS_ACS%% INNER JOIN %%AKS%% ON acsID = id"
            . " WHERE userID = :userID AND acsID = :acsID;";
        $acsResult = $db->selectSingle($sql, [
            ':userID' => $USER['id'],
            ':acsID' => $fleetData['fleet_group'],
        ]);

        return $acsResult;
    }

    public function getACSPageData($fleetID)
    {
        $USER =& Singleton()->USER;
        $LNG =& Singleton()->LNG;
        $db = Database::get();

        $sql = "SELECT fleet_start_time, fleet_end_id, fleet_group, fleet_mess FROM %%FLEETS%%"
            . " WHERE fleet_id = :fleetID;";
        $fleetData = $db->selectSingle($sql, [':fleetID' => $fleetID]);

        if ($db->rowCount() != 1) {
            return [];
        }

        if ($fleetData['fleet_mess'] == 1 || $fleetData['fleet_start_time'] <= TIMESTAMP) {
            return [];
        }

        if ($fleetData['fleet_group'] == 0) {
            $acsData = $this->createACS($fleetID, $fleetData);
        } else {
            $acsData = $this->loadACS($fleetData);
        }

        if (empty($acsData)) {
            return [];
        }

        $acsName = HTTP::_GP('acsName', '', UTF8_SUPPORT);
        if (!empty($acsName)) {
            if (!PlayerUtil::isNameValid($acsName)) {
                $this->sendJSON($LNG['fl_acs_newname_alphanum']);
            }

            $sql = "UPDATE %%AKS%% SET name = :acsName WHERE id = :acsID;";
            $db->update($sql, [
                ':acsName' => $acsName,
                ':acsID' => $acsData['id'],
            ]);
            $this->sendJSON(false);
        }

        $invitedUsers = [];

        $sql = "SELECT id, username FROM %%USERS_ACS%% INNER JOIN %%USERS%% ON userID = id WHERE acsID = :acsID;";
        $userResult = $db->select($sql, [':acsID' => $acsData['id']]);

        foreach ($userResult as $userRow) {
            $invitedUsers[$userRow['id']] = $userRow['username'];
        }

        $newUser = HTTP::_GP('username', '', UTF8_SUPPORT);
        $statusMessage = "";
        if (!empty($newUser)) {
            $sql = "SELECT id FROM %%USERS%% WHERE universe = :universe AND username = :username;";
            $newUserID = $db->selectSingle($sql, [
                ':universe' => Universe::current(),
                ':username' => $newUser,
            ], 'id');
            $blocked    = PlayerUtil::get_block_status($USER['id'], $newUserID);
            $blockedInverted = PlayerUtil::get_block_status($newUserID, $USER['id']);

            if (empty($newUserID)) {
                $statusMessage = $LNG['fl_player'] . " " . $newUser . " " . $LNG['fl_dont_exist'];
            } elseif (isset($invitedUsers[$newUserID])) {
                $statusMessage = $LNG['fl_player'] . " " . $newUser . " " . $LNG['fl_already_invited'];
            } elseif ($blocked['block_dm'] == 1 || $blockedInverted['block_dm'] == 1) {
                $statusMessage = $LNG['fl_player'] . " " . $newUser . " " . $LNG['fl_blocked'];
            } else {
                $statusMessage = $LNG['fl_player'] . " " . $newUser . " " . $LNG['fl_add_to_attack'];

                $sql = "INSERT INTO %%USERS_ACS%% SET acsID = :acsID, userID = :newUserID;";
                $db->insert($sql, [
                    ':acsID' => $acsData['id'],
                    ':newUserID' => $newUserID,
                ]);

                $invitedUsers[$newUserID] = $newUser;

                // get target player language while sending ACS invite instead of attack owner.
                $GetTargetLang = getLanguage(null, $newUserID);
                $inviteTitle = $GetTargetLang['fl_acs_invitation_title'];
                $inviteMessage = $GetTargetLang['fl_player'] . $USER['username']
                    . $GetTargetLang['fl_acs_invitation_message'];
                PlayerUtil::sendMessage(
                    $newUserID,
                    $USER['id'],
                    $USER['username'],
                    1,
                    $inviteTitle,
                    $inviteMessage,
                    TIMESTAMP
                );
            }
        }

        return [
            'invitedUsers' => $invitedUsers,
            'acsName' => $acsData['name'],
            'mainFleetID' => $fleetID,
            'statusMessage' => $statusMessage,
        ];
    }

    public function show()
    {
        $USER =& Singleton()->USER;
        $PLANET =& Singleton()->PLANET;
        $reslist =& Singleton()->reslist;
        $resource =& Singleton()->resource;
        $LNG =& Singleton()->LNG;
        $pricelist =& Singleton()->pricelist;
        $acsData = [];
        $FleetID = HTTP::_GP('fleetID', 0);
        $GetAction = HTTP::_GP('action', "");

        $db = Database::get();

        $this->tplObj->loadscript('flotten.js');

        if (!empty($FleetID) && !IsVacationMode($USER)) {
            switch ($GetAction) {
                case "sendfleetback":
                    FleetFunctions::sendFleetBack($USER, $FleetID);
                    break;
                case "acs":
                    $acsData = $this->getACSPageData($FleetID);
                    break;
            }
        }

        $techExpedition = $USER[$resource[124]];

        if ($techExpedition >= 1) {
            $activeExpedition = FleetFunctions::getCurrentFleets($USER['id'], 15, true);
            $maxExpedition = floor(sqrt($techExpedition));
        } else {
            $activeExpedition = 0;
            $maxExpedition = 0;
        }

        $maxFleetSlots = FleetFunctions::getMaxFleetSlots($USER);

        $targetGalaxy = HTTP::_GP('galaxy', (int)$PLANET['galaxy']);
        $targetSystem = HTTP::_GP('system', (int)$PLANET['system']);
        $targetPlanet = HTTP::_GP('planet', (int)$PLANET['planet']);
        $targetType = HTTP::_GP('planettype', (int)$PLANET['planet_type']);
        $targetMission = HTTP::_GP('target_mission', -1);

        $sql = "SELECT * FROM %%FLEETS%% WHERE fleet_owner = :userID AND fleet_mission <> :missile"
            . " ORDER BY fleet_end_time ASC;";
        $fleetResult = $db->select($sql, [
            ':userID' => $USER['id'],
            ':missile' => MISSION_MISSILE
        ]);

        $activeFleetSlots = $db->rowCount();

        $FlyingFleetList = [];

        foreach ($fleetResult as $fleetsRow) {
            $FleetList[$fleetsRow['fleet_id']] = FleetFunctions::unserialize($fleetsRow['fleet_array']);

            if ($fleetsRow['fleet_mission'] == MISSION_STATION && $fleetsRow['fleet_mess'] == FLEET_OUTWARD) {
                $returnTime = $fleetsRow['fleet_start_time'];
            } else {
                $returnTime = $fleetsRow['fleet_end_time'];
            }

            $FlyingFleetList[] = [
                'id'                => $fleetsRow['fleet_id'],
                'mission'           => $fleetsRow['fleet_mission'],
                'state'             => $fleetsRow['fleet_mess'],
                'no_returnable'     => $fleetsRow['fleet_no_m_return'],
                'startGalaxy'       => $fleetsRow['fleet_start_galaxy'],
                'startSystem'       => $fleetsRow['fleet_start_system'],
                'startPlanet'       => $fleetsRow['fleet_start_planet'],
                'startTime'         => _date($LNG['php_tdformat'], $fleetsRow['fleet_start_time'], $USER['timezone']),
                'endGalaxy'         => $fleetsRow['fleet_end_galaxy'],
                'endSystem'         => $fleetsRow['fleet_end_system'],
                'endPlanet'         => $fleetsRow['fleet_end_planet'],
                'metal'             => $fleetsRow['fleet_resource_metal'],
                'crystal'           => $fleetsRow['fleet_resource_crystal'],
                'deuterium'         => $fleetsRow['fleet_resource_deuterium'],
                'endTime'           => _date($LNG['php_tdformat'], $fleetsRow['fleet_end_time'], $USER['timezone']),
                'amount'            => pretty_number($fleetsRow['fleet_amount']),
                'returntime'        => $returnTime,
                'resttime'          => $returnTime - TIMESTAMP,
                'startTimestamp'    => $fleetsRow['start_time'],
                'FleetList'         => $FleetList[$fleetsRow['fleet_id']],
            ];
        }

        $FleetsOnPlanet = [];
        $shipinfos = [];
        foreach ($reslist['fleet'] as $FleetID) {
            $shipinfos[] = [
                'id' => $FleetID,
                'expo' => ($pricelist[$FleetID]['cost'][901] + $pricelist[$FleetID]['cost'][902]) * 5 / 1000,
                'capacity' => $pricelist[$FleetID]['capacity'],
            ];


            if ($PLANET[$resource[$FleetID]] == 0) {
                continue;
            }

            $FleetsOnPlanet[] = [
                'id' => $FleetID,
                'speed' => FleetFunctions::getFleetMaxSpeed($FleetID, $USER),
                'count' => $PLANET[$resource[$FleetID]],
            ];
        }

        require_once('includes/classes/class.FleetFunctions.php');

        $this->assign([
            'FleetsOnPlanet'        => $FleetsOnPlanet,
            'FlyingFleetList'       => $FlyingFleetList,
            'activeExpedition'      => $activeExpedition,
            'maxExpedition'         => $maxExpedition,
            'activeFleetSlots'      => $activeFleetSlots,
            'maxFleetSlots'         => $maxFleetSlots,
            'targetGalaxy'          => $targetGalaxy,
            'targetSystem'          => $targetSystem,
            'targetPlanet'          => $targetPlanet,
            'targetType'            => $targetType,
            'targetMission'         => $targetMission,
            'acsData'               => $acsData,
            'isVacation'            => IsVacationMode($USER),
            'bonusAttack'           => $USER[$resource[109]] * 10,
            'bonusShield'           => $USER[$resource[110]] * 10,
            'bonusDefensive'        => $USER[$resource[111]] * 10,
            'bonusCombustion'       => $USER[$resource[115]] * 10,
            'bonusImpulse'          => $USER[$resource[117]] * 20,
            'bonusHyperspace'       => $USER[$resource[118]] * 30,
            'maxExpo'               => FleetFunctions::calculateMaxFactorRes(Universe::current()),
            'shiptypes'             => $shipinfos
        ]);

        $this->display('page.fleetTable.default.tpl');
    }
}
