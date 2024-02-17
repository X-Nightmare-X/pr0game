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
        $USER =& Singleton()->USER;
        $LNG =& Singleton()->LNG;
        $reslist =& Singleton()->reslist;
        $resource =& Singleton()->resource;
        $db = Database::get();

        $sql = "SELECT r.elementID, r.level, r.userID, u.username, u.records_optIn, v.name
            FROM %%USERS%% u
            INNER JOIN %%RECORDS%% r ON r.userID = u.id
            INNER JOIN %%VARS%% v ON v.elementID = r.elementID
            WHERE r.universe = :universe;";
        $recordResult = $db->select($sql, [':universe' => Universe::current()]);

        $selected_tech = [];
        foreach ($reslist['tech'] as $Techno) {
            $selected_tech[] = $resource[$Techno];
        }

        $sql = "SELECT " . implode(", ", $selected_tech) . " FROM %%USERS%% WHERE id = :userId;";
        $userTechResult = $db->selectSingle($sql, [':userId' => $USER['id']]);

        $select_buildings = [];
        foreach ($reslist['build'] as $Building) {
            $select_buildings[] = sprintf('max(%1$s) AS \'%1$s\'', $resource[$Building]);
        }

        $sql = "SELECT " . implode(", ", $select_buildings) . " FROM %%PLANETS%% WHERE id_owner = :userId;";
        $userBuildResult = $db->selectSingle($sql, [':userId' => $USER['id']]);

        $defenseList = array_fill_keys($reslist['defense'], []);
        $fleetList = array_fill_keys($reslist['fleet'], []);
        $researchList = array_fill_keys($reslist['tech'], []);
        $buildList = array_fill_keys($reslist['build'], []);

        foreach ($recordResult as $recordRow) {
            $element = $recordRow['elementID'];
            if (BuildFunctions::isEnabled($element)) {
                if (in_array($element, $reslist['defense'])) {
                    $defenseList[$element][] = $recordRow;
                } elseif (in_array($element, $reslist['fleet'])) {
                    $fleetList[$element][] = $recordRow;
                } elseif (in_array($element, $reslist['tech'])) {
                    $researchList[$element][] = $recordRow;
                } elseif (in_array($element, $reslist['build'])) {
                    $buildList[$element][] = $recordRow;
                } elseif (in_array($element, $reslist['missile'])) {
                    $defenseList[$element][] = $recordRow;
                }
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
