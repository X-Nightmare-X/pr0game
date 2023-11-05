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


class ShowRepairdockPage extends AbstractGamePage
{
    public static $requireModule = MODULE_REPAIR_DOCK;

    public static $defaultController = 'show';

    public function __construct()
    {
        parent::__construct();
    }

    private function buildAuftr($fmenge)
    {
        $USER =& Singleton()->USER;
        $PLANET =& Singleton()->PLANET;
        $reslist =& Singleton()->reslist;
        $resource =& Singleton()->resource;
        $Missiles = [
            502 => $PLANET[$resource[502]],
            503 => $PLANET[$resource[503]],
        ];

        $totalCostResources = [
            RESOURCE_METAL => 0,
            RESOURCE_CRYSTAL => 0,
            RESOURCE_DEUT => 0,
        ];
        foreach ($fmenge as $Element => $Count) {
            if (
                empty($Count)
                || !in_array($Element, array_merge($reslist['fleet'], $reslist['defense'], $reslist['missile']))
                || !BuildFunctions::isTechnologieAccessible($USER, $PLANET, $Element)
            ) {
                continue;
            }

            $MaxElements = BuildFunctions::getMaxConstructibleElements($USER, $PLANET, $Element);
            $Count = is_numeric($Count) ? round($Count) : 0;
            $Count = max(min($Count, Config::get()->max_fleet_per_build), 0);
            $Count = min($Count, $MaxElements);

            $BuildArray = !empty($PLANET['b_hangar_id']) ? unserialize($PLANET['b_hangar_id']) : [];
            if (in_array($Element, $reslist['missile'])) {
                $MaxMissiles = BuildFunctions::getMaxConstructibleRockets($USER, $PLANET, $Missiles);
                $Count = min($Count, $MaxMissiles[$Element]);

                $Missiles[$Element] += $Count;
            } elseif (in_array($Element, $reslist['one'])) {
                $InBuild = false;
                foreach ($BuildArray as $ElementArray) {
                    if ($ElementArray[0] == $Element) {
                        $InBuild = true;
                        break;
                    }
                }

                if ($InBuild) {
                    continue;
                }

                if ($Count != 0 && $PLANET[$resource[$Element]] == 0 && $InBuild === false) {
                    $Count = 1;
                }
            }

            if (empty($Count)) {
                continue;
            }

            $costResources = BuildFunctions::getElementPrice($USER, $PLANET, $Element, false, $Count);

            foreach ($costResources as $resourceId => $amount) {
                $totalCostResources[$resourceId] += $amount;
            }
            require_once 'includes/classes/achievements/MiscAchievement.class.php';
            MiscAchievement::checkShipyardAchievements($USER['id'], $Count, $Element);
            $BuildArray[] = [$Element, $Count];
            $PLANET['b_hangar_id'] = serialize($BuildArray);
        }
        $this->ecoObj->removeResources($PLANET['id'], $totalCostResources, $PLANET);
        $this->ecoObj->saveShipyardQueue($PLANET);
    }

    public function show()
    {
        $USER =& Singleton()->USER;
        $PLANET =& Singleton()->PLANET;
        $LNG =& Singleton()->LNG;
        $resource =& Singleton()->resource;
        $db = Database::get();
        if ($PLANET[$resource[REPAIR_DOCK]] == 0) {
            $this->printMessage($LNG['bd_repairdock_required']);
        }
        $Messages = $USER['messages'];

        $sql = "SELECT * FROM %%PLANET_WRECKFIELD%% WHERE `planetID` = :planetID;";
        $wreckfield = $db->selectSingle($sql, [':planetID' => $PLANET['id']]);

        if (empty($wreckfield) || $wreckfield['created'] < TIMESTAMP + 1800) {
            $this->printMessage($LNG['bd_repairdock_empty']);
        }

        $ships = FleetFunctions::unserialize($wreckfield['ships']);
        $buildTodo = HTTP::_GP('fmenge', []);
        $action = HTTP::_GP('cmd', '');

        $ElementQueue = !empty($wreckfield['repair_order']) ? unserialize($wreckfield['repair_order']) : [];
        $buildList = [];
        if (!empty($action) && $action == 'deploy') {

        }
        elseif (empty($ElementQueue) && !empty($buildTodo) && $USER['urlaubs_modus'] == 0) {
            $QueueTime = 0;
            foreach ($buildTodo as $element => $amount) {
                if (isset($ships[$element])) {
                    $ElementQueue[$element] = min($amount, $ships[$element]);
                    $ElementTime = BuildFunctions::getBuildingTime($USER, $PLANET, $element);
                    $QueueTime += $ElementTime * $ElementQueue[$element];
                }
            }
            $QueueTime = min($QueueTime, TIME_12_HOURS);

            $sql = "UPDATE %%PLANET_WRECKFIELD%% SET `repair_order` = :repair_order, `repair_order_start = :start_time, `repair_order_end = :end_time`
                WHERE `planetID` = :planetID;";
            $db->update($sql, [
                ':repair_order' => serialize($ElementQueue),
                ':start_time' => TIMESTAMP,
                ':end_time' => TIMESTAMP + $QueueTime,
                ':planetID' => $PLANET['id'],
            ]);

            $buildList = [
                'ships' => $ElementQueue,
                'start_time' => TIMESTAMP,
                'time' => TIMESTAMP,
                'end_time' => TIMESTAMP + $QueueTime,
                'pretty_end_time' => pretty_time($QueueTime),
            ];
        }
        elseif (!empty($ElementQueue)) {
            $buildList = [
                'ships' => $ElementQueue,
                'start_time' => $wreckfield['repair_order_start'],
                'time' => $wreckfield['repair_order_start'] - TIMESTAMP,
                'end_time' => $wreckfield['repair_order_end'],
                'pretty_end_time' => pretty_time($wreckfield['repair_order_end']),
            ];
        }

        foreach ($ships as $Element => $amount) {
            if (!isset($ships[$Element])) {
                continue;
            }

            $elementTime = BuildFunctions::getBuildingTime($USER, $PLANET, $Element);
            $maxBuildable = floor($amount * $this->getRepairRate($PLANET[REPAIR_DOCK]));

            $elementList[$Element] = [
                'id' => $Element,
                'available' => $PLANET[$resource[$Element]],
                'elementTime' => $elementTime,
                'maxBuildable' => floatToString($maxBuildable),
            ];
        }
        $SolarEnergy = round((($PLANET['temp_max'] + 160) / 6) * Config::get()->energySpeed, 1);

        $this->assign([
            'umode'       => $USER['urlaubs_modus'],
            'buisy' => !empty($wreckfield['repair_order']),
            'elementList' => $elementList,
            'BuildList' => $buildList,
            'messages' => ($Messages > 0) ? (($Messages == 1) ? $LNG['ov_have_new_message']
                : sprintf($LNG['ov_have_new_messages'], pretty_number($Messages))) : false,
            'SolarEnergy' => $SolarEnergy,
        ]);

        $this->display('page.repairdock.default.tpl');
    }

    private function getRepairRate($repairDockLvl) : float {
        return (44 + pow($repairDockLvl, 0.85)) / 100;
    }
}
