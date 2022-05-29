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
        global $pricelist, $reslist, $resource, $USER;

        $db = Database::get();

        $sql = 'SELECT * FROM %%USERS%% WHERE id = :userId;';
        $senderUser = $db->selectSingle($sql, [
            ':userId'   => $this->_fleet['fleet_owner'],
        ]);

        $targetUser = $db->selectSingle($sql, [
            ':userId'   => $this->_fleet['fleet_target_owner']
        ]);

        $sql = 'SELECT * FROM %%PLANETS%% WHERE id = :planetId FOR UPDATE;';
        $targetPlanet = $db->selectSingle($sql, [
            ':planetId' => $this->_fleet['fleet_end_id'],
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

        $targetChance = mt_rand(0, min(($fleetAmount / 4) * ($targetSpyTech / $senderSpyTech), 100));
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

        // what is seen by the 'attacker' is already calculated above.
        // if target planet does not have fleet or def, it is safe to set $targetChance to -1
        // so probes are no longer destroyed by thin air or something

        $totalShipDefCount = 0;

        foreach (array_values($this->getPlanetFleet($targetPlanet)) as $ships) {
            $totalShipDefCount += $ships;
        }



        // I'm use template class here, because i want to exclude HTML in PHP.

        require_once 'includes/classes/class.template.php';

        $template = new template();

        $template->caching = true;
        $template->compile_id = $senderUser['lang'];
        $template->loadFilter('output', 'trimwhitespace');
        list($tplDir) = $template->getTemplateDir();
        $template->setTemplateDir($tplDir . 'game/');
        $template->assign_vars([
            'spyData'       => $spyData,
            'targetPlanet'  => $targetPlanet,
            'targetChance'  => $targetChance,
            'spyChance'     => $spyChance,
            'totalShipDefCount' => $totalShipDefCount,
            'isBattleSim'   => ENABLE_SIMULATOR_LINK == true && isModuleAvailable(MODULE_SIMULATOR),
            'title'         => sprintf(
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
			der_crystal = der_crystal + :crystal
			WHERE ' . $whereCol . ' = :planetId;';

            $db->update($sql, [
                ':metal'    => $fleetAmount * $pricelist[210]['cost'][901] * $config->Fleet_Cdr / 100,
                ':crystal'  => $fleetAmount * $pricelist[210]['cost'][902] * $config->Fleet_Cdr / 100,
                ':planetId' => $this->_fleet['fleet_end_id'],
            ]);

            $this->KillFleet();
        } else {
            $this->setState(FLEET_RETURN);
            $this->SaveFleet();
        }
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
