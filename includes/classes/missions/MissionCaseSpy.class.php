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
        global $reslist, $resource;

        $planetfleet = [];
        $resourceIDs = $reslist['fleet'];

        foreach ($resourceIDs as $shiptype) {
            $planetfleet[$shiptype] = $targetPlanet[$resource[$shiptype]];
        }

        return $planetfleet;
    }

    private function getPlanetDefense($targetPlanet)
    {
        global $reslist, $resource;

        $planetdef = [];
        $resourceIDs = $reslist['defense'];

        foreach ($resourceIDs as $deftype) {
            $planetdef[$deftype] = $targetPlanet[$resource[$deftype]];
        }

        return $planetdef;
    }

    public function TargetEvent()
    {
        global $pricelist, $reslist, $resource;

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

        $sql = 'SELECT name FROM %%PLANETS%% WHERE id = :planetId;';
        $senderPlanetName = $db->selectSingle($sql, [
            ':planetId' => $this->_fleet['fleet_start_id']
        ], 'name');

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
        // I'm use template class here, because i want to exclude HTML in PHP.

        require_once 'includes/classes/class.template.php';

        $template = new template();

        require_once 'includes/classes/class.theme.php';
        $THEME = new Theme();
        if (!empty($senderUser['dpath'])) {
            $THEME->setUserTheme($senderUser['dpath']);
        }

        // Summ up all ress from the planet and calculate how many you could raid
        $ressources = round($targetPlanet['metal'] + $targetPlanet['crystal'] + $targetPlanet['deuterium']);
        $ressourcesToRaid = round($ressources / 2);

        // determine if the spy report detects ships or deff
        if (isset($classIDs[400])) {
            $danger = $this->getDangerValue($spyData, true);
        } elseif (isset($classIDs[200])) {
            $danger = $this->getDangerValue($spyData, false);
        } else {
            $danger = 0;
        }
        $dangerClass = $danger > 0 ? "danger" : "nonedanger";

        // if the spy report got ships get the approximately number of needet recylers
        if (isset($classIDs[200])) {
            $recyclePotential = $this->getRecyleValue($spyData);
        } else {
            $recyclePotential = 0;
        }
        $nessesarryRecy = $this->estimateRecyclers($recyclePotential);

        // get the ressource value by market and set the background color class
        $ressourcesByMarketValue = $this->getRessoucesByDsuValue($targetPlanet['metal'], $targetPlanet['crystal'], $targetPlanet['deuterium']);
        if ($ressourcesByMarketValue > 150000) {
            $ressourcesByMarketValueClass = "realHighRess";
        } elseif ($ressourcesByMarketValue > 90000) {
            $ressourcesByMarketValueClass = "highRess";
        } elseif ($ressourcesByMarketValue > 30000) {
            $ressourcesByMarketValueClass = "midRess";
        } else {
            $ressourcesByMarketValueClass = "lowRess";
        }

        // calculate the needed transporters
        $nessesarrySmallTransporter = $this->estimateSmallTransporters($this->calculateNeededCapacity($targetPlanet['metal'], $targetPlanet['crystal'], $targetPlanet['deuterium']));
        $nessesarryGreatTransporter = $this->estimateLargeTransporters($this->calculateNeededCapacity($targetPlanet['metal'], $targetPlanet['crystal'], $targetPlanet['deuterium']));
        
        // get the best planet to start the raid from and the ress per time from there
        $targetCoordinates = [$targetPlanet["galaxy"], $targetPlanet["system"], $targetPlanet["planet"]];
        $bestPlanetArray = $this->bestplanet($senderUser["id"], $targetCoordinates);
        $bestPlanet = $bestPlanetArray[1][0] . ":" . $bestPlanetArray[1][1] . ":" . $bestPlanetArray[1][2];
        $bestRessPerTime = $this->bestRessPerTime($bestPlanetArray[0], $senderUser, $ressourcesByMarketValue);
        if ($bestRessPerTime > 20) {
            $bestRessPerTimeClass = "realHighRess";
        } elseif ($bestRessPerTime > 10) {
            $bestRessPerTimeClass = "highRess";
        } elseif ($bestRessPerTime > 5) {
            $bestRessPerTimeClass = "midRess";
        } else {
            $bestRessPerTimeClass = "lowRess";
        }
        
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
            'ressources'                        => $ressources,
            'danger'                            => $danger,
            'dangerClass'                       => $dangerClass,
            'ressourcesToRaid'                  => $ressourcesToRaid,
            'recyclePotential'                  => $recyclePotential,
            'nessesarryRecy'                    => $nessesarryRecy,
            'ressourcesByMarketValue'           => $ressourcesByMarketValue,
            'ressourcesByMarketValueClass'      => $ressourcesByMarketValueClass,
            'energy'                            => $energy,
            'energyClass'                       => $energyClass,
            'bestRessPerTime'                   => $bestRessPerTime,
            'bestRessPerTimeClass'              => $bestRessPerTimeClass,
            'bestPlanet'                        => $bestPlanet,
            'nessesarryGreatTransporter'        => $nessesarryGreatTransporter,
            'nessesarrySmallTransporter'        => $nessesarrySmallTransporter,
            'spyData'                           => $spyData,
            'targetPlanet'                      => $targetPlanet,
            'targetChance'                      => $targetChance,
            'spyChance'                         => $spyChance,
            'totalShipDefCount'                 => $totalShipDefCount,
            'dpath'                             => $THEME->getTheme(),
            'isBattleSim'                       => ENABLE_SIMULATOR_LINK == true && isModuleAvailable(MODULE_SIMULATOR),
            'title'                 => sprintf(
                $LNG['sys_mess_head'],
                $targetPlanet['name'],
                $targetPlanet['galaxy'],
                $targetPlanet['system'],
                $targetPlanet['planet'],
                _date($LNG['php_tdformat'], $this->_fleet['fleet_end_time'], $senderUser['timezone'], $LNG)
            ),
        ]);

        $template->assign_vars([
            'LNG'           => $LNG
        ], false);

        $spyReport = $template->fetch('shared.mission.spyReport.tpl');

        PlayerUtil::sendMessage(
            $this->_fleet['fleet_owner'],
            0,
            $LNG['sys_mess_qg'],
            0,
            $LNG['sys_mess_spy_report'],
            $spyReport,
            $this->_fleet['fleet_start_time'],
            null,
            1,
            $this->_fleet['fleet_universe']
        );

        $LNG = $this->getLanguage($targetUser['lang']);
        $targetMessage = $LNG['sys_mess_spy_ennemyfleet'] . " " . $senderPlanetName;

        if ($this->_fleet['fleet_start_type'] == 3) {
            $targetMessage .= $LNG['sys_mess_spy_report_moon'] . ' ';
        }

        $text = '<a href="game.php?page=galaxy&amp;galaxy=%1$s&amp;system=%2$s">[%1$s:%2$s:%3$s]</a> %7$s
		%8$s <a href="game.php?page=galaxy&amp;galaxy=%4$s&amp;system=%5$s">[%4$s:%5$s:%6$s]</a>';

        $targetMessage .= sprintf(
            $text,
            $this->_fleet['fleet_start_galaxy'],
            $this->_fleet['fleet_start_system'],
            $this->_fleet['fleet_start_planet'],
            $this->_fleet['fleet_end_galaxy'],
            $this->_fleet['fleet_end_system'],
            $this->_fleet['fleet_end_planet'],
            $LNG['sys_mess_spy_seen_at'],
            $targetPlanet['name']
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

    // calculates the given ressources, based by the worth of 1 deuterium
    public function getRessoucesByDsuValue($metal, $crystal, $deuterium)
    {
        require_once 'includes/classes/class.MarketManager.php';
        $pMarket = new MarketManager();
        $refrates = $pMarket->getReferenceRatios();
        $refratesMetal = $refrates["metal"];
        $refratesCrystal = $refrates["crystal"];
        $refratesDeuterium = $refrates["deuterium"];

        $ressoucesByDsuValue = $metal / $refratesMetal + $crystal / $refratesCrystal + $deuterium / $refratesDeuterium;

        return round($ressoucesByDsuValue);
    }

    public function calculateNeededCapacity($metal, $crystal, $deuterium)
    {
        $capacity = 0;
        $capacity = 0.5 * max($metal + $crystal + $deuterium, min(0.75 * (2 * $metal + $crystal + $deuterium), 2 * $metal + $deuterium));
        return $capacity;
    }

    public function estimateSmallTransporters($capacity)
    {
        global $pricelist;
        return ceil($capacity / $pricelist[SHIP_SMALL_CARGO]['capacity']) + 1;
    }

    public function estimateLargeTransporters($capacity)
    {
        global $pricelist;
        return ceil($capacity / $pricelist[SHIP_LARGE_CARGO]['capacity']) + 1;
    }

    public function getDangerValue($spyData, $bar)
    {

        $dangerValue = 0;

        // KT
        if (isset($spyData["200"][202]) && $spyData["200"][202] !== 0) {
            $dangerValue += $spyData["200"][202] * 5;
        }
        // GT
        if (isset($spyData["200"][203]) && $spyData["200"][203] !== 0) {
            $dangerValue += $spyData["200"][203] * 5;
        }
        // LJ
        if (isset($spyData["200"][204]) && $spyData["200"][204] !== 0) {
            $dangerValue += $spyData["200"][204] * 50;
        }
        // SJ
        if (isset($spyData["200"][205]) && $spyData["200"][205] !== 0) {
            $dangerValue += $spyData["200"][205] * 150;
        }
        // Xer:innen
        if (isset($spyData["200"][206]) && $spyData["200"][206] !== 0) {
            $dangerValue += $spyData["200"][206] * 400;
        }
        // SS
        if (isset($spyData["200"][207]) && $spyData["200"][207] !== 0) {
            $dangerValue += $spyData["200"][207] * 1000;
        }
        // Kolo
        if (isset($spyData["200"][208]) && $spyData["200"][208] !== 0) {
            $dangerValue += $spyData["200"][208] * 50;
        }
        // Rec
        if (isset($spyData["200"][209]) && $spyData["200"][209] !== 0) {
            $dangerValue += $spyData["200"][209] * 1;
        }
        // BBer
        if (isset($spyData["200"][211]) && $spyData["200"][211] !== 0) {
            $dangerValue += $spyData["200"][211] * 1000;
        }
        // Zerren
        if (isset($spyData["200"][213]) && $spyData["200"][213] !== 0) {
            $dangerValue += $spyData["200"][213] * 2000;
        }
        // RIP
        if (isset($spyData["200"][214]) && $spyData["200"][214] !== 0) {
            $dangerValue += $spyData["200"][214] * 200000;
        }
        // SXer
        if (isset($spyData["200"][215]) && $spyData["200"][215] !== 0) {
            $dangerValue += $spyData["200"][215] * 700;
        }

        if ($bar) {
            // Rak
            if (isset($spyData["400"][401]) && $spyData["400"][401] !== 0) {
                $dangerValue += $spyData["400"][401] * 80;
            }
            // LL
            if (isset($spyData["400"][402]) && $spyData["400"][402] !== 0) {
                $dangerValue += $spyData["400"][402] * 100;
            }
            // SL
            if (isset($spyData["400"][403]) && $spyData["400"][403] !== 0) {
                $dangerValue += $spyData["400"][403] * 250;
            }
            // Gaus
            if (isset($spyData["400"][404]) && $spyData["400"][404] !== 0) {
                $dangerValue += $spyData["400"][404] * 1100;
            }
            // Ionen
            if (isset($spyData["400"][405]) && $spyData["400"][405] !== 0) {
                $dangerValue += $spyData["400"][405] * 150;
            }
            // Plasma
            if (isset($spyData["400"][406]) && $spyData["400"][406] !== 0) {
                $dangerValue += $spyData["400"][406] * 3000;
            }
            // KSchild
            if (isset($spyData["400"][407]) && $spyData["400"][407] !== 0) {
                $dangerValue += $spyData["400"][407] * 1;
            }
            // G Schild
            if (isset($spyData["400"][408]) && $spyData["400"][408] !== 0) {
                $dangerValue += $spyData["400"][408] * 1;
            }
        }

        return  $dangerValue;
    }

    public function getRecyleValue($spyData)
    {
        global $pricelist;
        $pricelist[SHIP_SMALL_CARGO]['cost'][RESOURCE_METAL];

        $config = Config::get($this->_fleet['fleet_universe']);
        $fleetIntoDebris = $config->Fleet_Cdr;
        $defIntoDebris = $config->Defs_Cdr;

        $recycleValue = 0;

        // SHIP_SMALL_CARGO
        if (isset($spyData["200"][202]) && $spyData["200"][202] !== 0) {
            $recycleValue += $spyData["200"][202] * (($pricelist[SHIP_SMALL_CARGO]['cost'][RESOURCE_METAL] + $pricelist[SHIP_SMALL_CARGO]['cost'][RESOURCE_CRYSTAL]) / 100 * $fleetIntoDebris);
        }
        // SHIP_LARGE_CARGO
        if (isset($spyData["200"][203]) && $spyData["200"][203] !== 0) {
            $recycleValue += $spyData["200"][203] * (($pricelist[SHIP_LARGE_CARGO]['cost'][RESOURCE_METAL] + $pricelist[SHIP_LARGE_CARGO]['cost'][RESOURCE_CRYSTAL]) / 100 * $fleetIntoDebris);
        }
        // SHIP_LIGHT_FIGHTER
        if (isset($spyData["200"][204]) && $spyData["200"][204] !== 0) {
            $recycleValue += $spyData["200"][204] * (($pricelist[SHIP_LIGHT_FIGHTER]['cost'][RESOURCE_METAL] + $pricelist[SHIP_LIGHT_FIGHTER]['cost'][RESOURCE_CRYSTAL]) / 100 * $fleetIntoDebris);
        }
        // SHIP_HEAVY_FIGHTER
        if (isset($spyData["200"][205]) && $spyData["200"][205] !== 0) {
            $recycleValue += $spyData["200"][205] * (($pricelist[SHIP_HEAVY_FIGHTER]['cost'][RESOURCE_METAL] + $pricelist[SHIP_HEAVY_FIGHTER]['cost'][RESOURCE_CRYSTAL]) / 100 * $fleetIntoDebris);
        }
        // Xer:innen SHIP_CRUISER 
        if (isset($spyData["200"][206]) && $spyData["200"][206] !== 0) {
            $recycleValue += $spyData["200"][206] * (($pricelist[SHIP_CRUISER]['cost'][RESOURCE_METAL] + $pricelist[SHIP_CRUISER]['cost'][RESOURCE_CRYSTAL]) / 100 * $fleetIntoDebris);
        }
        // SHIP_BATTLESHIP
        if (isset($spyData["200"][207]) && $spyData["200"][207] !== 0) {
            $recycleValue += $spyData["200"][207] * (($pricelist[SHIP_BATTLESHIP]['cost'][RESOURCE_METAL] + $pricelist[SHIP_BATTLESHIP]['cost'][RESOURCE_CRYSTAL]) / 100 * $fleetIntoDebris);
        }
        // SHIP_COLOSHIP
        if (isset($spyData["200"][208]) && $spyData["200"][208] !== 0) {
            $recycleValue += $spyData["200"][208] * (($pricelist[SHIP_COLOSHIP]['cost'][RESOURCE_METAL] + $pricelist[SHIP_COLOSHIP]['cost'][RESOURCE_CRYSTAL]) / 100 * $fleetIntoDebris);
        }
        // SHIP_RECYCLER
        if (isset($spyData["200"][209]) && $spyData["200"][209] !== 0) {
            $recycleValue += $spyData["200"][209] * (($pricelist[SHIP_RECYCLER]['cost'][RESOURCE_METAL] + $pricelist[SHIP_RECYCLER]['cost'][RESOURCE_CRYSTAL]) / 100 * $fleetIntoDebris);
        }
        // SHIP_PROBE
        if (isset($spyData["200"][210]) && $spyData["200"][210] !== 0) {
            $recycleValue += $spyData["200"][210] * (($pricelist[SHIP_PROBE]['cost'][RESOURCE_METAL] + $pricelist[SHIP_PROBE]['cost'][RESOURCE_CRYSTAL]) / 100 * $fleetIntoDebris);
        }
        // SHIP_BOMBER
        if (isset($spyData["200"][211]) && $spyData["200"][211] !== 0) {
            $recycleValue += $spyData["200"][211] * (($pricelist[SHIP_BOMBER]['cost'][RESOURCE_METAL] + $pricelist[SHIP_BOMBER]['cost'][RESOURCE_CRYSTAL]) / 100 * $fleetIntoDebris);
        }
        // SHIP_SOLSAT
        if (isset($spyData["200"][212]) && $spyData["200"][212] !== 0) {
            $recycleValue += $spyData["200"][212] * (($pricelist[SHIP_SOLSAT]['cost'][RESOURCE_METAL] + $pricelist[SHIP_SOLSAT]['cost'][RESOURCE_CRYSTAL]) / 100 * $fleetIntoDebris);
        }
        // SHIP_DESTROYER
        if (isset($spyData["200"][213]) && $spyData["200"][213] !== 0) {
            $recycleValue += $spyData["200"][213] * (($pricelist[SHIP_DESTROYER]['cost'][RESOURCE_METAL] + $pricelist[SHIP_DESTROYER]['cost'][RESOURCE_CRYSTAL]) / 100 * $fleetIntoDebris);
        }
        // SHIP_RIP
        if (isset($spyData["200"][214]) && $spyData["200"][214] !== 0) {
            $recycleValue += $spyData["200"][214] * (($pricelist[SHIP_RIP]['cost'][RESOURCE_METAL] + $pricelist[SHIP_RIP]['cost'][RESOURCE_CRYSTAL]) / 100 * $fleetIntoDebris);
        }
        // SHIP_BATTLECRUISER
        if (isset($spyData["200"][215]) && $spyData["200"][215] !== 0) {
            $recycleValue += $spyData["200"][215] * (($pricelist[SHIP_BATTLECRUISER]['cost'][RESOURCE_METAL] + $pricelist[SHIP_BATTLECRUISER]['cost'][RESOURCE_CRYSTAL]) / 100 * $fleetIntoDebris);
        }

        // TODO: DEFF ins TF

        return  $recycleValue;
    }

    public function estimateRecyclers($recycleValue)
    {
        return ceil($recycleValue / 20000) + 1;
    }

    public function bestplanet($owner, $targetCoordinates)
    {
        $db = Database::get();
        $universe = Universe::current();

        $sql = "Select galaxy, system, planet from %%PLANETS%% where universe = :universe and id_owner = :owner ;";
        $targetPlanet = $db->select($sql, [
            ':universe' => $universe,
            ':owner'    => $owner
        ]);

        $bestPlanet = [];
        $bestDistance = 0;

        foreach ($targetPlanet as $positions) {

            $distance = FleetFunctions::getTargetDistance(
                [
                    $positions["galaxy"],
                    $positions["system"],
                    $positions["planet"]
                ],
                [
                    $targetCoordinates['0'],
                    $targetCoordinates['1'],
                    $targetCoordinates['2']
                ]
            );

            if (($bestDistance === 0) || ($bestDistance > $distance)) {
                $bestDistance = $distance;
                $bestPlanet = [$positions["galaxy"], $positions["system"], $positions["planet"]];
            }
        }

        return [$bestDistance, $bestPlanet];
    }

    public function bestRessPerTime($distance, $senderUser, $ressourcesByMarketValue)
    {
        $fleetArray = [214 => 1];
        $MaxFleetSpeed = FleetFunctions::getFleetMaxSpeed($fleetArray, $senderUser);
        $GameSpeed = Config::get()->fleet_speed / 2500;
        $flytime = FleetFunctions::getMissionDuration(100, $MaxFleetSpeed, $distance, $GameSpeed, $senderUser);

        $ressPerTime = $ressourcesByMarketValue / ($flytime * 2);

        return round($ressPerTime, 2);
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
