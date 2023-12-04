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


class ShowInformationPage extends AbstractGamePage
{
    public static $requireModule = MODULE_INFORMATION;

    // protected $disableEcoSystem = true;

    public function __construct()
    {
        parent::__construct();
    }

    protected static function getNextJumpWaitTime($lastTime, $jumpLevel)
    {
        $DefaultWaitTimeLevel1  = 60; //60 min
        $DefaultWaitTimeLevel2  = 53; //53 min
        $DefaultWaitTimeLevel3  = 47; //47 min
        $DefaultWaitTimeLevel4  = 41; //41 min 
        $DefaultWaitTimeLevel5  = 36; //36 min
        $DefaultWaitTimeLevel10 = 17; //17 min
        $DefaultWaitTimeLevel15 = 10; //10 min
        $cooldown = 60;
        if ($jumpLevel == 1) {
            $cooldown = $DefaultWaitTimeLevel1;
        } elseif ($jumpLevel == 2) {
            $cooldown = $DefaultWaitTimeLevel2;
        } elseif ($jumpLevel == 3) {
            $cooldown = $DefaultWaitTimeLevel3;
        } elseif ($jumpLevel == 4) {
            $cooldown = $DefaultWaitTimeLevel4;
        } elseif ($jumpLevel == 5) {
            $cooldown = $DefaultWaitTimeLevel5;
        } elseif ($jumpLevel >= 10 && $jumpLevel < 15) {
            $cooldown = $DefaultWaitTimeLevel10;
        } elseif ($jumpLevel >= 15) {
            $cooldown = $DefaultWaitTimeLevel15;
        }
        $calculatedCooldown = ($cooldown * 60)/(Config::get()->jumpgate_factor);
        
        return $lastTime + ceil($calculatedCooldown);
    }

    public function sendFleet()
    {      
        $PLANET =& Singleton()->PLANET;
        $USER =& Singleton()->USER;
        $resource =& Singleton()->resource;
        $LNG =& Singleton()->LNG;
        $reslist =& Singleton()->reslist;
        $db = Database::get();

        $NextJumpTime = self::getNextJumpWaitTime($PLANET['last_jump_time'], $PLANET['sprungtor']);

        if (TIMESTAMP < $NextJumpTime) {
            $this->sendJSON([
                'message'   => $LNG['in_jump_gate_already_used'] . ' ' . pretty_time($NextJumpTime - TIMESTAMP),
                'error'     => true
            ]);
        }

        $TargetPlanet = HTTP::_GP('jmpto', (int) $PLANET['id']);

        $sql = "SELECT id, last_jump_time, sprungtor FROM %%PLANETS%% WHERE id = :targetID AND id_owner = :userID AND"
            . " sprungtor > 0;";
        $TargetGate = $db->selectSingle($sql, [
            ':targetID' => $TargetPlanet,
            ':userID'   => $USER['id'],
        ]);

        if (!isset($TargetGate) || $TargetPlanet == $PLANET['id']) {
            $this->sendJSON([
                'message' => $LNG['in_jump_gate_doesnt_have_one'],
                'error' => true,
            ]);
        }

        $NextJumpTime   = self::getNextJumpWaitTime($TargetGate['last_jump_time'], $TargetGate['sprungtor']);

        if (TIMESTAMP < $NextJumpTime) {
            $this->sendJSON([
                'message' => $LNG['in_jump_gate_not_ready_target'] . ' ' . pretty_time($NextJumpTime - TIMESTAMP),
                'error' => true,
            ]);
        }
        
        $ShipArray      = [];
        $SubQueryOri    = "";
        $SubQueryDes    = "";
        $Ships          = HTTP::_GP('ship', []);

        foreach ($reslist['fleet'] as $Ship) {
            if (!isset($Ships[$Ship]) || $Ship == 212) {
                continue;
            }

            $ShipArray[$Ship]   = max(0, min($Ships[$Ship], $PLANET[$resource[$Ship]]));

            if (empty($ShipArray[$Ship])) {
                continue;
            }

            $SubQueryOri        .= $resource[$Ship] . " = " . $resource[$Ship] . " - " . $ShipArray[$Ship] . ", ";
            $SubQueryDes        .= $resource[$Ship] . " = " . $resource[$Ship] . " + " . $ShipArray[$Ship] . ", ";
            $PLANET[$resource[$Ship]] -= $ShipArray[$Ship];
        }

        if (empty($SubQueryOri)) {
            $this->sendJSON([
                'message' => $LNG['in_jump_gate_error_data'],
                'error' => true,
            ]);
        }

        $array_merge =  [':planetID' => $PLANET['id'],':jumptime' => TIMESTAMP];
        $sql  = "UPDATE %%PLANETS%% SET " . $SubQueryOri . " `last_jump_time` = :jumptime WHERE id = :planetID;";
        $db->update($sql, $array_merge);

        $sql  = "UPDATE %%PLANETS%% SET " . $SubQueryDes . " `last_jump_time` = :jumptime WHERE id = :targetID;";
        $db->update($sql, [
            ':targetID' => $TargetPlanet,
            ':jumptime' => TIMESTAMP,
        ]);

        $PLANET['last_jump_time']   = TIMESTAMP;
        $NextJumpTime   = self::getNextJumpWaitTime($PLANET['last_jump_time'], $PLANET['sprungtor']);
        $this->sendJSON([
            'message' => sprintf($LNG['in_jump_gate_done'], pretty_time($NextJumpTime - TIMESTAMP)),
            'error' => false,
        ]);
    }

    private function getAvailableFleets()
    {
        $reslist =& Singleton()->reslist;
        $resource =& Singleton()->resource;
        $PLANET =& Singleton()->PLANET;
        $fleetList  = [];

        foreach ($reslist['fleet'] as $Ship) {
            if ($Ship == 212 || $PLANET[$resource[$Ship]] <= 0) {
                continue;
            }

            $fleetList[$Ship]   = $PLANET[$resource[$Ship]];
        }

        return $fleetList;
    }

    public function destroyMissiles()
    {
        $resource =& Singleton()->resource;
        $PLANET =& Singleton()->PLANET;
        $db = Database::get();

        $Missle = HTTP::_GP('missile', []);
        $PLANET[$resource[502]] -= max(0, min($Missle[502], $PLANET[$resource[502]]));
        $PLANET[$resource[503]] -= max(0, min($Missle[503], $PLANET[$resource[503]]));

        $sql = "UPDATE %%PLANETS%% SET " . $resource[502] . " = :resource502Val, " . $resource[503]
            . " = :resource503Val WHERE id = :planetID;";
        $db->update($sql, [
            ':resource502Val'   => $PLANET[$resource[502]],
            ':resource503Val'   => $PLANET[$resource[503]],
            ':planetID'         => $PLANET['id'],
        ]);

        $this->sendJSON([$PLANET[$resource[502]], $PLANET[$resource[503]]]);
    }

    private function getTargetGates()
    {
        $resource =& Singleton()->resource;
        $USER =& Singleton()->USER;
        $PLANET =& Singleton()->PLANET;
        $db = Database::get();

        $order = $USER['planet_sort_order'] == 1 ? "DESC" : "ASC" ;
        $sort  = $USER['planet_sort'];

        $sql = "SELECT id, name, galaxy, system, planet, last_jump_time, sprungtor, " . $resource[43]
            . " FROM %%PLANETS%% WHERE id != :planetID AND id_owner = :userID AND planet_type = '3' AND "
            . $resource[43] . " > 0 ORDER BY ";

        switch ($sort) {
            case 1:
                $sql .= 'galaxy ' . $order . ', system ' . $order . ', planet ' . $order . ', planet_type ' . $order;
                break;
            case 2:
                $sql .= 'name ' . $order;
                break;
            default:
                $sql .= 'id ' . $order;
                break;
        }


        $moonResult = $db->select($sql, [
            ':planetID'         => $PLANET['id'],
            ':userID'           => $USER['id'],
        ]);

        $moonList   = [];

        foreach ($moonResult as $moonRow) {
            $NextJumpTime               = self::getNextJumpWaitTime($moonRow['last_jump_time'], $moonRow['sprungtor']);
            $moonList[$moonRow['id']]   = '[' . $moonRow['galaxy'] . ':' . $moonRow['system'] . ':' . $moonRow['planet']
                . '] ' . $moonRow['name']
                . (TIMESTAMP < $NextJumpTime ? ' (' . pretty_time($NextJumpTime - TIMESTAMP) . ')' : '');
        }

        return $moonList;
    }

    public function show()
    {
        $USER =& Singleton()->USER;
        $PLANET =& Singleton()->PLANET;
        $LNG =& Singleton()->LNG;
        $resource =& Singleton()->resource;
        $pricelist =& Singleton()->pricelist;
        $reslist =& Singleton()->reslist;
        $CombatCaps =& Singleton()->CombatCaps;
        $ProdGrid =& Singleton()->ProdGrid;
        $elementID  = HTTP::_GP('id', 0);

        $this->setWindow('popup');
        $this->initTemplate();

        $productionTable    = [];
        $FleetInfo          = [];
        $MissileList        = [];
        $gateData           = [];

        $CurrentLevel       = 0;

        $ressIDs            = array_merge([], $reslist['resstype'][1], $reslist['resstype'][2]);

        if (in_array($elementID, $reslist['prod'])) {
            /* Data for eval */
            $BuildEnergy        = $USER[$resource[113]];
            $BuildTemp          = $PLANET['temp_max'];
            $BuildLevelFactor   = $PLANET[$resource[$elementID] . '_porcent'];

            $CurrentLevel       = $PLANET[$resource[$elementID]];
            $BuildStartLvl      = max($CurrentLevel - 2, 0);
            for ($BuildLevel = $BuildStartLvl; $BuildLevel < $BuildStartLvl + 15; $BuildLevel++) {
                foreach ($ressIDs as $ID) {
                    if (!isset($ProdGrid[$elementID]['production'][$ID])) {
                        continue;
                    }

                    $Production = eval(ResourceUpdate::getProd($ProdGrid[$elementID]['production'][$ID], $elementID));

                    if (in_array($ID, $reslist['resstype'][2])) {
                        $Production *= Config::get()->energySpeed;
                    } else {
                        $Production *= Config::get()->resource_multiplier;
                    }

                    $productionTable['production'][$BuildLevel][$ID]    = $Production;
                }
            }

            $productionTable['usedResource']    = array_keys($productionTable['production'][$BuildStartLvl]);
        }
        if (in_array($elementID, $reslist['storage'])) {
            $CurrentLevel       = $PLANET[$resource[$elementID]];
            $BuildStartLvl      = max($CurrentLevel - 2, 0);

            for ($BuildLevel = $BuildStartLvl; $BuildLevel < $BuildStartLvl + 15; $BuildLevel++) {
                foreach ($ressIDs as $ID) {
                    if (!isset($ProdGrid[$elementID]['storage'][$ID])) {
                        continue;
                    }

                    $production = round(eval(ResourceUpdate::getProd($ProdGrid[$elementID]['storage'][$ID])));
                    $production *= Config::get()->storage_multiplier;

                    $productionTable['storage'][$BuildLevel][$ID]   = $production;
                }
            }

            $productionTable['usedResource']    = array_keys($productionTable['storage'][$BuildStartLvl]);
        }
        if (in_array($elementID, $reslist['fleet'])) {
            $FleetInfo  = [
                'structure'     => $pricelist[$elementID]['cost'][901] + $pricelist[$elementID]['cost'][902],
                'tech'          => $pricelist[$elementID]['tech'],
                'attack'        => $CombatCaps[$elementID]['attack'],
                'shield'        => $CombatCaps[$elementID]['shield'],
                'capacity'      => $pricelist[$elementID]['capacity'],
                'speed1'        => $pricelist[$elementID]['speed'],
                'speed2'        => $pricelist[$elementID]['speed2'],
                'speed3'        => $pricelist[$elementID]['speed3'],
                'consumption1'  => $pricelist[$elementID]['consumption'],
                'consumption2'  => $pricelist[$elementID]['consumption2'],
                'rapidfire'     => [
                    'from'  => [],
                    'to'    => [],
                ],
            ];

            $fleetIDs   = array_merge($reslist['fleet'], $reslist['defense']);

            foreach ($fleetIDs as $fleetID) {
                if (isset($CombatCaps[$elementID]['sd']) && !empty($CombatCaps[$elementID]['sd'][$fleetID])) {
                    $FleetInfo['rapidfire']['to'][$fleetID] = $CombatCaps[$elementID]['sd'][$fleetID];
                }

                if (isset($CombatCaps[$fleetID]['sd']) && !empty($CombatCaps[$fleetID]['sd'][$elementID])) {
                    $FleetInfo['rapidfire']['from'][$fleetID] = $CombatCaps[$fleetID]['sd'][$elementID];
                }
            }
        }
        if (in_array($elementID, $reslist['defense'])) {
            $FleetInfo  = [
                'structure'     => $pricelist[$elementID]['cost'][901] + $pricelist[$elementID]['cost'][902],
                'attack'        => $CombatCaps[$elementID]['attack'],
                'shield'        => $CombatCaps[$elementID]['shield'],
                'rapidfire'     => [
                    'from'  => [],
                    'to'    => [],
                ],
            ];

            $fleetIDs   = array_merge($reslist['fleet'], $reslist['defense']);

            foreach ($fleetIDs as $fleetID) {
                if (isset($CombatCaps[$elementID]['sd']) && !empty($CombatCaps[$elementID]['sd'][$fleetID])) {
                    $FleetInfo['rapidfire']['to'][$fleetID] = $CombatCaps[$elementID]['sd'][$fleetID];
                }

                if (isset($CombatCaps[$fleetID]['sd']) && !empty($CombatCaps[$fleetID]['sd'][$elementID])) {
                    $FleetInfo['rapidfire']['from'][$fleetID] = $CombatCaps[$fleetID]['sd'][$elementID];
                }
            }
        }

        if ($elementID == 43 && $PLANET[$resource[43]] > 0) {
            $this->tplObj->loadscript('gate.js');
            $nextTime   = self::getNextJumpWaitTime($PLANET['last_jump_time'], $PLANET['sprungtor']);
            $gateData   = [
                'nextTime'  => _date($LNG['php_tdformat'], $nextTime, $USER['timezone']),
                'restTime'  => max(0, $nextTime - TIMESTAMP),
                'startLink' => $PLANET['name'] . ' ' . strip_tags(BuildPlanetAddressLink($PLANET)),
                'gateList'  => $this->getTargetGates(),
                'fleetList' => $this->getAvailableFleets(),
            ];
        } elseif ($elementID == 44 && $PLANET[$resource[44]] > 0) {
            $MissileList    = [
                502 => $PLANET[$resource[502]],
                503 => $PLANET[$resource[503]],
            ];
        }

        $this->assign([
            'elementID'             => $elementID,
            'productionTable'       => $productionTable,
            'CurrentLevel'          => $CurrentLevel,
            'MissileList'           => $MissileList,
            'FleetInfo'             => $FleetInfo,
            'gateData'              => $gateData,
            'spytechStatsEnabled'   => isModuleAvailable(MODULE_SPYTECH_DEPENDENT_STATS),
        ]);

        if ($elementID <= 900 || $elementID >= 930) {
            $this->assign([
                'Bonus' => BuildFunctions::getAvalibleBonus($elementID),
            ]);
        }

        $this->display('page.information.default.tpl');
    }
}
