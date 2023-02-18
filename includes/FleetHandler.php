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

$token	= getRandomString();
$db		= Database::get();
$db->startTransaction();

$sql = "SELECT `fleetID` FROM %%FLEETS_EVENT%% fe JOIN %%FLEETS%% f ON f.fleet_id = fe.fleetID
    WHERE fe.lock IS NULL AND fe.time <= :time AND f.fleet_universe = :universe FOR UPDATE;";
$fleetResult = $db->select($sql, [
    ':time'		=> TIMESTAMP,
    ':universe' => Universe::current(),
]);

if (!empty($fleetResult) && is_array($fleetResult)) {
    $updateResult = true;
    foreach ($fleetResult as $key => $value) {
        $updateResult	= $updateResult && $db->update("UPDATE %%FLEETS_EVENT%% SET `lock` = :token WHERE fleetID = :id;", array(
            ':token' => $token,
            ':id' => $value['fleetID'],
        ));
    }

    if ($updateResult) {
        require 'includes/classes/class.FlyingFleetHandler.php';

        $fleetObj	= new FlyingFleetHandler();
        $fleetObj->setToken($token);
        $fleetObj->run();

        $db->update("UPDATE %%FLEETS_EVENT%% SET `lock` = NULL WHERE `lock` = :token;", array(
            ':token' => $token
        ));
    }
}

$db->commit();
