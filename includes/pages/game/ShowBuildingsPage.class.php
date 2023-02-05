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

class ShowBuildingsPage extends AbstractGamePage
{
    public static $requireModule = MODULE_BUILDING;

    public function __construct()
    {
        parent::__construct();
    }

    private function cancelBuildingFromQueue()
    {
        $PLANET =& Singleton()->PLANET;
        $USER =& Singleton()->USER;
        $resource =& Singleton()->resource;
        $CurrentQueue  = !empty($PLANET['b_building_id']) ? unserialize($PLANET['b_building_id']) : [];
        if (empty($CurrentQueue)) {
            $PLANET['b_building_id']    = '';
            $PLANET['b_building']       = 0;
            return false;
        }

        $Element                = $CurrentQueue[0][0];
        $BuildLevel             = $CurrentQueue[0][1];
        $BuildMode              = $CurrentQueue[0][4];

        $costResources = BuildFunctions::getElementPrice(
            $USER,
            $PLANET,
            $Element,
            $BuildMode == 'destroy',
            $BuildLevel
        );

        $this->ecoObj->addResources($PLANET['id'], $costResources, $PLANET);

        array_shift($CurrentQueue);
        if (count($CurrentQueue) == 0) {
            $PLANET['b_building']       = 0;
            $PLANET['b_building_id']    = '';
        } else {
            $PLANET['b_building']       = 0;
            $PLANET['b_building_id']    = '';
            foreach ($CurrentQueue as $ListIDArray) {
                $this->addBuildingToQueue($ListIDArray[0], 'build' == $ListIDArray[4]);
            }
        }
        return true;
    }

    private function removeBuildingFromQueue($QueueID)
    {
        $USER =& Singleton()->USER;
        $PLANET =& Singleton()->PLANET;
        if ($QueueID <= 1 || empty($PLANET['b_building_id'])) {
            return false;
        }

        $CurrentQueue  = unserialize($PLANET['b_building_id']);
        $ActualCount   = count($CurrentQueue);
        if ($ActualCount <= 1) {
            return $this->cancelBuildingFromQueue();
        }

        if ($QueueID - $ActualCount >= 1) {
            // Avoid race conditions
            return;
        }

        $Element        = $CurrentQueue[$QueueID - 1][0];
        $NewQueueArray = [];
        for ($i = $QueueID, $j = 0, $max = sizeof($CurrentQueue); $i <= $max; $i++, $j++) {
            if ($i != $QueueID) {
                $NewQueueArray[$j] = $CurrentQueue[$i - 1];
            }
            unset($CurrentQueue[$i - 1]);
        }

        if (!empty($CurrentQueue)) {
            $PLANET['b_building_id'] = serialize($CurrentQueue);
        } else {
            $PLANET['b_building_id'] = "";
        }

        foreach ($NewQueueArray as $ListIDArray) {
            $this->addBuildingToQueue($ListIDArray[0], 'build' == $ListIDArray[4]);
        }

        return true;
    }

    private function addBuildingToQueue($Element, $AddMode = true)
    {
        $PLANET =& Singleton()->PLANET;
        $USER =& Singleton()->USER;
        $resource =& Singleton()->resource;
        $reslist =& Singleton()->reslist;
        $pricelist =& Singleton()->pricelist;
        if (
            !in_array($Element, $reslist['allow'][$PLANET['planet_type']])
            || !BuildFunctions::isTechnologieAccessible($USER, $PLANET, $Element)
            || ($Element == 31 && $USER["b_tech_planet"] != 0)
            || (($Element == 15 || $Element == 21) && !empty($PLANET['b_hangar_id']))
            || (!$AddMode && $PLANET[$resource[$Element]] == 0)
        ) {
            return;
        }

        $CurrentQueue       = !empty($PLANET['b_building_id']) ? unserialize($PLANET['b_building_id']) : [];
        $DemolishedQueue = 0;

        if (!empty($CurrentQueue)) {
            $ActualCount    = count($CurrentQueue);
            $DemolishedQueue        = count($CurrentQueue);
            foreach ($this->getQueueData()['queue'] as $QueueInfo) {
                if ($QueueInfo['destroy']) {
                    $DemolishedQueue = $DemolishedQueue - 2;
                }
                $DemolishedQueue = max(0, $DemolishedQueue);
            }
        } else {
            $CurrentQueue = [];
            $ActualCount = 0;
        }

        $CurrentMaxFields   = CalculateMaxPlanetFields($PLANET);

        $config = Config::get();

        if (
            ($config->max_elements_build != 0 && $ActualCount == $config->max_elements_build)
            || ($AddMode && $PLANET["field_current"] >= ($CurrentMaxFields - $DemolishedQueue))
        ) {
            return;
        }

        $BuildMode          = $AddMode ? 'build' : 'destroy';
        $BuildLevel         = $PLANET[$resource[$Element]] + (int) $AddMode;

        if ($ActualCount == 0) {
            if ($pricelist[$Element]['max'] < $BuildLevel) {
                return;
            }

            $costResources      = BuildFunctions::getElementPrice($USER, $PLANET, $Element, !$AddMode, $BuildLevel);

            if (!BuildFunctions::isElementBuyable($USER, $PLANET, $Element, $costResources)) {
                return;
            }

            $this->ecoObj->removeResources($PLANET['id'], $costResources, $PLANET);

            $elementTime                = BuildFunctions::getBuildingTime($USER, $PLANET, $Element, $costResources);
            $BuildEndTime               = TIMESTAMP + $elementTime;

            $PLANET['b_building_id']    = serialize([[$Element, $BuildLevel, $elementTime, $BuildEndTime, $BuildMode]]);
            $PLANET['b_building']       = $BuildEndTime;
        } else {
            $addLevel = 0;
            foreach ($CurrentQueue as $QueueSubArray) {
                if ($QueueSubArray[0] != $Element) {
                    continue;
                }

                if ($QueueSubArray[4] == 'build') {
                    $addLevel++;
                } else {
                    $addLevel--;
                }
            }

            $BuildLevel                 += $addLevel;

            if (!$AddMode && $BuildLevel == 0) {
                return;
            }

            if ($pricelist[$Element]['max'] < $BuildLevel) {
                return;
            }

            $elementTime = BuildFunctions::getBuildingTime($USER, $PLANET, $Element, null, !$AddMode, $BuildLevel, $ActualCount);
            $BuildEndTime = $CurrentQueue[$ActualCount - 1][3] + $elementTime;
            $CurrentQueue[] = [$Element, $BuildLevel, $elementTime, $BuildEndTime, $BuildMode];
            $PLANET['b_building_id'] = serialize($CurrentQueue);
        }
    }

    private function getQueueData()
    {
        $LNG =& Singleton()->LNG;
        $PLANET =& Singleton()->PLANET;
        $USER =& Singleton()->USER;
        $scriptData = [];
        $quickinfo = [];

        if (empty($PLANET['b_building']) || empty($PLANET['b_building_id'])) {
            return ['queue' => $scriptData, 'quickinfo' => $quickinfo];
        }

        $buildQueue     = unserialize($PLANET['b_building_id']);

        foreach ($buildQueue as $BuildArray) {
            if ($BuildArray[3] < TIMESTAMP && !$USER['urlaubs_modus']) {
                continue;
            }

            $quickinfo[$BuildArray[0]]  = $BuildArray[1];

            $scriptData[] = [
                'element'   => $BuildArray[0],
                'level'     => $BuildArray[1],
                'time'      => $BuildArray[2],
                'resttime'  => $USER['urlaubs_modus'] ? ($BuildArray[3] - $USER['urlaubs_start']) : ($BuildArray[3] - TIMESTAMP),
                'destroy'   => ($BuildArray[4] == 'destroy'),
                'endtime'   => _date('U', $BuildArray[3], $USER['timezone']),
                'display'   => _date($LNG['php_tdformat'], $BuildArray[3], $USER['timezone']),
            ];
        }

        return ['queue' => $scriptData, 'quickinfo' => $quickinfo];
    }

    public function show()
    {
        $ProdGrid =& Singleton()->ProdGrid;
        $LNG =& Singleton()->LNG;
        $resource =& Singleton()->resource;
        $reslist =& Singleton()->reslist;
        $PLANET =& Singleton()->PLANET;
        $USER =& Singleton()->USER;
        $pricelist =& Singleton()->pricelist;
        $TheCommand     = HTTP::_GP('cmd', '');

        // wellformed buildURLs
        if (!empty($TheCommand) && $_SERVER['REQUEST_METHOD'] === 'POST' && $USER['urlaubs_modus'] == 0) {
            $Element        = HTTP::_GP('building', 0);
            $ListID         = HTTP::_GP('listid', 0);
            switch ($TheCommand) {
                case 'cancel':
                    $this->cancelBuildingFromQueue();
                    break;
                case 'remove':
                    $this->removeBuildingFromQueue($ListID);
                    break;
                case 'insert':
                    $this->addBuildingToQueue($Element, true);
                    break;
                case 'destroy':
                    $this->addBuildingToQueue($Element, false);
                    break;
            }

            $this->ecoObj->saveBuilingQueue($PLANET);
            $this->redirectTo('game.php?page=buildings');
        }

        $config             = Config::get();

        $queueData          = $this->getQueueData();
        $Queue              = $queueData['queue'];
        $QueueCount         = count($Queue);

        $QueueDestroy = $QueueCount;
        foreach ($Queue as $QueueInfo) {
            if ($QueueInfo['destroy']) {
                $QueueDestroy = $QueueDestroy - 2;
            }
            $QueueDestroy = max(0, $QueueDestroy);
        }

        $CanBuildElement = isVacationMode($USER)
            || $config->max_elements_build == 0
            || $QueueCount < $config->max_elements_build;
        $CurrentMaxFields = CalculateMaxPlanetFields($PLANET);

        $RoomIsOk           = $PLANET['field_current'] < ($CurrentMaxFields - $QueueDestroy);

        /* Data for eval */
        $BuildEnergy        = $USER[$resource[113]];
        $BuildLevelFactor   = 10;
        $BuildTemp          = $PLANET['temp_max'];

        $BuildInfoList      = [];
        $Messages       = $USER['messages'];
        $Elements           = $reslist['allow'][$PLANET['planet_type']];
        $metproduction = $PLANET['metal_perhour'] + $config->metal_basic_income * $config->resource_multiplier;
        $kristproduction = $PLANET['crystal_perhour'] + $config->crystal_basic_income * $config->resource_multiplier;
        $deutproduction = $PLANET['deuterium_perhour']  + $config->deuterium_basic_income * $config->resource_multiplier;


        foreach ($Elements as $Element) {
            if (!BuildFunctions::isTechnologieAccessible($USER, $PLANET, $Element)) {
                continue;
            }

            $infoEnergy = "";

            if (isset($queueData['quickinfo'][$Element])) {
                $levelToBuild   = $queueData['quickinfo'][$Element];
            } else {
                $levelToBuild   = $PLANET[$resource[$Element]];
            }

            if (in_array($Element, $reslist['prod'])) {
                $BuildLevel = $PLANET[$resource[$Element]];
                $Need       = eval(ResourceUpdate::getProd($ProdGrid[$Element]['production'][911], $Element));

                $BuildLevel = $levelToBuild + 1;
                $Prod       = eval(ResourceUpdate::getProd($ProdGrid[$Element]['production'][911], $Element));

                $requireEnergy  = $Prod - $Need;
                $requireEnergy  = round($requireEnergy * $config->energySpeed);

                if ($requireEnergy < 0) {
                    $infoEnergy = sprintf(
                        $LNG['bd_need_engine'],
                        pretty_number(abs($requireEnergy)),
                        $LNG['tech'][911]
                    );
                } else {
                    $infoEnergy = sprintf(
                        $LNG['bd_more_engine'],
                        pretty_number(abs($requireEnergy)),
                        $LNG['tech'][911]
                    );
                }
            }

            $costResources = BuildFunctions::getElementPrice(
                $USER,
                $PLANET,
                $Element,
                false,
                $levelToBuild + 1
            );
            $costOverflow = BuildFunctions::getRestPrice($USER, $PLANET, $Element, $costResources);
            $timetobuild= 0;
            if(array_key_exists(901, $costOverflow) && $costOverflow[901]!=0 && $metproduction >0){
                $timetobuild=max($timetobuild,$costOverflow[901]/$metproduction);
            }
            if(array_key_exists(902, $costOverflow) && $costOverflow[902]!=0 && $kristproduction >0){
                $timetobuild=max($timetobuild,$costOverflow[902]/$kristproduction);
            }
            if(array_key_exists(903, $costOverflow) && $costOverflow[903]!=0 && $deutproduction >0){
                $timetobuild=max($timetobuild,$costOverflow[903]/$deutproduction);
            }
            $timetobuild = floor($timetobuild*3600) ;

            $elementTime = BuildFunctions::getBuildingTime($USER, $PLANET, $Element, $costResources);
            $destroyResources = BuildFunctions::getElementPrice($USER, $PLANET, $Element, true);
            $destroyTime = BuildFunctions::getBuildingTime($USER, $PLANET, $Element, $destroyResources);
            $destroyOverflow = BuildFunctions::getRestPrice($USER, $PLANET, $Element, $destroyResources);
            $buyable = $QueueCount != 0 || BuildFunctions::isElementBuyable($USER, $PLANET, $Element, $costResources);

            $BuildInfoList[$Element]    = [
                'level'             => $PLANET[$resource[$Element]],
                'maxLevel'          => $pricelist[$Element]['max'],
                'infoEnergy'        => $infoEnergy,
                'costResources'     => $costResources,
                'costOverflow'      => $costOverflow,
                'elementTime'       => $elementTime,
                'destroyResources'  => $destroyResources,
                'destroyTime'       => $destroyTime,
                'destroyOverflow'   => $destroyOverflow,
                'buyable'           => $buyable,
                'levelToBuild'      => $levelToBuild,
                'timetobuild'       => $timetobuild
            ];
        }


        if ($QueueCount != 0) {
            $this->tplObj->loadscript('buildlist.js');
        }

        $this->assign([
            'umode'             => $USER['urlaubs_modus'],
            'BuildInfoList'     => $BuildInfoList,
            'CanBuildElement'   => $CanBuildElement,
            'RoomIsOk'          => $RoomIsOk,
            'Queue'             => $Queue,
            'isBusy'            => [
                'shipyard' => !empty($PLANET['b_hangar_id']),
                'research' => $USER['b_tech_planet'] != 0,
            ],
            'HaveMissiles'      => (bool) $PLANET[$resource[503]] + $PLANET[$resource[502]],
            'messages'          => ($Messages > 0) ?
                (($Messages == 1) ? $LNG['ov_have_new_message']
                    : sprintf($LNG['ov_have_new_messages'], pretty_number($Messages))) : false,
        ]);

        $this->display('page.buildings.default.tpl');
    }
}
