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

// VARS DB -> SCRIPT WRAPPER

$cache  = Cache::get();
$cache->add('vars', 'VarsBuildCache');

$vars = $cache->getData('vars');
$result = $vars[0];
require_once('includes/classes/Universe.class.php');
if (isset($vars[Universe::current()])) {
    $result = array_replace_recursive($result, $vars[Universe::current()]);
}
foreach ($result as $k => $v) {
    Singleton()->$k = $v;
    $$k =& Singleton()->$k;
}

$resource[901] = 'metal';
$resource[902] = 'crystal';
$resource[903] = 'deuterium';
$resource[911] = 'energy';

$reslist['ressources']  = [901, 902, 903, 911];
$reslist['resstype'][1] = [901, 902, 903];
$reslist['resstype'][2] = [911];
$reslist['resstype'][3] = [];
