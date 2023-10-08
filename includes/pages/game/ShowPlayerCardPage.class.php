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


class ShowPlayerCardPage extends AbstractGamePage
{
    public static $requireModule = MODULE_PLAYERCARD;

    protected $disableEcoSystem = true;

    public function __construct()
    {
        parent::__construct();
    }

    public function show()
    {
        $USER =& Singleton()->USER;
        $LNG =& Singleton()->LNG;
        $pricelist =& Singleton()->pricelist;
        $reslist =& Singleton()->reslist;
        $this->setWindow('popup');
        $this->initTemplate();

        $db = Database::get();

        $PlayerID 	= HTTP::_GP('id', 0);

        $advanced_stats = [];
        foreach ($reslist['fleet'] as $element) {
            $advanced_stats[] = 'ad.destroyed_' . $element;
            $advanced_stats[] = 'ad.lost_' . $element;
        }
        foreach ($reslist['defense'] as $element) {
            $advanced_stats[] = 'ad.destroyed_' . $element;
            $advanced_stats[] = 'ad.lost_' . $element;
        }

        $sql = "SELECT
				u.username, u.galaxy, u.system, u.planet, u.wons, u.loos, u.draws, u.kbmetal, u.kbcrystal, u.lostunits, u.desunits, u.ally_id,
				p.name,
				s.tech_rank, s.tech_points, s.build_rank, s.build_points, s.defs_rank, s.defs_points, s.fleet_rank, s.fleet_points, s.total_rank, s.total_points,
                a.ally_name, (s.tech_points / s.total_points * 100) as tech_percent, (s.build_points / s.total_points * 100) as build_percent,
				(s.defs_points / s.total_points * 100) as defs_percent, (s.fleet_points / s.total_points * 100) as fleet_percent, "
                . implode(', ', $advanced_stats) .
                " FROM %%USERS%% u
				INNER JOIN %%PLANETS%% p ON p.id = u.id_planet
				LEFT JOIN %%STATPOINTS%% s ON s.id_owner = u.id AND s.stat_type = 1
				LEFT JOIN %%ALLIANCE%% a ON a.id = u.ally_id
                LEFT JOIN %%ADVANCED_STATS%% ad ON ad.userId = u.id
				WHERE u.id = :playerID AND u.universe = :universe;";
        $query = $db->selectSingle($sql, [
            ':universe'	=> Universe::current(),
            ':playerID'	=> $PlayerID
        ]);

        if (!$query) {
            throw new Exception('the requested player does not exist');
        }

        $totalfights = $query['wons'] + $query['loos'] + $query['draws'];

        if ($totalfights == 0) {
            $siegprozent                = 0;
            $loosprozent                = 0;
            $drawsprozent               = 0;
        } else {
            $siegprozent                = 100 / $totalfights * $query['wons'];
            $loosprozent                = 100 / $totalfights * $query['loos'];
            $drawsprozent               = 100 / $totalfights * $query['draws'];
        }

        $config = Config::get();
        $fleetIntoDebris = $config->Fleet_Cdr;
        $defIntoDebris = $config->Defs_Cdr;

        $units_real_destroyed = 0;
        $units_real_lost = 0;
        $debris_real_metal = 0;
        $debris_real_crystal = 0;
        foreach ($reslist['fleet'] as $element) {
            $amount = $query['destroyed_' . $element];
            $units_real_destroyed += $pricelist[$element]['cost'][RESOURCE_METAL] * $amount;
            $debris_real_metal += ($pricelist[$element]['cost'][RESOURCE_METAL] * $amount * ($fleetIntoDebris / 100));
            $units_real_destroyed += $pricelist[$element]['cost'][RESOURCE_CRYSTAL] * $amount;
            $debris_real_crystal += ($pricelist[$element]['cost'][RESOURCE_CRYSTAL] * $amount * ($fleetIntoDebris / 100));

            $amount = $query['lost_' . $element];
            $units_real_lost += $pricelist[$element]['cost'][RESOURCE_METAL] * $amount;
            $debris_real_metal += ($pricelist[$element]['cost'][RESOURCE_METAL] * $amount * ($fleetIntoDebris / 100));
            $units_real_lost += $pricelist[$element]['cost'][RESOURCE_CRYSTAL] * $amount;
            $debris_real_crystal += ($pricelist[$element]['cost'][RESOURCE_CRYSTAL] * $amount * ($fleetIntoDebris / 100));
        }
        foreach ($reslist['defense'] as $element) {
            $amount = $query['destroyed_' . $element];
            $units_real_destroyed += $pricelist[$element]['cost'][RESOURCE_METAL] * $amount;
            $debris_real_metal += ($pricelist[$element]['cost'][RESOURCE_METAL] * $amount * ($defIntoDebris / 100));
            $units_real_destroyed += $pricelist[$element]['cost'][RESOURCE_CRYSTAL] * $amount;
            $debris_real_crystal += ($pricelist[$element]['cost'][RESOURCE_CRYSTAL] * $amount * ($defIntoDebris / 100));

            $amount = $query['lost_' . $element];
            $units_real_lost += $pricelist[$element]['cost'][RESOURCE_METAL] * $amount;
            $debris_real_metal += ($pricelist[$element]['cost'][RESOURCE_METAL] * $amount * ($defIntoDebris / 100));
            $units_real_lost += $pricelist[$element]['cost'][RESOURCE_CRYSTAL] * $amount;
            $debris_real_crystal += ($pricelist[$element]['cost'][RESOURCE_CRYSTAL] * $amount * ($defIntoDebris / 100));
        }

        $this->assign([
            'id'			=> $PlayerID,
            'yourid'		=> $USER['id'],
            'name'			=> $query['username'],
            'homeplanet'	=> $query['name'],
            'galaxy'		=> $query['galaxy'],
            'system'		=> $query['system'],
            'planet'		=> $query['planet'],
            'allyid'		=> $query['ally_id'],
            'tech_rank'     => pretty_number($query['tech_rank']),
            'tech_points'   => pretty_number($query['tech_points']),
            'build_rank'    => pretty_number($query['build_rank']),
            'build_points'  => pretty_number($query['build_points']),
            'defs_rank'     => pretty_number($query['defs_rank']),
            'defs_points'   => pretty_number($query['defs_points']),
            'fleet_rank'    => pretty_number($query['fleet_rank']),
            'fleet_points'  => pretty_number($query['fleet_points']),
            'total_rank'    => pretty_number($query['total_rank']),
            'total_points'  => pretty_number($query['total_points']),
            'allyname'		=> $query['ally_name'],
            'playerdestory' => sprintf($LNG['pl_destroy'], $query['username']),
            'realdestory'   => sprintf($LNG['pl_destroy_real'], $query['username']),
            'wons'          => pretty_number($query['wons']),
            'loos'          => pretty_number($query['loos']),
            'draws'         => pretty_number($query['draws']),
            'kbmetal'       => pretty_number($query['kbmetal']),
            'kbcrystal'     => pretty_number($query['kbcrystal']),
            'lostunits'     => pretty_number($query['lostunits']),
            'desunits'      => pretty_number($query['desunits']),
            'realdesunits'  => pretty_number($units_real_destroyed),
            'reallostunits' => pretty_number($units_real_lost),
            'realmetal'     => pretty_number($debris_real_metal),
            'realcrystal'   => pretty_number($debris_real_crystal),
            'totalfights'   => pretty_number($totalfights),
            'siegprozent'   => round($siegprozent, 2),
            'loosprozent'   => round($loosprozent, 2),
            'drawsprozent'  => round($drawsprozent, 2),
            'tech_percent'  => $query['tech_percent'] == 0 ? '0,00' : round($query['tech_percent'], 2),
            'build_percent' => $query['build_percent'] == 0 ? '0,00' : round($query['build_percent'], 2),
            'defs_percent'  => $query['defs_percent'] == 0 ? '0,00' : round($query['defs_percent'], 2),
            'fleet_percent' => $query['fleet_percent'] == 0 ? '0,00' : round($query['fleet_percent'], 2),
            'achievements'	=> $this->get_achievements($PlayerID),
        ]);

        $this->display('page.playerCard.default.tpl');
    }
    function get_achievements($userid)
	{
		$db = Database::get();
		$sql = "SELECT a.id, a.name, a.image FROM %%ACHIEVEMENTS%% a 
				INNER JOIN %%USERS_TO_ACHIEVEMENTS%% ua ON ua.achievementID = a.id
				WHERE ua.userID = :userID;";
		$achievementResult = $db->select($sql, array(
			':userID'	=> $userid
		));
		return $achievementResult;
	}
}
