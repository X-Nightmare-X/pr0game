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

    // protected $disableEcoSystem = true;

    public function __construct()
    {
        parent::__construct();
    }
    public function spyTechAllowed($necessaryLevel, $PlayerID, $USER)
    {
        $allowed = true;
        if(isModuleAvailable(MODULE_SPYTECH_DEPENDENT_STATS) && $PlayerID != $USER['id'] && $USER['authlevel'] == 0 && $USER['spy_tech'] < $necessaryLevel) {
            $allowed = false;
        }
        return $allowed;
    }
    public function show()
    {
        $USER =& Singleton()->USER;
        $LNG =& Singleton()->LNG;
        $this->setWindow('popup');
        $this->initTemplate();

        $PlayerID 	= HTTP::_GP('id', 0);

        $stats = $this->get_stats($PlayerID);
        $showPlayer = true;
        $showTotal = true;
        $showBuild = $this->spyTechAllowed(2, $PlayerID, $USER);
        $showTech = $this->spyTechAllowed(4, $PlayerID, $USER);
        $showDef = $this->spyTechAllowed(6, $PlayerID, $USER);
        $showFleet = $this->spyTechAllowed(6, $PlayerID, $USER);
        $showBattle = $this->spyTechAllowed(6, $PlayerID, $USER);

        $player_data = $this->get_player_data($stats, $showPlayer);
        $total_data = $this->get_total_data($stats, $showTotal);
        $build_data = $this->get_build_data($stats, $showBuild);
        $tech_data = $this->get_tech_data($stats, $showTech);
        $def_data = $this->get_def_data($stats, $showDef);
        $fleet_data = $this->get_fleet_data($stats, $showFleet);
        $battle_stats = $this->get_battle_stats($stats, $showBattle);
        $units = $this->get_units($stats, $showBattle);
        $real_units = $this->get_real_units($stats, $showBattle);
        $blocked = PlayerUtil::get_block_status($PlayerID, $USER['id']);
        $authlevel = PlayerUtil::get_authlevel($PlayerID);


        $this->assign([
            'id'			=> $PlayerID,
            'yourid'		=> $USER['id'],
            'name'			=> $player_data['username'],
            'homeplanet'	=> $player_data['planetname'],
            'galaxy'		=> $player_data['galaxy'],
            'system'		=> $player_data['system'],
            'planet'		=> $player_data['planet'],
            'allyid'		=> $player_data['ally_id'],
            'tech_rank'     => pretty_number($tech_data['tech_rank']),
            'tech_points'   => pretty_number($tech_data['tech_points']),
            'build_rank'    => pretty_number($build_data['build_rank']),
            'build_points'  => pretty_number($build_data['build_points']),
            'defs_rank'     => pretty_number($def_data['defs_rank']),
            'defs_points'   => pretty_number($def_data['defs_points']),
            'fleet_rank'    => pretty_number($fleet_data['fleet_rank']),
            'fleet_points'  => pretty_number($fleet_data['fleet_points']),
            'total_rank'    => pretty_number($total_data['total_rank']),
            'total_points'  => pretty_number($total_data['total_points']),
            'allyname'		=> $player_data['ally_name'],
            'playerdestory' => sprintf($LNG['pl_destroy'], $player_data['username']),
            'realdestory'   => sprintf($LNG['pl_destroy_real'], $player_data['username']),
            'wons'          => pretty_number($battle_stats['wons']),
            'loos'          => pretty_number($battle_stats['loos']),
            'draws'         => pretty_number($battle_stats['draws']),
            'kbmetal'       => pretty_number($units['kbmetal']),
            'kbcrystal'     => pretty_number($units['kbcrystal']),
            'lostunits'     => pretty_number($units['lostunits']),
            'desunits'      => pretty_number($units['desunits']),
            'realdesunits'  => pretty_number($real_units["destroyed"]),
            'reallostunits' => pretty_number($real_units["lost"]),
            'realmetal'     => pretty_number($real_units["debris_metal"]),
            'realcrystal'   => pretty_number($real_units["debris_crystal"]),
            'totalfights'   => pretty_number($battle_stats['totalfights']),
            'siegprozent'   => round($battle_stats["siegprozent"], 2),
            'loosprozent'   => round($battle_stats["loosprozent"], 2),
            'drawsprozent'  => round($battle_stats["drawsprozent"], 2),
            'tech_percent'  => $tech_data['tech_percent'] == 0 ? '0,00' : round($tech_data['tech_percent'], 2),
            'build_percent' => $build_data['build_percent'] == 0 ? '0,00' : round($build_data['build_percent'], 2),
            'defs_percent'  => $def_data['defs_percent'] == 0 ? '0,00' : round($def_data['defs_percent'], 2),
            'fleet_percent' => $fleet_data['fleet_percent'] == 0 ? '0,00' : round($fleet_data['fleet_percent'], 2),
            'achievements'	=> $this->get_achievements($PlayerID),
            'showBuild'     => $showBuild,
            'showTech'      => $showTech,
            'showDef'       => $showDef,
            'showFleet'     => $showFleet,
            'showBattle'    => $showBattle,
            'blockTrade'    => $blocked['block_trade'],
            'blockDm'       => $blocked['block_dm'],
            'targetRights'  => $authlevel,
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

    function get_real_units($stats, $show = true) 
    {
        $reslist =& Singleton()->reslist;
        $pricelist =& Singleton()->pricelist;
        $config = Config::get();
        $fleetIntoDebris = $config->fleet_debris_percentage;
        $defIntoDebris = $config->def_debris_percentage;

        $units_real_destroyed = 0;
        $units_real_lost = 0;
        $debris_real_metal = 0;
        $debris_real_crystal = 0;
        if ($show) {
            foreach ($reslist['fleet'] as $element) {
                $amount = $stats['destroyed_' . $element];
                $units_real_destroyed += $pricelist[$element]['cost'][RESOURCE_METAL] * $amount;
                $debris_real_metal += ($pricelist[$element]['cost'][RESOURCE_METAL] * $amount * ($fleetIntoDebris / 100));
                $units_real_destroyed += $pricelist[$element]['cost'][RESOURCE_CRYSTAL] * $amount;
                $debris_real_crystal += ($pricelist[$element]['cost'][RESOURCE_CRYSTAL] * $amount * ($fleetIntoDebris / 100));

                $amount = $stats['lost_' . $element];
                $units_real_lost += $pricelist[$element]['cost'][RESOURCE_METAL] * $amount;
                $debris_real_metal += ($pricelist[$element]['cost'][RESOURCE_METAL] * $amount * ($fleetIntoDebris / 100));
                $units_real_lost += $pricelist[$element]['cost'][RESOURCE_CRYSTAL] * $amount;
                $debris_real_crystal += ($pricelist[$element]['cost'][RESOURCE_CRYSTAL] * $amount * ($fleetIntoDebris / 100));
            }
            foreach ($reslist['defense'] as $element) {
                $amount = $stats['destroyed_' . $element];
                $units_real_destroyed += $pricelist[$element]['cost'][RESOURCE_METAL] * $amount;
                $debris_real_metal += ($pricelist[$element]['cost'][RESOURCE_METAL] * $amount * ($defIntoDebris / 100));
                $units_real_destroyed += $pricelist[$element]['cost'][RESOURCE_CRYSTAL] * $amount;
                $debris_real_crystal += ($pricelist[$element]['cost'][RESOURCE_CRYSTAL] * $amount * ($defIntoDebris / 100));

                $amount = $stats['lost_' . $element];
                $units_real_lost += $pricelist[$element]['cost'][RESOURCE_METAL] * $amount;
                $debris_real_metal += ($pricelist[$element]['cost'][RESOURCE_METAL] * $amount * ($defIntoDebris / 100));
                $units_real_lost += $pricelist[$element]['cost'][RESOURCE_CRYSTAL] * $amount;
                $debris_real_crystal += ($pricelist[$element]['cost'][RESOURCE_CRYSTAL] * $amount * ($defIntoDebris / 100));
            }
        }
        return array(
            "destroyed"         => $units_real_destroyed, 
            "lost"              => $units_real_lost, 
            "debris_metal"      => $debris_real_metal, 
            "debris_crystal"    => $debris_real_crystal);
    }

    function get_units($stats, $show = true)
    {
        if ($show) {
            $kbmetal = $stats['kbmetal'];
            $kbcrystal = $stats['kbcrystal'];
            $lostunits = $stats['lostunits'];
            $desunits = $stats['desunits'];
        } else {
            $kbmetal = 0;
            $kbcrystal = 0;
            $lostunits = 0;
            $desunits = 0;
        }
        return array(
            "kbmetal"       => $kbmetal,
            "kbcrystal"     => $kbcrystal,
            "lostunits"     => $lostunits,
            "desunits"      => $desunits
        );
    }

    function get_battle_stats($stats, $show = true) 
    {
        if ($show) {
            $wons = $stats['wons'];
            $loos = $stats['loos'];
            $draws = $stats['draws'];
        } else {
            $wons = 0;
            $loos = 0;
            $draws = 0;
        }
        $totalfights = $wons + $loos + $draws;

        if ($totalfights == 0) {
            $siegprozent                = 0;
            $loosprozent                = 0;
            $drawsprozent               = 0;
        } else {
            $siegprozent                = 100 / $totalfights * $stats['wons'];
            $loosprozent                = 100 / $totalfights * $stats['loos'];
            $drawsprozent               = 100 / $totalfights * $stats['draws'];
        }
        return array(
            "totalfights"   => $totalfights, 
            "siegprozent"   => $siegprozent, 
            "loosprozent"   => $loosprozent, 
            "drawsprozent"  => $drawsprozent,
            "wons"          => $wons,
            "loos"          => $loos,
            "draws"         => $draws
        );
    }

    function get_stats($playerId)
    {
        $db = Database::get();
        $reslist =& Singleton()->reslist;
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
            ':playerID'	=> $playerId
        ]);

        if (!$query) {
            throw new Exception('the requested player does not exist');
        }
        return $query;
    }

    function get_player_data($data)
    {
        return array(
            "username"      => $data['username'],
            "planetname"    => $data['name'],
            "galaxy"        => $data['galaxy'],
            "system"        => $data['system'],
            "planet"        => $data['planet'],
            "ally_id"       => $data['ally_id'],
            "ally_name"     => $data['ally_name']
        );
    }

    function get_tech_data($data, $show = true)
    {
        if ($show) {
            $tech_rank = $data['tech_rank'];
            $tech_points = $data['tech_points'];
            $tech_percent = $data['tech_percent'];
        } else {
            $tech_rank = 0;
            $tech_points = 0;
            $tech_percent = 0;
        }
        return array(
            "tech_rank"     => $tech_rank,
            "tech_points"   => $tech_points,
            "tech_percent"  => $tech_percent
        );
    }

    function get_build_data($data, $show = true)
    {
        if ($show) {
            $build_rank = $data['build_rank'];
            $build_points = $data['build_points'];
            $build_percent = $data['build_percent'];
        } else {
            $build_rank = 0;
            $build_points = 0;
            $build_percent = 0;
        }
        return array(
            "build_rank"    => $build_rank,
            "build_points"  => $build_points,
            "build_percent" => $build_percent
        );
    }

    function get_def_data($data, $show = true)
    {
        if ($show) {
            $defs_rank = $data['defs_rank'];
            $defs_points = $data['defs_points'];
            $defs_percent = $data['defs_percent'];
        } else {
            $defs_rank = 0;
            $defs_points = 0;
            $defs_percent = 0;
        }
        return array(
            "defs_rank"     => $defs_rank,
            "defs_points"   => $defs_points,
            "defs_percent"  => $defs_percent
        );
    }

    function get_fleet_data($data, $show = true)
    {
        if ($show) {
            $fleet_rank = $data['fleet_rank'];
            $fleet_points = $data['fleet_points'];
            $fleet_percent = $data['fleet_percent'];
        } else {
            $fleet_rank = 0;
            $fleet_points = 0;
            $fleet_percent = 0;
        }
        return array(
            "fleet_rank"    => $fleet_rank,
            "fleet_points"  => $fleet_points,
            "fleet_percent" => $fleet_percent
        );
    }

    function get_total_data($data)
    {
        return array(
            "total_rank"    => $data['total_rank'],
            "total_points"  => $data['total_points']
        );
    }

    /**
	 * Toggle player block status
	 * 
     * returns staus code 200 on success
	 */
    public function toggleBlock()
    {
        $USER =& Singleton()->USER;
        $db = Database::get();

        $this->initTemplate();
        $this->setWindow('ajax');
        
        $PlayerID 	    = HTTP::_GP('id', 0);
        $blockTrade     = HTTP::_GP('blockTrade', 0);
        $blockDm        = HTTP::_GP('blockDm', 0);

        $sql = "SELECT id FROM %%USERS_BLOCKLIST%% 
                WHERE blocking_user = :userID AND blocked_user = :targetID;";
        $blockid = $db->selectSingle($sql, [
            ':userID'       => $USER['id'],
            ':targetID'     => $PlayerID,
        ], 'id');
        if ($blockid && $blockTrade == 0 && $blockDm == 0) {
            $sql =  "DELETE FROM %%USERS_BLOCKLIST%% 
		        WHERE blocking_user = :userID AND blocked_user = :targetID;";
            $db->delete($sql, [
                ':userID'             => $USER['id'],
                ':targetID'           => $PlayerID,
            ]);
        } else if ($blockid) {
            $sql =  "UPDATE %%USERS_BLOCKLIST%% SET
		        block_dm		= :block_dm,
		        block_trade	    = :block_trade
		        WHERE blocking_user = :userID AND blocked_user = :targetID;";
            $db->update($sql, [
                ':block_dm'           => $blockDm,
                ':block_trade'        => $blockTrade,
                ':userID'             => $USER['id'],
                ':targetID'           => $PlayerID,
            ]);
        } else {
            $sql = "INSERT INTO %%USERS_BLOCKLIST%% SET
                blocking_user	= :userID,
                blocked_user	= :targetID,
                block_dm		= :block_dm,
                block_trade	    = :block_trade;";
            $db->insert($sql, [
                ':userID'       => $USER['id'],
                ':targetID'     => $PlayerID,
                ':block_dm'     => $blockDm,
                ':block_trade'  => $blockTrade,
            ]);
        }
        $this->sendData(200, 'Successfully changed block settings');
    }

    /**
	 * sends json data to client
	 */
    private function sendData($Code, $Message)
    {
        $this->sendJSON([
            'code' => $Code,
            'mess' => $Message,
        ]);
    }
}
