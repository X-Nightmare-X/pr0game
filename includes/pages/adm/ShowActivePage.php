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

if (!allowedTo(str_replace([dirname(__FILE__), '\\', '/', '.php'], '', __FILE__))) {
    throw new Exception("Permission error!");
}

function ShowActivePage()
{
    $LNG =& Singleton()->LNG;
    $USER =& Singleton()->USER;
    $id = HTTP::_GP('id', 0);
    if (!isset($_GET['action'])) {
        $_GET['action'] = '';
    }
    if ($_GET['action'] == 'delete' && !empty($id)) {
        $GLOBALS['DATABASE']->query("DELETE FROM ".USERS_VALID." WHERE `validationID` = '".$id."' AND `universe` = '".Universe::getEmulated()."';");
    }

    $query = $GLOBALS['DATABASE']->query("SELECT * FROM ".USERS_VALID." WHERE `universe` = '".Universe::getEmulated()."' ORDER BY validationID ASC");

    $Users	= [];
    while ($User = $GLOBALS['DATABASE']->fetch_array($query)) {
        $Users[]	= [
            'id'			=> $User['validationID'],
            'name'			=> $User['userName'],
            'date'			=> _date($LNG['php_tdformat'], $User['date'], $USER['timezone']),
            'email'			=> $User['email'],
            'ip'			=> $User['ip'],
            'password'		=> $User['password'],
            'validationKey'	=> $User['validationKey'],
        ];
    }

    $template	= new template();

    $template->assign_vars([
        'Users'				=> $Users,
        'uni'				=> Universe::getEmulated(),
        'signalColors'      => $USER['signalColors'],
    ]);

    $template->show('ActivePage.tpl');
}
