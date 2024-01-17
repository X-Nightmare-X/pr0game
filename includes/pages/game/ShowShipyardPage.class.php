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


class ShowShipyardPage extends AbstractGamePage
{
    public static $requireModule = 0;

    public static $defaultController = 'show';

    public function __construct()
    {
        parent::__construct();
    }

    private function cancelAuftr()
    {
        $USER =& Singleton()->USER;
        $PLANET =& Singleton()->PLANET;
        $resource =& Singleton()->resource;
        $ElementQueue = !empty($PLANET['b_hangar_id']) ? unserialize($PLANET['b_hangar_id']) : [];

        $CancelArray = HTTP::_GP('auftr', []);

        if (!is_array($CancelArray)) {
            return false;
        }

        foreach ($CancelArray as $Auftr) {
            if (!isset($ElementQueue[$Auftr])) {
                continue;
            }

            if ($Auftr == 0) {
                $PLANET['b_hangar'] = 0;
            }

            $Element = $ElementQueue[$Auftr][0];
            $Count = $ElementQueue[$Auftr][1];

            $costResources = BuildFunctions::getElementPrice($USER, $PLANET, $Element, false, $Count);

            $this->ecoObj->addResources($PLANET['id'], $costResources, $PLANET, FACTOR_CANCEL_SHIPYARD);

            unset($ElementQueue[$Auftr]);
        }

        if (empty($ElementQueue)) {
            $PLANET['b_hangar_id'] = '';
        } else {
            $PLANET['b_hangar_id'] = serialize(array_values($ElementQueue));
        }

        $this->ecoObj->saveShipyardQueue($PLANET);

        return true;
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
        $reslist =& Singleton()->reslist;
        if ($PLANET[$resource[21]] == 0) {
            $this->printMessage($LNG['bd_shipyard_required']);
        }
        $Messages = $USER['messages'];

        $type = HTTP::_GP('type', 'fleet');
        $buildTodo = HTTP::_GP('fmenge', []);
        $action = HTTP::_GP('action', '');

        $NotBuilding = true;
        if (!empty($PLANET['b_building_id'])) {
            $CurrentQueue = !empty($PLANET['b_building_id']) ? unserialize($PLANET['b_building_id']) : [];
            foreach ($CurrentQueue as $ElementArray) {
                if ($ElementArray[0] == 21 || $ElementArray[0] == 15) {
                    $NotBuilding = false;
                    break;
                }
            }
        }

        $ElementQueue = !empty($PLANET['b_hangar_id']) ? unserialize($PLANET['b_hangar_id']) : [];
        if (empty($ElementQueue)) {
            $Count = 0;
        } else {
            $Count = count($ElementQueue);
        }

        if ($USER['urlaubs_modus'] == 0 && $NotBuilding == true) {
            if (!empty($buildTodo)) {
                $maxBuildQueue = Config::get()->max_elements_ships;
                if ($maxBuildQueue != 0 && $Count >= $maxBuildQueue) {
                    $this->printMessage(sprintf($LNG['bd_max_builds'], $maxBuildQueue));
                }

                $db = Database::get();
                $db->startTransaction();
                $PLANET = $db->selectSingle("SELECT * FROM %%PLANETS%% WHERE id = :planetId FOR UPDATE;", [':planetId' => $PLANET['id']]);
                $this->buildAuftr($buildTodo);
                $db->commit();
                $this->redirectTo('game.php?page=shipyard&type=' . $type);
            }

            if ($action == "delete") {
                $db = Database::get();
                $db->startTransaction();
                $PLANET = $db->selectSingle("SELECT * FROM %%PLANETS%% WHERE id = :planetId FOR UPDATE;", [':planetId' => $PLANET['id']]);
                $this->cancelAuftr();
                $db->commit();
                $this->redirectTo('game.php?page=shipyard&type=' . $type);
            }
        }

        $elementInQueue = [];
        $ElementQueue = !empty($PLANET['b_hangar_id']) ? unserialize($PLANET['b_hangar_id']) : [];
        $buildList = [];
        $elementList = [];

        if (!empty($ElementQueue)) {
            $Shipyard = [];
            $QueueTime = 0;
            foreach ($ElementQueue as $Element) {
                if (empty($Element)) {
                    continue;
                }

                $elementInQueue[$Element[0]] = true;
                $ElementTime = BuildFunctions::getBuildingTime($USER, $PLANET, $Element[0]);
                $QueueTime += $ElementTime * $Element[1];
                $Shipyard[] = [$LNG['tech'][$Element[0]], $Element[1], $ElementTime, $Element[0]];
            }

            $buildList = [
                'Queue' => $Shipyard,
                'b_hangar_id_plus' => $PLANET['b_hangar'],
                'queue_time' => max($QueueTime - $PLANET['b_hangar'], 0),
                'pretty_time_b_hangar' => pretty_time(max($QueueTime - $PLANET['b_hangar'], 0)),
            ];
        }

        if ($type == 'defense') {
            $elementIDs = array_merge($reslist['defense'], $reslist['missile']);
        } else {
            $elementIDs = $reslist['fleet'];
        }

        $Missiles = [];

        foreach ($reslist['missile'] as $elementID) {
            $Missiles[$elementID] = $PLANET[$resource[$elementID]];
        }

        $MaxMissiles = BuildFunctions::getMaxConstructibleRockets($USER, $PLANET, $Missiles);

        foreach ($elementIDs as $Element) {
            if (!BuildFunctions::isTechnologieAccessible($USER, $PLANET, $Element)) {
                continue;
            }

            $costResources = BuildFunctions::getElementPrice($USER, $PLANET, $Element);
            $costOverflow = BuildFunctions::getRestPrice($USER, $PLANET, $Element, $costResources);
            $elementTime = BuildFunctions::getBuildingTime($USER, $PLANET, $Element, $costResources);
            $buyable = BuildFunctions::isElementBuyable($USER, $PLANET, $Element, $costResources);
            $maxBuildable = BuildFunctions::getMaxConstructibleElements($USER, $PLANET, $Element, $costResources);
            $SolarEnergy = round((($PLANET['temp_max'] + 160) / 6) * Config::get()->energy_multiplier, 1);

            if (isset($MaxMissiles[$Element])) {
                $maxBuildable = min($maxBuildable, $MaxMissiles[$Element]);
            }

            $AlreadyBuild = in_array($Element, $reslist['one'])
                && (isset($elementInQueue[$Element]) || $PLANET[$resource[$Element]] != 0);

            $elementList[$Element] = [
                'id' => $Element,
                'available' => $PLANET[$resource[$Element]],
                'costResources' => $costResources,
                'costOverflow' => $costOverflow,
                'elementTime' => $elementTime,
                'buyable' => $buyable,
                'maxBuildable' => floatToString($maxBuildable),
                'AlreadyBuild' => $AlreadyBuild,
            ];
        }

        $this->assign([
            'umode'       => $USER['urlaubs_modus'],
            'elementList' => $elementList,
            'NotBuilding' => $NotBuilding,
            'BuildList' => $buildList,
            'maxlength' => strlen(Config::get()->max_fleet_per_build),
            'type' => $type,
            'messages' => ($Messages > 0) ? (($Messages == 1) ? $LNG['ov_have_new_message']
                : sprintf($LNG['ov_have_new_messages'], pretty_number($Messages))) : false,
            'message_type' => $USER['showMessageCategory'] === 1 ? $USER['message_type'] : false,
            'SolarEnergy' => $SolarEnergy,
        ]);

        $this->display('page.shipyard.default.tpl');
    }
}
