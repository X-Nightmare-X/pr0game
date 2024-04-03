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

class ShowResourcesPage extends AbstractGamePage
{
    public static $requireModule = MODULE_RESSOURCE_LIST;

    public function __construct()
    {
        parent::__construct();
    }

    public function send()
    {
        $resource =& Singleton()->resource;
        $USER =& Singleton()->USER;
        $PLANET =& Singleton()->PLANET;
        if ($USER['urlaubs_modus'] == 0) {
            $updateSQL = [];
            if (!isset($_POST['prod'])) {
                $_POST['prod'] = [];
            }

            $db = Database::get();
            $db->startTransaction();

            $param = [':planetId' => $PLANET['id']];
            $sql = "SELECT * FROM %%PLANETS%% WHERE id = :planetId FOR UPDATE;";
            $PLANET = $db->selectSingle($sql, $param);

            foreach ($_POST['prod'] as $resourceId => $Value) {
                $FieldName = $resource[$resourceId] . '_porcent';
                if (!isset($PLANET[$FieldName]) || !in_array($Value, range(0, 10))) {
                    continue;
                }

                $updateSQL[] = $FieldName . " = :" . $FieldName;
                $param[':' . $FieldName] = (int) $Value;
                $PLANET[$FieldName] = (int) $Value;
            }

            if (!empty($updateSQL)) {
                $sql = "UPDATE %%PLANETS%% SET " . implode(', ', $updateSQL) . ", eco_hash = '' WHERE id = :planetId;";
                $db->update($sql, $param);
                $PLANET['eco_hash'] = '';

                list($USER, $PLANET) = $this->ecoObj->CalcResource($USER, $PLANET, true, TIMESTAMP + 1);
            }

            $db->commit();
        }

        $this->redirectTo('game.php?page=resources');
    }

    public function show()
    {
        $LNG =& Singleton()->LNG;
        $ProdGrid =& Singleton()->ProdGrid;
        $resource =& Singleton()->resource;
        $reslist =& Singleton()->reslist;
        $USER =& Singleton()->USER;
        $PLANET =& Singleton()->PLANET;
        $config = Config::get();
       
        if ($USER['urlaubs_modus'] == 1 || $PLANET['planet_type'] != 1) {
            $basicIncome[RESOURCE_METAL] = 0;
            $basicIncome[RESOURCE_CRYSTAL] = 0;
            $basicIncome[RESOURCE_DEUT] = 0;
            $basicIncome[RESOURCE_ENERGY] = 0;
        } else {
            $basicIncome[RESOURCE_METAL] = $config->{$resource[RESOURCE_METAL] . '_basic_income'};
            $basicIncome[RESOURCE_CRYSTAL] = $config->{$resource[RESOURCE_CRYSTAL] . '_basic_income'};
            $basicIncome[RESOURCE_DEUT] = $config->{$resource[RESOURCE_DEUT] . '_basic_income'};
            $basicIncome[RESOURCE_ENERGY] = $config->{$resource[RESOURCE_ENERGY] . '_basic_income'};
        }

        $temp = [
            RESOURCE_METAL => [
                'plus'  => 0,
                'minus' => 0,
            ],
            RESOURCE_CRYSTAL => [
                'plus'  => 0,
                'minus' => 0,
            ],
            RESOURCE_DEUT => [
                'plus'  => 0,
                'minus' => 0,
            ],
            RESOURCE_ENERGY => [
                'plus'  => 0,
                'minus' => 0,
                'bonus' => 0,
            ],
        ];
        $ressIDs = array_merge([], $reslist['resstype'][1], $reslist['resstype'][2]);

        $productionList = [];

        if ($PLANET['energy_used'] != 0) {
            $prodLevel = min(1, $PLANET['energy'] / abs($PLANET['energy_used']));
        } else {
            $prodLevel = 0;
        }

        /* Data for eval */
        $BuildEnergy = $USER[$resource[ENERGY_TECH]];
        $BuildTemp = $PLANET['temp_max'];

        foreach ($reslist['prod'] as $ProdID) {
            if (isset($PLANET[$resource[$ProdID]]) && $PLANET[$resource[$ProdID]] == 0) {
                continue;
            }

            if (isset($USER[$resource[$ProdID]]) && $USER[$resource[$ProdID]] == 0) {
                continue;
            }

            $productionList[$ProdID] = [
                'production'    => [
                    RESOURCE_METAL => 0,
                    RESOURCE_CRYSTAL => 0,
                    RESOURCE_DEUT => 0,
                    RESOURCE_ENERGY => 0,
                ],
                'elementLevel'  => $PLANET[$resource[$ProdID]],
                'prodLevel'     => $PLANET[$resource[$ProdID] . '_porcent'],
            ];

            /* Data for eval */
            $BuildLevel = $PLANET[$resource[$ProdID]];
            $BuildLevelFactor = $PLANET[$resource[$ProdID] . '_porcent'];

            foreach ($ressIDs as $ID) {
                if (!isset($ProdGrid[$ProdID]['production'][$ID])) {
                    continue;
                }

                $Production = eval(ResourceUpdate::getProd($ProdGrid[$ProdID]['production'][$ID], $ProdID));

                if (in_array($ID, $reslist['resstype'][2])) {
                    $Production *= $config->energy_multiplier;
                } else {
                    $Production *= $prodLevel * $config->resource_multiplier;
                }

                if ($ProdID == fusion_plant && $ID == RESOURCE_ENERGY) {
                    $BuildEnergy = 0;
                    $bonus = eval(ResourceUpdate::getProd($ProdGrid[$ProdID]['production'][$ID], $ProdID));
                    $temp[RESOURCE_ENERGY]['bonus'] = $Production - $bonus;
                    $$BuildEnergy = $USER[$resource[ENERGY_TECH]];
                }

                $productionList[$ProdID]['production'][$ID] = $Production;

                if ($Production > 0) {
                    if ($PLANET[$resource[$ID]] == 0) {
                        continue;
                    }

                    $temp[$ID]['plus'] += $Production;
                } else {
                    $temp[$ID]['minus'] += $Production;
                }
            }            
        }

        $storage = [
            RESOURCE_METAL => shortly_number($PLANET[$resource[RESOURCE_METAL] . '_max']),
            RESOURCE_CRYSTAL => shortly_number($PLANET[$resource[RESOURCE_CRYSTAL] . '_max']),
            RESOURCE_DEUT => shortly_number($PLANET[$resource[RESOURCE_DEUT] . '_max']),
        ];

        $basicProduction = [
            RESOURCE_METAL => $basicIncome[RESOURCE_METAL] * $config->resource_multiplier,
            RESOURCE_CRYSTAL => $basicIncome[RESOURCE_CRYSTAL] * $config->resource_multiplier,
            RESOURCE_DEUT => $basicIncome[RESOURCE_DEUT] * $config->resource_multiplier,
            RESOURCE_ENERGY => $basicIncome[RESOURCE_ENERGY] * $config->energy_multiplier,
        ];

        $universe_config = Config::get(Universe::current());
        $positionBasedRessourceBonus = $universe_config->planet_ressource_bonus;

        $bonusProductionPositionBased = [];

        if ($positionBasedRessourceBonus) {
            $metal_bonus_percent     = $PLANET['metal_bonus_percent'] / 100;
            $crystal_bonus_percent   = $PLANET['crystal_bonus_percent'] / 100;
            $deuterium_bonus_percent = $PLANET['deuterium_bonus_percent'] / 100;

            $bonusProductionPositionBased = [
                RESOURCE_METAL => $temp[RESOURCE_METAL]['plus'] * $metal_bonus_percent,
                RESOURCE_CRYSTAL => $temp[RESOURCE_CRYSTAL]['plus'] * $crystal_bonus_percent,
                RESOURCE_DEUT => $temp[RESOURCE_DEUT]['plus'] * $deuterium_bonus_percent,
                RESOURCE_ENERGY => $basicIncome[RESOURCE_ENERGY] * 0,
            ];
        } 
        if ($positionBasedRessourceBonus) {
            $bonusProduction = [
                RESOURCE_METAL => ($temp[RESOURCE_METAL]['plus'] + $bonusProductionPositionBased[RESOURCE_METAL]) * (0.02 * $USER[$resource[METAL_PROC_TECH]]),
                RESOURCE_CRYSTAL => ($temp[RESOURCE_CRYSTAL]['plus'] + $bonusProductionPositionBased[RESOURCE_CRYSTAL]) * (0.02 * $USER[$resource[CRYSTAL_PROC_TECH]]),
                RESOURCE_DEUT => ($temp[RESOURCE_DEUT]['plus'] + $bonusProductionPositionBased[RESOURCE_DEUT]) * (0.02 * $USER[$resource[DEUTERIUM_PROC_TECH]]),
                RESOURCE_ENERGY => $temp[RESOURCE_ENERGY]['bonus'],
            ];

            $totalProduction = [
                RESOURCE_METAL => $temp[RESOURCE_METAL]['plus'] + $bonusProduction[RESOURCE_METAL] + $bonusProductionPositionBased[RESOURCE_METAL] + $basicProduction[RESOURCE_METAL],
                RESOURCE_CRYSTAL => $temp[RESOURCE_CRYSTAL]['plus'] + $bonusProduction[RESOURCE_CRYSTAL] + $bonusProductionPositionBased[RESOURCE_CRYSTAL] + $basicProduction[RESOURCE_CRYSTAL],
                RESOURCE_DEUT => $temp[RESOURCE_DEUT]['plus'] + $bonusProduction[RESOURCE_DEUT] + $bonusProductionPositionBased[RESOURCE_DEUT] + $basicProduction[RESOURCE_DEUT],
                RESOURCE_ENERGY => $PLANET[$resource[RESOURCE_ENERGY]] + $basicProduction[RESOURCE_ENERGY] + $PLANET[$resource[RESOURCE_ENERGY] . '_used'],
            ];
        } else {
            $bonusProduction = [
                RESOURCE_METAL => $temp[RESOURCE_METAL]['plus'] * (0.02 * $USER[$resource[METAL_PROC_TECH]]),
                RESOURCE_CRYSTAL => $temp[RESOURCE_CRYSTAL]['plus'] * (0.02 * $USER[$resource[CRYSTAL_PROC_TECH]]),
                RESOURCE_DEUT => $temp[RESOURCE_DEUT]['plus'] * (0.02 * $USER[$resource[DEUTERIUM_PROC_TECH]]),
                RESOURCE_ENERGY => $temp[RESOURCE_ENERGY]['bonus'],
            ];

            $totalProduction = [
                RESOURCE_METAL => $temp[RESOURCE_METAL]['plus'] + $bonusProduction['RESOURCE_METAL'] + $basicProduction[RESOURCE_METAL],
                RESOURCE_CRYSTAL => $temp[RESOURCE_CRYSTAL]['plus'] + $bonusProduction['RESOURCE_CRYSTAL'] + $basicProduction[RESOURCE_CRYSTAL],
                RESOURCE_DEUT => $temp[RESOURCE_DEUT]['plus'] + $bonusProduction[RESOURCE_DEUT] + $basicProduction[RESOURCE_DEUT],
                RESOURCE_ENERGY => $PLANET[$resource[RESOURCE_ENERGY]] + $basicProduction[RESOURCE_ENERGY] + $PLANET[$resource[RESOURCE_ENERGY] . '_used'],
            ];
        }
        
        $dailyProduction = [
            RESOURCE_METAL => $totalProduction[RESOURCE_METAL] * 24,
            RESOURCE_CRYSTAL => $totalProduction[RESOURCE_CRYSTAL] * 24,
            RESOURCE_DEUT => $totalProduction[RESOURCE_DEUT] * 24,
            RESOURCE_ENERGY => $totalProduction[RESOURCE_ENERGY],
        ];

        $weeklyProduction = [
            RESOURCE_METAL => $totalProduction[RESOURCE_METAL] * 168,
            RESOURCE_CRYSTAL => $totalProduction[RESOURCE_CRYSTAL] * 168,
            RESOURCE_DEUT => $totalProduction[RESOURCE_DEUT] * 168,
            RESOURCE_ENERGY => $totalProduction[RESOURCE_ENERGY],
        ];

        $prodSelector = [];

        foreach (range(10, 0) as $percent) {
            $prodSelector[$percent] = ($percent * 10) . '%';
        }

        if ( isset($productionList[fusion_plant]['production'][RESOURCE_ENERGY])){
            $productionList[fusion_plant]['production'][RESOURCE_ENERGY] = $productionList[fusion_plant]['production'][RESOURCE_ENERGY] - $bonusProduction[RESOURCE_ENERGY];
        }
        
        $this->assign([
            'header'                            => sprintf($LNG['rs_production_on_planet'], $PLANET['name']),
            'prodSelector'                      => $prodSelector,
            'productionList'                    => $productionList,
            'basicProduction'                   => $basicProduction,
            'totalProduction'                   => $totalProduction,
            'bonusProduction'                   => $bonusProduction,
            'positionBasedRessourceBonus'       => $positionBasedRessourceBonus,
            'bonusProductionPositionBased'      => $bonusProductionPositionBased,
            'dailyProduction'                   => $dailyProduction,
            'weeklyProduction'                  => $weeklyProduction,
            'storage'                           => $storage,
            'metalProcTech'                     => $USER[$resource[METAL_PROC_TECH]],
            'crystalProcTech'                   => $USER[$resource[CRYSTAL_PROC_TECH]],
            'deuteriumProcTech'                 => $USER[$resource[DEUTERIUM_PROC_TECH]],
            'metalBonusPercent'                 => $metal_bonus_percent * 100,     
            'crystalBonusPercent'               => $crystal_bonus_percent * 100,   
            'deuteriumBonusPercent'             => $deuterium_bonus_percent * 100, 
        ]);

        $this->display('page.resources.default.tpl');
    }
}
