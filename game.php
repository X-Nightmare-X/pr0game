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

define('MODE', 'INGAME');
define('ROOT_PATH', dirname(__FILE__) . '/');
set_include_path(
    ROOT_PATH . 'includes/libs/BBCodeParser2/' . PATH_SEPARATOR . ROOT_PATH . PATH_SEPARATOR . get_include_path()
);
require_once('HTML/BBCodeParser2.php');

require 'includes/pages/game/ShowErrorPage.class.php';
require 'includes/common.php';
require 'includes/pages/game/AbstractGamePage.class.php';
/** @var $LNG Language */

$page = HTTP::_GP('page', 'overview');
$mode = HTTP::_GP('mode', 'show');
$page = str_replace(['_', '\\', '/', '.', "\0"], '', $page);
$pageClass = 'Show' . ucwords($page) . 'Page';

$path = 'includes/pages/game/' . $pageClass . '.class.php';

if (!file_exists($path)) {
    ShowErrorPage::printError($LNG['page_doesnt_exist']);
}

// Added Autoload in feature Versions
require $path;

$pageObj = new $pageClass();
// PHP 5.2 FIX
// can't use $pageObj::$requireModule
$pageProps = get_class_vars(get_class($pageObj));

if (
    isset($pageProps['requireModule'])
    && $pageProps['requireModule'] !== 0
    && !isModuleAvailable($pageProps['requireModule'])
) {
    ShowErrorPage::printError($LNG['sys_module_inactive']);
}

if (!is_callable([$pageObj, $mode])) {
    if (!isset($pageProps['defaultController']) || !is_callable([$pageObj, $pageProps['defaultController']])) {
        ShowErrorPage::printError($LNG['page_doesnt_exist']);
    }
    $mode = $pageProps['defaultController'];
}

$pageObj->{$mode}();
