<?php

/**
 *  2Moons
 *   by Jan-Otto Kröpke 2009-2016
 * For the full copyright and license information, please view the LICENSE
 * @package 2Moons
 * @author Jan-Otto Kröpke <slaver7@gmail.com>
 * @copyright 2009 Lucky
 * @copyright 2016 Jan-Otto Kröpke <slaver7@gmail.com>
 * @licence MIT
 * @version 1.8.0
 * @link https://github.com/jkroepke/2Moons
 */

class statbuilder
{
    const STAT_TYPE_USER = 1;
    const STAT_TYPE_ALLIANCE = 2;

    private float $startTime;
    private array $memory;
    private int $time;
    private array $universes;

    function __construct()
    {
        $this->startTime = microtime(true);
        $this->memory = [round(memory_get_usage() / 1024, 1), round(memory_get_usage(1) / 1024, 1)];
        $this->time = TIMESTAMP;

        $this->getUniverses();
    }

    /**
     * @throws Exception
     */
    final public function generateStats(): array
    {
        global $resource;

        $allianceScores = [];
        $userStats = $this->getUserInfosFromDB();
        $userScores = $this->setUserScoresByPlanets($userStats);
        $universeData = [];

        foreach ($userStats['Users'] as $userData) {
            $statPoints = new StatPoints(
                $userData['id'],
                $userData['ally_id'],
                self::STAT_TYPE_USER,
                $userData['universe']
            );

            if (!isset($universeData[$userData['universe']])) {
                $universeData[$userData['universe']] = 0;
            }

            $universeData[$userData['universe']]++;

            if ((in_array(Config::get()->stat, [1, 2]) && $userData['authlevel'] >= Config::get()->stat_level) || !empty($userData['bana'])) {
                $this->updateUserStats($statPoints);
                continue;
            }

            if (isset($userStats['Fleets'][$userData['id']])) {
                foreach ($userStats['Fleets'][$userData['id']] as $ID => $Amount)
                    $userData[$resource[$ID]] += $Amount;
            }

            $techPoints = $this->getResearchScoreByUser($userData);
            $fleetPoints = $this->getFleetScoreByUser($userData);
            $defensePoints = $this->getDefenseScoreByUser($userData);

            $statPoints->build_count = (int)$userScores[$userData['id']]['build']['count'];
            $statPoints->build_points = (int)$userScores[$userData['id']]['build']['points'];
            $statPoints->fleet_count = (int)$fleetPoints['count'];
            $statPoints->fleet_points = (int)$fleetPoints['points'];
            $statPoints->defs_count = (int)$defensePoints['count'];
            $statPoints->defs_points = (int)$defensePoints['points'];
            $statPoints->tech_count = (int)$techPoints['count'];
            $statPoints->tech_points = (int)$techPoints['points'];

            $statPoints->build_old_rank = (int)$userData['old_build_rank'];
            $statPoints->fleet_old_rank = (int)$userData['old_fleet_rank'];
            $statPoints->defs_old_rank = (int)$userData['old_defs_rank'];
            $statPoints->tech_old_rank = (int)$userData['old_tech_rank'];
            $statPoints->total_old_rank = (int)$userData['old_total_rank'];

            $statPoints->setTotalStats();
            $this->updateUserStats($statPoints);

            if ($userData['ally_id'] != 0) {
                if (!isset($allianceScores[$userData['ally_id']])) {
                    $allianceScores[$userData['ally_id']] = new StatPoints(
                        $userData['ally_id'],
                        0,
                        self::STAT_TYPE_ALLIANCE,
                        $userData['universe']
                    );
                }

                if ($userData['onlinetime'] >= TIMESTAMP - INACTIVE) {
                    $allianceScores[$userData['ally_id']]->build_count += (int)$userScores[$userData['id']]['build']['count'];
                    $allianceScores[$userData['ally_id']]->build_points += (int)$userScores[$userData['id']]['build']['points'] ?? 0;
                    $allianceScores[$userData['ally_id']]->fleet_count += (int)$fleetPoints['count'];
                    $allianceScores[$userData['ally_id']]->fleet_points += (int)$fleetPoints['points'];
                    $allianceScores[$userData['ally_id']]->defs_count += (int)$defensePoints['count'];
                    $allianceScores[$userData['ally_id']]->defs_points += (int)$defensePoints['points'];
                    $allianceScores[$userData['ally_id']]->tech_count += (int)$techPoints['count'];
                    $allianceScores[$userData['ally_id']]->tech_points += (int)$techPoints['points'];
                    $allianceScores[$userData['ally_id']]->setTotalStats();
                }
            }
        }

        if (count($allianceScores) != 0) {
            foreach ($allianceScores as $allianceData) {
                $this->updateUserStats($allianceData);
            }
        }

        $this->SetNewRanks();
        $this->checkUniverseAccounts($universeData);

        $this->generateJson();

        return $this->getStatsArray();
    }

    /**
     * @throws Exception
     */
    private function getUniverses(): void
    {
        $this->universes = [];

        $uniResult = Database::get()->select("SELECT uni FROM %%CONFIG%% ORDER BY uni ASC;");
        foreach ($uniResult as $uni) {
            $this->universes[] = $uni['uni'];
        }
    }

    private function getStatsArray(): array
    {
        return [
            'stats_time' => $this->time,
            'totaltime' => round(microtime(true) - $this->startTime, 7),
            'memory_peak' => [round(memory_get_peak_usage() / 1024, 1), round(memory_get_peak_usage(1) / 1024, 1)],
            'initial_memory' => $this->memory,
            'end_memory' => [round(memory_get_usage() / 1024, 1), round(memory_get_usage(1) / 1024, 1)],
            'sql_count' => Database::get()->getQueryCounter(),
        ];
    }

    private function checkUniverseAccounts($UniData)
    {
        $UniData = $UniData + array_combine($this->universes, array_fill(1, count($this->universes), 0));
        foreach ($UniData as $Uni => $Amount) {
            $config = Config::get($Uni);
            $config->users_amount = $Amount;
            $config->save();
        }
    }

    private function getUserInfosFromDB(): array
    {
        global $resource, $reslist;
        $select_defenses = '';
        $select_buildings = '';
        $selected_tech = '';
        $select_fleets = '';

        foreach ($reslist['build'] as $Building) {
            $select_buildings .= " p." . $resource[$Building] . ",";
        }

        foreach ($reslist['tech'] as $Techno) {
            $selected_tech .= " u." . $resource[$Techno] . ",";
        }

        foreach ($reslist['fleet'] as $Fleet) {
            $select_fleets .= " SUM(p." . $resource[$Fleet] . ") as " . $resource[$Fleet] . ",";
        }

        foreach ($reslist['defense'] as $Defense) {
            $select_defenses .= " SUM(p." . $resource[$Defense] . ") as " . $resource[$Defense] . ",";
        }

        foreach ($reslist['missile'] as $Defense) {
            $select_defenses .= " SUM(p." . $resource[$Defense] . ") as " . $resource[$Defense] . ",";
        }

        $database = Database::get();
        $FlyingFleets = [];
        $SQLFleets = $database->select('SELECT fleet_array, fleet_owner FROM %%FLEETS%%;');
        foreach ($SQLFleets as $CurFleets) {
            $FleetRec = explode(";", $CurFleets['fleet_array']);

            if (!is_array($FleetRec)) continue;

            foreach ($FleetRec as $Group) {
                if (empty($Group)) continue;

                $Ship = explode(",", $Group);
                if (!isset($FlyingFleets[$CurFleets['fleet_owner']][$Ship[0]]))
                    $FlyingFleets[$CurFleets['fleet_owner']][$Ship[0]] = $Ship[1];
                else
                    $FlyingFleets[$CurFleets['fleet_owner']][$Ship[0]] += $Ship[1];
            }
        }

        $Return['Fleets'] = $FlyingFleets;
        $Return['Planets'] = $database->select('SELECT SQL_BIG_RESULT DISTINCT ' . $select_buildings . ' p.id, p.universe, p.id_owner, u.authlevel, u.bana, u.username FROM %%PLANETS%% as p LEFT JOIN %%USERS%% as u ON u.id = p.id_owner;');
        $Return['Users'] = $database->select('SELECT SQL_BIG_RESULT DISTINCT ' . $selected_tech . $select_fleets . $select_defenses . ' u.id, u.ally_id, u.authlevel, u.bana, u.universe, u.username, u.onlinetime, s.tech_rank AS old_tech_rank, s.build_rank AS old_build_rank, s.defs_rank AS old_defs_rank, s.fleet_rank AS old_fleet_rank, s.total_rank AS old_total_rank FROM %%USERS%% as u LEFT JOIN %%STATPOINTS%% as s ON s.stat_type = 1 AND s.id_owner = u.id LEFT JOIN %%PLANETS%% as p ON u.id = p.id_owner GROUP BY s.id_owner, u.id, u.authlevel;');
        $Return['Alliance'] = $database->select('SELECT SQL_BIG_RESULT DISTINCT a.id, a.ally_universe, s.tech_rank AS old_tech_rank, s.build_rank AS old_build_rank, s.defs_rank AS old_defs_rank, s.fleet_rank AS old_fleet_rank, s.total_rank AS old_total_rank FROM %%ALLIANCE%% as a LEFT JOIN %%STATPOINTS%% as s ON s.stat_type = 2 AND s.id_owner = a.id GROUP BY a.id;');

        return $Return;
    }

    private function getResearchScoreByUser($USER): array
    {
        global $resource, $reslist, $pricelist;
        $TechCounts = 0;
        $TechPoints = 0;

        foreach ($reslist['tech'] as $Techno) {
            if ($USER[$resource[$Techno]] == 0) continue;

            // PointsPerCot == Config::get()->stat_settings
            for ($i = 1; $i <= $USER[$resource[$Techno]]; $i++) {
                $TechPoints += ($pricelist[$Techno]['cost'][901] + $pricelist[$Techno]['cost'][902] + $pricelist[$Techno]['cost'][903]) * pow($pricelist[$Techno]['factor'], $i - 1);
            }
            $TechCounts += $USER[$resource[$Techno]];
        }

        return ['count' => $TechCounts, 'points' => ($TechPoints / Config::get()->stat_settings)];
    }

    private function getBuildingScoreByPlanet($PLANET): array
    {
        global $resource, $reslist, $pricelist;
        $BuildCounts = 0;
        $BuildPoints = 0;

        foreach ($reslist['build'] as $Build) {
            if ($PLANET[$resource[$Build]] == 0) continue;

            // PointsPerCot == Config::get()->stat_settings
            for ($i = 1; $i <= $PLANET[$resource[$Build]]; $i++) {
                $BuildPoints += ($pricelist[$Build]['cost'][901] + $pricelist[$Build]['cost'][902] + $pricelist[$Build]['cost'][903]) * pow($pricelist[$Build]['factor'], $i - 1);
            }
            $BuildCounts += $PLANET[$resource[$Build]];
        }
        return ['count' => $BuildCounts, 'points' => ($BuildPoints / Config::get()->stat_settings)];
    }

    private function getDefenseScoreByUser($USER): array
    {
        global $resource, $reslist, $pricelist;
        $DefenseCounts = 0;
        $DefensePoints = 0;

        foreach (array_merge($reslist['defense'], $reslist['missile']) as $Defense) {
            if ($USER[$resource[$Defense]] == 0) continue;

            $Units = $pricelist[$Defense]['cost'][901] + $pricelist[$Defense]['cost'][902] + $pricelist[$Defense]['cost'][903];
            $DefensePoints += $Units * $USER[$resource[$Defense]];
            $DefenseCounts += $USER[$resource[$Defense]];
        }

        return ['count' => $DefenseCounts, 'points' => ($DefensePoints / Config::get()->stat_settings)];
    }

    private function getFleetScoreByUser($USER): array
    {
        global $resource, $reslist, $pricelist;
        $FleetCounts = 0;
        $FleetPoints = 0;

        foreach ($reslist['fleet'] as $Fleet) {
            if ($USER[$resource[$Fleet]] == 0) continue;

            $Units = $pricelist[$Fleet]['cost'][901] + $pricelist[$Fleet]['cost'][902] + $pricelist[$Fleet]['cost'][903];
            $FleetPoints += $Units * $USER[$resource[$Fleet]];
            $FleetCounts += $USER[$resource[$Fleet]];
        }

        return ['count' => $FleetCounts, 'points' => ($FleetPoints / Config::get()->stat_settings)];
    }

    private function SetNewRanks()
    {
        foreach ($this->universes as $uni) {
            foreach (['tech', 'build', 'defs', 'fleet', 'total'] as $type) {
                Database::get()->nativeQuery('SELECT @i := 0;');

                $sql = 'UPDATE %%STATPOINTS%% SET ' . $type . '_rank = (SELECT @i := @i + 1)
				WHERE universe = :uni AND stat_type = :type
				ORDER BY ' . $type . '_points DESC, id_owner ASC;';

                Database::get()->update($sql, [
                    ':uni' => $uni,
                    ':type' => 1,
                ]);

                Database::get()->nativeQuery('SELECT @i := 0;');

                Database::get()->update($sql, [
                    ':uni' => $uni,
                    ':type' => 2,
                ]);
            }
        }
    }

    private function setUserScoresByPlanets(array $userStats): array
    {
        $userScores = [];

        foreach ($userStats['Planets'] as $PlanetData) {
            if ((in_array(Config::get()->stat, [1, 2]) && $PlanetData['authlevel'] >= Config::get()->stat_level) || !empty($PlanetData['bana'])) {
                continue;
            }

            if (!isset($userScores[$PlanetData['id_owner']])) {
                $userScores[$PlanetData['id_owner']]['build']['count'] = $userScores[$PlanetData['id_owner']]['build']['points'] = 0;
            }

            $BuildPoints = $this->getBuildingScoreByPlanet($PlanetData);
            $userScores[$PlanetData['id_owner']]['build']['count'] += $BuildPoints['count'];
            $userScores[$PlanetData['id_owner']]['build']['points'] += $BuildPoints['points'];
        }

        return $userScores;
    }

    private function updateUserStats(StatPoints $data)
    {
        $query = "INSERT IGNORE INTO `%%STATPOINTS%%` SET
                    `id_owner` = :id_owner,
                    `id_ally` = :id_ally,
                    `stat_type` = :stat_type,
                    `universe` = :universe,
                    `tech_rank` = :tech_rank,
                    `tech_old_rank` = :tech_old_rank,
                    `tech_points` = :tech_points,
                    `tech_count` = :tech_count,
                    `build_rank` = :build_rank,
                    `build_old_rank` = :build_old_rank,
                    `build_points` = :build_points,
                    `build_count` = :build_count,
                    `defs_rank` = :defs_rank,
                    `defs_old_rank` = :defs_old_rank,
                    `defs_points` = :defs_points,
                    `defs_count` = :defs_count,
                    `fleet_rank` = :fleet_rank,
                    `fleet_old_rank` = :fleet_old_rank,
                    `fleet_points` = :fleet_points,
                    `fleet_count` = :fleet_count,
                    `total_rank` = :total_rank,
                    `total_old_rank` = :total_old_rank,
                    `total_points` = :total_points,
                    `total_count` = :total_count
                ON DUPLICATE KEY UPDATE
                    `id_ally` = :id_ally,
                    `stat_type` = :stat_type,
                    `universe` = :universe,
                    `tech_rank` = :tech_rank,
                    `tech_old_rank` = :tech_old_rank,
                    `tech_points` = :tech_points,
                    `tech_count` = :tech_count,
                    `build_rank` = :build_rank,
                    `build_old_rank` = :build_old_rank,
                    `build_points` = :build_points,
                    `build_count` = :build_count,
                    `defs_rank` = :defs_rank,
                    `defs_old_rank` = :defs_old_rank,
                    `defs_points` = :defs_points,
                    `defs_count` = :defs_count,
                    `fleet_rank` = :fleet_rank,
                    `fleet_old_rank` = :fleet_old_rank,
                    `fleet_points` = :fleet_points,
                    `fleet_count` = :fleet_count,
                    `total_rank` = :total_rank,
                    `total_old_rank` = :total_old_rank,
                    `total_points` = :total_points,
                    `total_count` = :total_count";

        Database::get()->insert($query, [
            ':id_owner' => $data->id_owner,
            ':id_ally' => $data->id_ally,
            ':stat_type' => $data->stat_type,
            ':universe' => $data->universe,
            ':tech_rank' => $data->tech_rank,
            ':tech_old_rank' => $data->tech_old_rank,
            ':tech_points' => $data->tech_points,
            ':tech_count' => $data->tech_count,
            ':build_rank' => $data->build_rank,
            ':build_old_rank' => $data->build_old_rank,
            ':build_points' => $data->build_points,
            ':build_count' => $data->build_count,
            ':defs_rank' => $data->defs_rank,
            ':defs_old_rank' => $data->defs_old_rank,
            ':defs_points' => $data->defs_points,
            ':defs_count' => $data->defs_count,
            ':fleet_rank' => $data->fleet_rank,
            ':fleet_old_rank' => $data->fleet_old_rank,
            ':fleet_points' => $data->fleet_points,
            ':fleet_count' => $data->fleet_count,
            ':total_rank' => $data->total_rank,
            ':total_old_rank' => $data->total_old_rank,
            ':total_points' => $data->total_points,
            ':total_count' => $data->total_count,
        ]);
    }

    private function generateJson()
    {
        $columns = [
            '%%STATPOINTS%%.id_owner AS playerId',
            '%%USERS%%.username AS playerName',
            '%%USERS%%.universe AS playerUniverse',
            '%%USERS%%.galaxy AS playerGalaxy',
            '%%STATPOINTS%%.id_ally AS allianceId',
            '(SELECT ally_name FROM %%ALLIANCE%% WHERE %%ALLIANCE%%.id = %%STATPOINTS%%.id_ally) AS allianceName',
            '%%STATPOINTS%%.total_rank AS rank',
            '%%STATPOINTS%%.total_points AS score',
            '%%STATPOINTS%%.tech_rank AS researchRank',
            '%%STATPOINTS%%.tech_points AS researchScore',
            '%%STATPOINTS%%.build_rank AS buildingRank',
            '%%STATPOINTS%%.build_points AS buildingScore',
            '%%STATPOINTS%%.defs_rank AS defensiveRank',
            '%%STATPOINTS%%.defs_points AS defensiveScore',
            '%%STATPOINTS%%.fleet_rank AS fleetRank',
            '%%STATPOINTS%%.fleet_points AS fleetScore',
            '%%USERS%%.wons AS battlesWon',
            '%%USERS%%.loos AS battlesLost',
            '%%USERS%%.draws AS battlesDraw',
            '%%USERS%%.kbmetal AS debrisMetal',
            '%%USERS%%.kbcrystal AS debrisCrystal',
            '%%USERS%%.desunits AS unitsDestroyed',
            '%%USERS%%.lostunits AS unitsLost',
        ];

        $database = Database::get();
        $sql = trim("SELECT SQL_BIG_RESULT
            " . implode(',', $columns) . "
            FROM %%STATPOINTS%%
            INNER JOIN %%USERS%%
            ON %%USERS%%.`id` = %%STATPOINTS%%.`id_owner`
            WHERE %%STATPOINTS%%.`stat_type` = 1 AND %%USERS%%.universe = :universe
            ORDER BY %%STATPOINTS%%.`total_rank`");

        foreach ($this->universes as $uni) {
            $scores = $database->select($sql, [
                ':universe' => $uni,
            ]);

            $name = Config::get($uni)->uni_name;
            $name = str_replace(' ', '_', $name);
    
            $fh = fopen(ROOT_PATH . 'stats/stats_' . $name . '_' . date('Y-m-d_H') . '.json', 'w+');
            fwrite($fh, json_encode($scores, JSON_PRETTY_PRINT));
            fclose($fh);
    
            $fh = fopen(ROOT_PATH . 'stats_' . $name . '.json', 'w+');
            fwrite($fh, json_encode($scores, JSON_PRETTY_PRINT));
            fclose($fh);
        }
    }
    
    private function getHighestUser($type) 
    {
        GLOBAL $reslist, $resource;
        $db = Database::get();
        $result = array();
        foreach($reslist['tech'] as $Techno) 
		{
			$techName = $resource[$Techno];
			
            $sql = "SELECT id, " . $techName . " as level FROM %%USERS%% where " . $techName . " = (SELECT max(" . $techName . ") from %%USERS%%);";
            $data = $db->select($sql);
            
            if($data[0]['level'] > 0) {
                $dataFinal = array();
                foreach($data as $row) {
                    array_push($row, $Techno);
                    array_push($dataFinal, $row);
                }
                array_push($result, $dataFinal);
            }
        }
        return $result;
    }

    private function getHighestPlanet($type) 
    {
        GLOBAL $reslist, $resource;
        $db = Database::get();
        $result = array();
        foreach($reslist[$type] as $kind) {
            $kindName = $resource[$kind];
            $sql = "SELECT id_owner as id, " . $kindName . " as level FROM %%PLANETS%% where " . $kindName . " = (select max(" . $kindName . ") from %%PLANETS%%);";
            $data = $db->select($sql);
            if($data[0]['level'] > 0) {
                $dataFinal = array();
                foreach($data as $row) {
                    array_push($row, $kind);
                    array_push($dataFinal, $row);
                }
                array_push($result, $dataFinal);
            }
        }
        return $result;
    }

    private function insertQueries($records) {
        $db = Database::get();
        $queries = array();
        foreach($records as $record) {
            foreach($record as $row) {
                $sql = "INSERT INTO %%RECORDS%% (userID, elementID, level) VALUES (:userID, :elementID, :level);";
                $db->insert($sql, [
                    ':userID' => $row['id'],
                    ':elementID' => $row['0'],
                    ':level' => $row['level'],
                ]);
            }
            
        }
    }

    public function buildRecords() {
        $db = Database::get();
        $tech = $this->getHighestUser('tech');
        $buildings = $this->getHighestPlanet('build');
        //$defenses = $this->getHighestPlanet('defense');
        //$fleet = $this->getHighestPlanet('fleet');
        //$missiles = $this->getHighestPlanet('missile');
        $sql = "DELETE FROM %%RECORDS%%;";
        $db->delete($sql);
        $this->insertQueries($tech);
        $this->insertQueries($buildings);

	}
}

/* 
	Enable to debug 
*/
// define('MODE', 'INSTALL');
// define('ROOT_PATH', 'G:/xampp/htdocs/pr0game/');
// set_include_path(
//     ROOT_PATH . 'includes/libs/BBCodeParser2/' . PATH_SEPARATOR . ROOT_PATH . PATH_SEPARATOR . get_include_path()
// );

// require 'includes/pages/game/AbstractGamePage.class.php';
// require 'includes/pages/game/ShowErrorPage.class.php';
// require 'includes/common.php';

// // require 'includes/classes/Database.class.php';
// require 'includes/vars.php';
// require 'includes/models/StatPoints.php';
// $class = new statbuilder();
// try {
//     $class->generateStats();
// } catch (\Throwable $th) {
//     echo $th;
// }