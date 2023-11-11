<?php

/**
 *
 * For the full copyright and license information, please view the LICENSE
 *
 * @package 2Moons
 * @author Reflexrecon
 * @copyright 2023 Reflexrecon
 * @licence MIT
 * @version 1.8.0
 */

function ShowReshufflePage()
{
    $USER =& Singleton()->USER;
    $LNG =& Singleton()->LNG;
    $ACTION	= HTTP::_GP('action', '');
    if ($ACTION == 'send') {
        $selectedUniverse	= HTTP::_GP('universe', '');
        require_once('includes/classes/PlayerUtil.class.php');
        $result = PlayerUtil::reshuffleAllPlanets($selectedUniverse);
        if ($result === true) {
            exit($LNG['rp_done']);
        }
        exit($result);
    }
    $universeSelect	= [];
    foreach (Universe::availableUniverses() as $uniId) {
        $config = Config::get($uniId);
        $universeSelect[$uniId]	= sprintf('%s (ID: %d)', $config->uni_name, $uniId);
    }
    ksort($universeSelect);

    $template	= new template();
    $template->assign_vars([
        'universes' 	=> $universeSelect,
        'signalColors' 	=> $USER['signalColors'],
    ]);
    $template->show('ReshufflePage.tpl');
}
