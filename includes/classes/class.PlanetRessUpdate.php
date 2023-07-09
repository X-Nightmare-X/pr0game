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

class ResourceUpdate
{
    /**
     * reference of the config object
     * @var Config
     */
    private $config = null;

    private $TIME = null;
    private $HASH = null;
    private $ProductionTime = null;

    private $Build = true;
    private $Tech = true;

    private $PLANET = [];
    private $USER = [];
    private $Builded = [];

    public function __construct($Build = true, $Tech = true)
    {
        $this->Build = $Build;
        $this->Tech = $Tech;

        $this->Builded[RESOURCE_METAL] = 0;
        $this->Builded[RESOURCE_CRYSTAL] = 0;
        $this->Builded[RESOURCE_DEUT] = 0;
    }

    public function setData($USER, $PLANET)
    {
        $this->USER = $USER;
        $this->PLANET = $PLANET;
    }

    public function getData()
    {
        return [$this->USER, $this->PLANET];
    }

    public function ReturnVars()
    {
        return [$this->USER, $this->PLANET];
    }

    public function CreateHash()
    {
        $reslist =& Singleton()->reslist;
        $resource =& Singleton()->resource;
        $Hash = [];
        foreach ($reslist['prod'] as $ID) {
            $Hash[] = $this->PLANET[$resource[$ID]];
            $Hash[] = $this->PLANET[$resource[$ID] . '_porcent'];
        }

        $ressource = array_merge([], $reslist['resstype'][1], $reslist['resstype'][2]);
        foreach ($ressource as $ID) {
            $Hash[] = $this->config->{$resource[$ID] . '_basic_income'};
        }

        $Hash[] = $this->config->resource_multiplier;
        $Hash[] = $this->config->storage_multiplier;
        $Hash[] = $this->config->energySpeed;
        $Hash[] = $this->PLANET[$resource[22]];
        $Hash[] = $this->PLANET[$resource[23]];
        $Hash[] = $this->PLANET[$resource[24]];
        $Hash[] = $this->USER[$resource[113]];
        $Hash[] = $this->USER[$resource[131]];
        $Hash[] = $this->USER[$resource[132]];
        $Hash[] = $this->USER[$resource[133]];
        return md5(implode("::", $Hash));
    }

    public function CalcResource($USER, $PLANET, $SAVE = false, $TIME = null, $HASH = true)
    {
        $this->USER = $USER;
        $this->PLANET = $PLANET;
        $this->TIME = is_null($TIME) ? TIMESTAMP : $TIME;
        $this->config = Config::get($this->USER['universe']);

        if ($this->USER['urlaubs_modus'] == 1 || $this->config->uni_status == STATUS_CLOSED || $this->config->uni_status == STATUS_REG_ONLY) {
            return $this->ReturnVars();
        }

        if ($this->Build) {
            $this->ShipyardQueue();

            while (($this->Tech && $this->USER['b_tech'] != 0 && $this->USER['b_tech'] < $this->TIME) ||
                    ($this->PLANET['b_building'] != 0 && $this->PLANET['b_building'] < $this->TIME)) {
                if ($this->PLANET['b_building'] != 0 && (!$this->Tech || $this->USER['b_tech'] == 0 || $this->PLANET['b_building'] <= $this->USER['b_tech'])) {
                    if ($this->CheckPlanetBuildingQueue()) {
                        $this->SetNextQueueElementOnTop();
                    }
                } elseif ($this->Tech && $this->USER['b_tech'] != null) {
                    if ($this->CheckUserTechQueue()) {
                        $this->SetNextQueueTechOnTop();
                    }
                }
            }
        }

        $this->UpdateResource($this->TIME, $HASH);

        if ($SAVE === true) {
            $this->SavePlanetToDB($this->USER, $this->PLANET);
        }

        return $this->ReturnVars();
    }

    private function UpdateResource($TIME, $HASH = false)
    {
        $this->ProductionTime = ($TIME - $this->PLANET['last_update']);

        if ($this->ProductionTime > 0) {
            $this->PLANET['last_update'] = $TIME;
            if ($HASH === false) {
                $this->ReBuildCache();
            } else {
                $this->HASH = $this->CreateHash();

                if ($this->PLANET['eco_hash'] !== $this->HASH) {
                    $this->PLANET['eco_hash'] = $this->HASH;
                    $this->ReBuildCache();
                }
            }
            $this->ExecCalc();
        }
    }

    private function ExecCalc()
    {
        if ($this->PLANET['planet_type'] == 3) {
            return;
        }

        $MaxMetalStorage = $this->PLANET['metal_max'] * $this->config->max_overflow;
        $MaxCristalStorage = $this->PLANET['crystal_max'] * $this->config->max_overflow;
        $MaxDeuteriumStorage = $this->PLANET['deuterium_max'] * $this->config->max_overflow;

        $MetalTheoretical = $this->ProductionTime * (
                ($this->config->metal_basic_income * $this->config->resource_multiplier)
                + $this->PLANET['metal_perhour']
            ) / 3600;

        $this->setProduction(RESOURCE_METAL, $MetalTheoretical, $MaxMetalStorage);

        $CristalTheoretical = $this->ProductionTime * (
                ($this->config->crystal_basic_income * $this->config->resource_multiplier)
                + $this->PLANET['crystal_perhour']
            ) / 3600;

        $this->setProduction(RESOURCE_CRYSTAL, $CristalTheoretical, $MaxCristalStorage);

        $DeuteriumTheoretical = $this->ProductionTime * (
                ($this->config->deuterium_basic_income * $this->config->resource_multiplier)
                + $this->PLANET['deuterium_perhour']
            ) / 3600;

        $this->setProduction(RESOURCE_DEUT, $DeuteriumTheoretical, $MaxDeuteriumStorage);
    }

    private function setProduction($resource_type, $produced, $storage)
    {
        $resource =& Singleton()->resource;

        if ($produced < 0) {
            if ($this->PLANET[$resource[$resource_type]] + $produced < 0) {
                $produced = 1 + (floor($this->PLANET[$resource[$resource_type]]) * -1);
            }
        }
        elseif ($this->PLANET[$resource[$resource_type]] <= $storage) {
            if ($this->PLANET[$resource[$resource_type]] + $produced > $storage) {
                $produced = $storage - $this->PLANET[$resource[$resource_type]];
            }
        }
        else {
            $produced = 0;
        }

        $this->Builded[$resource_type] += $produced;
        $this->PLANET[$resource[$resource_type]] += $produced;
    }

    public static function getProd($Calculation, $Element = false)
    {
        $resource =& Singleton()->resource;
        $reslist =& Singleton()->reslist;
        $USER =& Singleton()->USER;
        $PLANET =& Singleton()->PLANET;
        if ($Element) {
            $BuildEnergy = $USER[$resource[113]];
            $BuildTemp = $PLANET['temp_max'];
            $BuildLevelFactor = $PLANET[$resource[$Element] . '_porcent'];

            if (in_array($Element, array_merge($reslist['build'], $reslist['fleet'], $reslist['defense']))) {
                $BuildLevel = $PLANET[$resource[$Element]];
            } elseif (in_array($Element, $reslist['tech'])) {
                $BuildLevel = $USER[$resource[$Element]];
            } else {
                $BuildLevel = 0;
            }

            $Calculation = str_replace('this->', "", $Calculation);
        }

        return 'return ' . $Calculation . ';';
    }

    public static function getNetworkLevel($USER, $PLANET)
    {
        $resource =& Singleton()->resource;

        $researchLevelList = [$PLANET[$resource[31]]];
        if ($USER[$resource[123]] > 0) {
            $sql = 'SELECT ' . $resource[31]
                    . ' FROM %%PLANETS%% WHERE id != :planetId AND id_owner = :userId AND destruyed = 0 ORDER BY '
                    . $resource[31] . ' DESC LIMIT :limit;';

            $researchResult = Database::get()->select($sql, [
                ':limit'    => (int) $USER[$resource[123]],
                ':planetId' => $PLANET['id'],
                ':userId'   => $USER['id']
            ]);

            foreach ($researchResult as $researchRow) {
                $researchLevelList[] = $researchRow[$resource[31]];
            }
        }

        return $researchLevelList;
    }

    public function ReBuildCache()
    {
        $ProdGrid =& Singleton()->ProdGrid;
        $resource =& Singleton()->resource;
        $reslist =& Singleton()->reslist;
        if ($this->PLANET['planet_type'] == 3) {
            $this->config->metal_basic_income = 0;
            $this->config->crystal_basic_income = 0;
            $this->config->deuterium_basic_income = 0;
        }

        $temp = [
            901 => [
                'max'   => 0,
                'plus'  => 0,
                'minus' => 0,
            ],
            902 => [
                'max'   => 0,
                'plus'  => 0,
                'minus' => 0,
            ],
            903 => [
                'max'   => 0,
                'plus'  => 0,
                'minus' => 0,
            ],
            911 => [
                'plus'  => 0,
                'minus' => 0,
            ]
        ];

        /* Data for eval */
        $BuildTemp = $this->PLANET['temp_max'];
        $BuildEnergy = $this->USER[$resource[113]];

        foreach ($reslist['storage'] as $ProdID) {
            foreach ($reslist['resstype'][1] as $ID) {
                if (!isset($ProdGrid[$ProdID]['storage'][$ID])) {
                    continue;
                }

                /* Data for eval */
                $BuildLevel = $this->PLANET[$resource[$ProdID]];
                $temp[$ID]['max']   += round(eval(self::getProd($ProdGrid[$ProdID]['storage'][$ID])));
            }
        }

        $ressIDs = array_merge([], $reslist['resstype'][1], $reslist['resstype'][2]);

        foreach ($reslist['prod'] as $ProdID) {
            /* Data for eval */
            $BuildLevelFactor = $this->PLANET[$resource[$ProdID] . '_porcent'];
            $BuildLevel = $this->PLANET[$resource[$ProdID]];

            foreach ($ressIDs as $ID) {
                if (!isset($ProdGrid[$ProdID]['production'][$ID])) {
                    continue;
                }

                $Production = eval(self::getProd($ProdGrid[$ProdID]['production'][$ID]));

                if ($Production > 0) {
                    $temp[$ID]['plus']  += $Production;
                } else {
                    if (in_array($ID, $reslist['resstype'][1]) && $this->PLANET[$resource[$ID]] == 0) {
                        continue;
                    }

                    $temp[$ID]['minus'] += $Production;
                }
            }
        }

        $this->PLANET['metal_max'] = $temp[901]['max'] * $this->config->storage_multiplier;
        $this->PLANET['crystal_max'] = $temp[902]['max'] * $this->config->storage_multiplier;
        $this->PLANET['deuterium_max'] = $temp[903]['max'] * $this->config->storage_multiplier;

        $this->PLANET['energy'] = round($temp[911]['plus'] * $this->config->energySpeed);
        $this->PLANET['energy_used'] = $temp[911]['minus'] * $this->config->energySpeed;
        if ($this->PLANET['energy_used'] == 0) {
            $this->PLANET['metal_perhour'] = 0;
            $this->PLANET['crystal_perhour'] = 0;
            $this->PLANET['deuterium_perhour'] = 0;
        } else {
            $prodLevel = min(1, $this->PLANET['energy'] / abs($this->PLANET['energy_used']));

            $this->PLANET['metal_perhour'] = (
                $temp[901]['plus'] * (1 + 0.02 * $this->USER[$resource[131]]) * $prodLevel + $temp[901]['minus']
            ) * $this->config->resource_multiplier;

            $this->PLANET['crystal_perhour'] = (
                $temp[902]['plus'] * (1 + 0.02 * $this->USER[$resource[132]]) * $prodLevel + $temp[902]['minus']
            ) * $this->config->resource_multiplier;

            $this->PLANET['deuterium_perhour'] = (
                $temp[903]['plus'] * (1 + 0.02 * $this->USER[$resource[133]]) * $prodLevel + $temp[903]['minus']
            ) * $this->config->resource_multiplier;
        }
    }

    private function ShipyardQueue()
    {
        $resource =& Singleton()->resource;

        $BuildQueue = !empty($this->PLANET['b_hangar_id']) ? unserialize($this->PLANET['b_hangar_id']) : [];
        if (!$BuildQueue) {
            $this->PLANET['b_hangar'] = 0;
            $this->PLANET['b_hangar_id'] = '';
            return false;
        }

        $this->PLANET['b_hangar']   += ($this->TIME - $this->PLANET['last_update']);
        $BuildArray = [];
        foreach ($BuildQueue as $Item) {
            $AcumTime = BuildFunctions::getBuildingTime($this->USER, $this->PLANET, $Item[0]);
            $BuildArray[] = [$Item[0], $Item[1], $AcumTime];
        }

        $NewQueue = [];
        $Done = false;
        foreach ($BuildArray as $Item) {
            $Element = $Item[0];
            $Count = $Item[1];

            if ($Done == false) {
                $BuildTime = $Item[2];
                $Element = (int)$Element;
                if ($BuildTime == 0) {
                    if (!isset($this->Builded[$Element])) {
                        $this->Builded[$Element] = 0;
                    }

                    $this->Builded[$Element]            += $Count;
                    $this->PLANET[$resource[$Element]]  += $Count;
                    continue;
                }

                $Build = max(min(floor($this->PLANET['b_hangar'] / $BuildTime), $Count), 0);

                if ($Build == 0) {
                    $NewQueue[] = [$Element, $Count];
                    $Done = true;
                    continue;
                }

                if (!isset($this->Builded[$Element])) {
                    $this->Builded[$Element] = 0;
                }

                $this->Builded[$Element]            += $Build;
                $this->PLANET['b_hangar']           -= $Build * $BuildTime;
                $this->PLANET[$resource[$Element]]  += $Build;
                $Count                              -= $Build;

                if ($Count == 0) {
                    continue;
                } else {
                    $Done = true;
                }
            }
            $NewQueue[] = [$Element, $Count];
        }
        $this->PLANET['b_hangar_id'] = !empty($NewQueue) ? serialize($NewQueue) : '';

        return true;
    }

    private function CheckPlanetBuildingQueue()
    {
        $resource =& Singleton()->resource;
        $reslist =& Singleton()->reslist;
        if (empty($this->PLANET['b_building_id']) || empty($this->PLANET['b_building_id']) || $this->PLANET['b_building'] > $this->TIME) {
            return false;
        }

        $CurrentQueue = unserialize($this->PLANET['b_building_id']);

        $Element = $CurrentQueue[0][0];
        $BuildLevel = $CurrentQueue[0][1];
        $BuildEndTime = $CurrentQueue[0][3];
        $BuildMode = $CurrentQueue[0][4];

        if (!isset($this->Builded[$Element])) {
            $this->Builded[$Element] = 0;
        }

        $OnHash = in_array($Element, $reslist['prod']);
        $this->UpdateResource($BuildEndTime, !$OnHash);

        if ($BuildMode == 'build') {
            $this->PLANET['field_current']      += 1;
            $this->PLANET[$resource[$Element]]  = $BuildLevel;
            $this->Builded[$Element]            = $BuildLevel;
        } else {
            $this->PLANET['field_current']      -= 1;
            $this->PLANET[$resource[$Element]]  = $BuildLevel - 1;
            $this->Builded[$Element]            = $BuildLevel - 1;
        }

        array_shift($CurrentQueue);

        if (count($CurrentQueue) == 0) {
            $this->PLANET['b_building'] = 0;
            $this->PLANET['b_building_id'] = '';

            return false;
        } else {
            $this->PLANET['b_building_id'] = serialize($CurrentQueue);
            return true;
        }
    }

    public function SetNextQueueElementOnTop()
    {
        $resource =& Singleton()->resource;
        $LNG =& Singleton()->LNG;
        if (empty($this->PLANET['b_building_id'])) {
            $this->PLANET['b_building'] = 0;
            $this->PLANET['b_building_id'] = '';
            return false;
        }

        $CurrentQueue = !empty($this->PLANET['b_building_id']) ? unserialize($this->PLANET['b_building_id']) : [];
        $Loop = true;

        $BuildEndTime = 0;
        $NewQueue = '';

        while ($Loop === true) {
            $ListIDArray = $CurrentQueue[0];
            $Element = $ListIDArray[0];
            $Level = $ListIDArray[1];
            $BuildMode = $ListIDArray[4];
            $ForDestroy = ($BuildMode == 'destroy') ? true : false;
            $costResources = BuildFunctions::getElementPrice($this->USER, $this->PLANET, $Element, $ForDestroy, $Level);
            $BuildTime = BuildFunctions::getBuildingTime($this->USER, $this->PLANET, $Element, $costResources);
            $HaveResources = BuildFunctions::isElementBuyable($this->USER, $this->PLANET, $Element, $costResources);
            $BuildEndTime = $this->PLANET['b_building'] + $BuildTime;
            $CurrentQueue[0] = [$Element, $Level, $BuildTime, $BuildEndTime, $BuildMode];
            $HaveNoMoreLevel = false;

            if ($ForDestroy && $this->PLANET[$resource[$Element]] == 0) {
                $HaveResources = false;
                $HaveNoMoreLevel = true;
            }

            if ($HaveResources === true) {
                if (isset($costResources[RESOURCE_METAL])) {
                    $this->Builded[RESOURCE_METAL] -= $costResources[RESOURCE_METAL];
                    $this->PLANET[$resource[RESOURCE_METAL]] -= $costResources[RESOURCE_METAL];
                }
                if (isset($costResources[RESOURCE_CRYSTAL])) {
                    $this->Builded[RESOURCE_CRYSTAL] -= $costResources[RESOURCE_CRYSTAL];
                    $this->PLANET[$resource[RESOURCE_CRYSTAL]] -= $costResources[RESOURCE_CRYSTAL];
                }
                if (isset($costResources[RESOURCE_DEUT])) {
                    $this->Builded[RESOURCE_DEUT] -= $costResources[RESOURCE_DEUT];
                    $this->PLANET[$resource[RESOURCE_DEUT]] -= $costResources[RESOURCE_DEUT];
                }
                $NewQueue = serialize($CurrentQueue);
                $Loop = false;
            } else {
                if ($this->USER['hof'] == 1) {
                    if ($HaveNoMoreLevel) {
                        $Message = sprintf($LNG['sys_nomore_level'], $LNG['tech'][$Element]);
                    } else {
                        if (!isset($costResources[RESOURCE_METAL])) {
                            $costResources[RESOURCE_METAL] = 0;
                        }
                        if (!isset($costResources[RESOURCE_CRYSTAL])) {
                            $costResources[RESOURCE_CRYSTAL] = 0;
                        }
                        if (!isset($costResources[RESOURCE_DEUT])) {
                            $costResources[RESOURCE_DEUT] = 0;
                        }

                        $LNG =& Singleton()->LNG;

                        if (empty($LNG)) {
                            // Fallback language
                            $LNG = new Language('en');
                            $LNG->includeData(['L18N', 'INGAME', 'TECH', 'CUSTOM']);
                        }

                        $Message = sprintf(
                            $LNG['sys_notenough_money'],
                            $this->PLANET['name'],
                            $this->PLANET['id'],
                            $this->PLANET['galaxy'],
                            $this->PLANET['system'],
                            $this->PLANET['planet'],
                            $LNG['tech'][$Element],
                            pretty_number($this->PLANET['metal']),
                            $LNG['tech'][RESOURCE_METAL],
                            pretty_number($this->PLANET['crystal']),
                            $LNG['tech'][RESOURCE_CRYSTAL],
                            pretty_number($this->PLANET['deuterium']),
                            $LNG['tech'][RESOURCE_DEUT],
                            pretty_number($costResources[RESOURCE_METAL]),
                            $LNG['tech'][RESOURCE_METAL],
                            pretty_number($costResources[RESOURCE_CRYSTAL]),
                            $LNG['tech'][RESOURCE_CRYSTAL],
                            pretty_number($costResources[RESOURCE_DEUT]),
                            $LNG['tech'][RESOURCE_DEUT]
                        );
                    }

                    PlayerUtil::sendMessage(
                        $this->USER['id'],
                        0,
                        $LNG['sys_buildlist'],
                        99,
                        $LNG['sys_buildlist_fail'],
                        $Message,
                        $this->TIME
                    );
                }

                array_shift($CurrentQueue);

                if (count($CurrentQueue) == 0) {
                    $BuildEndTime = 0;
                    $NewQueue = '';
                    $Loop = false;
                } else {
                    $BaseTime = $BuildEndTime - $BuildTime;
                    $NewQueue = [];
                    foreach ($CurrentQueue as $ID => $ListIDArray) {
                        $ListIDArray[2] = BuildFunctions::getBuildingTime(
                            $this->USER,
                            $this->PLANET,
                            $ListIDArray[0],
                            null,
                            $ListIDArray[4] == 'destroy',
                            $ID
                        );
                        $BaseTime += $ListIDArray[2];
                        $ListIDArray[3] = $BaseTime;
                        $NewQueue[] = $ListIDArray;
                    }
                    $CurrentQueue = $NewQueue;
                }
            }
        }

        $this->PLANET['b_building'] = $BuildEndTime;
        $this->PLANET['b_building_id'] = $NewQueue;

        return true;
    }

    private function CheckUserTechQueue()
    {
        $resource =& Singleton()->resource;

        if (empty($this->USER['b_tech_id']) || $this->USER['b_tech'] > $this->TIME) {
            return false;
        }

        if (!isset($this->Builded[$this->USER['b_tech_id']])) {
            $this->Builded[$this->USER['b_tech_id']] = 0;
        }

        $CurrentQueue = !empty($this->USER['b_tech_queue']) ? unserialize($this->USER['b_tech_queue']) : [];

        $Element = $this->USER['b_tech_id'];
        $BuildLevel = $CurrentQueue[0][1];
        $BuildEndTime = $this->USER['b_tech'];

        $hashTechs = [113, 131, 132, 133];
        if (in_array($Element, $hashTechs)) {
            $sql = 'SELECT * FROM %%PLANETS%% WHERE id_owner = :userId AND id <> :planetId FOR UPDATE;';
            $PLANETS = Database::get()->select($sql, [
                ':userId' => $this->USER['id'],
                ':planetId' => $this->PLANET['id'],
            ]);
            foreach ($PLANETS as $planetId => $cplanet) {
                $ressUpdate = new ResourceUpdate(true, false);
                $ressUpdate->CalcResource($this->USER, $cplanet, true, $BuildEndTime, false);
            }
            $this->UpdateResource($BuildEndTime, false);
        }

        $this->Builded[$Element] = $BuildLevel;
        $this->USER[$resource[$Element]] = $BuildLevel;

        array_shift($CurrentQueue);

        if (count($CurrentQueue) == 0) {
            $this->USER['b_tech'] = 0;
            $this->USER['b_tech_id'] = 0;
            $this->USER['b_tech_planet'] = 0;
            $this->USER['b_tech_queue'] = '';
            return false;
        } else {
            $this->USER['b_tech_id'] = 0;
            $this->USER['b_tech_queue'] = serialize(array_values($CurrentQueue));
            return true;
        }
    }

    public function SetNextQueueTechOnTop($save = false)
    {
        $resource =& Singleton()->resource;
        $LNG =& Singleton()->LNG;
        if (empty($this->USER['b_tech_queue'])) {
            $this->USER['b_tech'] = 0;
            $this->USER['b_tech_id'] = 0;
            $this->USER['b_tech_planet'] = 0;
            $this->USER['b_tech_queue'] = '';
            return false;
        }

        $CurrentQueue = !empty($this->USER['b_tech_queue']) ? unserialize($this->USER['b_tech_queue']) : [];
        $Loop = true;
        while ($Loop == true) {
            $ListIDArray = $CurrentQueue[0];
            $isAnotherPlanet = $ListIDArray[4] != $this->PLANET['id'];
            if ($isAnotherPlanet) {
                $sql = 'SELECT * FROM %%PLANETS%% WHERE id = :planetId FOR UPDATE;';
                $PLANET = Database::get()->selectSingle($sql, [
                    ':planetId' => $ListIDArray[4],
                ]);

                $RPLANET = new ResourceUpdate(true, false);
                list(, $PLANET) = $RPLANET->CalcResource($this->USER, $PLANET, true, $this->USER['b_tech']);
            } else {
                $PLANET = $this->PLANET;
            }

            $PLANET[$resource[31] . '_inter'] = self::getNetworkLevel($this->USER, $PLANET);

            $Element = $ListIDArray[0];
            $Level = $ListIDArray[1];
            $costResources = BuildFunctions::getElementPrice($this->USER, $PLANET, $Element, false, $Level);
            $BuildTime = BuildFunctions::getBuildingTime($this->USER, $PLANET, $Element, $costResources);
            $HaveResources = BuildFunctions::isElementBuyable($this->USER, $PLANET, $Element, $costResources);
            $BuildEndTime = $this->USER['b_tech'] + $BuildTime;
            $CurrentQueue[0] = [$Element, $Level, $BuildTime, $BuildEndTime, $PLANET['id']];

            if ($HaveResources == true) {
                if (isset($costResources[RESOURCE_METAL])) {
                    if (!$isAnotherPlanet) {
                        $this->Builded[RESOURCE_METAL] -= $costResources[RESOURCE_METAL];
                    }
                    $PLANET[$resource[RESOURCE_METAL]] -= $costResources[RESOURCE_METAL];
                }
                if (isset($costResources[RESOURCE_CRYSTAL])) {
                    if (!$isAnotherPlanet) {
                        $this->Builded[RESOURCE_CRYSTAL] -= $costResources[RESOURCE_CRYSTAL];
                    }
                    $PLANET[$resource[RESOURCE_CRYSTAL]] -= $costResources[RESOURCE_CRYSTAL];
                }
                if (isset($costResources[RESOURCE_DEUT])) {
                    if (!$isAnotherPlanet) {
                        $this->Builded[RESOURCE_DEUT] -= $costResources[RESOURCE_DEUT];
                    }
                    $PLANET[$resource[RESOURCE_DEUT]] -= $costResources[RESOURCE_DEUT];
                }

                if ($isAnotherPlanet) {
                    $this->removeResources($ListIDArray[4], $costResources);
                }

                $this->USER['b_tech_id'] = $Element;
                $this->USER['b_tech'] = $BuildEndTime;
                $this->USER['b_tech_planet'] = $PLANET['id'];
                $this->USER['b_tech_queue'] = serialize($CurrentQueue);

                $Loop = false;
            } else {
                if ($this->USER['hof'] == 1) {
                    if (!isset($costResources[RESOURCE_METAL])) {
                        $costResources[RESOURCE_METAL] = 0;
                    }
                    if (!isset($costResources[RESOURCE_CRYSTAL])) {
                        $costResources[RESOURCE_CRYSTAL] = 0;
                    }
                    if (!isset($costResources[RESOURCE_DEUT])) {
                        $costResources[RESOURCE_DEUT] = 0;
                    }

                    $LNG =& Singleton()->LNG;

                    if (empty($LNG)) {
                        // Fallback language
                        $LNG = new Language('en');
                        $LNG->includeData(['L18N', 'INGAME', 'TECH', 'CUSTOM']);
                    }

                    $Message = sprintf(
                        $LNG['sys_notenough_money'],
                        $PLANET['name'],
                        $PLANET['id'],
                        $PLANET['galaxy'],
                        $PLANET['system'],
                        $PLANET['planet'],
                        $LNG['tech'][$Element],
                        pretty_number($PLANET['metal']),
                        $LNG['tech'][RESOURCE_METAL],
                        pretty_number($PLANET['crystal']),
                        $LNG['tech'][RESOURCE_CRYSTAL],
                        pretty_number($PLANET['deuterium']),
                        $LNG['tech'][RESOURCE_DEUT],
                        pretty_number($costResources[RESOURCE_METAL]),
                        $LNG['tech'][RESOURCE_METAL],
                        pretty_number($costResources[RESOURCE_CRYSTAL]),
                        $LNG['tech'][RESOURCE_CRYSTAL],
                        pretty_number($costResources[RESOURCE_DEUT]),
                        $LNG['tech'][RESOURCE_DEUT]
                    );
                    PlayerUtil::sendMessage(
                        $this->USER['id'],
                        0,
                        $LNG['sys_techlist'],
                        99,
                        $LNG['sys_buildlist_fail'],
                        $Message,
                        $this->TIME
                    );
                }

                array_shift($CurrentQueue);

                if (count($CurrentQueue) == 0) {
                    $this->USER['b_tech'] = 0;
                    $this->USER['b_tech_id'] = 0;
                    $this->USER['b_tech_planet'] = 0;
                    $this->USER['b_tech_queue'] = '';

                    $Loop = false;
                } else {
                    $BaseTime = $BuildEndTime - $BuildTime;
                    $NewQueue = [];
                    foreach ($CurrentQueue as $ListIDArray) {
                        $ListIDArray[2] = BuildFunctions::getBuildingTime($this->USER, $PLANET, $ListIDArray[0]);
                        $BaseTime += $ListIDArray[2];
                        $ListIDArray[3] = $BaseTime;
                        $NewQueue[] = $ListIDArray;
                    }
                    $CurrentQueue = $NewQueue;
                }
            }

            if ($isAnotherPlanet) {
                $RPLANET = null;
                unset($RPLANET);
            } else {
                $this->PLANET = $PLANET;
            }
        }

        if ($save) {
            $this->SavePlanetToDB($this->USER, $this->PLANET);
        }

        return true;
    }

    private function SavePlanetToDB($USER, $PLANET)
    {
        $resource =& Singleton()->resource;
        $reslist =& Singleton()->reslist;

        $buildQueries = [];

        $currentBuildings = 0;
        foreach ($reslist['build'] as $Element) {
            if (isset($PLANET[$resource[$Element]])) {
                $currentBuildings += $PLANET[$resource[$Element]];
            }
        }

        $params = [
            ':userId'               => $USER['id'],
            ':planetId'             => $PLANET['id'],
            ':ecoHash'              => $PLANET['eco_hash'],
            ':lastUpdateTime'       => $PLANET['last_update'],
            ':b_building'           => $PLANET['b_building'],
            ':b_building_id'        => $PLANET['b_building_id'],
            ':field_current'        => $currentBuildings > 0 ? $currentBuildings : $PLANET['field_current'],
            ':b_hangar_id'          => $PLANET['b_hangar_id'],
            ':metal_perhour'        => $PLANET['metal_perhour'],
            ':crystal_perhour'      => $PLANET['crystal_perhour'],
            ':deuterium_perhour'    => $PLANET['deuterium_perhour'],
            ':metal_max'            => $PLANET['metal_max'],
            ':crystal_max'          => $PLANET['crystal_max'],
            ':deuterium_max'        => $PLANET['deuterium_max'],
            ':energy_used'          => $PLANET['energy_used'],
            ':energy'               => $PLANET['energy'],
            ':b_hangar'             => $PLANET['b_hangar'],
            ':b_tech'               => $USER['b_tech'],
            ':b_tech_id'            => $USER['b_tech_id'],
            ':b_tech_planet'        => $USER['b_tech_planet'],
            ':b_tech_queue'         => $USER['b_tech_queue']
        ];

        if (!empty($this->Builded)) {
            foreach ($this->Builded as $Element => $Count) {
                $Element = (int) $Element;

                if (empty($resource[$Element])) {
                    continue;
                }

                if (in_array($Element, $reslist['one'])) {
                    $buildQueries[] = ', p.' . $resource[$Element] . ' = :' . $resource[$Element];
                    $params[':' . $resource[$Element]] = '1';
                } elseif (isset($PLANET[$resource[$Element]])) {
                    if ($Element < 100) { // Set building level directly
                        $buildQueries[] = ', p.' . $resource[$Element] . ' = :'
                            . $resource[$Element];
                        $params[':' . $resource[$Element]] = floatToString($Count);
                    } else {
                        $buildQueries[] = ', p.' . $resource[$Element] . ' = p.' . $resource[$Element] . ' + :'
                            . $resource[$Element];
                        $params[':' . $resource[$Element]] = floatToString($Count);
                    }
                    if ($Element >= 200 && $Element < 900) { // Update Builded stats for ships, def and rockets
                        $buildQueries[] = ', s.build_' . $Element . ' = s.build_' . $Element . ' + :'
                            . $resource[$Element];
                        $params[':' . $resource[$Element]] = floatToString($Count);
                    }
                } elseif (isset($USER[$resource[$Element]])) { // Set research level directly
                    $buildQueries[] = ', u.' . $resource[$Element] . ' = :'
                        . $resource[$Element];
                    $params[':' . $resource[$Element]] = floatToString($Count);
                }
            }
        }

        $sql = 'UPDATE %%PLANETS%% as p, %%USERS%% as u, %%ADVANCED_STATS%% as s SET
		p.eco_hash = :ecoHash,
		p.last_update = :lastUpdateTime,
		p.b_building = :b_building,
		p.b_building_id = :b_building_id,
		p.field_current = :field_current,
		p.b_hangar_id = :b_hangar_id,
		p.metal_perhour = :metal_perhour,
		p.crystal_perhour = :crystal_perhour,
		p.deuterium_perhour = :deuterium_perhour,
		p.metal_max = :metal_max,
		p.crystal_max = :crystal_max,
		p.deuterium_max = :deuterium_max,
		p.energy_used = :energy_used,
		p.energy = :energy,
		p.b_hangar = :b_hangar,
		u.b_tech = :b_tech,
		u.b_tech_id = :b_tech_id,
		u.b_tech_planet = :b_tech_planet,
		u.b_tech_queue = :b_tech_queue
		' . implode("\n", $buildQueries) . '
		WHERE p.id = :planetId AND u.id = :userId AND s.userId = :userId;';

        Database::get()->update($sql, $params);

        $this->Builded = [
            RESOURCE_METAL => 0,
            RESOURCE_CRYSTAL => 0,
            RESOURCE_DEUT => 0,
        ];

        return [$USER, $PLANET];
    }

    public function removeResources(int $planetId, array $resources, array &$planet = null)
    {
        global $resource;
        $params = [':planetId' => $planetId];
        $planetQuery = [];
        foreach ($resources as $resourceId => $amount) {
            if ($resourceId == RESOURCE_ENERGY) {
                continue;
            }
            $planetQuery[] = $resource[$resourceId] . " = " . $resource[$resourceId] . " - :" . $resource[$resourceId];
            $params[':' . $resource[$resourceId]] = $amount;

            if (isset($planet, $planet[$resource[$resourceId]])) {
                $planet[$resource[$resourceId]] -= $amount;
            }
        }

        if (!empty($planetQuery)) {
            $sql = 'UPDATE %%PLANETS%% SET ' .
            implode(', ', $planetQuery) .
            ' WHERE id = :planetId;';
            Database::get()->update($sql, $params);
        }
    }

    public function addResources(int $planetId, array $resources, array &$planet = null, float $factor = 1)
    {
        global $resource;
        $params = [':planetId' => $planetId];
        foreach ($resources as $resourceId => $amount) {
            if ($resourceId == RESOURCE_ENERGY) {
                continue;
            }
            $planetQuery[] = $resource[$resourceId] . " = " . $resource[$resourceId] . " + :" . $resource[$resourceId];
            $params[':' . $resource[$resourceId]] = $amount * $factor;

            if (isset($planet, $planet[$resource[$resourceId]])) {
                $planet[$resource[$resourceId]] += $amount * $factor;
            }
        }

        $sql = 'UPDATE %%PLANETS%% SET ' .
        implode(', ', $planetQuery) .
        ' WHERE id = :planetId;';
        Database::get()->update($sql, $params);
    }

    public function saveResources(array $PLANET)
    {
        $params = [
            ':planetId'             => $PLANET['id'],
            ':metal'                => $PLANET['metal'],
            ':crystal'              => $PLANET['crystal'],
            ':deuterium'            => $PLANET['deuterium'],
        ];
        $sql = 'UPDATE %%PLANETS%% SET
		metal = :metal,
		crystal = :crystal,
		deuterium = :deuterium
		WHERE id = :planetId;';

        Database::get()->update($sql, $params);
    }

    public function saveTechQueue(array $USER)
    {
        $params = [
            ':userId'        => $USER['id'],
            ':b_tech'        => $USER['b_tech'],
            ':b_tech_id'     => $USER['b_tech_id'],
            ':b_tech_planet' => $USER['b_tech_planet'],
            ':b_tech_queue'  => $USER['b_tech_queue']
        ];
        $sql = 'UPDATE %%USERS%% SET
		b_tech = :b_tech,
		b_tech_id = :b_tech_id,
		b_tech_planet = :b_tech_planet,
		b_tech_queue = :b_tech_queue
		WHERE id = :userId;';

        Database::get()->update($sql, $params);
    }

    public function saveBuilingQueue(array $PLANET)
    {
        $params = [
            ':planetId'             => $PLANET['id'],
            ':b_building'           => $PLANET['b_building'],
            ':b_building_id'        => $PLANET['b_building_id'],
        ];
        $sql = 'UPDATE %%PLANETS%% SET
        b_building = :b_building,
		b_building_id = :b_building_id
		WHERE id = :planetId;';

        Database::get()->update($sql, $params);
    }

    public function saveShipyardQueue(array $PLANET)
    {
        $params = [
            ':planetId'             => $PLANET['id'],
            ':b_hangar_id'          => $PLANET['b_hangar_id'],
            ':b_hangar'             => $PLANET['b_hangar'],
        ];
        $sql = 'UPDATE %%PLANETS%% SET
        b_hangar_id = :b_hangar_id,
		b_hangar = :b_hangar
		WHERE id = :planetId;';

        Database::get()->update($sql, $params);
    }
}
