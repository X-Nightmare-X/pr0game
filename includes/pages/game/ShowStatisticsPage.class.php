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


class ShowStatisticsPage extends AbstractGamePage
{
    public static $requireModule = MODULE_STATISTICS;

    public function __construct()
    {
        parent::__construct();
    }

    public function show()
    {
        $USER =& Singleton()->USER;
        $LNG =& Singleton()->LNG;
        $who = HTTP::_GP('who', 1);
        $type = HTTP::_GP('type', 1);
        $range = HTTP::_GP('range', 1);
        if (!isModuleAvailable(MODULE_SPYTECH_DEPENDENT_STATS) || $USER['authlevel'] > 0){
            switch ($type) {
                case 2:
                    $Order = "fleet_rank";
                    $Points = "fleet_points";
                    $Rank = "fleet_rank";
                    $OldRank = "fleet_old_rank";
                    break;
                case 3:
                    $Order = "tech_rank";
                    $Points = "tech_points";
                    $Rank = "tech_rank";
                    $OldRank = "tech_old_rank";
                    break;
                case 4:
                    $Order = "build_rank";
                    $Points = "build_points";
                    $Rank = "build_rank";
                    $OldRank = "build_old_rank";
                    break;
                case 5:
                    $Order = "defs_rank";
                    $Points = "defs_points";
                    $Rank = "defs_rank";
                    $OldRank = "defs_old_rank";
                    break;
                default:
                    $Order = "total_rank";
                    $Points = "total_points";
                    $Rank = "total_rank";
                    $OldRank = "total_old_rank";
                    break;
            }
        } else {
            //prevent url manipulation
            $spytech = $USER['spy_tech'];
            if ($type == 2 && $spytech >= 6){
                $Order = "fleet_rank";
                $Points = "fleet_points";
                $Rank = "fleet_rank";
                $OldRank = "fleet_old_rank";
            } elseif ($type == 3 && $spytech >= 4){
                $Order = "tech_rank";
                $Points = "tech_points";
                $Rank = "tech_rank";
                $OldRank = "tech_old_rank";
            } elseif ($type == 4 && $spytech >= 2){
                $Order = "build_rank";
                $Points = "build_points";
                $Rank = "build_rank";
                $OldRank = "build_old_rank";
            } elseif ($type == 5 && $spytech >= 6){
                $Order = "defs_rank";
                $Points = "defs_points";
                $Rank = "defs_rank";
                $OldRank = "defs_old_rank";
            } else {
                $Order = "total_rank";
                $Points = "total_points";
                $Rank = "total_rank";
                $OldRank = "total_old_rank";
            }
        }

        $RangeList = [];

        $db = Database::get();
        $config = Config::get();

        switch ($who) {
            case 1:
                $MaxUsers = $config->users_amount;
                $range = min($range, $MaxUsers);
                $LastPage = max(1, ceil($MaxUsers / 100));

                for ($Page = 0; $Page < $LastPage; $Page++) {
                    $PageValue = ($Page * 100) + 1;
                    $PageRange = $PageValue + 99;
                    $Selector['range'][$PageValue] = $PageValue . "-" . $PageRange;
                }

                $start = max(floor(($range - 1) / 100) * 100, 0);

                if ($config->stat == 2) {
                    $sql = "SELECT DISTINCT s.*, u.id, u.username, u.ally_id, u.banaday, u.urlaubs_modus, u.onlinetime,"
                        . " a.ally_name, (a.ally_owner=u.id) as is_leader, a.ally_owner_range, r.DIPLOMATIC as is_diplo, "
                        . " buddy.id as buddy, d.level as diploLevel"
                        . " FROM %%STATPOINTS%% as s"
                        . " INNER JOIN %%USERS%% as u ON u.id = s.id_owner"
                        . " LEFT JOIN %%ALLIANCE%% as a ON a.id = s.id_ally"
                        . " LEFT JOIN %%ALLIANCE_RANK%% as r ON r.allianceID = s.id_ally AND r.rankID = u.ally_rank_id"
                        . " LEFT JOIN %%DIPLO%% as d ON ((d.owner_1 = :allianceId AND d.owner_2 = a.id) OR (d.owner_1 = a.id AND d.owner_2 = :allianceId)) AND d.accept = 1"
                        . " LEFT JOIN %%BUDDY%% as buddy ON ((buddy.sender = :userId AND buddy.owner = u.id) OR (buddy.sender = u.id AND buddy.owner = :userId)) AND u.id != :userId"
                        . " WHERE s.universe = :universe AND s.stat_type = 1 AND u.authlevel < :authLevel"
                        . " ORDER BY " . $Order . " ASC LIMIT :offset, :limit;";
                    $query = $db->select($sql, [
                        ':allianceId'   => $USER['ally_id'],
                        ':userId'       => $USER['id'],
                        ':universe'     => Universe::current(),
                        ':authLevel'    => $config->stat_level,
                        ':offset'       => $start,
                        ':limit'        => 100,
                    ]);
                } else {
                    $sql = "SELECT DISTINCT s.*, u.id, u.username, u.ally_id, u.banaday, u.urlaubs_modus, u.onlinetime,"
                        . " a.ally_name, (a.ally_owner=u.id) as is_leader, a.ally_owner_range, r.DIPLOMATIC as is_diplo, "
                        . " buddy.id as buddy, d.level as diploLevel"
                        . " FROM %%STATPOINTS%% as s"
                        . " INNER JOIN %%USERS%% as u ON u.id = s.id_owner"
                        . " LEFT JOIN %%ALLIANCE%% as a ON a.id = s.id_ally"
                        . " LEFT JOIN %%ALLIANCE_RANK%% as r ON r.allianceID = s.id_ally AND r.rankID = u.ally_rank_id"
                        . " LEFT JOIN %%DIPLO%% as d ON ((d.owner_1 = :allianceId AND d.owner_2 = a.id) OR (d.owner_1 = a.id AND d.owner_2 = :allianceId)) AND d.accept = 1"
                        . " LEFT JOIN %%BUDDY%% as buddy ON ((buddy.sender = :userId AND buddy.owner = u.id) OR (buddy.sender = u.id AND buddy.owner = :userId)) AND u.id != :userId"
                        . " WHERE s.universe = :universe AND s.stat_type = 1"
                        . " ORDER BY " . $Order . " ASC LIMIT :offset, :limit;";
                    $query = $db->select($sql, [
                        ':allianceId'   => $USER['ally_id'],
                        ':userId'       => $USER['id'],
                        ':universe'     => Universe::current(),
                        ':offset'       => $start,
                        ':limit'        => 100,
                    ]);
                }

                try {
                    $USER += $db->selectSingle(
                        'SELECT total_points FROM %%STATPOINTS%% WHERE id_owner = :userId AND stat_type = :statType',
                        [
                            ':userId'   => $USER['id'],
                            ':statType' => 1
                        ]
                    ) ?: ['total_points' => 0];
                } catch (Exception $e) {
                    $USER['total_points'] = 0;
                }

                foreach ($query as $StatRow) {
                    $IsNoobProtec = CheckNoobProtec($USER, $StatRow, $StatRow);
                    $Class = userStatus($StatRow, $IsNoobProtec);
                    $AllyClass = [];
                    if (!empty($StatRow['ally_id'])) {
                        switch ($StatRow['diploLevel']) {
                            case 1:
                            case 2:
                                $AllyClass = ['member'];
                                break;
                            case 3:
                                $AllyClass = ['trade'];
                                break;
                            case 4:
                                $AllyClass = ['friend'];
                                break;
                            case 5:
                                $AllyClass = ['enemy'];
                                break;
                            case 6:
                                $AllyClass = ['secret'];
                                break;
                        }

                        if ($USER['ally_id'] == $StatRow['ally_id']) {
                            $AllyClass = ['member'];
                        }
                    }

                    $RangeList[] = [
                        'id'               => $StatRow['id'],
                        'name'             => $StatRow['username'],
                        'class'            => $Class,
                        'isBuddy'          => $StatRow['buddy'] != null,
                        'is_leader'        => $StatRow['is_leader'],
                        'is_diplo'         => $StatRow['is_diplo'],
                        'ally_owner_range' => $StatRow['ally_owner_range'],
                        'points'           => pretty_number($StatRow[$Points]),
                        'allyid'           => $StatRow['ally_id'],
                        'allyClass'        => $AllyClass,
                        'rank'             => $StatRow[$Rank],
                        'allyname'         => $StatRow['ally_name'],
                        'ranking'          => $StatRow[$OldRank] - $StatRow[$Rank],
                    ];
                }

                break;
            case 2:
                $sql = "SELECT COUNT(*) as state FROM %%ALLIANCE%% WHERE `ally_universe` = :universe;";
                $MaxAllys = $db->selectSingle($sql, [':universe' => Universe::current()], 'state');

                $range = min($range, $MaxAllys);
                $LastPage = max(1, ceil($MaxAllys / 100));

                for ($Page = 0; $Page < $LastPage; $Page++) {
                    $PageValue = ($Page * 100) + 1;
                    $PageRange = $PageValue + 99;
                    $Selector['range'][$PageValue] = $PageValue . "-" . $PageRange;
                }

                $start = max(floor(($range - 1) / 100) * 100, 0);

                $sql = 'SELECT DISTINCT s.*, a.id, a.ally_members, a.ally_name FROM %%STATPOINTS%% as s
                INNER JOIN %%ALLIANCE%% as a ON a.id = s.id_owner
                WHERE universe = :universe AND stat_type = 2
                ORDER BY ' . $Order . ' ASC LIMIT :offset, :limit;';
                $query = $db->select($sql, [
                    ':universe' => Universe::current(),
                    ':offset'   => $start,
                    ':limit'    => 100,
                ]);

                foreach ($query as $StatRow) {
                    $RangeList[] = [
                        'id'       => $StatRow['id'],
                        'name'     => $StatRow['ally_name'],
                        'members'  => $StatRow['ally_members'],
                        'rank'     => $StatRow[$Rank],
                        'mppoints' => pretty_number(floor($StatRow[$Points] / (max($StatRow['ally_members'], 1)))),
                        'points'   => pretty_number($StatRow[$Points]),
                        'ranking'  => $StatRow[$OldRank] - $StatRow[$Rank],
                    ];
                }

                break;
        }

        $Selector['who'] = [
            1 => $LNG['st_player'],
            2 => $LNG['st_alliance'],
        ];
        if (!isModuleAvailable(MODULE_SPYTECH_DEPENDENT_STATS) || $USER['authlevel'] > 0){
            $Selector['type'] = [
                1 => $LNG['st_points'],
                2 => $LNG['st_fleets'],
                3 => $LNG['st_researh'],
                4 => $LNG['st_buildings'],
                5 => $LNG['st_defenses'],
            ];
        } else {
            // only show selectable stat types
            $spytech = $USER['spy_tech'];
            switch ($spytech) {
                case 0:
                case 1:
                    $Selector['type'] = [
                        1 => $LNG['st_points'],
                    ];
                    break;
                case 2:
                case 3:
                    $Selector['type'] = [
                        1 => $LNG['st_points'],
                        4 => $LNG['st_buildings'],
                    ];
                    break;
                case 4:
                case 5:
                    $Selector['type'] = [
                        1 => $LNG['st_points'],
                        3 => $LNG['st_researh'],
                        4 => $LNG['st_buildings'],
                    ];
                    break;
                default:
                    $Selector['type'] = [
                        1 => $LNG['st_points'],
                        2 => $LNG['st_fleets'],
                        3 => $LNG['st_researh'],
                        4 => $LNG['st_buildings'],
                        5 => $LNG['st_defenses'],
                    ];
                    break;
            }
        }
        

        require_once 'includes/classes/Cronjob.class.php';

        $this->assign([
            'Selectors'   => $Selector,
            'who'         => $who,
            'type'        => $type,
            'range'       => floor(($range - 1) / 100) * 100 + 1,
            'RangeList'   => $RangeList,
            'CUser_ally'  => $USER['ally_id'],
            'CUser_id'    => $USER['id'],
            'stat_date'   => _date($LNG['php_tdformat'], Cronjob::getLastExecutionTime('statistic'), $USER['timezone']),
            'ShortStatus' => [
                'vacation'      => $LNG['gl_short_vacation'],
                'banned'        => $LNG['gl_short_ban'],
                'inactive'      => $LNG['gl_short_inactive'],
                'longinactive'  => $LNG['gl_short_long_inactive'],
                'noob'          => $LNG['gl_short_newbie'],
                'strong'        => $LNG['gl_short_strong'],
                'enemy'         => $LNG['gl_short_enemy'],
                'friend'        => $LNG['gl_short_friend'],
                'member'        => $LNG['gl_short_member'],
            ],
        ]);

        $this->display('page.statistics.default.tpl');
    }
}
