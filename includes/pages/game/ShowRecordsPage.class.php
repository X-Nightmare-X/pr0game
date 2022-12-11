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
        require_once('includes/classes/class.statbuilder.php');
            require_once('includes/models/StatPoints.php');
            $stat = new Statbuilder();
		    $stat -> buildRecords();
        $sql = "SELECT r.elementID, r.level, r.userID, u.username
		FROM %%USERS%% u
		INNER JOIN %%RECORDS%% r ON r.userID = u.id
		WHERE r.universe = :universe;";

        $recordResult = $db->select($sql, [':universe' => Universe::current()]);

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
        ]);

        $this->display('page.records.default.tpl');
    }
}
