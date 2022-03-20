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

                $allianceScores[$userData['ally_id']]->build_count += $userScores[$userData['id']]['build']['count'] ?? 0;
                $allianceScores[$userData['ally_id']]->build_points += $userScores[$userData['id']]['build']['points'] ?? 0;
                $allianceScores[$userData['ally_id']]->fleet_count += $userScores[$userData['id']]['fleet']['count'] ?? 0;
                $allianceScores[$userData['ally_id']]->fleet_points += $userScores[$userData['id']]['fleet']['points'] ?? 0;
                $allianceScores[$userData['ally_id']]->defs_count += $userScores[$userData['id']]['defense']['count'] ?? 0;
                $allianceScores[$userData['ally_id']]->defs_points += $userScores[$userData['id']]['defense']['points'] ?? 0;
                $allianceScores[$userData['ally_id']]->tech_count += $userScores[$userData['id']]['techno']['count'] ?? 0;
                $allianceScores[$userData['ally_id']]->tech_points += $userScores[$userData['id']]['techno']['points'] ?? 0;
                $allianceScores[$userData['ally_id']]->total_count += $userScores[$userData['id']]['total']['count'] ?? 0;
                $allianceScores[$userData['ally_id']]->total_points += $userScores[$userData['id']]['total']['points'] ?? 0;
            }
        }

        if (count($allianceScores) != 0) {
            foreach ($allianceScores as $allianceData) {
                $this->updateUserStats($allianceData);
            }
        }

        $this->SetNewRanks();
        $this->checkUniverseAccounts($universeData);

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
        $Return['Users'] = $database->select('SELECT SQL_BIG_RESULT DISTINCT ' . $selected_tech . $select_fleets . $select_defenses . ' u.id, u.ally_id, u.authlevel, u.bana, u.universe, u.username, s.tech_rank AS old_tech_rank, s.build_rank AS old_build_rank, s.defs_rank AS old_defs_rank, s.fleet_rank AS old_fleet_rank, s.total_rank AS old_total_rank FROM %%USERS%% as u LEFT JOIN %%STATPOINTS%% as s ON s.stat_type = 1 AND s.id_owner = u.id LEFT JOIN %%PLANETS%% as p ON u.id = p.id_owner GROUP BY s.id_owner, u.id, u.authlevel;');
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
}
