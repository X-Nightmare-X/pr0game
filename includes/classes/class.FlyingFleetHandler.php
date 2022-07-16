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

class FlyingFleetHandler
{
    protected $token;

    public static $missionObjPattern    = array(
        MISSION_ATTACK          => 'MissionCaseAttack',
        MISSION_ACS             => 'MissionCaseACS',
        MISSION_TRANSPORT       => 'MissionCaseTransport',
        MISSION_STATION         => 'MissionCaseStay',
        MISSION_HOLD            => 'MissionCaseStayAlly',
        MISSION_SPY             => 'MissionCaseSpy',
        MISSION_COLONISATION    => 'MissionCaseColonisation',
        MISSION_RECYCLING       => 'MissionCaseRecycling',
        MISSION_DESTRUCTION     => 'MissionCaseDestruction',
        MISSION_MISSILE         => 'MissionCaseMIP',
        MISSION_EXPEDITION      => 'MissionCaseExpedition',
        MISSION_TRADE           => 'MissionCaseTrade',
        MISSION_TRANSFER        => 'MissionCaseTransfer',
    );

    public function setToken($token)
    {
        $this->token    = $token;
    }

    public function run()
    {
        require_once 'includes/classes/class.MissionFunctions.php';
        require_once 'includes/classes/missions/Mission.interface.php';

        $db = Database::get();
		$db->startTransaction();

        $sql = 'SELECT %%FLEETS%%.*
		FROM %%FLEETS_EVENT%%
		INNER JOIN %%FLEETS%% ON fleetID = fleet_id
		WHERE `lock` = :token FOR UPDATE;';

        $fleetResult = $db->select($sql, array(
            ':token'    => $this->token
        ));

        foreach ($fleetResult as $fleetRow) {
            if (!isset(self::$missionObjPattern[$fleetRow['fleet_mission']])) {
                $sql = 'DELETE FROM %%FLEETS%% WHERE fleet_id = :fleetId;';

                $db->delete($sql, array(
                    ':fleetId'  => $fleetRow['fleet_id']
                ));

                continue;
            }

            $missionName    = self::$missionObjPattern[$fleetRow['fleet_mission']];

            $path   = 'includes/classes/missions/' . $missionName . '.class.php';
            require_once $path;
            /** @var $missionObj Mission */
            $missionObj = new $missionName($fleetRow);

            switch ($fleetRow['fleet_mess']) {
                case 0:
                    $missionObj->TargetEvent();
                    break;
                case 1:
                    $missionObj->ReturnEvent();
                    break;
                case 2:
                    $missionObj->EndStayEvent();
                    break;
            }

            
        }
        $db->commit();
    }
}
