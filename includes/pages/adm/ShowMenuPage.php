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

function ShowMenuPage()
{
	global $USER;
	$template	= new template();
	if(isset($USER['id'])) {
		$signalColors = PlayerUtil::player_signal_colors($USER);
	}
	else {
		$signalColors = array('colorPositive' => '#00ff00', 'colorNegative' => '#ff0000', 'colorNeutral' => '#ffd600');
	}
	$template->assign_vars(array(	
		'supportticks'	=> $GLOBALS['DATABASE']->getFirstCell("SELECT COUNT(*) FROM ".TICKETS." WHERE universe = ".Universe::getEmulated()." AND status = 0;"),
		'signalColors'	=> $signalColors,
	));
	
	$template->show('ShowMenuPage.tpl');
}
