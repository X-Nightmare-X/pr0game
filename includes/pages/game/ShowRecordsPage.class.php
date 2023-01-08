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


class ShowRecordsPage extends AbstractGamePage
{
    public static $requireModule = MODULE_RECORDS;

    public function __construct()
    {
        parent::__construct();
    }

    public function show()
    {
        global $USER, $LNG, $reslist;

        $db = Database::get();

        $sql = "SELECT r.elementID, r.level, r.userID, u.username, u.records_optIn, v.name
		FROM %%USERS%% u
		INNER JOIN %%RECORDS%% r ON r.userID = u.id
        INNER JOIN %%VARS%% v ON v.elementID = r.elementID
		WHERE r.universe = :universe;";

        $recordResult = $db->select($sql, [':universe' => Universe::current()]);

        $sql = "SELECT spy_tech, computer_tech, military_tech, defence_tech, shield_tech, 
        energy_tech, hyperspace_tech, combustion_tech, impulse_motor_tech, hyperspace_motor_tech, laser_tech, 
        ionic_tech, buster_tech, intergalactic_tech, expedition_tech, metal_proc_tech, crystal_proc_tech,
        deuterium_proc_tech, graviton_tech
        FROM %%USERS%% WHERE id = :userId;";

        $userTechResult = $db->selectSingle($sql, [':userId' => $USER['id']]);

        $sql = "SELECT max(metal_mine) AS 'metal_mine', max(crystal_mine) AS 'crystal_mine', 
        max(deuterium_sintetizer) AS 'deuterium_sintetizer', max(solar_plant) AS 'solar_plant', 
        max(fusion_plant) AS 'fusion_plant', max(robot_factory) AS 'robot_factory', 
        max(nano_factory) AS 'nano_factory', max(hangar) AS 'hangar', max(metal_store) AS 'metal_store', 
        max(crystal_store) AS 'crystal_store', max(deuterium_store) AS 'deuterium_store', 
        max(laboratory) AS 'laboratory', max(terraformer) AS 'terraformer', max(university) AS 'university', 
        max(ally_deposit) AS 'ally_deposit', max(silo) AS 'silo', max(mondbasis) AS 'mondbasis', 
        max(phalanx) AS 'phalanx', max(sprungtor)  AS 'sprungtor'
        FROM %%PLANETS%% WHERE id_owner = :userId;";

        $userBuildResult = $db->selectSingle($sql, [':userId' => $USER['id']]);
        $defenseList = array_fill_keys($reslist['defense'], []);
        $fleetList = array_fill_keys($reslist['fleet'], []);
        $researchList = array_fill_keys($reslist['tech'], []);
        $buildList = array_fill_keys($reslist['build'], []);

        foreach ($recordResult as $recordRow) {
            if (in_array($recordRow['elementID'], $reslist['defense'])) {
                $defenseList[$recordRow['elementID']][] = $recordRow;
            } elseif (in_array($recordRow['elementID'], $reslist['fleet'])) {
                $fleetList[$recordRow['elementID']][] = $recordRow;
            } elseif (in_array($recordRow['elementID'], $reslist['tech'])) {
                $researchList[$recordRow['elementID']][] = $recordRow;
            } elseif (in_array($recordRow['elementID'], $reslist['build'])) {
                $buildList[$recordRow['elementID']][] = $recordRow;
            } elseif (in_array($recordRow['elementID'], $reslist['missile'])) {
                $defenseList[$recordRow['elementID']][] = $recordRow;
            }
        }

        require_once 'includes/classes/Cronjob.class.php';
        $this->assign([
            'defenseList'   => $defenseList,
            'fleetList'     => $fleetList,
            'researchList'  => $researchList,
            'buildList'     => $buildList,
            'update'        => _date(
                $LNG['php_tdformat'],
                Cronjob::getLastExecutionTime('statistic'),
                $USER['timezone']
            ),
            'userTech'      => $userTechResult,
            'userBuild'     => $userBuildResult,
        ]);

        $this->display('page.records.default.tpl');
    }
}
