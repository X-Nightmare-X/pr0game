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

class MissionCaseSpy extends MissionFunctions implements Mission
{
    public function __construct($Fleet)
    {
        $this->_fleet   = $Fleet;
    }

    /**
     *
     * @param array $targetPlanet
     * @return array[]
     *
     * call AFTER stayfleets calculation, or calculate those yourself
     *
     */
    private function getPlanetFleet($targetPlanet)
    {
        $reslist =& Singleton()->reslist;
        $resource =& Singleton()->resource;
        $planetfleet = [];
        $resourceIDs = $reslist['fleet'];

        foreach ($resourceIDs as $shiptype) {
            $planetfleet[$shiptype] = $targetPlanet[$resource[$shiptype]];
        }

        return $planetfleet;
    }

    private function getPlanetDefense($targetPlanet)
    {
        $reslist =& Singleton()->reslist;
        $resource =& Singleton()->resource;
        $planetdef = [];
        $resourceIDs = $reslist['defense'];

        foreach ($resourceIDs as $deftype) {
            $planetdef[$deftype] = $targetPlanet[$resource[$deftype]];
        }

        return $planetdef;
    }

    public function TargetEvent()
    {
        $pricelist =& Singleton()->pricelist;
        $reslist =& Singleton()->reslist;
        $resource =& Singleton()->resource;
        $db = Database::get();

        $sql = 'SELECT * FROM %%PLANETS%% WHERE id = :planetId FOR UPDATE;';
        $targetPlanet = $db->selectSingle($sql, [
            ':planetId' => $this->_fleet['fleet_end_id'],
        ]);

        // return fleet if target planet deleted
        if ($targetPlanet == false) {
            $this->setState(FLEET_RETURN);
            $this->SaveFleet();
            return;
        }

        $sql = 'SELECT * FROM %%USERS%% WHERE id = :userId;';
        $senderUser = $db->selectSingle($sql, [
            ':userId'   => $this->_fleet['fleet_owner'],
        ]);

        $targetUser = $db->selectSingle($sql, [
            ':userId'   => $this->_fleet['fleet_target_owner']
        ]);

        $sql = 'SELECT `name`, `planet_type` FROM %%PLANETS%% WHERE id = :planetId;';
        $senderPlanet = $db->selectSingle($sql, [
            ':planetId' => $this->_fleet['fleet_start_id']
        ]);

        $LNG = $this->getLanguage($senderUser['lang']);

        $planetUpdater = new ResourceUpdate();
        list($targetUser, $targetPlanet) = $planetUpdater->CalcResource(
            $targetUser,
            $targetPlanet,
            true,
            $this->_fleet['fleet_start_time']
        );

        $sql = 'SELECT * FROM %%FLEETS%%
		WHERE fleet_end_id 		= :planetId
		AND fleet_mission 		= :mission
		AND fleet_start_time    <= :time
		AND fleet_end_stay      >= :time FOR UPDATE;';

        $targetStayFleets = $db->select($sql, [
            ':planetId' => $this->_fleet['fleet_end_id'],
            ':time'     => TIMESTAMP,
            ':mission'  => MISSION_HOLD,
        ]);

        foreach ($targetStayFleets as $fleetRow) {
            $fleetData = FleetFunctions::unserialize($fleetRow['fleet_array']);
            foreach ($fleetData as $shipId => $shipAmount) {
                $targetPlanet[$resource[$shipId]] += $shipAmount;
            }
        }

        $fleetAmount = $this->_fleet['fleet_amount'];

        $senderSpyTech = max($senderUser['spy_tech'], 1);
        $targetSpyTech = max($targetUser['spy_tech'], 1);

        $techDifference = abs($senderSpyTech - $targetSpyTech);
        $MinAmount = ($senderSpyTech > $targetSpyTech ? -1 : 1) * pow($techDifference * SPY_DIFFENCE_FACTOR, 2);
        $SpyFleet = $fleetAmount >= $MinAmount;
        $SpyDef = $fleetAmount >= $MinAmount + 1 * SPY_VIEW_FACTOR;
        $SpyBuild = $fleetAmount >= $MinAmount + 3 * SPY_VIEW_FACTOR;
        $SpyTechno = $fleetAmount >= $MinAmount + 5 * SPY_VIEW_FACTOR;

        $classIDs[900] = array_merge($reslist['resstype'][1], $reslist['resstype'][2]);


        if ($SpyFleet) {
            $classIDs[200] = $reslist['fleet'];
        }

        if ($SpyDef) {
            $classIDs[400] = array_merge($reslist['defense'], $reslist['missile']);
        }

        if ($SpyBuild) {
            $classIDs[0] = $reslist['build'];
        }

        if ($SpyTechno) {
            $classIDs[100] = $reslist['tech'];
        }

        // what is seen by the 'attacker' is already calculated above.
        // if target planet does not have fleet or def, it is safe to set $targetChance to -1
        // so probes are no longer destroyed by thin air or something

        $totalShipDefCount = 0;
        foreach (array_values($this->getPlanetFleet($targetPlanet)) as $ships) {
            $totalShipDefCount += $ships;
        }

        // $targetChance = mt_rand(0, min(($fleetAmount / 4) * ($targetSpyTech / $senderSpyTech), 100));

        // New calculation: 1% Counterspy-Chance for 250 Ships * target spy bonus, if > sender spy tec
        // $targetChance = ($totalShipDefCount/250)*MAX($senderSpyTech-$targetSpyTech, 1) + (($fleetAmount / 4) * ($targetSpyTech / $senderSpyTech));

        // Calculation from https://ogame.fandom.com/wiki/Counterespionage
        $targetChance = min(round(pow(2, $targetSpyTech - $senderSpyTech) * $fleetAmount * $totalShipDefCount * 0.0025), 100);

        $spyChance = mt_rand(0, 100);
        $spyData = [];

        foreach ($classIDs as $classID => $elementIDs) {
            foreach ($elementIDs as $elementID) {
                if (isset($targetUser[$resource[$elementID]])) {
                    $spyData[$classID][$elementID] = $targetUser[$resource[$elementID]];
                } else {
                    $spyData[$classID][$elementID] = $targetPlanet[$resource[$elementID]];
                }
            }

            if ($senderUser['spyMessagesMode'] == 1) {
                $spyData[$classID] = array_filter($spyData[$classID]);
            }
        }

        require_once 'includes/classes/missions/functions/calculateSteal.php';
        $sumPlanetRessources = 2 * ($targetPlanet[$resource[RESOURCE_METAL]] + $targetPlanet[$resource[RESOURCE_CRYSTAL]] + $targetPlanet[$resource[RESOURCE_DEUT]]);
        $simulated = [];
        $simulated['fleetDetail']['fleet_resource_metal'] = 0;
        $simulated['fleetDetail']['fleet_resource_crystal'] = 0;
        $simulated['fleetDetail']['fleet_resource_deuterium'] = 0;
        $simulated['unit'] = [
            '203' => ceil($sumPlanetRessources / $pricelist[SHIP_LARGE_CARGO]['capacity']),
        ];
        $fleetSimulate[] = $simulated;

        $stealResource = calculateSteal($fleetSimulate, [
            'metal' => getNumber($targetPlanet[$resource[RESOURCE_METAL]]),
            'crystal' => getNumber($targetPlanet[$resource[RESOURCE_CRYSTAL]]),
            'deuterium' => getNumber($targetPlanet[$resource[RESOURCE_DEUT]]),
            ], true);
        $sumSteal = array_sum($stealResource);
        $smallCargoNeeded = ceil($sumSteal / $pricelist[SHIP_SMALL_CARGO]['capacity']);
        $largeCargoNeeded = ceil($sumSteal / $pricelist[SHIP_LARGE_CARGO]['capacity']);

        // I'm use template class here, because i want to exclude HTML in PHP.

        require_once 'includes/classes/class.template.php';

        $template = new template();

        require_once 'includes/classes/class.theme.php';
        $THEME = new Theme();
        if (!empty($senderUser['dpath'])) {
            $THEME->setUserTheme($senderUser['dpath']);
        }

        // Usernamen in Spyreports
        // keep in mind if you manipulate the sprintf you have to add the element in the $LNG['sys_mess_head'] language\*\FLEET.php
        $spyTime = _date($LNG['php_tdformat'], $this->_fleet['fleet_end_stay'], $senderUser['timezone'], $LNG);
        $title = sprintf(
            $LNG['sys_mess_spy_report_details_' . $targetPlanet['planet_type']],
            $targetPlanet['name'],
            $targetUser['username'],
            $this->_fleet['fleet_end_galaxy'],
            $this->_fleet['fleet_end_system'],
            $this->_fleet['fleet_end_planet'],
            $spyTime,
        );

        // Scavengers Tool Box

        $stbSettings = PlayerUtil::player_stb_settings($senderUser);

        // determine if the spy report detects ships or deff
        $danger = $this->getDangerValue($spyData);
        $dangerClass = $danger > 0 ? "danger" : "nonedanger";

        // get the energy to indicate if someone is pushing to gravi
        $energy = $targetPlanet["energy"];
        if ($energy > 100000) {
            $energyClass = "midRess";
        } elseif ($energy > 250000) {
            $energyClass = "lowRess";
        } else {
            $energyClass = "";
        }

        $template->caching = true;
        $template->compile_id = $senderUser['lang'];
        $template->loadFilter('output', 'trimwhitespace');

        list($tplDir) = $template->getTemplateDir();
        $template->setTemplateDir($tplDir . 'game/');
        $template->assign_vars([
            'stbEnabled'                        => $stbSettings['stb_enabled'],
            'danger'                            => $danger,
            'dangerClass'                       => $dangerClass,
            'smallCargoNeeded'                  => $smallCargoNeeded,
            'largeCargoNeeded'                  => $largeCargoNeeded,
            'energy'                            => $energy,
            'energyClass'                       => $energyClass,
            'spyData'                           => $spyData,
            'targetPlanet'                      => $targetPlanet,
            'targetChance'                      => $targetChance,
            'spyChance'                         => $spyChance,
            'totalShipDefCount'                 => $totalShipDefCount,
            'dpath'                             => $THEME->getTheme(),
            'isBattleSim'                       => ENABLE_SIMULATOR_LINK == true && isModuleAvailable(MODULE_SIMULATOR),
            'title'                             => $title,
        ]);

        $template->assign_vars([
            'LNG'           => $LNG
        ], false);

        $spyReport = $template->fetch('shared.mission.spyReport.tpl');

        PlayerUtil::sendMessage(
            $this->_fleet['fleet_owner'],
            0,
            $LNG['sys_mess_spy_qg'],
            0,
            $LNG['sys_mess_spy_report'],
            $spyReport,
            $this->_fleet['fleet_start_time'],
            null,
            1,
            $this->_fleet['fleet_universe']
        );

        $LNG = $this->getLanguage($targetUser['lang']);

        $sourceLink = sprintf(
            $LNG['sys_mess_spy_link'],
            $senderPlanet['name'],
            $senderUser['username'],
            $this->_fleet['fleet_start_galaxy'],
            $this->_fleet['fleet_start_system'],
            $this->_fleet['fleet_start_planet'],
        );

        $targetLink = sprintf(
            $LNG['sys_mess_spy_link'],
            $targetPlanet['name'],
            $LNG['type_planet_short_' . $targetPlanet['planet_type']],
            $this->_fleet['fleet_end_galaxy'],
            $this->_fleet['fleet_end_system'],
            $this->_fleet['fleet_end_planet'],
        );

        $targetMessage = sprintf(
            $LNG['sys_mess_spy_ennemyfleet_' . $senderPlanet['planet_type']],
            $sourceLink,
            $targetLink,
        );

        PlayerUtil::sendMessage(
            $this->_fleet['fleet_target_owner'],
            0,
            $LNG['sys_mess_spy_control'],
            0,
            $LNG['sys_mess_spy_activity'],
            $targetMessage,
            $this->_fleet['fleet_start_time'],
            null,
            1,
            $this->_fleet['fleet_universe']
        );

        if ($totalShipDefCount > 0 && $targetChance >= $spyChance) {
            $config = Config::get($this->_fleet['fleet_universe']);
            $whereCol = $this->_fleet['fleet_end_type'] == 3 ? "id_luna" : "id";

            $sql = 'UPDATE %%PLANETS%% SET
			der_metal   = der_metal + :metal,
			der_crystal = der_crystal + :crystal,
            tf_active = :new_debris
			WHERE ' . $whereCol . ' = :planetId;';

            $db->update($sql, [
                ':metal'    => $fleetAmount * $pricelist[210]['cost'][901] * $config->Fleet_Cdr / 100,
                ':crystal'  => $fleetAmount * $pricelist[210]['cost'][902] * $config->Fleet_Cdr / 100,
                ':new_debris' => 1,
                ':planetId' => $this->_fleet['fleet_end_id'],
            ]);

            MissionFunctions::updateDestroyedAdvancedStats($this->_fleet['fleet_target_owner'], $this->_fleet['fleet_owner'], 210, $fleetAmount);

            $this->KillFleet();
        } else {
            $this->setState(FLEET_RETURN);
            $this->SaveFleet();
        }
    }





    /**
     * Returns the combined base attack values of ships and defences
     */
    public function getDangerValue($spyData)
    {
        $CombatCaps =& Singleton()->CombatCaps;
        $dangerValue = 0;

        // ships
        if (isset($spyData[200])) {
            foreach ($spyData[200] as $elementID => $amount) {
                $dangerValue += $CombatCaps[$elementID]['attack'] * $amount;
            }
        }

        // defence
        if (isset($spyData[400])) {
            foreach ($spyData[400] as $elementID => $amount) {
                if (
                    !($elementID == INTERCEPTOR_MISSILE) and
                    !($elementID == INTERPLANETARY_MISSILE)
                ) {
                    $dangerValue += $CombatCaps[$elementID]['attack'] * $amount;
                }
            }
        }

        return  $dangerValue;
    }

    /**
     * Returns the combined recycle values of ships and defences
     */
    public function getRecyleValue($spyData)
    {
        $pricelist =& Singleton()->pricelist;

        $config = Config::get($this->_fleet['fleet_universe']);
        $fleetIntoDebris = $config->Fleet_Cdr;
        $defIntoDebris = $config->Defs_Cdr;

        $recycleValue = 0;

        // ships
        if (isset($spyData[200]) && $fleetIntoDebris > 0) {
            foreach ($spyData[200] as $elementID => $amount) {
                $recycleValue += (($pricelist[$elementID]['cost'][RESOURCE_METAL] +
                    $pricelist[$elementID]['cost'][RESOURCE_CRYSTAL]) * $fleetIntoDebris / 100) * $amount;
            }
        }

        // defence
        if (isset($spyData[400]) && $defIntoDebris > 0) {
            foreach ($spyData[400] as $elementID => $amount) {
                $recycleValue += (($pricelist[$elementID]['cost'][RESOURCE_METAL] +
                    $pricelist[$elementID]['cost'][RESOURCE_CRYSTAL]) * $defIntoDebris / 100) * $amount;
            }
        }

        return  $recycleValue;
    }

    public function estimateRecyclers($recycleValue)
    {
        $pricelist =& Singleton()->pricelist;
        return ceil($recycleValue / $pricelist[SHIP_RECYCLER]["capacity"]) + 1;
    }



    public function EndStayEvent()
    {
        return;
    }

    public function ReturnEvent()
    {
        $this->RestoreFleet();
    }
}
