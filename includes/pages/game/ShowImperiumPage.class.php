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


class ShowImperiumPage extends AbstractGamePage
{
    public static $requireModule = MODULE_IMPERIUM;

    public function __construct()
    {
        parent::__construct();
    }

    public function show()
    {
        $USER =& Singleton()->USER;
        $resource =& Singleton()->resource;
        $reslist =& Singleton()->reslist;
        $db = Database::get();
        $db->startTransaction();
        $db->selectSingle("SELECT * FROM %%USERS%% WHERE id = :userID FOR UPDATE;", [':userID' => $USER['id']]);
        $order = $USER['planet_sort_order'] == 1 ? 'DESC' : 'ASC';

        $sql = "SELECT * FROM %%PLANETS%% WHERE id_owner = :userID AND destruyed = '0' ORDER BY ";

        switch ($USER['planet_sort']) {
            case 2:
                $sql .= 'name ' . $order;
                break;
            case 1:
                $sql .= 'galaxy ' . $order . ', system ' . $order . ', planet ' . $order . ', planet_type ' . $order;
                break;
            default:
                $sql .= 'id ' . $order;
                break;
        }

        $sql .= " FOR UPDATE";
        $PlanetsRAW = $db->select($sql, [
            ':userID' => $USER['id']
        ]);

        $PLANETS = [];

        $PlanetRess = new ResourceUpdate();

        foreach ($PlanetsRAW as $CPLANET) {
            list($USER, $CPLANET) = $PlanetRess->CalcResource($USER, $CPLANET, true);

            $PLANETS[] = $CPLANET;
            unset($CPLANET);
        }

        $db->commit();

        $planetList = [];
        $config = Config::get($USER['universe']);

        if (!empty($USER['b_tech_queue'])) {
            $researchque=unserialize($USER['b_tech_queue']);

            $researchid= $researchque[0][0];
            $researchlvl= $researchque[0][1];
            $planetid=$researchque[0][4];
        } else {
            $researchid= -1;
            $researchlvl= -1;
            $planetid= -1;
        }
        $this->assign([
            'researchq_id'      => $researchid,
            'researchq_lvl'     => $researchlvl,
            'researchq_planet'  => $planetid,
        ]);
        foreach ($PLANETS as $Planet) {
            $planetList['name'][$Planet['id']] = $Planet['name'];
            $planetList['image'][$Planet['id']] = $Planet['image'];

            $planetList['coords'][$Planet['id']]['galaxy'] = $Planet['galaxy'];
            $planetList['coords'][$Planet['id']]['system'] = $Planet['system'];
            $planetList['coords'][$Planet['id']]['planet'] = $Planet['planet'];

            $planetList['field'][$Planet['id']]['current'] = $Planet['field_current'];
            $planetList['field'][$Planet['id']]['max'] = CalculateMaxPlanetFields($Planet);

            $planetList['energy_used'][$Planet['id']] = $Planet['energy'] + $Planet['energy_used'];


            $planetList['resource'][901][$Planet['id']] = $Planet['metal'];
            $planetList['resource'][902][$Planet['id']] = $Planet['crystal'];
            $planetList['resource'][903][$Planet['id']] = $Planet['deuterium'];
            $planetList['resource'][911][$Planet['id']] = $Planet['energy'] + $Planet['energy_used'];
            ;

            if ($Planet['planet_type'] == 1) {
                $basic[901][$Planet['id']] = $config->metal_basic_income * $config->resource_multiplier;
                $basic[902][$Planet['id']] = $config->crystal_basic_income * $config->resource_multiplier;
                $basic[903][$Planet['id']] = $config->deuterium_basic_income * $config->resource_multiplier;
                $planetList['resourceFull'][901][$Planet['id']] = floor(max($Planet['metal_max'] - $Planet['metal'], 0) / max(1, $Planet['metal_perhour'] + $basic[901][$Planet['id']]));
                $planetList['resourceFull'][902][$Planet['id']] = floor(max($Planet['crystal_max'] - $Planet['crystal'], 0) / max(1, $Planet['crystal_perhour'] + $basic[902][$Planet['id']]));
                $planetList['resourceFull'][903][$Planet['id']] = floor(max($Planet['deuterium_max'] - $Planet['deuterium'], 0) / max($basic[903][$Planet['id']] + $Planet['deuterium_perhour'], 1));
            } else {
                $basic[901][$Planet['id']] = 0;
                $basic[902][$Planet['id']] = 0;
                $basic[903][$Planet['id']] = 0;
            }
            if (!empty($Planet['b_building_id'])) {
                $Queue = unserialize($Planet['b_building_id']);
                $planetList['currentBuilding'][$Planet['id']] = $Queue[0][0];
                $planetList['currentBuildinglvl'][$Planet['id']] = $Queue[0][1];
            } else {
                $planetList['currentBuilding'][$Planet['id']] = -1;
                $planetList['currentBuildinglvl'][$Planet['id']] = -1;
            }


            $planetList['resourcePerHour'][901][$Planet['id']] = $basic[901][$Planet['id']] + $Planet['metal_perhour'];
            $planetList['resourcePerHour'][902][$Planet['id']] = $basic[902][$Planet['id']] + $Planet['crystal_perhour'];
            $planetList['resourcePerHour'][903][$Planet['id']] = $basic[903][$Planet['id']] + $Planet['deuterium_perhour'];

            $planetList['planet_type'][$Planet['id']] = $Planet['planet_type'];


            foreach ($reslist['build'] as $elementID) {
                if (!BuildFunctions::isEnabled($elementID)) {
                    continue;
                }
                $planetList['build'][$elementID][$Planet['id']] = $Planet[$resource[$elementID]];
            }
            $planetList['currentShipyard'][$Planet['id']] = [];
            foreach ($reslist['fleet'] as $elementID) {
                if (!BuildFunctions::isEnabled($elementID)) {
                    continue;
                }
                $planetList['fleet'][$elementID][$Planet['id']] = $Planet[$resource[$elementID]];
                $planetList['currentShipyard'][$elementID][$Planet['id']] = 0;
            }


            foreach ($reslist['defense'] as $elementID) {
                if (!BuildFunctions::isEnabled($elementID)) {
                    continue;
                }
                $planetList['defense'][$elementID][$Planet['id']] = $Planet[$resource[$elementID]];
                $planetList['currentShipyard'][$elementID][$Planet['id']] = 0;
            }

            $planetList['currentShipyard'][502][$Planet['id']] = 0;
            $planetList['currentShipyard'][503][$Planet['id']] = 0;
            if (!empty($Planet['b_hangar_id'])) {
                foreach (unserialize($Planet['b_hangar_id']) as $que) {
                    $planetList['currentShipyard'][$que[0]][$Planet['id']] += $que[1];
                }
            }

            $planetList['missiles'][502][$Planet['id']] = $Planet[$resource[502]];
            $planetList['missiles'][503][$Planet['id']] = $Planet[$resource[503]];
        }

        foreach ($reslist['tech'] as $elementID) {
            if (!BuildFunctions::isEnabled($elementID)) {
                continue;
            }
            $planetList['tech'][$elementID] = $USER[$resource[$elementID]];
        }

        $this->assign([
            'colspan'       => count($PLANETS) + 2,
            'planetList'    => $planetList,
        ]);

        $this->display('page.empire.default.tpl');
    }
}
