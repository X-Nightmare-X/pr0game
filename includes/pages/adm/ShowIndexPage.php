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

function ShowIndexPage()
{
    $LNG =& Singleton()->LNG;
    $USER =& Singleton()->USER;
    $template	= new template();
    $template->assign_vars([
        'game_name'		=> Config::get()->game_name,
        'adm_cp_title'	=> $LNG['adm_cp_title'],
        'signalColors'  => $USER['signalColors']
    ]);

    $template->display('adm/ShowIndexPage.tpl');
}
