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

require_once('AbstractGamePage.class.php');

class ShowResearchPage extends AbstractGamePage
{
    public static $requireModule = MODULE_RESEARCH;

    public function __construct()
    {
        parent::__construct();
    }

    private function checkLabSettingsInQueue()
    {
        $USER =& Singleton()->USER;
        $db = Database::get();
        $sql    = "SELECT * FROM %%PLANETS%% WHERE id_owner = :owner AND destruyed = 0 FOR UPDATE;";
        $planets    = $db->select($sql, [
            ':owner'    => $USER['id'],
        ]);

        foreach ($planets as $planet) {
            if (empty($planet['b_building']) || empty($planet['b_building_id'])) {
                continue;
            }

            $CurrentQueue       = unserialize($planet['b_building_id']);
            foreach ($CurrentQueue as $ListIDArray) {
                if ($ListIDArray[0] == 6 || $ListIDArray[0] == 31) {
                    return false;
                }
            }
        }

        return true;
    }

    private function cancelResearchFromQueue()
    {
        $PLANET =& Singleton()->PLANET;
        $USER =& Singleton()->USER;
        $resource =& Singleton()->resource;
        $CurrentQueue  = !empty($USER['b_tech_queue']) ? unserialize($USER['b_tech_queue']) : [];
        if (empty($CurrentQueue) || empty($USER['b_tech'])) {
            $USER['b_tech_queue']   = '';
            $USER['b_tech_planet']  = 0;
            $USER['b_tech_id']      = 0;
            $USER['b_tech']         = 0;

            return false;
        }

        $db = Database::get();

        $elementId = $USER['b_tech_id'];
        $costResources = BuildFunctions::getElementPrice(
            $USER,
            $PLANET,
            $elementId,
            false,
            $USER[$resource[$elementId]] + 1
        );

        if ($PLANET['id'] == $USER['b_tech_planet']) {
            $this->ecoObj->addResources($USER['b_tech_planet'], $costResources, $PLANET);
        } else {
            $this->ecoObj->addResources($USER['b_tech_planet'], $costResources);
        }

        $USER['b_tech_id']          = 0;
        $USER['b_tech']             = 0;
        $USER['b_tech_planet']      = 0;

        array_shift($CurrentQueue);

        if (count($CurrentQueue) == 0) {
            $USER['b_tech_queue']   = '';
            $USER['b_tech_planet']  = 0;
            $USER['b_tech_id']      = 0;
            $USER['b_tech']         = 0;
        } else {
            $BuildEndTime       = TIMESTAMP;
            $NewCurrentQueue    = [];
            $saveRessources = false; //Ressourcen speichern, falls eine der neuen techs auf dem aktuellen Planete ist
            foreach ($CurrentQueue as $ListIDArray) {
                if ($elementId == $ListIDArray[0] || empty($ListIDArray[0])) {
                    continue;
                }

                if ($ListIDArray[4] != $PLANET['id']) {
                    $sql = "SELECT " . $resource[6] ." , " . $resource[31] . ", id FROM %%PLANETS%% WHERE id = :id;";
                    $CPLANET = $db->selectSingle($sql, [
                        ':id'           => $ListIDArray[4],
                    ]);
                } else {
                    $CPLANET = $PLANET;
                    $saveRessources = true;
                }

                $CPLANET[$resource[31] . '_inter']    = $this->ecoObj->getNetworkLevel($USER, $CPLANET);
                $BuildEndTime += BuildFunctions::getBuildingTime(
                    $USER,
                    $CPLANET,
                    $ListIDArray[0],
                    null,
                    false,
                    $ListIDArray[1]
                );
                $ListIDArray[3] = $BuildEndTime;
                $NewCurrentQueue[] = $ListIDArray;
            }

            if (!empty($NewCurrentQueue)) {
                $USER['b_tech']             = TIMESTAMP;
                $USER['b_tech_queue']       = serialize($NewCurrentQueue);
                $this->ecoObj->setData($USER, $PLANET);
                $this->ecoObj->SetNextQueueTechOnTop($saveRessources);
                list($USER, $PLANET)        = $this->ecoObj->getData();
            } else {
                $USER['b_tech']             = 0;
                $USER['b_tech_queue']       = '';
            }
        }

        return true;
    }

    private function removeResearchFromQueue($QueueID)
    {
        $USER =& Singleton()->USER;
        $PLANET =& Singleton()->PLANET;
        $resource =& Singleton()->resource;
        $CurrentQueue  = !empty($USER['b_tech_queue']) ? unserialize($USER['b_tech_queue']) : [];
        if ($QueueID <= 1 || empty($CurrentQueue)) {
            return false;
        }

        $ActualCount   = count($CurrentQueue);
        if ($ActualCount <= 1) {
            return $this->cancelResearchFromQueue();
        }

        if (!isset($CurrentQueue[$QueueID - 2])) {
            return false;
        }

        $elementId      = $CurrentQueue[$QueueID - 2][0];
        $BuildEndTime   = $CurrentQueue[$QueueID - 2][3];
        unset($CurrentQueue[$QueueID - 1]);
        $NewCurrentQueue    = [];
        foreach ($CurrentQueue as $ID => $ListIDArray) {
            if ($ID < $QueueID - 1) {
                $NewCurrentQueue[]  = $ListIDArray;
            } else {
                if ($elementId == $ListIDArray[0]) {
                    continue;
                }

                if ($ListIDArray[4] != $PLANET['id']) {
                    $db = Database::get();

                    $sql = "SELECT " . $resource[6] . ", " . $resource[31] . ", id FROM %%PLANETS%% WHERE id = :id;";
                    $CPLANET = $db->selectSingle($sql, [
                        ':id'           => $ListIDArray[4]
                    ]);
                } else {
                    $CPLANET                = $PLANET;
                }

                $CPLANET[$resource[31] . '_inter']    = $this->ecoObj->getNetworkLevel($USER, $CPLANET);

                $BuildEndTime       += BuildFunctions::getBuildingTime($USER, $CPLANET, null, $ListIDArray[0]);
                $ListIDArray[3]     = $BuildEndTime;
                $NewCurrentQueue[]  = $ListIDArray;
            }
        }

        if (!empty($NewCurrentQueue)) {
            $USER['b_tech_queue'] = serialize($NewCurrentQueue);
        } else {
            $USER['b_tech_queue'] = "";
        }

        return true;
    }

    private function addResearchToQueue($elementId, $AddMode = true)
    {
        $PLANET =& Singleton()->PLANET;
        $USER =& Singleton()->USER;
        $resource =& Singleton()->resource;
        $reslist =& Singleton()->reslist;
        $pricelist =& Singleton()->pricelist;
        if (
            !in_array($elementId, $reslist['tech'])
            || !BuildFunctions::isTechnologieAccessible($USER, $PLANET, $elementId)
            || !$this->checkLabSettingsInQueue($PLANET)
        ) {
            return false;
        }

        $CurrentQueue       = !empty($USER['b_tech_queue']) ? unserialize($USER['b_tech_queue']) : [];

        if (!empty($CurrentQueue)) {
            $ActualCount    = count($CurrentQueue);
        } else {
            $CurrentQueue   = [];
            $ActualCount    = 0;
        }

        if (Config::get()->max_elements_tech != 0 && Config::get()->max_elements_tech <= $ActualCount) {
            return false;
        }

        $BuildLevel                 = $USER[$resource[$elementId]] + 1;
        if ($ActualCount == 0) {
            if ($pricelist[$elementId]['max'] < $BuildLevel) {
                return false;
            }

            $costResources      = BuildFunctions::getElementPrice($USER, $PLANET, $elementId, !$AddMode, $BuildLevel);

            if (!BuildFunctions::isElementBuyable($USER, $PLANET, $elementId, $costResources)) {
                return false;
            }

            $this->ecoObj->removeResources($PLANET['id'], $costResources, $PLANET);

            $elementTime                = BuildFunctions::getBuildingTime($USER, $PLANET, $elementId, $costResources);
            $BuildEndTime               = TIMESTAMP + $elementTime;

            $USER['b_tech_queue'] = serialize([[$elementId, $BuildLevel, $elementTime, $BuildEndTime, $PLANET['id']]]);
            $USER['b_tech'] = $BuildEndTime;
            $USER['b_tech_id'] = $elementId;
            $USER['b_tech_planet'] = $PLANET['id'];
        } else {
            $addLevel = 0;
            foreach ($CurrentQueue as $QueueSubArray) {
                if ($QueueSubArray[0] != $elementId) {
                    continue;
                }

                $addLevel++;
            }

            $BuildLevel                 += $addLevel;

            if ($pricelist[$elementId]['max'] < $BuildLevel) {
                return false;
            }

            $elementTime = BuildFunctions::getBuildingTime($USER, $PLANET, $elementId, null, !$AddMode, $BuildLevel);

            $BuildEndTime = $CurrentQueue[$ActualCount - 1][3] + $elementTime;
            $CurrentQueue[] = [$elementId, $BuildLevel, $elementTime, $BuildEndTime, $PLANET['id']];
            $USER['b_tech_queue'] = serialize($CurrentQueue);
        }

        return true;
    }

    private function getQueueData()
    {
        $LNG =& Singleton()->LNG;
        $PLANET =& Singleton()->PLANET;
        $USER =& Singleton()->USER;
        $scriptData     = [];
        $quickinfo      = [];

        if ($USER['b_tech'] == 0) {
            return ['queue' => $scriptData, 'quickinfo' => $quickinfo];
        }

        $CurrentQueue   = !empty($USER['b_tech_queue']) ? unserialize($USER['b_tech_queue']) : [];

        foreach ($CurrentQueue as $BuildArray) {
            if ($BuildArray[3] < TIMESTAMP && !$USER['urlaubs_modus']) {
                continue;
            }

            $PlanetName = '';

            $quickinfo[$BuildArray[0]]  = $BuildArray[1];

            if ($BuildArray[4] != $PLANET['id']) {
                $PlanetName     = $USER['PLANETS'][$BuildArray[4]]['name'];
            }

            $scriptData[] = [
                'element'   => $BuildArray[0],
                'level'     => $BuildArray[1],
                'time'      => $BuildArray[2],
                'resttime'  => $USER['urlaubs_modus'] ? ($BuildArray[3] - $USER['urlaubs_start']) : ($BuildArray[3] - TIMESTAMP),
                'destroy'   => ($BuildArray[4] == 'destroy'),
                'endtime'   => _date('U', $BuildArray[3], $USER['timezone']),
                'display'   => _date($LNG['php_tdformat'], $BuildArray[3], $USER['timezone']),
                'planet'    => $PlanetName,
            ];
        }

        return ['queue' => $scriptData, 'quickinfo' => $quickinfo];
    }

    public function show()
    {
        $PLANET =& Singleton()->PLANET;
        $USER =& Singleton()->USER;
        $LNG =& Singleton()->LNG;
        $resource =& Singleton()->resource;
        $reslist =& Singleton()->reslist;
        $pricelist =& Singleton()->pricelist;
        if ($PLANET[$resource[31]] == 0) {
            $this->printMessage($LNG['bd_lab_required']);
        }

        $TheCommand     = HTTP::_GP('cmd', '');
        $elementId      = HTTP::_GP('tech', 0);
        $ListID         = HTTP::_GP('listid', 0);

        $Messages       = $USER['messages'];

        $PLANET[$resource[31] . '_inter'] = ResourceUpdate::getNetworkLevel($USER, $PLANET);

        if (!empty($TheCommand) && $_SERVER['REQUEST_METHOD'] === 'POST' && $USER['urlaubs_modus'] == 0) {
            $db = Database::get();
            $db->startTransaction();
            $USER = $db->selectSingle("SELECT * FROM %%USERS%% WHERE id = :userID FOR UPDATE;", [':userID' => $USER['id']]);
            $PLANET = $db->selectSingle("SELECT * FROM %%PLANETS%% WHERE id = :planetId FOR UPDATE;", [':planetId' => $PLANET['id']]);
            $db->select("SELECT * FROM %%PLANETS%% WHERE id_owner = :userID FOR UPDATE;", [':userID' => $USER['id']]);
            $PLANET[$resource[31] . '_inter'] = ResourceUpdate::getNetworkLevel($USER, $PLANET);
            switch ($TheCommand) {
                case 'cancel':
                    $this->cancelResearchFromQueue();
                    break;
                case 'remove':
                    $this->removeResearchFromQueue($ListID);
                    break;
                case 'insert':
                    $this->addResearchToQueue($elementId, true);
                    break;
                case 'destroy':
                    $this->addResearchToQueue($elementId, false);
                    break;
            }

            $this->ecoObj->saveTechQueue($USER);
            $db->commit();
            $this->redirectTo('game.php?page=research');
        }

        $bContinue      = $this->checkLabSettingsInQueue($PLANET);

        $queueData      = $this->getQueueData();
        $TechQueue      = $queueData['queue'];
        $QueueCount     = count($TechQueue);
        $ResearchList   = [];
        $config         = Config::get();
        $metproduction  = $PLANET['metal_perhour'] + $config->metal_basic_income * $config->resource_multiplier;
        $kristproduction = $PLANET['crystal_perhour'] + $config->crystal_basic_income * $config->resource_multiplier;
        $deutproduction = $PLANET['deuterium_perhour']  + $config->deuterium_basic_income * $config->resource_multiplier;
        foreach ($reslist['tech'] as $elementId) {
            if (!BuildFunctions::isTechnologieAccessible($USER, $PLANET, $elementId)) {
                continue;
            }

            if (isset($queueData['quickinfo'][$elementId])) {
                $levelToBuild   = $queueData['quickinfo'][$elementId];
            } else {
                $levelToBuild   = $USER[$resource[$elementId]];
            }

            $costResources = BuildFunctions::getElementPrice($USER, $PLANET, $elementId, false, $levelToBuild + 1);
            $costOverflow = BuildFunctions::getRestPrice($USER, $PLANET, $elementId, $costResources);
            $timetobuild= 0;
            if (array_key_exists(901, $costOverflow) && $costOverflow[901]!=0) {
                $timetobuild=max($timetobuild, $costOverflow[901]/$metproduction);
            }
            if (array_key_exists(902, $costOverflow) && $costOverflow[902]!=0) {
                $timetobuild=max($timetobuild, $costOverflow[902]/$kristproduction);
            }
            if (array_key_exists(903, $costOverflow) && $costOverflow[903]!=0 && $deutproduction >0) {
                $timetobuild=max($timetobuild, $costOverflow[903]/$deutproduction);
            }
            $timetobuild = floor($timetobuild*3600) ;
            $elementTime = BuildFunctions::getBuildingTime($USER, $PLANET, $elementId, $costResources);
            $buyable = $QueueCount != 0 || BuildFunctions::isElementBuyable($USER, $PLANET, $elementId, $costResources);

            $ResearchList[$elementId]   = [
                'id'                => $elementId,
                'level'             => $USER[$resource[$elementId]],
                'maxLevel'          => $pricelist[$elementId]['max'],
                'costResources'     => $costResources,
                'costOverflow'      => $costOverflow,
                'elementTime'       => $elementTime,
                'buyable'           => $buyable,
                'levelToBuild'      => $levelToBuild,
                'timetobuild'       => $timetobuild
            ];
        }

        $this->assign([
            'umode'         => $USER['urlaubs_modus'],
            'ResearchList'  => $ResearchList,
            'IsLabinBuild'  => !$bContinue,
            'IsFullQueue'   => Config::get()->max_elements_tech == 0
                || Config::get()->max_elements_tech == count($TechQueue),
            'Queue'         => $TechQueue,
            'messages'      => ($Messages > 0) ? (($Messages == 1)
                ? $LNG['ov_have_new_message']
                : sprintf($LNG['ov_have_new_messages'], pretty_number($Messages))) : false,
        ]);

        $this->display('page.research.default.tpl');
    }
}
