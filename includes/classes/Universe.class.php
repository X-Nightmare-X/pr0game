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

class Universe
{
    private static $currentUniverse = null;
    private static $emulatedUniverse = null;
    private static $availableUniverses = [];

    /**
     * Return the current universe id.
     *
     * @return int
     */

    public static function current()
    {
        if (is_null(self::$currentUniverse)) {
            self::$currentUniverse = self::defineCurrentUniverse();
        }

        return self::$currentUniverse;
    }

    public static function add($universe)
    {
        self::$availableUniverses[]	= $universe;
    }

    public static function getEmulated()
    {
        if (is_null(self::$emulatedUniverse)) {
            $session	= Session::load();
            if (isset($session->emulatedUniverse)) {
                self::setEmulated($session->emulatedUniverse);
            } else {
                self::setEmulated(self::current());
            }
        }

        return self::$emulatedUniverse;
    }

    public static function setEmulated($universeId)
    {
        if (!self::exists($universeId)) {
            throw new Exception('Unknown universe ID: '.$universeId);
        }

        $session	= Session::load();
        $session->emulatedUniverse	= $universeId;
        $session->save();

        self::$emulatedUniverse	= $universeId;

        return true;
    }

    /**
     * Find current universe id using cookies, get parameter or session keys.
     *
     * @return int
     */

    private static function defineCurrentUniverse()
    {
        $universe = null;
        if (MODE === 'INSTALL' || MODE === 'UPGRADE') {
            // Installer are always in the first universe.
            return ROOT_UNI;
        }

        if (count(self::availableUniverses()) != 1) {
            if (MODE == 'LOGIN') {
                if (isset($_REQUEST['uni'])) {
                    $universe = (int) $_REQUEST['uni'];
                }
            } elseif (MODE == 'ADMIN' && isset($_SESSION['admin_uni'])) {
                $universe = (int) $_SESSION['admin_uni'];
            }


            if (is_null($universe)) {
                if (UNIS_WILDCAST) {
                    $temp = explode('.', $_SERVER['HTTP_HOST']);
                    $temp = substr($temp[0], 3);
                    if (is_numeric($temp)) {
                        $universe = $temp;
                    } else {
                        $universe = ROOT_UNI;
                    }
                } else {
                    if (isset($_SERVER['REDIRECT_UNI'])) {
                        // Apache - faster then preg_match
                        $universe = $_SERVER["REDIRECT_UNI"];
                    } elseif (isset($_SERVER['REDIRECT_REDIRECT_UNI'])) {
                        // Patch for www.top-hoster.de - Hoster
                        $universe = $_SERVER["REDIRECT_REDIRECT_UNI"];
                    } elseif (preg_match('!/uni([0-9]+)/!', HTTP_PATH, $match)) {
                        if (isset($match[1])) {
                            $universe = $match[1];
                        }
                    } else {
                        $universe = ROOT_UNI;
                    }
                }

                if (!isset($universe) || !self::exists($universe)) {
                    HTTP::redirectToUniverse(ROOT_UNI);
                }
            }
        } else {
            if (HTTP_ROOT != HTTP_BASE) {
                HTTP::redirectTo(PROTOCOL.HTTP_HOST.HTTP_BASE.HTTP_FILE, true);
            }
            $universe = ROOT_UNI;
        }

        return $universe;
    }

    /**
     * Return an array of all universe ids
     *
     * @return array
     */

    public static function availableUniverses()
    {
        return self::$availableUniverses;
    }

    /**
     * Find current universe id using cookies, get parameter or session keys.
     *
     * @param int universe id
     *
     * @return int
     */

    public static function exists($universeId)
    {
        return in_array($universeId, self::availableUniverses());
    }
}
