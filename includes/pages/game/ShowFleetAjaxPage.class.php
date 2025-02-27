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

class ShowFleetAjaxPage extends AbstractGamePage
{
    public $returnData = [];

    public static $requireModule = 0;

    public function __construct()
    {
        parent::__construct();
        $this->setWindow('ajax');
    }

    private function sendData($Code, $Message)
    {
        $this->returnData['code'] = $Code;
        $this->returnData['mess'] = $Message;
        $this->sendJSON($this->returnData);
    }

    public function show()
    {
        $USER =& Singleton()->USER;
        $PLANET =& Singleton()->PLANET;
        $resource =& Singleton()->resource;
        $LNG =& Singleton()->LNG;
        $pricelist =& Singleton()->pricelist;
        $UserDeuterium = $PLANET['deuterium'];

        $planetID = HTTP::_GP('planetID', 0);
        $targetMission = HTTP::_GP('mission', 0);

        $activeSlots = FleetFunctions::getCurrentFleets($USER['id']);
        $maxSlots = FleetFunctions::getMaxFleetSlots($USER);

        $this->returnData['slots'] = $activeSlots;

        if (IsVacationMode($USER)) {
            $this->sendData(620, $LNG['fa_vacation_mode_current']);
        }

        if (empty($planetID)) {
            $this->sendData(601, $LNG['fa_planet_not_exist']);
        }

        if ($maxSlots <= $activeSlots) {
            $this->sendData(612, $LNG['fa_no_more_slots']);
        }

        $fleetArray = [];

        $db = Database::get();

        switch ($targetMission) {
            case 6:
                if (!isModuleAvailable(MODULE_MISSION_SPY)) {
                    $this->sendData(699, $LNG['sys_module_inactive']);
                }

                $ships = min($USER['spio_anz'], $PLANET[$resource[210]]);

                if (empty($ships)) {
                    $this->sendData(611, $LNG['fa_no_spios']);
                }

                $fleetArray = [210 => $ships];
                $this->returnData['ships'][210] = $PLANET[$resource[210]] - $ships;
                break;
            case 8:
                if (!isModuleAvailable(MODULE_MISSION_RECYCLE)) {
                    $this->sendData(699, $LNG['sys_module_inactive']);
                }

                $sql = "SELECT (der_metal + der_crystal) as sum FROM %%PLANETS%% WHERE id = :planetID;";
                $totalDebris = $db->selectSingle($sql, [':planetID' => $planetID], 'sum');

                $recElementIDs = [219, 209];

                $fleetArray = [];

                foreach ($recElementIDs as $elementID) {
                    $a = $pricelist[$elementID]['capacity'];
                    $shipsNeed = min(ceil($totalDebris / $a), $PLANET[$resource[$elementID]]);
                    $totalDebris -= ($shipsNeed * $a);

                    $fleetArray[$elementID] = $shipsNeed;
                    $this->returnData['ships'][$elementID] = $PLANET[$resource[$elementID]] - $shipsNeed;

                    if ($totalDebris <= 0) {
                        break;
                    }
                }

                if (empty($fleetArray)) {
                    $this->sendData(611, $LNG['fa_no_recyclers']);
                }
                break;
            default:
                $this->sendData(610, $LNG['fa_not_enough_probes']);
                break;
        }

        $fleetArray = array_filter($fleetArray);

        if (empty($fleetArray)) {
            $this->sendData(610, $LNG['fa_not_enough_probes']);
        }

        $sql = "SELECT planet.id_owner as id_owner,
		planet.galaxy as galaxy,
		planet.system as system,
		planet.planet as planet,
		planet.planet_type as planet_type,
		total_points, onlinetime, urlaubs_modus, banaday, authattack
		FROM %%PLANETS%% planet
		INNER JOIN %%USERS%% user ON planet.id_owner = user.id
		LEFT JOIN %%STATPOINTS%% as stat ON stat.id_owner = user.id AND stat.stat_type = '1'
		WHERE planet.id = :planetID;";

        $targetData = $db->selectSingle($sql, [':planetID' => $planetID]);

        if (empty($targetData)) {
            $this->sendData(601, $LNG['fa_planet_not_exist']);
        }

        if ($targetMission == MISSION_SPY) {
            if (Config::get()->adm_attack == 1 && $targetData['authattack'] > $USER['authlevel']) {
                $this->sendData(619, $LNG['fa_action_not_allowed']);
            }

            if (IsVacationMode($targetData)) {
                $this->sendData(605, $LNG['fa_vacation_mode']);
            }
            $sql = 'SELECT total_points
			FROM %%STATPOINTS%%
			WHERE id_owner = :userId AND stat_type = :statType';

            try {
                $USER += Database::get()->selectSingle($sql, [
                    ':userId' => $USER['id'],
                    ':statType' => 1
                ]) ?: ['total_points' => 0];
            } catch (Exception $exception) {
            }

            $IsNoobProtec = CheckNoobProtec($USER, $targetData, $targetData);

            if ($IsNoobProtec['NoobPlayer']) {
                $this->sendData(603, $LNG['fa_week_player']);
            }

            if ($IsNoobProtec['StrongPlayer']) {
                $this->sendData(604, $LNG['fa_strong_player']);
            }

            if ($USER['id'] == $targetData['id_owner']) {
                $this->sendData(618, $LNG['fa_not_spy_yourself']);
            }
        }

        $SpeedFactor = FleetFunctions::getGameSpeedFactor();
        $Distance = FleetFunctions::getTargetDistance(
            [$PLANET['galaxy'], $PLANET['system'], $PLANET['planet']],
            [$targetData['galaxy'], $targetData['system'], $targetData['planet']]
        );
        $SpeedAllMin = FleetFunctions::getFleetMaxSpeed($fleetArray, $USER);
        $Duration = FleetFunctions::getMissionDuration(MISSION_MISSILE, $SpeedAllMin, $Distance, $SpeedFactor, $USER);
        $consumption = FleetFunctions::getFleetConsumption($fleetArray, $Duration, $Distance, $USER, $SpeedFactor);

        $UserDeuterium -= $consumption;

        if ($UserDeuterium < 0) {
            $this->sendData(613, $LNG['fa_not_enough_fuel']);
        }

        if ($consumption > FleetFunctions::getFleetRoom($fleetArray)) {
            $this->sendData(613, $LNG['fa_no_fleetroom']);
        }

        if (connection_aborted()) {
            exit;
        }

        $this->returnData['slots']++;

        $fleetResource = [
            901 => 0,
            902 => 0,
            903 => 0,
        ];

        $fleetStartTime = $Duration + TIMESTAMP;
        $fleetStayTime = $fleetStartTime;
        $fleetEndTime = $fleetStayTime + $Duration;

        $shipID = array_keys($fleetArray);
        $PLANET['deuterium'] -= $consumption;

        FleetFunctions::sendFleet(
            $fleetArray,
            $targetMission,
            $USER['id'],
            $PLANET['id'],
            $PLANET['galaxy'],
            $PLANET['system'],
            $PLANET['planet'],
            $PLANET['planet_type'],
            $targetData['id_owner'],
            $planetID,
            $targetData['galaxy'],
            $targetData['system'],
            $targetData['planet'],
            $targetData['planet_type'],
            $fleetResource,
            $fleetStartTime,
            $fleetStayTime,
            $fleetEndTime,
            0,
            0,
            0,
            $consumption
        );

        $this->sendData(
            600,
            sprintf(
                '%s %d %s %s %d:%d:%d ...',
                $LNG['fa_sending'],
                (int)floor(array_sum($fleetArray)),
                $LNG['tech'][$shipID[0]],
                $LNG['gl_to'],
                (int)$targetData['galaxy'],
                (int)$targetData['system'],
                (int)$targetData['planet']
            )
        );
    }
}
