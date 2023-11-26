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

class ShowBattleSimulatorPage extends AbstractGamePage
{
    public static $requireModule = MODULE_SIMULATOR;

    public function __construct()
    {
        parent::__construct();
    }

    public function send()
    {
        $reslist =& Singleton()->reslist;
        $pricelist =& Singleton()->pricelist;
        $LNG =& Singleton()->LNG;
        if (!isset($_REQUEST['battleinput'])) {
            $this->sendJSON(0);
        }

        $BattleArray = $_REQUEST['battleinput'];
        $elements = [0, 0];
        foreach ($BattleArray as $BattleSlotID => $BattleSlot) {
            if (isset($BattleSlot[0]) && (array_sum($BattleSlot[0]) > 0 || $BattleSlotID == 0)) {
                $attacker = [];
                $attacker['fleetDetail'] = [
                    'fleet_start_galaxy' => 1,
                    'fleet_start_system' => 33,
                    'fleet_start_planet' => 7,
                    'fleet_start_type' => 1,
                    'fleet_end_galaxy' => 1,
                    'fleet_end_system' => 33,
                    'fleet_end_planet' => 7,
                    'fleet_end_type' => 1,
                    'fleet_resource_metal' => 0,
                    'fleet_resource_crystal' => 0,
                    'fleet_resource_deuterium' => 0,
                ];

                $attacker['player'] = [
                    'id' => (1000 + $BattleSlotID + 1),
                    'username'  => $LNG['bs_atter'] . ' Nr.' . ($BattleSlotID + 1),
                    'military_tech' => getNumber($BattleSlot[0][109]),
                    'shield_tech' => getNumber($BattleSlot[0][110]),
                    'defence_tech' => getNumber($BattleSlot[0][111]),
                ];

                foreach ($BattleSlot[0] as $ID => $Count) {
                    if (!in_array($ID, $reslist['fleet']) || getNumber($BattleSlot[0][$ID]) <= 0) {
                        unset($BattleSlot[0][$ID]);
                    }
                }

                $attacker['unit'] = $BattleSlot[0];

                $fleetAttack[] = $attacker;
            }

            if (isset($BattleSlot[1]) && (array_sum($BattleSlot[1]) > 0 || $BattleSlotID == 0)) {
                $defender = [];
                $defender['fleetDetail'] = [
                    'fleet_start_galaxy' => 1,
                    'fleet_start_system' => 33,
                    'fleet_start_planet' => 7,
                    'fleet_start_type' => 1,
                    'fleet_end_galaxy' => 1,
                    'fleet_end_system' => 33,
                    'fleet_end_planet' => 7,
                    'fleet_end_type' => 1,
                    'fleet_resource_metal' => 0,
                    'fleet_resource_crystal' => 0,
                    'fleet_resource_deuterium' => 0,
                ];

                $defender['player'] = [
                    'id' => (2000 + $BattleSlotID + 1),
                    'username'  => $LNG['bs_deffer'] . ' Nr.' . ($BattleSlotID + 1),
                    'military_tech' => getNumber($BattleSlot[1][109]),
                    'shield_tech' => getNumber($BattleSlot[1][110]),
                    'defence_tech' => getNumber($BattleSlot[1][111]),
                ];

                foreach ($BattleSlot[1] as $ID => $Count) {
                    if (
                        (!in_array($ID, $reslist['fleet'])
                            && !in_array($ID, $reslist['defense'])) || getNumber($BattleSlot[1][$ID]) <= 0
                    ) {
                        unset($BattleSlot[1][$ID]);
                    }
                }

                $defender['unit'] = $BattleSlot[1];
                $fleetDefend[] = $defender;
            }
        }

        $LNG->includeData(['FLEET']);

        require_once 'includes/classes/missions/functions/calculateAttack.php';
        require_once 'includes/classes/missions/functions/calculateSteal.php';
        require_once 'includes/classes/missions/functions/GenerateReport.php';

        $combatResult = calculateAttack($fleetAttack, $fleetDefend, Config::get()->Fleet_Cdr, Config::get()->Defs_Cdr, true);

        if ($combatResult['won'] == "a") {
            $stealResource = calculateSteal($fleetAttack, [
            'metal' => getNumber($BattleArray[0][1][901]),
            'crystal' => getNumber($BattleArray[0][1][902]),
            'deuterium' => getNumber($BattleArray[0][1][903]),
            ], true);
        } else {
            $stealResource = [
                901 => 0,
                902 => 0,
                903 => 0,
            ];
        }

        $debris = [];

        foreach ([901, 902] as $elementID) {
            $debris[$elementID] = $combatResult['debris']['attacker'][$elementID]
                + $combatResult['debris']['defender'][$elementID];
        }

        $debrisTotal = array_sum($debris);

        $moonFactor = Config::get()->moon_factor;
        $maxMoonChance = Config::get()->moon_chance;

        $chanceCreateMoon = floor($debrisTotal / 100000 * $moonFactor);
        $chanceCreateMoon = min($chanceCreateMoon, $maxMoonChance);

        $sumSteal = array_sum($stealResource);

        $stealResourceInformation = sprintf(
            $LNG['bs_derbis_raport'],
            pretty_number(ceil($debrisTotal / $pricelist[209]['capacity'])),
            $LNG['tech'][209]
        );

        $stealResourceInformation .= '<br>';

        $stealResourceInformation .= sprintf(
            $LNG['bs_steal_raport'],
            pretty_number(ceil($sumSteal / $pricelist[202]['capacity'])),
            $LNG['tech'][202],
            pretty_number(ceil($sumSteal / $pricelist[203]['capacity'])),
            $LNG['tech'][203]
        );

        $reportInfo = [
            'thisFleet'             => [
                'fleet_start_galaxy'    => 1,
                'fleet_start_system'    => 33,
                'fleet_start_planet'    => 7,
                'fleet_start_type'      => 1,
                'fleet_end_galaxy'      => 1,
                'fleet_end_system'      => 33,
                'fleet_end_planet'      => 7,
                'fleet_end_type'        => 1,
                'fleet_start_time'      => TIMESTAMP,
            ],
            'debris'                => $debris,
            'stealResource'         => $stealResource,
            'moonChance'            => $chanceCreateMoon,
            'additionalChance'      => 0,
            'moonDestroy'           => false,
            'moonName'              => null,
            'moonDestroyChance'     => null,
            'moonDestroySuccess'    => null,
            'fleetDestroyChance'    => null,
            'fleetDestroySuccess'   => null,
            'additionalInfo'        => $stealResourceInformation,
        ];

        $reportData = GenerateReport($combatResult, $reportInfo, SIM_FIGHT);
        unset($reportData['repaired']);
        $reportID = md5(uniqid('', true) . TIMESTAMP);

        $db = Database::get();

        $sql = "INSERT INTO %%RW%% SET rid = :reportID, raport = :reportData, time = :time;";
        $db->insert($sql, [
            ':reportID'     => $reportID,
            ':reportData'   => serialize($reportData),
            ':time'         => TIMESTAMP
        ]);

        $this->sendJSON($reportID);
    }

    public function show()
    {
        $USER =& Singleton()->USER;
        $PLANET =& Singleton()->PLANET;
        $reslist =& Singleton()->reslist;
        $resource =& Singleton()->resource;
        $Slots = HTTP::_GP('slots', 1);


        $BattleArray[0][0][109] = $USER[$resource[109]];
        $BattleArray[0][0][110] = $USER[$resource[110]];
        $BattleArray[0][0][111] = $USER[$resource[111]];

        if (empty($_REQUEST['battleinput'])) {
            foreach ($reslist['fleet'] as $ID) {
                if (FleetFunctions::getFleetMaxSpeed($ID, $USER) > 0) {
                    // Add just flyable elements
                    $BattleArray[0][0][$ID] = $PLANET[$resource[$ID]];
                }
            }
        } else {
            $BattleArray = HTTP::_GP('battleinput', []);
        }

        if (isset($_REQUEST['im'])) {
            foreach ($_REQUEST['im'] as $ID => $Count) {
                $BattleArray[0][1][$ID] = floatToString($Count);
            }
        }

        $this->tplObj->loadscript('battlesim.js');

        $this->assign([
            'Slots'         => $Slots,
            'battleinput'   => $BattleArray,
            'fleetList'     => $reslist['fleet'],
            'defensiveList' => $reslist['defense'],
        ]);

        $this->display('page.battleSimulator.default.tpl');
    }
}
