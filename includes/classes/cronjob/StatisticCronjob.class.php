<?php

/**
 *  2Moons
 *   by Jan-Otto Kröpke 2009-2016
 * For the full copyright and license information, please view the LICENSE
 * @package 2Moons
 * @author Jan-Otto Kröpke <slaver7@gmail.com>
 * @copyright 2009 Lucky
 * @copyright 2016 Jan-Otto Kröpke <slaver7@gmail.com>
 * @licence MIT
 * @version 1.8.0
 * @link https://github.com/jkroepke/2Moons
 */

require_once 'includes/classes/cronjob/CronjobTask.interface.php';

class StatisticCronjob implements CronjobTask
{
    function run()
    {
        try {
            require_once('includes/classes/class.statbuilder.php');
            require_once('includes/models/StatPoints.php');
            $stat = new Statbuilder();
            $stat->generateStats();
		    $statbuilder -> buildRecords();
        } catch (Exception $exception) {
            echo $exception->getMessage();
        }
    }
}
