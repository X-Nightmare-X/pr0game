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

require 'includes/classes/class.FlyingFleetsTable.php';

/**
 * Sends one fleet back. Uses the same function as for the user
 *
 * @param integer $fleetID
 * @return void
 */
function returnFleet(int $fleetID)
{
    $db = Database::get();

    $sql = "SELECT u.id FROM %%USERS%% u
    JOIN %%FLEETS%% f ON f.fleet_owner = u.id
    WHERE fleet_id = :id;";
    $USER = $db->selectSingle($sql, [
        ':id' => $fleetID,
    ]);

    FleetFunctions::sendFleetBack($USER, $fleetID);
}

/**
 * Returns all flying fleets with mission Attack, ACS and Destroy on active players to its owner using the original return time.
 *
 * @return void
 */
function returnAttackingFleets()
{
    $db = Database::get();

    $sql = "SELECT f.fleet_id, f.fleet_group, f.fleet_end_time FROM %%FLEETS%% f
    JOIN %%USERS%% u ON u.id = f.fleet_target_owner
    WHERE f.fleet_mission IN " . '(' . MISSION_ATTACK . ', ' . MISSION_ACS . ', ' . MISSION_DESTRUCTION . ')' .
    " AND u.onlinetime < :inactive AND fleet_mess <> :fleetMess;";
    $fleets = $db->select($sql, [
        ':inactive' => TIMESTAMP - INACTIVE,
        ':fleetMess' => FLEET_RETURN,
    ]);

    foreach ($fleets as $fleet) {
        if ($fleet['fleet_group'] > 0) {
            $sql = "DELETE %%AKS%%, %%USERS_ACS%%
            FROM %%AKS%%
            LEFT JOIN %%USERS_ACS%% ON acsID = %%AKS%%.id
            WHERE %%AKS%%.id = :acsId;";

            $db->delete($sql, [
                ':acsId' => $fleet['fleet_group'],
            ]);
        }

        $sql = "UPDATE %%FLEETS%% SET
        hasCanceled = :hasCanceled,
        fleet_mess = :fleetMess,
        fleet_group	= :fleetGroup
        WHERE fleet_id = :id;";
        $db->update($sql, [
            ':id' => $fleet['fleet_id'],
            ':hasCanceled' => 1,
            ':fleetMess' => FLEET_RETURN,
            ':fleetGroup' => 0,
        ]);

        $sql = "UPDATE %%LOG_FLEETS%% SET
        hasCanceled = :hasCanceled,
        fleet_mess = :fleetMess,
        fleet_state = :fleetState
        WHERE fleet_id = :id;";

        $db->update($sql, [
            ':id' => $fleet['fleet_id'],
            ':hasCanceled' => 1,
            ':fleetMess' => FLEET_RETURN,
            ':fleetState' => 2,
        ]);

        $sql = "UPDATE %%FLEETS_EVENT%% SET
        time = :endTime
        WHERE fleetID = :id;";
        $db->update($sql, [
            ':id' => $fleet['fleet_id'],
            ':endTime' => $fleet['fleet_end_time'],
        ]);
    }
}

function ShowFlyingFleetPage()
{
    $LNG = &Singleton()->LNG;
    $USER = &Singleton()->USER;
    $id = HTTP::_GP('id', 0);
    $massunlock = HTTP::_GP('massunlock', 0);
    $sendFleetsBack = HTTP::_GP('sendfleetsback', 0);
    $sendFleetBack = HTTP::_GP('sendfleetback', 0);

    if (!empty($id)) {
        $lock   = HTTP::_GP('lock', 0);
        $SQL    = ($lock == 0) ? "NULL" : "'ADM_LOCK'";

        $GLOBALS['DATABASE']->query("UPDATE " . FLEETS . " SET `fleet_busy` = '" . $lock . "' WHERE `fleet_id` = '"
            . $id . "' AND `fleet_universe` = '" . Universe::getEmulated() . "';");
        $GLOBALS['DATABASE']->query("UPDATE " . FLEETS_EVENT . " SET `lock` = " . $SQL . " WHERE `fleetID` = " . $id
            . ";");
    } elseif (!empty($massunlock)) {
        $GLOBALS['DATABASE']->query("UPDATE " . FLEETS . " SET `fleet_busy` = '0' WHERE `fleet_universe` = '"
            . Universe::getEmulated() . "';");
        $GLOBALS['DATABASE']->query("UPDATE " . FLEETS_EVENT . " SET `lock` = NULL;");
    } elseif (!empty($sendFleetsBack)) {
        returnAttackingFleets();
    } elseif (!empty($sendFleetBack)) {
        returnFleet($sendFleetBack);
    }

    $orderBy        = "fleet_id";

    $fleetResult    = $GLOBALS['DATABASE']->query("SELECT
	fleet.*,
	event.`lock`,
	COUNT(event.fleetID) as error,
	pstart.name as startPlanetName,
	ptarget.name as targetPlanetName,
	ustart.username as startUserName,
	utarget.username as targetUserName,
	acs.name as acsName
	FROM " . FLEETS . " fleet
	LEFT JOIN " . FLEETS_EVENT . " event ON fleetID = fleet_id
	LEFT JOIN " . PLANETS . " pstart ON pstart.id = fleet_start_id
	LEFT JOIN " . PLANETS . " ptarget ON ptarget.id = fleet_end_id
	LEFT JOIN " . USERS . " ustart ON ustart.id = fleet_owner
	LEFT JOIN " . USERS . " utarget ON utarget.id = fleet_target_owner
	LEFT JOIN " . AKS . " acs ON acs.id = fleet_group
	WHERE fleet_universe = " . Universe::getEmulated() . "
	GROUP BY event.fleetID
	ORDER BY " . $orderBy . ";");

    $FleetList  = [];

    while ($fleetRow = $GLOBALS['DATABASE']->fetch_array($fleetResult)) {
        $shipList = [];
        $shipArray = array_filter(explode(';', $fleetRow['fleet_array']));
        foreach ($shipArray as $ship) {
            $shipDetail = explode(',', $ship);
            $shipList[$shipDetail[0]] = $shipDetail[1];
        }

        $USER = &Singleton()->USER;
        $FleetList[] = [
            'fleetID'               => $fleetRow['fleet_id'],
            'lock'                  => !empty($fleetRow['lock']),
            'count'                 => $fleetRow['fleet_amount'],
            'error'                 => !$fleetRow['error'],
            'ships'                 => $shipList,
            'state'                 => $fleetRow['fleet_mess'],
            'starttime'             => _date($LNG['php_tdformat'], $fleetRow['start_time'], $USER['timezone']),
            'arrivaltime'           => _date($LNG['php_tdformat'], $fleetRow['fleet_start_time'], $USER['timezone']),
            'stayhour'              => round(($fleetRow['fleet_end_stay'] - $fleetRow['fleet_start_time']) / 3600),
            'staytime'              => $fleetRow['fleet_start_time'] !== $fleetRow['fleet_end_stay']
                ? _date($LNG['php_tdformat'], $fleetRow['fleet_end_stay'], $USER['timezone']) : 0,
            'endtime'               => _date($LNG['php_tdformat'], $fleetRow['fleet_end_time'], $USER['timezone']),
            'missionID'             => $fleetRow['fleet_mission'],
            'acsID'                 => $fleetRow['fleet_group'],
            'acsName'               => $fleetRow['acsName'],
            'startUserID'           => $fleetRow['fleet_owner'],
            'startUserName'         => $fleetRow['startUserName'],
            'startPlanetID'         => $fleetRow['fleet_start_id'],
            'startPlanetName'       => $fleetRow['startPlanetName'],
            'startPlanetGalaxy'     => $fleetRow['fleet_start_galaxy'],
            'startPlanetSystem'     => $fleetRow['fleet_start_system'],
            'startPlanetPlanet'     => $fleetRow['fleet_start_planet'],
            'startPlanetType'       => $fleetRow['fleet_start_type'],
            'targetUserID'          => $fleetRow['fleet_target_owner'],
            'targetUserName'        => $fleetRow['targetUserName'],
            'targetPlanetID'        => $fleetRow['fleet_end_id'],
            'targetPlanetName'      => $fleetRow['targetPlanetName'],
            'targetPlanetGalaxy'    => $fleetRow['fleet_end_galaxy'],
            'targetPlanetSystem'    => $fleetRow['fleet_end_system'],
            'targetPlanetPlanet'    => $fleetRow['fleet_end_planet'],
            'targetPlanetType'      => $fleetRow['fleet_end_type'],
            'resource'              => [
                901 => $fleetRow['fleet_resource_metal'],
                902 => $fleetRow['fleet_resource_crystal'],
                903 => $fleetRow['fleet_resource_deuterium'],
            ],
        ];
    }


    $GLOBALS['DATABASE']->free_result($fleetResult);

    $template = new template();
    $template->assign_vars([
        'FleetList'     => $FleetList,
        'signalColors'  => $USER['signalColors']
    ]);
    $template->show('FlyingFleetPage.tpl');
}
