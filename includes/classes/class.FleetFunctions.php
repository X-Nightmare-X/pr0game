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

class FleetFunctions
{
    public static $allowedSpeed = [
        10 => 100,
        9 => 90,
        8 => 80,
        7 => 70,
        6 => 60,
        5 => 50,
        4 => 40,
        3 => 30,
        2 => 20,
        1 => 10
    ];

    private static function getShipConsumption($Ship, $Player)
    {
        global $pricelist;

        return (
            ($Player['impulse_motor_tech'] >= 5 && $Ship == 202)
                || ($Player['hyperspace_motor_tech'] >= 8 && $Ship == 211)
        ) ? $pricelist[$Ship]['consumption2'] : $pricelist[$Ship]['consumption'];
    }

    private static function onlyShipByID($Ships, $ShipID)
    {
        return isset($Ships[$ShipID]) && count($Ships) === 1;
    }

    private static function getShipSpeed($Ship, $Player)
    {
        global $pricelist;

        $techSpeed = $pricelist[$Ship]['tech'];

        if ($techSpeed == 4) {
            $techSpeed = $Player['impulse_motor_tech'] >= 5 ? 2 : 1;
        }
        if ($techSpeed == 5) {
            $techSpeed = $Player['hyperspace_motor_tech'] >= 8 ? 3 : 2;
        }
        if ($techSpeed == 6) {
            if ($Player['hyperspace_motor_tech'] >= 15) {
                $techSpeed = 3;
            } elseif ($Player['impulse_motor_tech'] >= 17) {
                $techSpeed = 2;
            } else {
                $techSpeed = 1;
            }
        }

        $base_speed = $pricelist[$Ship]['speed'];

        if ($Player['impulse_motor_tech'] >= 5 && $Ship == 202) {
            $base_speed = $pricelist[$Ship]['speed2'];
        }
        if ($Player['hyperspace_motor_tech'] >= 8 && $Ship == 211) {
            $base_speed = $pricelist[$Ship]['speed2'];
        }
        if ($Player['impulse_motor_tech'] >= 17 && $Ship == 209) {
            $base_speed = $pricelist[$Ship]['speed2'];
        }
        if ($Player['hyperspace_motor_tech'] >= 15 && $Ship == 209) {
            $base_speed = $pricelist[$Ship]['speed3'];
        }


        switch ($techSpeed) {
            case 1:
                $speed = $base_speed * (1 + (0.1 * $Player['combustion_tech']));
                break;
            case 2:
                $speed = $base_speed * (1 + (0.2 * $Player['impulse_motor_tech']));
                break;
            case 3:
                $speed = $base_speed * (1 + (0.3 * $Player['hyperspace_motor_tech']));
                break;
            default:
                $speed = 0;
                break;
        }

        return $speed;
    }

    public static function getExpeditionLimit($USER)
    {
        return floor(sqrt($USER[$GLOBALS['resource'][124]]));
    }

    public static function getMissileRange($Level)
    {
        return max(($Level * 5) - 1, 0);
    }

    public static function checkUserSpeed($speed)
    {
        return isset(self::$allowedSpeed[$speed]);
    }

    public static function getTargetDistance($start, $target)
    {
        if ($start[0] != $target[0]) {
            return abs($start[0] - $target[0]) * 20000;
        }

        if ($start[1] != $target[1]) {
            return abs($start[1] - $target[1]) * 95 + 2700;
        }

        if ($start[2] != $target[2]) {
            return abs($start[2] - $target[2]) * 5 + 1000;
        }

        return 5;
    }

    public static function getMissionDuration($SpeedFactor, $MaxFleetSpeed, $Distance, $GameSpeed, $USER)
    {
        $SpeedFactor = (3500 / ($SpeedFactor * 0.1));
        $SpeedFactor *= pow($Distance * 10 / $MaxFleetSpeed, 0.5);
        $SpeedFactor += 10;
        $SpeedFactor    /= $GameSpeed;

        if (isset($USER['factor']['FlyTime'])) {
            $SpeedFactor *= max(0, 1 + $USER['factor']['FlyTime']);
        }

        return max($SpeedFactor, MIN_FLEET_TIME);
    }

    public static function getMIPDuration($startSystem, $targetSystem)
    {
        $Distance = abs($startSystem - $targetSystem);
        $Duration = max(round((30 + 60 * $Distance) / self::getGameSpeedFactor()), MIN_FLEET_TIME);

        return $Duration;
    }

    public static function getGameSpeedFactor()
    {
        return Config::get()->fleet_speed / 2500;
    }

    public static function getMaxFleetSlots($USER)
    {
        global $resource;
        return 1 + $USER[$resource[108]];
    }

    public static function getFleetRoom($Fleet)
    {
        global $pricelist, $USER;
        $FleetRoom = 0;
        foreach ($Fleet as $ShipID => $amount) {
            $FleetRoom += $pricelist[$ShipID]['capacity'] * $amount;
        }
        return $FleetRoom;
    }

    public static function getFleetMaxSpeed($Fleets, $Player)
    {
        if (empty($Fleets)) {
            return 0;
        }

        $FleetArray = (!is_array($Fleets)) ? [$Fleets => 1] : $Fleets;
        $speedalls = [];

        foreach ($FleetArray as $Ship => $Count) {
            $speedalls[$Ship] = self::getShipSpeed($Ship, $Player);
        }

        return min($speedalls);
    }

    public static function getFleetConsumption($FleetArray, $MissionDuration, $MissionDistance, $Player, $GameSpeed)
    {
        $consumption = 0;

        foreach ($FleetArray as $Ship => $Count) {
            $ShipSpeed = self::getShipSpeed($Ship, $Player);
            $ShipConsumption = self::getShipConsumption($Ship, $Player);

            $spd = 35000 / max($MissionDuration * $GameSpeed - 10, 1) * sqrt($MissionDistance * 10 / $ShipSpeed);
            $basicConsumption = $ShipConsumption * $Count;
            $consumption += $basicConsumption * $MissionDistance / 35000 * (($spd / 10) + 1) * (($spd / 10) + 1);
        }
        return (round($consumption) + 1);
    }

    public static function getFleetMissions($USER, $MisInfo, $Planet)
    {
        global $resource;
        $Missions = self::getAvailableMissions($USER, $MisInfo, $Planet);
        $stayBlock = [];
        $exchange = false;

        $haltSpeed = Config::get($USER['universe'])->halt_speed;

        if (in_array(MISSION_EXPEDITION, $Missions)) {
            for ($i = 1; $i <= $USER[$resource[124]]; $i++) {
                $stayBlock[$i] = round($i / $haltSpeed, 2);
            }
        } elseif (in_array(MISSION_HOLD, $Missions)) {
            $stayBlock = [1 => 1, 2 => 2, 4 => 4, 8 => 8, 12 => 12, 16 => 16, 32 => 32];
        } elseif (in_array(MISSION_TRADE, $Missions)) {
            $stayBlock = [1 => 1, 2 => 2, 4 => 4, 8 => 8, 12 => 12, 24 => 24, 48 => 48,48 => 48, 168 => 168];
            $exchange = true;
        }

        return ['MissionSelector' => $Missions, 'StayBlock' => $stayBlock, 'Exchange' => $exchange];
    }

    /*
     *
     * Unserialize an Fleetstring to an array
     *
     * @param string
     *
     * @return array
     *
     */

    public static function unserialize($fleetAmount)
    {
        $fleetTyps = explode(';', $fleetAmount);

        $fleetAmount = [];

        foreach ($fleetTyps as $fleetTyp) {
            $temp = explode(',', $fleetTyp);

            if (empty($temp[0])) {
                continue;
            }

            if (!isset($fleetAmount[$temp[0]])) {
                $fleetAmount[$temp[0]] = 0;
            }

            $fleetAmount[$temp[0]] += $temp[1];
        }

        return $fleetAmount;
    }

    public static function getACSDuration($acsId)
    {
        if (empty($acsId)) {
            return 0;
        }

        $sql = 'SELECT ankunft FROM %%AKS%% WHERE id = :acsId;';
        $acsEndTime = Database::get()->selectSingle($sql, array(
            ':acsId'    => $acsId
        ), 'ankunft');

        return empty($acsEndTime) ? $acsEndTime - TIMESTAMP : 0;
    }

    public static function setACSTime($timeDifference, $acsId)
    {
        if (empty($acsId)) {
            throw new InvalidArgumentException('Missing acsId on ' . __CLASS__ . '::' . __METHOD__);
        }

        $db = Database::get();

        $sql = 'UPDATE %%AKS%% SET ankunft = ankunft + :time WHERE id = :acsId;';
        $db->update($sql, array(
            ':time'     => $timeDifference,
            ':acsId'    => $acsId,
        ));

        $sql = 'UPDATE %%FLEETS%%, %%FLEETS_EVENT%% SET
		fleet_start_time = fleet_start_time + :time,
		fleet_end_stay   = fleet_end_stay + :time,
		fleet_end_time   = fleet_end_time + :time,
		time             = time + :time
		WHERE fleet_group = :acsId AND fleet_id = fleetID;';

        $db->update($sql, array(
            ':time'     => $timeDifference,
            ':acsId'    => $acsId,
        ));

        return true;
    }

    public static function getCurrentFleets($userId, $fleetMission = 10, $thisMission = false)
    {
        if ($thisMission) {
            $sql = 'SELECT COUNT(*) as state
			FROM %%FLEETS%%
			WHERE fleet_owner = :userId
			AND fleet_mission = :fleetMission;';
        } else {
            $sql = 'SELECT COUNT(*) as state
			FROM %%FLEETS%%
			WHERE fleet_owner = :userId
			AND fleet_mission != :fleetMission;';
        }

        $ActualFleets = Database::get()->selectSingle($sql, array(
            ':userId'       => $userId,
            ':fleetMission' => $fleetMission,
        ));
        return $ActualFleets['state'];
    }

    public static function sendFleetBack($USER, $FleetID)
    {
        $db = Database::get();

        $sql = 'SELECT start_time, fleet_start_time, fleet_mission, fleet_group, fleet_owner, fleet_mess '
                . 'FROM %%FLEETS%% WHERE fleet_id = :fleetId AND fleet_no_m_return = :fleetNoMReturn;';
        $fleetResult = $db->selectSingle($sql, [
            ':fleetId'  => $FleetID,
            ':fleetNoMReturn'  => 0,
        ]);

        if (empty($fleetResult['start_time'])) {
            $fleetResult['start_time'] = 0;
        }
        if (empty($fleetResult['fleet_start_time'])) {
            $fleetResult['fleet_start_time'] = 0;
        }
        if (empty($fleetResult['fleet_mission'])) {
            $fleetResult['fleet_mission'] = 0;
        }
        if (empty($fleetResult['fleet_group'])) {
            $fleetResult['fleet_group'] = 0;
        }
        if (empty($fleetResult['fleet_owner'])) {
            $fleetResult['fleet_owner'] = 0;
        }
        if (empty($fleetResult['fleet_mess'])) {
            $fleetResult['fleet_mess'] = 0;
        }

        if ($fleetResult['fleet_owner'] != $USER['id'] || $fleetResult['fleet_mess'] == FLEET_RETURN) {
            return false;
        }

        $sqlWhere = 'fleet_id';

        if ($fleetResult['fleet_mission'] == MISSION_ATTACK && $fleetResult['fleet_group'] != 0) {
            $sql = 'SELECT COUNT(*) as state FROM %%USERS_ACS%% WHERE acsID = :acsId;';
            $isInGroup = $db->selectSingle($sql, array(
                ':acsId'    => $fleetResult['fleet_group'],
            ), 'state');

            if ($isInGroup) {
                $sql = 'DELETE %%AKS%%, %%USERS_ACS%%
				FROM %%AKS%%
				LEFT JOIN %%USERS_ACS%% ON acsID = %%AKS%%.id
				WHERE %%AKS%%.id = :acsId;';

                $db->delete($sql, array(
                    ':acsId'    => $fleetResult['fleet_group']
                ));

                $FleetID = $fleetResult['fleet_group'];
                $sqlWhere = 'fleet_group';
            }
        }

        if (
            ($fleetResult['fleet_mission'] == MISSION_HOLD || $fleetResult['fleet_mission'] == MISSION_TRADE)
            && $fleetResult['fleet_mess'] == FLEET_HOLD
        ) {
            $fleetEndTime = ($fleetResult['fleet_start_time'] - $fleetResult['start_time']) + TIMESTAMP;
        } else {
            $fleetEndTime = (TIMESTAMP - $fleetResult['start_time']) + TIMESTAMP;
        }

        $sql = 'UPDATE %%FLEETS%%, %%FLEETS_EVENT%% SET
		fleet_group			= :fleetGroup,
		fleet_end_stay		= :endStayTime,
		fleet_end_time		= :endTime,
		fleet_mess			= :fleetState,
		hasCanceled			= :hasCanceled,
		time				= :endTime
		WHERE ' . $sqlWhere . ' = :id AND fleet_id = fleetID;';

        $db->update($sql, array(
            ':id'           => $FleetID,
            ':endStayTime'  => TIMESTAMP,
            ':endTime'      => $fleetEndTime,
            ':fleetGroup'   => 0,
            ':hasCanceled'  => 1,
            ':fleetState'   => FLEET_RETURN
        ));

        $sql = 'UPDATE %%LOG_FLEETS%% SET
		fleet_end_stay	= :endStayTime,
		fleet_end_time	= :endTime,
		fleet_mess		= :fleetState,
		fleet_state		= 2,
		hasCanceled		= :hasCanceled
		WHERE ' . $sqlWhere . ' = :id;';

        $db->update($sql, [
            ':id'           => $FleetID,
            ':endStayTime'  => TIMESTAMP,
            ':endTime'      => $fleetEndTime,
            ':fleetState'   => FLEET_RETURN,
            ':hasCanceled'  => 1
        ]);

        return true;
    }

    public static function getFleetShipInfo($FleetArray, $Player)
    {
        $FleetInfo = [];
        foreach ($FleetArray as $ShipID => $Amount) {
            $FleetInfo[$ShipID] = [
                'consumption' => self::getShipConsumption($ShipID, $Player),
                'speed' => self::getFleetMaxSpeed($ShipID, $Player),
                'amount' => floatToString($Amount)
            ];
        }
        return $FleetInfo;
    }

    public static function gotoFleetPage($Code = 0)
    {
        global $LNG;
        if (Config::get()->debug == 1) {
            $temp = debug_backtrace();
            echo sprintf(
                "%s on %d | Code: %d | Error: %s",
                str_replace($_SERVER["DOCUMENT_ROOT"], '.', $temp[0]['file']),
                $temp[0]['line'],
                $Code,
                (isset($LNG['fl_send_error'][$Code]) ? $LNG['fl_send_error'][$Code] : '')
            );
            exit;
        }

        HTTP::redirectTo('game.php?page=fleetTable&code=' . $Code);
    }

    public static function getAvailableMissions($USER, $MissionInfo, $GetInfoPlanet)
    {
        $YourPlanet = (!empty($GetInfoPlanet['id_owner']) && $GetInfoPlanet['id_owner'] == $USER['id']) ? true : false;
        $UsedPlanet = (!empty($GetInfoPlanet['id_owner'])) ? true : false;
        $availableMissions = [];

        if (
            $MissionInfo['planet'] == (Config::get($USER['universe'])->max_planets + 1)
            && isModuleAvailable(MODULE_MISSION_EXPEDITION)
        ) {
            $availableMissions[] = MISSION_EXPEDITION;
        } elseif (
            $MissionInfo['planet'] == (Config::get($USER['universe'])->max_planets + 2)
            && isModuleAvailable(MODULE_MISSION_TRADE)
        ) {
            $availableMissions[] = MISSION_TRADE;
        } elseif ($MissionInfo['planettype'] == 2) {
            if (
                (isset($MissionInfo['Ship'][209]) || isset($MissionInfo['Ship'][219]))
                && isModuleAvailable(MODULE_MISSION_RECYCLE) && !($GetInfoPlanet['der_metal'] == 0
                && $GetInfoPlanet['der_crystal'] == 0)
            ) {
                $availableMissions[] = MISSION_RECYCLING;
            }
        } else {
            if (!$UsedPlanet) {
                if (
                    isset($MissionInfo['Ship'][208]) && $MissionInfo['planettype'] == 1
                    && isModuleAvailable(MODULE_MISSION_COLONY)
                ) {
                    $availableMissions[] = MISSION_COLONISATION;
                }
            } else {
                if (isModuleAvailable(MODULE_MISSION_TRANSPORT)) {
                    $MissionInfo['planet'];
                    $availableMissions[] = MISSION_TRANSPORT;
                }

                if (
                    !$YourPlanet && self::onlyShipByID($MissionInfo['Ship'], 210)
                    && isModuleAvailable(MODULE_MISSION_SPY)
                ) {
                    $availableMissions[] = MISSION_SPY;
                }

                if (!$YourPlanet) {
                    if (isModuleAvailable(MODULE_MISSION_TRANSFER)) {
                        $availableMissions[] = MISSION_TRANSFER;
                    }

                    if (isModuleAvailable(MODULE_MISSION_ATTACK)) {
                        $availableMissions[] = MISSION_ATTACK;
                    }
                    if (isModuleAvailable(MODULE_MISSION_HOLD)) {
                        $availableMissions[] = MISSION_HOLD;
                    }
                } elseif (isModuleAvailable(MODULE_MISSION_STATION)) {
                    $availableMissions[] = MISSION_STATION;
                }

                if (
                    !empty($MissionInfo['IsAKS']) && !$YourPlanet && isModuleAvailable(MODULE_MISSION_ATTACK)
                    && isModuleAvailable(MODULE_MISSION_ACS)
                ) {
                    $availableMissions[] = MISSION_ACS;
                }

                if (
                    !$YourPlanet && $MissionInfo['planettype'] == 3 && isset($MissionInfo['Ship'][214])
                    && isModuleAvailable(MODULE_MISSION_DESTROY)
                ) {
                    $availableMissions[] = MISSION_DESTRUCTION;
                }
            }
        }

        return $availableMissions;
    }

    public static function checkBash($Target)
    {
        global $USER;

        if (!BASH_ON) {
            return false;
        }

        $PlanetOwner = Database::get()->selectSingle('SELECT id_owner FROM %%PLANETS%% where id = :id', [
            ':id'       => $Target,
        ], 'id_owner');

        $targetAllianceId = Database::get()->selectSingle('SELECT ally_id FROM %%USERS%% where id = :id', [
            ':id'       => $PlanetOwner,
        ], 'ally_id');

        $targetOnlineTime = Database::get()->selectSingle('SELECT onlinetime FROM %%USERS%% where id = :id', [
            ':id'       => $PlanetOwner,
        ], 'onlinetime');

        if ($targetOnlineTime < TIMESTAMP - INACTIVE) {
            return false;
        }

        $sql = 'SELECT accept, request_time from %%DIPLO%%
        where (owner_1 = :allyA and owner_2 = :allyD)
        or (owner_1 = :allyD and owner_2 = :allyA)
        and level = 5';

        $War = Database::get()->selectSingle($sql, [
            ':allyA'    => $USER['ally_id'],
            ':allyD'    => $targetAllianceId
        ]);

        if (!empty($War) && ($War['accept'] == 1 || $War['request_time'] < TIMESTAMP - 86400)) {
            return false;
        }

        $sql = 'SELECT fleet_array
		FROM %%LOG_FLEETS%%
		WHERE fleet_owner = :fleetOwner
		AND fleet_end_id = :fleetEndId
		AND fleet_start_time > :fleetStartTime
		AND fleet_mission IN (:att,:aks,:dest)
        AND fleet_group = 0
        AND hasCanceled = 0;';

        $log_fleets = Database::get()->select($sql, [
            ':fleetOwner'       => $USER['id'],
            ':fleetEndId'       => $Target,
            ':fleetStartTime'   => (TIMESTAMP - BASH_TIME),
            ':att'              => MISSION_ATTACK,
            ':aks'              => MISSION_ACS,
            ':dest'             => MISSION_DESTRUCTION,
        ]);

        $Count = 0;
        foreach ($log_fleets as $log_fleet) {
            $fleetArray = FleetFunctions::unserialize($log_fleet['fleet_array']);
            if (count($fleetArray) != 1 || !array_key_exists(210, $fleetArray)) {
                $Count++;
            }
        }

        $sql = 'SELECT COUNT(DISTINCT fleet_group) as state
		FROM %%LOG_FLEETS%%
		WHERE fleet_owner = :fleetOwner
		AND fleet_end_id = :fleetEndId
		AND fleet_start_time > :fleetStartTime
		AND fleet_mission IN (:att,:aks,:dest)
        AND fleet_group != 0
        AND hasCanceled = 0;';

        $Count2 = Database::get()->selectSingle($sql, [
            ':fleetOwner'       => $USER['id'],
            ':fleetEndId'       => $Target,
            ':fleetStartTime'   => (TIMESTAMP - BASH_TIME),
            ':att'              => MISSION_ATTACK,
            ':aks'              => MISSION_ACS,
            ':dest'             => MISSION_DESTRUCTION,
        ]);

        return ($Count + $Count2['state']) >= BASH_COUNT;
    }

    public static function sendFleet(
        $fleetArray,
        $fleetMission,
        $fleetStartOwner,
        $fleetStartPlanetID,
        $fleetStartPlanetGalaxy,
        $fleetStartPlanetSystem,
        $fleetStartPlanetPlanet,
        $fleetStartPlanetType,
        $fleetTargetOwner,
        $fleetTargetPlanetID,
        $fleetTargetPlanetGalaxy,
        $fleetTargetPlanetSystem,
        $fleetTargetPlanetPlanet,
        $fleetTargetPlanetType,
        $fleetResource,
        $fleetStartTime,
        $fleetStayTime,
        $fleetEndTime,
        $fleetGroup = 0,
        $missileTarget = 0,
        $fleetNoMReturn = 0,
        $consumption = 0
    ) {
        global $resource;
        $fleetShipCount = array_sum($fleetArray);
        $fleetData = [];

        $db = Database::get();

        $params = [':planetId' => $fleetStartPlanetID];

        $planetQuery = [];
        foreach ($fleetArray as $ShipID => $ShipCount) {
            $fleetData[] = $ShipID . ',' . floatToString($ShipCount);
            $planetQuery[] = $resource[$ShipID] . " = " . $resource[$ShipID] . " - :" . $resource[$ShipID];

            $params[':' . $resource[$ShipID]] = floatToString($ShipCount);
        }

        if ($consumption > 0) {
            $planetQuery[] = $resource[903] . " = " . $resource[903] . " - :" . $resource[903];
            $params[':' . $resource[903]] = $consumption;
        }


        $sql = 'UPDATE %%PLANETS%% SET ' . implode(', ', $planetQuery) . ' WHERE id = :planetId;';

        $db->update($sql, $params);

        $sql = 'INSERT INTO %%FLEETS%% SET
		fleet_owner					= :fleetStartOwner,
		fleet_target_owner			= :fleetTargetOwner,
		fleet_mission				= :fleetMission,
		fleet_amount				= :fleetShipCount,
		fleet_array					= :fleetData,
		fleet_start_array			= :fleetDataStart,
		fleet_universe				= :universe,
		fleet_start_time			= :fleetStartTime,
		fleet_end_stay				= :fleetStayTime,
		fleet_end_time				= :fleetEndTime,
		fleet_start_id				= :fleetStartPlanetID,
		fleet_start_galaxy			= :fleetStartPlanetGalaxy,
		fleet_start_system			= :fleetStartPlanetSystem,
		fleet_start_planet			= :fleetStartPlanetPlanet,
		fleet_start_type			= :fleetStartPlanetType,
		fleet_end_id				= :fleetTargetPlanetID,
		fleet_end_galaxy			= :fleetTargetPlanetGalaxy,
		fleet_end_system			= :fleetTargetPlanetSystem,
		fleet_end_planet			= :fleetTargetPlanetPlanet,
		fleet_end_type				= :fleetTargetPlanetType,
		fleet_resource_metal		= :fleetResource901,
		fleet_resource_crystal		= :fleetResource902,
		fleet_resource_deuterium    = :fleetResource903,
		fleet_no_m_return           = :fleetNoMReturn,
		fleet_group					= :fleetGroup,
		fleet_target_obj			= :missileTarget,
		start_time					= :timestamp;';

        $db->insert($sql, [
            ':fleetStartOwner'          => $fleetStartOwner,
            ':fleetTargetOwner'         => $fleetTargetOwner,
            ':fleetMission'             => $fleetMission,
            ':fleetShipCount'           => $fleetShipCount,
            ':fleetData'                => implode(';', $fleetData),
            ':fleetDataStart'           => implode(';', $fleetData),
            ':fleetStartTime'           => $fleetStartTime,
            ':fleetStayTime'            => $fleetStayTime,
            ':fleetEndTime'             => $fleetEndTime,
            ':fleetStartPlanetID'       => $fleetStartPlanetID,
            ':fleetStartPlanetGalaxy'   => $fleetStartPlanetGalaxy,
            ':fleetStartPlanetSystem'   => $fleetStartPlanetSystem,
            ':fleetStartPlanetPlanet'   => $fleetStartPlanetPlanet,
            ':fleetStartPlanetType'     => $fleetStartPlanetType,
            ':fleetTargetPlanetID'      => $fleetTargetPlanetID,
            ':fleetTargetPlanetGalaxy'  => $fleetTargetPlanetGalaxy,
            ':fleetTargetPlanetSystem'  => $fleetTargetPlanetSystem,
            ':fleetTargetPlanetPlanet'  => $fleetTargetPlanetPlanet,
            ':fleetTargetPlanetType'    => $fleetTargetPlanetType,
            ':fleetResource901'         => $fleetResource[901],
            ':fleetResource902'         => $fleetResource[902],
            ':fleetResource903'         => $fleetResource[903],
            ':fleetNoMReturn'           => $fleetNoMReturn,
            ':fleetGroup'               => $fleetGroup,
            ':missileTarget'            => $missileTarget,
            ':timestamp'                => TIMESTAMP,
            ':universe'                 => Universe::current(),
        ]);

        $fleetId = $db->lastInsertId();

        $sql = 'INSERT INTO %%FLEETS_EVENT%% SET fleetID	= :fleetId, `time` = :endTime;';
        $db->insert($sql, [
            ':fleetId'  => $fleetId,
            ':endTime'  => $fleetStartTime
        ]);

        $sql = 'INSERT INTO %%LOG_FLEETS%% SET
		fleet_id					= :fleetId,
		fleet_owner					= :fleetStartOwner,
		fleet_target_owner			= :fleetTargetOwner,
		fleet_mission				= :fleetMission,
		fleet_amount				= :fleetShipCount,
		fleet_array					= :fleetData,
		fleet_universe				= :universe,
		fleet_start_time			= :fleetStartTime,
		fleet_end_stay				= :fleetStayTime,
		fleet_end_time				= :fleetEndTime,
		fleet_start_id				= :fleetStartPlanetID,
		fleet_start_galaxy			= :fleetStartPlanetGalaxy,
		fleet_start_system			= :fleetStartPlanetSystem,
		fleet_start_planet			= :fleetStartPlanetPlanet,
		fleet_start_type			= :fleetStartPlanetType,
		fleet_end_id				= :fleetTargetPlanetID,
		fleet_end_galaxy			= :fleetTargetPlanetGalaxy,
		fleet_end_system			= :fleetTargetPlanetSystem,
		fleet_end_planet			= :fleetTargetPlanetPlanet,
		fleet_end_type				= :fleetTargetPlanetType,
		fleet_resource_metal		= :fleetResource901,
		fleet_resource_crystal		= :fleetResource902,
		fleet_resource_deuterium    = :fleetResource903,
		fleet_no_m_return           = :fleetNoMReturn,
		fleet_group					= :fleetGroup,
		fleet_target_obj			= :missileTarget,
		start_time					= :timestamp;';

        $db->insert($sql, [
            ':fleetId'                  => $fleetId,
            ':fleetStartOwner'          => $fleetStartOwner,
            ':fleetTargetOwner'         => $fleetTargetOwner,
            ':fleetMission'             => $fleetMission,
            ':fleetShipCount'           => $fleetShipCount,
            ':fleetData'                => implode(';', $fleetData),
            ':fleetStartTime'           => $fleetStartTime,
            ':fleetStayTime'            => $fleetStayTime,
            ':fleetEndTime'             => $fleetEndTime,
            ':fleetStartPlanetID'       => $fleetStartPlanetID,
            ':fleetStartPlanetGalaxy'   => $fleetStartPlanetGalaxy,
            ':fleetStartPlanetSystem'   => $fleetStartPlanetSystem,
            ':fleetStartPlanetPlanet'   => $fleetStartPlanetPlanet,
            ':fleetStartPlanetType'     => $fleetStartPlanetType,
            ':fleetTargetPlanetID'      => $fleetTargetPlanetID,
            ':fleetTargetPlanetGalaxy'  => $fleetTargetPlanetGalaxy,
            ':fleetTargetPlanetSystem'  => $fleetTargetPlanetSystem,
            ':fleetTargetPlanetPlanet'  => $fleetTargetPlanetPlanet,
            ':fleetTargetPlanetType'    => $fleetTargetPlanetType,
            ':fleetResource901'         => $fleetResource[901],
            ':fleetResource902'         => $fleetResource[902],
            ':fleetResource903'         => $fleetResource[903],
            ':fleetNoMReturn'           => $fleetNoMReturn,
            ':fleetGroup'               => $fleetGroup,
            ':missileTarget'            => $missileTarget,
            ':timestamp'                => TIMESTAMP,
            ':universe'                 => Universe::current(),
        ]);
        return $fleetId;
    }
}
