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
            $basicIncome[901] = 0;
            $basicIncome[902] = 0;
            $basicIncome[903] = 0;
            $basicIncome[911] = 0;
        } else {
            $basicIncome[901] = $config->{$resource[901] . '_basic_income'};
            $basicIncome[902] = $config->{$resource[902] . '_basic_income'};
            $basicIncome[903] = $config->{$resource[903] . '_basic_income'};
            $basicIncome[911] = $config->{$resource[911] . '_basic_income'};
        }

        $temp = [
            901 => [
                'plus'  => 0,
                'minus' => 0,
            ],
            902 => [
                'plus'  => 0,
                'minus' => 0,
            ],
            903 => [
                'plus'  => 0,
                'minus' => 0,
            ],
            911 => [
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
        $BuildEnergy = $USER[$resource[113]];
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
                    901 => 0,
                    902 => 0,
                    903 => 0,
                    911 => 0,
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

                if ($ProdID == 12 && $ID == RESOURCE_ENERGY) {
                    $BuildEnergy = 0;
                    $bonus = eval(ResourceUpdate::getProd($ProdGrid[$ProdID]['production'][$ID], $ProdID));
                    $temp[911]['bonus'] = $Production - $bonus;
                    $$BuildEnergy = $USER[$resource[113]];
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
            901 => shortly_number($PLANET[$resource[901] . '_max']),
            902 => shortly_number($PLANET[$resource[902] . '_max']),
            903 => shortly_number($PLANET[$resource[903] . '_max']),
        ];

        $basicProduction = [
            901 => $basicIncome[901] * $config->resource_multiplier,
            902 => $basicIncome[902] * $config->resource_multiplier,
            903 => $basicIncome[903] * $config->resource_multiplier,
            911 => $basicIncome[911] * $config->energy_multiplier,
        ];

        $universe_config = Config::get(Universe::current());
        $positionBasedRessourceBonus = $universe_config->planet_ressource_bonus;

        $bonusProductionPositionBased = [];

        if ($positionBasedRessourceBonus) {
            $metal_bonus_percent     = $PLANET['metal_bonus_percent'] / 100;
            $crystal_bonus_percent   = $PLANET['crystal_bonus_percent'] / 100;
            $deuterium_bonus_percent = $PLANET['deuterium_bonus_percent'] / 100;

            $bonusProductionPositionBased = [
                901 => $temp[901]['plus'] * $metal_bonus_percent,
                902 => $temp[902]['plus'] * $crystal_bonus_percent,
                903 => $temp[903]['plus'] * $deuterium_bonus_percent,
                911 => $basicIncome[911] * 0,
            ];
        } 
        if ($positionBasedRessourceBonus) {
            $bonusProduction = [
                901 => ($temp[901]['plus'] + $bonusProductionPositionBased[901]) * (0.02 * $USER[$resource[131]]),
                902 => ($temp[902]['plus'] + $bonusProductionPositionBased[902]) * (0.02 * $USER[$resource[132]]),
                903 => ($temp[903]['plus'] + $bonusProductionPositionBased[903]) * (0.02 * $USER[$resource[133]]),
                911 => $temp[911]['bonus'],
            ];
        } else {
            $bonusProduction = [
                901 => $temp[901]['plus'] * (0.02 * $USER[$resource[131]]),
                902 => $temp[902]['plus'] * (0.02 * $USER[$resource[132]]),
                903 => $temp[903]['plus'] * (0.02 * $USER[$resource[133]]),
                911 => $temp[911]['bonus'],
            ];
        }
        
        $totalProduction = [
            901 => $temp[901]['plus'] + $bonusProduction['901'] + $bonusProductionPositionBased['901'] + $basicProduction[901],
            902 => $temp[902]['plus'] + $bonusProduction['902'] + $bonusProductionPositionBased['902'] + $basicProduction[902],
            903 => $temp[903]['plus'] + $bonusProduction['903'] + $bonusProductionPositionBased['903'] + $basicProduction[903],
            911 => $PLANET[$resource[911]] + $basicProduction[911] + $PLANET[$resource[911] . '_used'],
        ];


        $dailyProduction = [
            901 => $totalProduction[901] * 24,
            902 => $totalProduction[902] * 24,
            903 => $totalProduction[903] * 24,
            911 => $totalProduction[911],
        ];

        $weeklyProduction = [
            901 => $totalProduction[901] * 168,
            902 => $totalProduction[902] * 168,
            903 => $totalProduction[903] * 168,
            911 => $totalProduction[911],
        ];

        $prodSelector = [];

        foreach (range(10, 0) as $percent) {
            $prodSelector[$percent] = ($percent * 10) . '%';
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
        ]);

        $this->display('page.resources.default.tpl');
    }
}
