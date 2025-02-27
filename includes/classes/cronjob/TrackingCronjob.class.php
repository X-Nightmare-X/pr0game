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

require_once 'includes/classes/cronjob/CronjobTask.interface.php';

class TrackingCronjob implements CronjobTask
{
    public function run()
    {
        $serverData['php']			= PHP_VERSION;

        try {
            $sql	= 'SELECT register_time FROM %%USERS%% WHERE id = :userId';
            $serverData['installSince']	= Database::get()->selectSingle($sql, [
                ':userId'	=> ROOT_USER
            ], 'register_time');
        } catch (Exception $e) {
            $serverData['installSince']	= null;
        }

        try {
            $sql	= 'SELECT COUNT(*) as state FROM %%USERS%%;';
            $serverData['users']		= Database::get()->selectSingle($sql, [], 'state');
        } catch (Exception $e) {
            $serverData['users']		= null;
        }

        try {
            $sql	= 'SELECT COUNT(*) as state FROM %%CONFIG_UNIVERSE%%;';
            $serverData['unis']			= Database::get()->selectSingle($sql, [], 'state');
        } catch (Exception $e) {
            $serverData['unis']			= null;
        }

        $serverData['version']		= Config::get(ROOT_UNI)->version;

        $ch	= curl_init('http://tracking.jkroepke.de/');
        curl_setopt($ch, CURLOPT_HTTPGET, true);
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $serverData);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (compatible; pr0game/".$serverData['version']."; +https://pr0game.com)");

        curl_exec($ch);
        curl_close($ch);
    }
}
