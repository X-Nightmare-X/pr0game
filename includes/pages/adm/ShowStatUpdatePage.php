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

if (!allowedTo(str_replace([dirname(__FILE__), '\\', '/', '.php'], '', __FILE__))) throw new Exception("Permission error!");

function ShowStatUpdatePage()
{
    global $LNG, $USER;
    if(isset($USER['id'])) {
		$signalColors = PlayerUtil::player_signal_colors($USER);
	}
	else {
		$signalColors = array('colorPositive' => '#00ff00', 'colorNegative' => '#ff0000', 'colorNeutral' => '#ffd600');
	}

    try {
        require_once('includes/classes/class.statbuilder.php');
        require_once('includes/models/StatPoints.php');
        $stat = new statbuilder();
        $result = $stat->generateStats();
        $memory_p = str_replace(["%p", "%m"], $result['memory_peak'], $LNG['sb_top_memory']);
        $memory_e = str_replace(["%e", "%m"], $result['end_memory'], $LNG['sb_final_memory']);
        $memory_i = str_replace(["%i", "%m"], $result['initial_memory'], $LNG['sb_start_memory']);
        $stats_end_time = sprintf($LNG['sb_stats_update'], $result['totaltime']);
        $stats_sql = sprintf($LNG['sb_sql_counts'], $result['sql_count']);

        require_once 'includes/classes/class.Discord.php';
        Discord::sendLog('Statistik (manual)', $result);

        $template = new template();
        $template->assign_vars([
            'signalColors' => $signalColors
        ]);
        $template->message($LNG['sb_stats_updated'] . $stats_end_time . $memory_i . $memory_e . $memory_p . $stats_sql, false, 0, true);
    } catch (Exception $exception) {
        $template = new template();
        $template->assign_vars([
            'signalColors' => $signalColors
        ]);
        $template->message($exception->getMessage(), false, 0, true);

        require_once 'includes/classes/class.Discord.php';
        Discord::sendLog('Statistik (manual) FAILED', null, $exception);

    }
}
