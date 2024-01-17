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

function ShowModulePage()
{
    $LNG =& Singleton()->LNG;
    $USER =& Singleton()->USER;
    $config = Config::get(Universe::getEmulated());
    $module = $config->moduls;

    if (isset($_GET['mode'])) {
        $module[HTTP::_GP('id', 0)] = ($_GET['mode'] == 'aktiv') ? 1 : 0;
        $config->moduls = $module;
        $config->saveModules();
        ClearCache();
    }

    foreach ($module as $ID => $state) {
        $Modules[$ID] = [
            'name'  => $LNG['modul_' . $ID],
            'state' => $state,
        ];
    }

    asort($Modules);
    $template = new template();

    $template->assign_vars([
        'Modules'               => $Modules,
        'mod_module'            => $LNG['mod_module'],
        'mod_info'              => $LNG['mod_info'],
        'mod_active'            => $LNG['mod_active'],
        'mod_deactive'          => $LNG['mod_deactive'],
        'mod_change_active'     => $LNG['mod_change_active'],
        'mod_change_deactive'   => $LNG['mod_change_deactive'],
        'signalColors'          => $USER['signalColors'],
    ]);

    $template->show('ModulePage.tpl');
}
