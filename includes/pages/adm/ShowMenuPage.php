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
    $USER =& Singleton()->USER;
    $db = Database::get();
    $template	= new template();
    $template->setCaching(Smarty::CACHING_OFF);

    $sql = "SELECT COUNT(*) AS anz FROM %%TICKETS%% WHERE `universe` = :universe AND `status` = 0;";
    $anzTickets = $db->selectSingle($sql, [':universe' => Universe::getEmulated()], 'anz');

    $template->assign_vars([
        'supportticks'	=> $anzTickets,
        'signalColors'	=> $USER['signalColors'],
    ]);

    $template->show('ShowMenuPage.tpl');
}
