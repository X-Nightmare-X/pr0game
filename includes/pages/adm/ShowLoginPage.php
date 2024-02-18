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

if ($USER['authlevel'] == AUTH_USR) {
    throw new PagePermissionException("Permission error!");
}

function ShowLoginPage()
{
    $USER =& Singleton()->USER;
    $session = Session::create();
    $session->universe = $USER['universe'];
    if ($session->adminAccess == 1) {
        HTTP::redirectTo('admin.php');
    }

    if (isset($_REQUEST['admin_pw'])) {
        if (password_verify($_REQUEST['admin_pw'], $USER['password'])) {
            $session->adminAccess	= 1;
            HTTP::redirectTo('admin.php');
        }
    }

    $template	= new template();

    $template->assign_vars([
        'bodyclass'		=> 'standalone',
        'email'		    => $USER['email'],
        'signalColors'  => $USER['signalColors']
    ]);
    $template->show('LoginPage.tpl');
}
