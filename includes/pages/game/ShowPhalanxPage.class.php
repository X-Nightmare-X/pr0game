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


class ShowPhalanxPage extends AbstractGamePage
{
    public static $requireModule = MODULE_PHALANX;

    public static function allowPhalanx($toGalaxy, $toSystem, $targetUser)
    {
        $PLANET =& Singleton()->PLANET;
        $resource =& Singleton()->resource;
        $USER =& Singleton()->USER;
        $checknoob = CheckNoobProtec($USER, $targetUser, $targetUser);

        if ($PLANET['galaxy'] != $toGalaxy || $PLANET[$resource[42]] == 0 || !isModuleAvailable(MODULE_PHALANX) || $PLANET[$resource[903]] < PHALANX_DEUTERIUM || $USER['urlaubs_modus'] == 1 || $checknoob['NoobPlayer'] || $checknoob['StrongPlayer']) {
            return false;
        }

        $PhRange	= self::GetPhalanxRange($PLANET[$resource[42]]);
        // if (Config::get()->galaxy_type == 2) {
        // TODO or else
        // } else if
        if (Config::get()->galaxy_type == 1) {
            $max = Config::get()->max_system;
            $thisSystem = $PLANET['system'];

            if ($thisSystem + $PhRange > $max) {
                $systemMax = $thisSystem + $PhRange - $max;
            } else {
                $systemMax = $thisSystem + $PhRange;
            }

            if ($thisSystem - $PhRange < 1) {
                $systemMin = $max + ($thisSystem - $PhRange);
            } else {
                $systemMin = $thisSystem - $PhRange;
            }

            if ($systemMin <= $systemMax) {
                return $toSystem >= $systemMin && $toSystem <= $systemMax;
            } else {
                return ($toSystem >= $systemMin && $toSystem <= $max) || ($toSystem >= 1 && $toSystem <= $systemMax);
            }
        } else {
            $systemMin  = max(1, $PLANET['system'] - $PhRange);
            $systemMax  = $PLANET['system'] + $PhRange;

            return $toSystem >= $systemMin && $toSystem <= $systemMax;
        }
    }

    public static function GetPhalanxRange($PhalanxLevel)
    {
        return ($PhalanxLevel == 1) ? 0 : pow($PhalanxLevel, 2) - 1;
    }

    public function __construct()
    {
    }

    public function show()
    {
        $PLANET =& Singleton()->PLANET;
        $LNG =& Singleton()->LNG;
        $resource =& Singleton()->resource;
        $USER =& Singleton()->USER;
        $this->initTemplate();
        $this->setWindow('popup');
        $this->tplObj->loadscript('phalanx.js');

        $Galaxy 			= HTTP::_GP('galaxy', 0);
        $System 			= HTTP::_GP('system', 0);
        $Planet 			= HTTP::_GP('planet', 0);

        if ($PLANET[$resource[903]] < PHALANX_DEUTERIUM) {
            $this->printMessage($LNG['px_no_deuterium']);
        }

        $db = Database::get();
        $sql = "UPDATE %%PLANETS%% SET deuterium = deuterium - :phalanxDeuterium WHERE id = :planetID;";
        $db->update($sql, [
            ':phalanxDeuterium'	=> PHALANX_DEUTERIUM,
            ':planetID'			=> $PLANET['id']
        ]);

        $sql = "SELECT id, name, id_owner FROM %%PLANETS%% WHERE universe = :universe
		AND galaxy = :galaxy AND system = :system AND planet = :planet AND :type;";

        $TargetInfo = $db->selectSingle($sql, [
            ':universe'	=> Universe::current(),
            ':galaxy'	=> $Galaxy,
            ':system'	=> $System,
            ':planet'	=> $Planet,
            ':type'		=> 1
        ]);

        $sql = "SELECT u.id, s.total_points, u.onlinetime, u.banaday FROM %%USERS%% u ".
        "LEFT JOIN %%STATPOINTS%% s ON s.id_owner = u.id AND s.stat_type = :statTypeUser ".
        "WHERE u.universe = :universe AND id = :id_owner;";

        $TargetUser = $db->selectSingle($sql, [
            ':statTypeUser'     => 1,
            ':universe'			=> Universe::current(),
            ':id_owner'			=> $TargetInfo['id_owner'],
        ]);

        if (!$this->allowPhalanx($Galaxy, $System, $TargetUser)) {
            $this->printMessage($LNG['px_out_of_range']);
        }
        if (empty($TargetInfo)) {
            $this->printMessage($LNG['px_out_of_range']);
        }

        require 'includes/classes/class.FlyingFleetsTable.php';

        $fleetTableObj = new FlyingFleetsTable();
        $fleetTableObj->setPhalanxMode();
        $fleetTableObj->setUser($TargetInfo['id_owner']);
        $fleetTableObj->setPlanet($TargetInfo['id']);
        $fleetTable	=  $fleetTableObj->renderTable();
        $this->assign([
            'galaxy'  		=> $Galaxy,
            'system'  		=> $System,
            'planet'   		=> $Planet,
            'name'    		=> $TargetInfo['name'],
            'fleetTable'	=> $fleetTable,
            'colors'		=> $USER['colors'],
        ]);

        $this->display('page.phalanx.default.tpl');
    }
}
