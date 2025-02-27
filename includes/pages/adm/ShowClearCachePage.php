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

function ShowClearCachePage()
{
    $LNG =& Singleton()->LNG;
    $USER =& Singleton()->USER;
    ClearCache();
    $template = new template();
    $template->assign_vars([
        'signalColors'	=> $USER['signalColors']
    ]);
    $template->message($LNG['cc_cache_clear'], );
}
