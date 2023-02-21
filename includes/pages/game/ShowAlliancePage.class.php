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

class ShowAlliancePage extends AbstractGamePage
{
    public static $requireModule = MODULE_ALLIANCE;

    private $allianceData;
    private $ranks;
    private $rights;
    private $hasAlliance = false;
    private $hasApply = false;
    public $availableRanks = [
        'MEMBERLIST',
        'ONLINESTATE',
        'TRANSFER',
        'SEEAPPLY',
        'MANAGEAPPLY',
        'ROUNDMAIL',
        'ADMIN',
        'KICK',
        'DIPLOMATIC',
        'RANKS',
        'MANAGEUSERS'
    ];

    public function __construct()
    {
        $USER =& Singleton()->USER;
        parent::__construct();
        $this->hasAlliance = $USER['ally_id'] != 0;
        $this->hasApply = $this->isApply();
        if ($this->hasAlliance && !$this->hasApply) {
            $this->setAllianceData($USER['ally_id']);
        }
    }

    private function setAllianceData($allianceId)
    {
        $USER =& Singleton()->USER;
        $db = Database::get();

        $sql = 'SELECT * FROM %%ALLIANCE%% WHERE id = :allianceId;';
        $this->allianceData = $db->selectSingle($sql, [
            ':allianceId' => $allianceId
        ]);

        if (!$this->allianceData) {
            throw new Exception('the requested alliance does not exist');
        }

        if ($USER['ally_id'] == $allianceId) {
            if ($this->allianceData['ally_owner'] == $USER['id']) {
                $this->rights = array_combine($this->availableRanks, array_fill(0, count($this->availableRanks), true));
            } elseif ($USER['ally_rank_id'] != 0) {
                $sql = 'SELECT ' . implode(
                    ', ',
                    $this->availableRanks
                ) . ' FROM %%ALLIANCE_RANK%% WHERE allianceId = :allianceId AND rankID = :ally_rank_id;';
                $this->rights = $db->selectSingle($sql, [
                    ':allianceId' => $allianceId,
                    ':ally_rank_id' => $USER['ally_rank_id'],
                ]);
            }

            if (!isset($this->rights)) {
                $this->rights = array_combine(
                    $this->availableRanks,
                    array_fill(0, count($this->availableRanks), false)
                );
            }

            if (isset($this->tplObj)) {
                $this->assign([
                    'rights' => $this->rights,
                    'AllianceOwner' => $this->allianceData['ally_owner'] == $USER['id'],
                ]);
            }
        }
    }

    private function isApply()
    {
        $USER =& Singleton()->USER;
        $db = Database::get();
        $sql = "SELECT COUNT(*) as count FROM %%ALLIANCE_REQUEST%% WHERE userId = :userId;";
        return $db->selectSingle($sql, [
            ':userId' => $USER['id']
        ], 'count');
    }

    public function info()
    {
        $LNG =& Singleton()->LNG;
        $USER =& Singleton()->USER;
        $allianceId = HTTP::_GP('id', 0);

        $statisticData = [];
        $diplomaticmaticData = false;
        $diplomats = false;

        $this->setAllianceData($allianceId);

        if (!isset($this->allianceData)) {
            $this->printMessage($LNG['al_not_exists']);
        }

        if ($this->allianceData['ally_diplo'] == 1) {
            $diplomaticmaticData = $this->getDiplomatic();

            $sql = "SELECT u.id, u.username, r.rankName FROM %%USERS%% u
                JOIN %%ALLIANCE_RANK%% r ON r.allianceID = u.ally_id AND r.rankID = u.ally_rank_id
                WHERE r.DIPLOMATIC = 1 AND u.ally_id = :ally_id;";
            $diplomats = Database::get()->select($sql, [':ally_id' => $this->allianceData['id']]);
        }

        if ($this->allianceData['ally_stats'] == 1) {
            $sql = 'SELECT SUM(wons) as wons, SUM(loos) as loos, SUM(draws) as draws, SUM(kbmetal) as kbmetal,
            SUM(kbcrystal) as kbcrystal, SUM(lostunits) as lostunits, SUM(desunits) as desunits
            FROM %%USERS%% WHERE ally_id = :allyID;';

            $statisticResult = Database::get()->selectSingle($sql, [
                ':allyID' => $this->allianceData['id']
            ]);

            $statisticData = [
                'totalfight' => $statisticResult['wons'] + $statisticResult['loos'] + $statisticResult['draws'],
                'fightwon' => $statisticResult['wons'],
                'fightlose' => $statisticResult['loos'],
                'fightdraw' => $statisticResult['draws'],
                'unitsshot' => pretty_number($statisticResult['desunits']),
                'unitslose' => pretty_number($statisticResult['lostunits']),
                'dermetal' => pretty_number($statisticResult['kbmetal']),
                'dercrystal' => pretty_number($statisticResult['kbcrystal']),
            ];
        }

        $sql = 'SELECT total_points
		FROM %%STATPOINTS%%
		WHERE id_owner = :userId AND stat_type = :statType';

        $userPoints = Database::get()->selectSingle($sql, [
            ':userId' => $USER['id'],
            ':statType' => 1
        ], 'total_points');
        if (isset($this->allianceData['ally_description'])) {
            $allydesc = nl2br($this->allianceData['ally_description']);
        } else {
            $allydesc = '';
        }
        $this->assign([
            'diplomaticData' => $diplomaticmaticData,
            'diplomats' => $diplomats,
            'statisticData' => $statisticData,
            'ally_description' => $allydesc,
            'ally_id' => $this->allianceData['id'],
            'ally_image' => $this->allianceData['ally_image'],
            'ally_web' => $this->allianceData['ally_web'],
            'ally_member_scount' => $this->allianceData['ally_members'],
            'ally_max_members' => $this->allianceData['ally_max_members'],
            'ally_name' => $this->allianceData['ally_name'],
            'ally_tag' => $this->allianceData['ally_tag'],
            'ally_stats' => $this->allianceData['ally_stats'],
            'ally_diplo' => $this->allianceData['ally_diplo'],
            'ally_request' => !$this->hasAlliance
                && !$this->hasApply
                && $this->allianceData['ally_request_notallow'] == 0
                && $this->allianceData['ally_max_members'] > $this->allianceData['ally_members'],
            'ally_request_min_points' => $userPoints >= $this->allianceData['ally_request_min_points'],
            'ally_request_min_points_info' => sprintf(
                $LNG['al_requests_min_points'],
                pretty_number($this->allianceData['ally_request_min_points'])
            )
        ]);

        $this->display('page.alliance.info.tpl');
    }

    public function show()
    {
        if ($this->hasAlliance) {
            $this->homeAlliance();
        } elseif ($this->hasApply) {
            $this->applyWaitScreen();
        } else {
            $this->createSelection();
        }
    }

    private function redirectToHome()
    {
        $this->redirectTo('game.php?page=alliance');
    }

    private function getAction()
    {
        return HTTP::_GP('action', '');
    }

    private function applyWaitScreen()
    {
        $USER =& Singleton()->USER;
        $LNG =& Singleton()->LNG;
        $db = Database::get();
        $sql = "SELECT a.id, a.ally_name, a.ally_tag
            FROM %%ALLIANCE_REQUEST%% r
            INNER JOIN %%ALLIANCE%% a ON a.id = r.allianceId
            WHERE r.userId = :userId;";
        $allianceResult = $db->selectSingle($sql, [
            ':userId' => $USER['id']
        ]);

        if (empty($allianceResult['ally_tag'])) {
            $allianceResult['ally_tag'] = 0;
        }

        $link = '<a href="?page=alliance&mode=info&id=' . $allianceResult['id'] . '">[' . $allianceResult['ally_tag']
             . '] ' . $allianceResult['ally_name'] . '</a>';
        $this->assign([
            'request_text' => sprintf($LNG['al_request_wait_message'], $link),
        ]);

        $this->display('page.alliance.applyWait.tpl');
    }

    private function createSelection()
    {
        $this->display('page.alliance.createSelection.tpl');
    }

    public function search()
    {
        if ($this->hasApply) {
            $this->redirectToHome();
        }

        $searchText = HTTP::_GP('searchtext', '', UTF8_SUPPORT);
        $searchList = [];

        if (!empty($searchText)) {
            $db = Database::get();
            $sql = "SELECT id, ally_name, ally_tag, ally_members
			FROM %%ALLIANCE%%
			WHERE ally_universe = :universe AND ally_name LIKE :searchTextLike
			ORDER BY (
			  IF(ally_name = :searchTextEqual, 1, 0) + IF(ally_name LIKE :searchTextLike, 1, 0)
			) DESC,ally_name ASC LIMIT 25;";

            $searchResult = $db->select($sql, [
                ':universe' => Universe::current(),
                ':searchTextLike' => '%' . $searchText . '%',
                ':searchTextEqual' => $searchText
            ]);

            foreach ($searchResult as $searchRow) {
                $searchList[] = [
                    'id' => $searchRow['id'],
                    'tag' => $searchRow['ally_tag'],
                    'members' => $searchRow['ally_members'],
                    'name' => $searchRow['ally_name'],
                ];
            }
        }

        $this->assign([
            'searchText' => $searchText,
            'searchList' => $searchList,
        ]);

        $this->display('page.alliance.search.tpl');
    }

    public function apply()
    {
        $LNG =& Singleton()->LNG;
        $USER =& Singleton()->USER;
        if ($this->hasApply) {
            $this->redirectToHome();
        }

        if ($USER['ally_id'] != 0) {
            $this->redirectToHome();
        }

        $text = HTTP::_GP('text', '', true);
        $allianceId = HTTP::_GP('id', 0);

        $db = Database::get();
        $sql = "SELECT ally_tag, ally_request, ally_request_notallow, ally_owner FROM %%ALLIANCE%% WHERE"
            . " id = :allianceId AND ally_universe = :universe;";
        $allianceResult = $db->selectSingle($sql, [
            ':allianceId' => $allianceId,
            ':universe' => Universe::current()
        ]);

        if (!isset($allianceResult)) {
            $this->redirectToHome();
        }

        if ($allianceResult['ally_request_notallow'] == 1) {
            $this->printMessage($LNG['al_alliance_closed'], [[
                'label' => $LNG['sys_forward'],
                'url' => '?page=alliance'
            ]]);
        }

        if (!empty($text)) {
            $sql = "INSERT INTO %%ALLIANCE_REQUEST%% SET
                allianceId	= :allianceId,
                text		= :text,
                time		= :time,
                userId		= :userId;";

            $db->insert($sql, [
                ':allianceId' => $allianceId,
                ':text' => $text,
                ':time' => TIMESTAMP,
                ':userId' => $USER['id']
            ]);

            $receivers = $this->getMessageReceivers($allianceId, false, true);

            foreach ($receivers as $receiver) {
                $lang = getLanguage($receiver['lang']);

                $applyMessage = sprintf(
                    $lang['al_new_apply'],
                    $USER['id'],
                    $USER['username'],
                    $USER['username']
                );

                PlayerUtil::sendMessage(
                    $receiver['id'],
                    0,
                    $lang['al_alliance'],
                    2,
                    $lang['al_request'],
                    $applyMessage,
                    TIMESTAMP
                );
            }

            $this->printMessage($LNG['al_request_confirmation_message'], [[
                'label' => $LNG['al_ok'],
                'url' => '?page=overview'
            ]]);
        }

        $this->assign([
            'allyid' => $allianceId,
            'applytext' => $allianceResult['ally_request'],
            'al_write_request' => sprintf($LNG['al_write_request'], $allianceResult['ally_tag']),
        ]);

        $this->display('page.alliance.apply.tpl');
    }

    public function cancelApply()
    {
        $LNG =& Singleton()->LNG;
        $USER =& Singleton()->USER;
        if (!$this->hasApply) {
            $this->redirectToHome();
        }

        $db = Database::get();
        $sql = "SELECT a.ally_tag FROM %%ALLIANCE_REQUEST%% r INNER JOIN %%ALLIANCE%% a ON a.id = r.allianceId"
            . " WHERE r.userId = :userId;";
        $allyTag = $db->selectSingle($sql, [
            ':userId' => $USER['id']
        ], 'ally_tag');

        $sql = "DELETE FROM %%ALLIANCE_REQUEST%% WHERE userId = :userId;";
        $db->delete($sql, [
            ':userId' => $USER['id']
        ]);

        $this->printMessage(sprintf($LNG['al_request_deleted'], $allyTag), [[
            'label' => $LNG['sys_forward'],
            'url' => '?page=alliance'
        ]]);
    }

    public function create()
    {
        $USER =& Singleton()->USER;
        $LNG =& Singleton()->LNG;
        if ($this->hasApply) {
            $this->redirectToHome();
        }
        $sql = 'SELECT total_points
		FROM %%STATPOINTS%%
		WHERE id_owner = :userId AND stat_type = :statType';

        $userPoints = Database::get()->selectSingle($sql, [
            ':userId' => $USER['id'],
            ':statType' => 1
        ], 'total_points');

        $min_points = Config::get()->alliance_create_min_points;

        if ($userPoints >= $min_points) {
            $action = $this->getAction();
            if ($action == "send") {
                $this->createAlliance();
            } else {
                $this->display('page.alliance.create.tpl');
            }
        } else {
            $diff_points = $min_points - $userPoints;
            $messageText = sprintf(
                $LNG['al_make_ally_insufficient_points'],
                pretty_number($min_points),
                pretty_number($diff_points)
            );

            $this->printMessage($messageText, [[
                'label' => $LNG['sys_back'],
                'url' => '?page=alliance'
            ]]);
        }
    }

    private function createAlliance()
    {
        $action = $this->getAction();
        if ($action == "send") {
            $this->createAllianceProcessor();
        } else {
            $this->display('page.alliance.create.tpl');
        }
    }

    private function createAllianceProcessor()
    {
        $USER =& Singleton()->USER;
        $LNG =& Singleton()->LNG;
        $allianceTag = HTTP::_GP('atag', '', UTF8_SUPPORT);
        $allianceName = HTTP::_GP('aname', '', UTF8_SUPPORT);

        $allianceTag = preg_replace('/[^a-zA-Z0-9 (-)_äöüßÄÖÜ]/', '', $allianceTag);
        $allianceName = preg_replace('/[^a-zA-Z0-9 (-)_äöüßÄÖÜ]/', '', $allianceName);

        if (empty($allianceTag)) {
            $this->printMessage($LNG['al_tag_required'], [[
                'label' => $LNG['sys_back'],
                'url' => '?page=alliance&mode=create'
            ]]);
        }

        if (empty($allianceName)) {
            $this->printMessage($LNG['al_name_required'], [[
                'label' => $LNG['sys_back'],
                'url' => '?page=alliance&mode=create'
            ]]);
        }

        if (!PlayerUtil::isNameValid($allianceName) || !PlayerUtil::isNameValid($allianceTag)) {
            $this->printMessage($LNG['al_newname_specialchar'], [[
                'label' => $LNG['sys_back'],
                'url' => '?page=alliance&mode=create'
            ]]);
        }

        $db = Database::get();

        $sql = 'SELECT COUNT(*) as count FROM %%ALLIANCE%% WHERE ally_universe = :universe
        AND (ally_tag = :allianceTag OR ally_name = :allianceName);';

        $allianceCount = $db->selectSingle($sql, [
            ':universe' => Universe::current(),
            ':allianceTag' => $allianceTag,
            ':allianceName' => $allianceName
        ], 'count');

        if ($allianceCount != 0) {
            $this->printMessage(sprintf($LNG['al_already_exists'], $allianceName), [[
                'label' => $LNG['sys_back'],
                'url' => '?page=alliance&mode=create'
            ]]);
        }

        $sql = "INSERT INTO %%ALLIANCE%% SET ally_name = :allianceName, ally_tag = :allianceTag, ally_owner = :userId,"
            . " ally_owner_range = :allianceOwnerRange, ally_members = 1, ally_register_time = :time,"
            . " ally_universe = :universe;";
        $db->insert($sql, [
            ':allianceName' => $allianceName,
            ':allianceTag' => $allianceTag,
            ':userId' => $USER['id'],
            ':allianceOwnerRange' => $LNG['al_default_leader_name'],
            ':time' => TIMESTAMP,
            ':universe' => Universe::current(),
        ]);

        // $sql = "SELECT `id` FROM %%ALLIANCE%% WHERE ally_name = :allianceName AND ally_tag = :allianceTag AND ally_owner = :userId AND ally_universe = :universe";
        // $allianceId = $db->selectSingle($sql, [
        //     ':allianceName' => $allianceName,
        //     ':allianceTag' => $allianceTag,
        //     ':userId' => $USER['id'],
        //     ':universe' => Universe::current(),
        // ], 'id');

        $allianceId = $db->lastInsertId();

        $sql = "UPDATE %%USERS%% SET ally_id	= :allianceId, ally_rank_id	= 0, ally_register_time = :time"
            . " WHERE id = :userId;";
        $db->update($sql, [
            ':allianceId' => $allianceId,
            ':time' => TIMESTAMP,
            ':userId' => $USER['id']
        ]);

        $sql = "UPDATE %%STATPOINTS%% SET id_ally = :allianceId WHERE id_owner = :userId;";
        $db->update($sql, [
            ':allianceId' => $allianceId,
            ':userId' => $USER['id']
        ]);

        $this->printMessage(sprintf($LNG['al_created'], $allianceName . ' [' . $allianceTag . ']'), [[
            'label' => $LNG['sys_forward'],
            'url' => '?page=alliance'
        ]]);
    }

    private function getDiplomatic()
    {
        $Return = [];
        $db = Database::get();

        $sql = "SELECT d.level, d.accept, d.accept_text, d.id, a.id as ally_id, a.ally_name, a.ally_tag, d.owner_1,"
            . " d.owner_2 FROM %%DIPLO%% as d INNER JOIN %%ALLIANCE%% as a ON IF(:allianceId = d.owner_1,"
            . " a.id = d.owner_2, a.id = d.owner_1) WHERE :allianceId = d.owner_1 OR :allianceId = d.owner_2;";
        $DiploResult = $db->select($sql, [
            ':allianceId' => $this->allianceData['id'],
        ]);

        foreach ($DiploResult as $CurDiplo) {
            if ($CurDiplo['level'] == 1 && $CurDiplo['owner_2'] == $this->allianceData['id']) {
                $CurDiplo['level'] = 0;
            }

            if ($CurDiplo['accept'] == 0 && $CurDiplo['owner_2'] == $this->allianceData['id']) {
                $Return[5][$CurDiplo['id']] = [
                    $CurDiplo['ally_name'],
                    $CurDiplo['ally_id'],
                    $CurDiplo['level'],
                    $CurDiplo['accept_text'],
                    $CurDiplo['ally_tag'],
                ];
            } elseif ($CurDiplo['accept'] == 0 && $CurDiplo['owner_1'] == $this->allianceData['id']) {
                $Return[6][$CurDiplo['id']] = [
                    $CurDiplo['ally_name'],
                    $CurDiplo['ally_id'],
                    $CurDiplo['level'],
                    $CurDiplo['accept_text'],
                    $CurDiplo['ally_tag'],
                ];
            } else {
                $Return[$CurDiplo['level']][$CurDiplo['id']] = [
                    $CurDiplo['ally_name'],
                    $CurDiplo['ally_id'],
                    $CurDiplo['owner_1'],
                    $CurDiplo['ally_tag'],
                ];
            }
        }
        return $Return;
    }

    private function homeAlliance()
    {
        $USER =& Singleton()->USER;
        $LNG =& Singleton()->LNG;
        $db = Database::get();

        if ($this->allianceData['ally_owner'] == $USER['id']) {
            $rankName = ($this->allianceData['ally_owner_range'] != '')
                ? $this->allianceData['ally_owner_range'] : $LNG['al_founder_rank_text'];
        } elseif ($USER['ally_rank_id'] != 0) {
            $sql = "SELECT rankName FROM %%ALLIANCE_RANK%% WHERE rankID = :UserRankID;";
            $rankName = $db->selectSingle($sql, [
                ':UserRankID' => $USER['ally_rank_id']
            ], 'rankName');
        }

        if (empty($rankName)) {
            $rankName = $LNG['al_new_member_rank_text'];
        }

        $sql = "SELECT SUM(wons) as wons, SUM(loos) as loos, SUM(draws) as draws, SUM(kbmetal) as kbmetal,"
            . " SUM(kbcrystal) as kbcrystal, SUM(lostunits) as lostunits, SUM(desunits) as desunits FROM %%USERS%%"
            . " WHERE ally_id = :AllianceID;";
        $statisticResult = $db->selectSingle($sql, [
            ':AllianceID' => $this->allianceData['id']
        ]);

        $sql = "SELECT u.id, u.username, r.rankName FROM %%USERS%% u
                JOIN %%ALLIANCE_RANK%% r ON r.allianceID = u.ally_id AND r.rankID = u.ally_rank_id
                WHERE r.DIPLOMATIC = 1 AND u.ally_id = :ally_id;";
        $diplomats = Database::get()->select($sql, [':ally_id' => $this->allianceData['id']]);

        $sql = "SELECT COUNT(*) as count FROM %%ALLIANCE_REQUEST%% WHERE allianceId = :AllianceID;";
        $ApplyCount = $db->selectSingle($sql, [
            ':AllianceID' => $this->allianceData['id']
        ], 'count');

        $sql = "SELECT COUNT(*) as count FROM %%DIPLO%% WHERE owner_2 = :AllianceID AND accept = 0;";
        $DiploCountIn = $db->selectSingle($sql, [
            ':AllianceID' => $this->allianceData['id']
        ], 'count');
        $sql = "SELECT COUNT(*) as count FROM %%DIPLO%% WHERE owner_1 = :AllianceID AND accept = 0;";
        $DiploCountOut = $db->selectSingle($sql, [
            ':AllianceID' => $this->allianceData['id']
        ], 'count');
        if (isset($this->allianceData['ally_description'])) {
            $allydesc = nl2br($this->allianceData['ally_description']);
        } else {
            $allydesc = '';
        }
        if (isset($this->allianceData['ally_text'])) {
            $allytext = nl2br($this->allianceData['ally_text']);
        } else {
            $allytext = '';
        }

        $this->assign([
            'DiploInfo' => $this->getDiplomatic(),
            'ally_web' => $this->allianceData['ally_web'],
            'ally_tag' => $this->allianceData['ally_tag'],
            'ally_members' => $this->allianceData['ally_members'],
            'ally_max_members' => $this->allianceData['ally_max_members'],
            'ally_name' => $this->allianceData['ally_name'],
            'ally_image' => $this->allianceData['ally_image'],
            'ally_description' => $allydesc,
            'ally_text' => $allytext,
            'rankName' => $rankName,
            'requests' => sprintf($LNG['al_new_requests'], $ApplyCount),
            'applyCount' => $ApplyCount,
            'diplomats' => $diplomats,
            'diploRequestsIn' => $LNG['al_diplo_accept'] . ' - ' . $DiploCountIn,
            'diploRequestsOut' => $LNG['al_diplo_accept_send'] . ' - ' . $DiploCountOut,
            'diploCountIn' => $DiploCountIn,
            'diploCountOut' => $DiploCountOut,
            'totalfight' => $statisticResult['wons'] + $statisticResult['loos'] + $statisticResult['draws'],
            'fightwon' => $statisticResult['wons'],
            'fightlose' => $statisticResult['loos'],
            'fightdraw' => $statisticResult['draws'],
            'unitsshot' => pretty_number($statisticResult['desunits']),
            'unitslose' => pretty_number($statisticResult['lostunits']),
            'dermetal' => pretty_number($statisticResult['kbmetal']),
            'dercrystal' => pretty_number($statisticResult['kbcrystal']),
            'isOwner' => $this->allianceData['ally_owner'] == $USER['id']
        ]);

        $this->display('page.alliance.home.tpl');
    }

    public function memberList()
    {
        $USER =& Singleton()->USER;
        $LNG =& Singleton()->LNG;
        if (!$this->rights['MEMBERLIST']) {
            $this->redirectToHome();
        }

        $rankList = [];

        $db = Database::get();
        $sql = "SELECT rankID, rankName FROM %%ALLIANCE_RANK%% WHERE allianceId = :AllianceID";
        $rankResult = $db->select($sql, [
            ':AllianceID' => $this->allianceData['id']
        ]);

        foreach ($rankResult as $rankRow) {
            $rankList[$rankRow['rankID']] = $rankRow['rankName'];
        }

        $memberList = [];

        $sql = "SELECT DISTINCT u.id, u.username,u.galaxy, u.system, u.planet, u.banaday, u.urlaubs_modus,"
            . " u.ally_register_time, u.onlinetime, u.ally_rank_id, s.total_points FROM %%USERS%% u"
            . " LEFT JOIN %%STATPOINTS%% as s ON s.stat_type = '1' AND s.id_owner = u.id WHERE ally_id = :AllianceID;";
        $memberListResult = $db->select($sql, [
            ':AllianceID' => $this->allianceData['id']
        ]);

        try {
            $USER += $db->selectSingle(
                'SELECT total_points FROM %%STATPOINTS%% WHERE id_owner = :userId AND stat_type = :statType',
                [
                    ':userId' => $USER['id'],
                    ':statType' => 1,
                ]
            ) ?: ['total_points' => 0];
        } catch (Exception $e) {
            $USER['total_points'] = 0;
        }

        foreach ($memberListResult as $memberListRow) {
            $IsNoobProtec = CheckNoobProtec($USER, $memberListRow, $memberListRow);
            $Class = userStatus($memberListRow, $IsNoobProtec);

            if ($this->allianceData['ally_owner'] == $memberListRow['id']) {
                $memberListRow['ally_rankName'] = empty($this->allianceData['ally_owner_range'])
                    ? $LNG['al_founder_rank_text'] : $this->allianceData['ally_owner_range'];
            } elseif ($memberListRow['ally_rank_id'] != 0 && isset($rankList[$memberListRow['ally_rank_id']])) {
                $memberListRow['ally_rankName'] = $rankList[$memberListRow['ally_rank_id']];
            } else {
                $memberListRow['ally_rankName'] = $LNG['al_new_member_rank_text'];
            }

            $memberList[$memberListRow['id']] = [
                'class' => $Class,
                'username' => $memberListRow['username'],
                'galaxy' => $memberListRow['galaxy'],
                'system' => $memberListRow['system'],
                'planet' => $memberListRow['planet'],
                'register_time' => $memberListRow['ally_register_time'],
                'points' => $memberListRow['total_points'],
                'rankName' => $memberListRow['ally_rankName'],
                'onlinetime' => floor((TIMESTAMP - $memberListRow['onlinetime']) / 60),
            ];
        }

        $this->assign([
            'memberList' => $memberList,
            'mainList' => $this->getMainMember(),
            'wingList' => $this->getWingMember(),
            'al_users_list' => sprintf($LNG['al_users_list'], count($memberList)),
            'timezone' => $USER['timezone'],
            'ShortStatus' => [
                'vacation' => $LNG['gl_short_vacation'],
                'banned' => $LNG['gl_short_ban'],
                'inactive' => $LNG['gl_short_inactive'],
                'longinactive' => $LNG['gl_short_long_inactive'],
                'noob' => $LNG['gl_short_newbie'],
                'strong' => $LNG['gl_short_strong'],
                'enemy' => $LNG['gl_short_enemy'],
                'friend' => $LNG['gl_short_friend'],
                'member' => $LNG['gl_short_member'],
            ],
        ]);

        $this->display('page.alliance.memberList.tpl');
    }

    private function getMainMember()
    {
        $USER =& Singleton()->USER;
        $LNG =& Singleton()->LNG;
        $db = Database::get();

        $sql = "SELECT a.id, a.ally_name, a.ally_tag, ally_owner, ally_owner_range FROM %%DIPLO%% d
            JOIN %%ALLIANCE%% a ON a.id = d.owner_1
            WHERE d.owner_2 = :AllianceID AND d.level = 1 AND accept = 1;";
        $mainData = $db->select($sql, [
            ':AllianceID' => $this->allianceData['id']
        ]);

        $mainList = [];

        if ($mainData) {
            foreach ($mainData as $main) {
                $sql = "SELECT rankID, rankName FROM %%ALLIANCE_RANK%% WHERE allianceId = :AllianceID";
                $rankResult = $db->select($sql, [
                    ':AllianceID' => $main['id']
                ]);

                foreach ($rankResult as $rankRow) {
                    $rankList[$rankRow['rankID']] = $rankRow['rankName'];
                }

                $mainMemberList = [];

                $sql = "SELECT DISTINCT u.id, u.username,u.galaxy, u.system, u.planet, u.banaday, u.urlaubs_modus,"
                    . " u.ally_register_time, u.onlinetime, u.ally_rank_id, s.total_points FROM %%USERS%% u"
                    . " LEFT JOIN %%STATPOINTS%% as s ON s.stat_type = '1' AND s.id_owner = u.id WHERE ally_id = :AllianceID;";
                $mainListResult = $db->select($sql, [
                    ':AllianceID' => $main['id']
                ]);

                try {
                    $USER += $db->selectSingle(
                        'SELECT total_points FROM %%STATPOINTS%% WHERE id_owner = :userId AND stat_type = :statType',
                        [
                            ':userId' => $USER['id'],
                            ':statType' => 1,
                        ]
                    ) ?: ['total_points' => 0];
                } catch (Exception $e) {
                    $USER['total_points'] = 0;
                }

                foreach ($mainListResult as $mainListRow) {
                    $IsNoobProtec = CheckNoobProtec($USER, $mainListRow, $mainListRow);
                    $Class = userStatus($mainListRow, $IsNoobProtec);

                    if ($main['ally_owner'] == $mainListRow['id']) {
                        $mainListRow['ally_rankName'] = empty($main['ally_owner_range'])
                            ? $LNG['al_founder_rank_text'] : $main['ally_owner_range'];
                    } elseif ($mainListRow['ally_rank_id'] != 0 && isset($rankList[$mainListRow['ally_rank_id']])) {
                        $mainListRow['ally_rankName'] = $rankList[$mainListRow['ally_rank_id']];
                    } else {
                        $mainListRow['ally_rankName'] = $LNG['al_new_member_rank_text'];
                    }

                    $mainMemberList[$mainListRow['id']] = [
                        'class' => $Class,
                        'username' => $mainListRow['username'],
                        'galaxy' => $mainListRow['galaxy'],
                        'system' => $mainListRow['system'],
                        'planet' => $mainListRow['planet'],
                        'register_time' => $mainListRow['ally_register_time'],
                        'points' => $mainListRow['total_points'],
                        'rankName' => $mainListRow['ally_rankName'],
                        'onlinetime' => floor((TIMESTAMP - $mainListRow['onlinetime']) / 60),
                    ];
                }
                $mainList[$main['id']]['header'] = sprintf($LNG['al_users_list_main'], count($mainMemberList), '['.$main['ally_tag'].'] '.$main['ally_name']);
                $mainList[$main['id']]['mainData'] = $main;
                $mainList[$main['id']]['memberList'] = $mainMemberList;
            }
        }
        return $mainList;
    }

    private function getWingMember()
    {
        $USER =& Singleton()->USER;
        $LNG =& Singleton()->LNG;
        $db = Database::get();

        $sql = "SELECT a.id, a.ally_name, a.ally_tag, ally_owner, ally_owner_range FROM %%DIPLO%% d
            JOIN %%ALLIANCE%% a ON a.id = d.owner_2
            WHERE d.owner_1 = :AllianceID AND d.level = 1 AND accept = 1;";
        $wingData = $db->select($sql, [
            ':AllianceID' => $this->allianceData['id']
        ]);

        $wingList = [];

        if ($wingData) {
            foreach ($wingData as $wing) {
                $sql = "SELECT rankID, rankName FROM %%ALLIANCE_RANK%% WHERE allianceId = :AllianceID";
                $rankResult = $db->select($sql, [
                    ':AllianceID' => $wing['id']
                ]);

                foreach ($rankResult as $rankRow) {
                    $rankList[$rankRow['rankID']] = $rankRow['rankName'];
                }

                $wingMemberList = [];

                $sql = "SELECT DISTINCT u.id, u.username,u.galaxy, u.system, u.planet, u.banaday, u.urlaubs_modus,"
                    . " u.ally_register_time, u.onlinetime, u.ally_rank_id, s.total_points FROM %%USERS%% u"
                    . " LEFT JOIN %%STATPOINTS%% as s ON s.stat_type = '1' AND s.id_owner = u.id WHERE ally_id = :AllianceID;";
                $wingListResult = $db->select($sql, [
                    ':AllianceID' => $wing['id']
                ]);

                try {
                    $USER += $db->selectSingle(
                        'SELECT total_points FROM %%STATPOINTS%% WHERE id_owner = :userId AND stat_type = :statType',
                        [
                            ':userId' => $USER['id'],
                            ':statType' => 1,
                        ]
                    ) ?: ['total_points' => 0];
                } catch (Exception $e) {
                    $USER['total_points'] = 0;
                }

                foreach ($wingListResult as $wingListRow) {
                    $IsNoobProtec = CheckNoobProtec($USER, $wingListRow, $wingListRow);
                    $Class = userStatus($wingListRow, $IsNoobProtec);

                    if ($wing['ally_owner'] == $wingListRow['id']) {
                        $wingListRow['ally_rankName'] = empty($wing['ally_owner_range'])
                            ? $LNG['al_founder_rank_text'] : $wing['ally_owner_range'];
                    } elseif ($wingListRow['ally_rank_id'] != 0 && isset($rankList[$wingListRow['ally_rank_id']])) {
                        $wingListRow['ally_rankName'] = $rankList[$wingListRow['ally_rank_id']];
                    } else {
                        $wingListRow['ally_rankName'] = $LNG['al_new_member_rank_text'];
                    }

                    $wingMemberList[$wingListRow['id']] = [
                        'class' => $Class,
                        'username' => $wingListRow['username'],
                        'galaxy' => $wingListRow['galaxy'],
                        'system' => $wingListRow['system'],
                        'planet' => $wingListRow['planet'],
                        'register_time' => $wingListRow['ally_register_time'],
                        'points' => $wingListRow['total_points'],
                        'rankName' => $wingListRow['ally_rankName'],
                        'onlinetime' => floor((TIMESTAMP - $wingListRow['onlinetime']) / 60),
                    ];
                }
                $wingList[$wing['id']]['header'] = sprintf($LNG['al_users_list_wing'], count($wingMemberList), '['.$wing['ally_tag'].'] '.$wing['ally_name']);
                $wingList[$wing['id']]['wingData'] = $wing;
                $wingList[$wing['id']]['memberList'] = $wingMemberList;
            }
        }
        return $wingList;
    }

    public function close()
    {
        $LNG =& Singleton()->LNG;
        $USER =& Singleton()->USER;
        $db = Database::get();

        $receivers = $this->getMessageReceivers($this->allianceData['id'], false, true);

        foreach ($receivers as $receiver) {
            if ($receiver['id'] == $USER['id']) {
                continue;
            }
            $lang = getLanguage($receiver['lang']);

            $applyMessage = sprintf(
                $lang['al_leaving_msg'],
                $USER['id'],
                $USER['username'],
                $USER['username']
            );

            PlayerUtil::sendMessage(
                $receiver['id'],
                0,
                $lang['al_alliance'],
                2,
                $lang['al_leaving'],
                $applyMessage,
                TIMESTAMP
            );
        }

        $sql = "UPDATE %%USERS%% SET ally_id = 0, ally_register_time = 0, ally_register_time = 5 WHERE id = :UserID;";
        $db->update($sql, [
            ':UserID' => $USER['id']
        ]);

        $sql = "UPDATE %%STATPOINTS%% SET id_ally = 0 WHERE id_owner = :UserID AND stat_type = 1;";
        $db->update($sql, [
            ':UserID' => $USER['id']
        ]);

        $sql = "UPDATE %%ALLIANCE%% SET ally_members = (SELECT COUNT(*) FROM %%USERS%% WHERE ally_id = :AllianceID)"
            . " WHERE id = :AllianceID;";
        $db->update($sql, [
            ':AllianceID' => $this->allianceData['id']
        ]);

        $this->printMessage(sprintf($LNG['al_leave_sucess'], $this->allianceData['ally_name']), [[
            'label' => $LNG['sys_back'],
            'url' => '?page=alliance'
        ]]);
    }

    public function circular()
    {
        $LNG =& Singleton()->LNG;
        $USER =& Singleton()->USER;
        if (!$this->rights['ROUNDMAIL']) {
            $this->redirectToHome();
        }

        $action = HTTP::_GP('action', '');

        if ($action == "send") {
            $rankId = HTTP::_GP('rankID', 0);
            $subject = HTTP::_GP('subject', '', true);
            $text = HTTP::_GP('text', $LNG['mg_no_subject'], true);

            if (empty($text)) {
                $this->sendJSON(['message' => $LNG['mg_empty_text'], 'error' => true]);
            }

            $db = Database::get();

            if ($rankId == 0) {
                $sql = 'SELECT id, username FROM %%USERS%% WHERE ally_id = :AllianceID;';
                $sendUsersResult = $db->select($sql, [
                    ':AllianceID' => $this->allianceData['id'],
                ]);
            } else {
                $sql = 'SELECT id, username FROM %%USERS%% WHERE ally_id = :AllianceID AND ally_rank_id = :RankID;';
                $sendUsersResult = $db->select($sql, [
                    ':AllianceID' => $this->allianceData['id'],
                    ':RankID' => $rankId
                ]);
            }

            $sendList = $LNG['al_circular_sended'];
            $title = $LNG['al_circular_alliance'] . $this->allianceData['ally_tag'];
            $text = sprintf($LNG['al_circular_front_text'], $USER['username']) . "\r\n" . $text;

            foreach ($sendUsersResult as $sendUsersRow) {
                PlayerUtil::sendMessage(
                    $sendUsersRow['id'],
                    $USER['id'],
                    $title,
                    2,
                    $subject,
                    makebr($text),
                    TIMESTAMP
                );
                $sendList .= "\n" . $sendUsersRow['username'];
            }

            $this->sendJSON(['message' => $sendList, 'error' => false]);
        }

        $this->initTemplate();
        $this->setWindow('popup');
        $RangeList[] = $LNG['al_all_players'];

        if (is_array($this->ranks)) {
            foreach ($this->ranks as $id => $array) {
                $RangeList[$id + 1] = $array['name'];
            }
        }

        $this->assign([
            'RangeList' => $RangeList,
        ]);

        $this->display('page.alliance.circular.tpl');
    }

    public function admin()
    {
        $LNG =& Singleton()->LNG;

        $action = HTTP::_GP('action', 'overview');
        $methodName = 'admin' . ucwords($action);

        if (!is_callable([$this, $methodName])) {
            ShowErrorPage::printError($LNG['page_doesnt_exist']);
        }

        $this->{$methodName}();
    }

    protected function adminOverview()
    {
        $LNG =& Singleton()->LNG;
        $send = HTTP::_GP('send', 0);
        $textMode = HTTP::_GP('textMode', 'external');

        if ($send) {
            $db = Database::get();

            $this->allianceData['ally_owner_range'] = HTTP::_GP('owner_range', '', true);
            $this->allianceData['ally_web'] = filter_var(HTTP::_GP('web', ''), FILTER_VALIDATE_URL);
            $this->allianceData['ally_image'] = filter_var(HTTP::_GP('image', ''), FILTER_VALIDATE_URL);
            $this->allianceData['ally_request_notallow'] = HTTP::_GP('request_notallow', 0);
            $this->allianceData['ally_max_members'] = max(
                HTTP::_GP('ally_max_members', ''),
                $this->allianceData['ally_members']
            );
            $this->allianceData['ally_request_min_points'] = HTTP::_GP('request_min_points', 0);
            $this->allianceData['ally_stats'] = HTTP::_GP('stats', 0);
            $this->allianceData['ally_diplo'] = HTTP::_GP('diplo', 0);

            $new_ally_tag = HTTP::_GP('ally_tag', $this->allianceData['ally_tag'], UTF8_SUPPORT);
            $new_ally_name = HTTP::_GP('ally_name', $this->allianceData['ally_name'], UTF8_SUPPORT);

            if (!PlayerUtil::isNameValid($new_ally_tag) || !PlayerUtil::isNameValid($new_ally_name)) {
                $this->printMessage($LNG['al_newname_specialchar'], [[
                    'label' => $LNG['sys_back'],
                    'url' => '?page=alliance&mode=admin'
                ]]);
            }

            if (!empty($new_ally_tag) && $this->allianceData['ally_tag'] != $new_ally_tag) {
                $sql = "SELECT COUNT(*) as count FROM %%ALLIANCE%% WHERE ally_universe = :universe"
                    . " AND ally_tag = :NewAllianceTag;";
                $allianceCount = $db->selectSingle($sql, [
                    ':universe' => Universe::current(),
                    ':NewAllianceTag' => $new_ally_tag
                ], 'count');

                if ($allianceCount != 0) {
                    $this->printMessage(sprintf($LNG['al_already_exists'], $new_ally_tag), [[
                        'label' => $LNG['sys_back'],
                        'url' => 'game.php?page=alliance&mode=admin'
                    ]]);
                } else {
                    $this->allianceData['ally_tag'] = $new_ally_tag;
                }
            }

            if (!empty($new_ally_name) && $this->allianceData['ally_name'] != $new_ally_name) {
                $sql = "SELECT COUNT(*) as count FROM %%ALLIANCE%% WHERE ally_universe = :universe"
                    . " AND ally_name = :NewAllianceName;";
                $allianceCount = $db->selectSingle($sql, [
                    ':universe' => Universe::current(),
                    ':NewAllianceName' => $new_ally_name
                ], 'count');

                if ($allianceCount != 0) {
                    $this->printMessage(sprintf($LNG['al_already_exists'], $new_ally_name), [[
                        'label' => $LNG['sys_back'],
                        'url' => 'game.php?page=alliance&mode=admin'
                    ]]);
                } else {
                    $this->allianceData['ally_name'] = $new_ally_name;
                }
            }

            if (
                $this->allianceData['ally_request_notallow'] != 0
                && $this->allianceData['ally_request_notallow'] != 1
            ) {
                $this->allianceData['ally_request_notallow'] = 0;
            }

            $text = HTTP::_GP('text', '', true);
            $textMode = HTTP::_GP('textMode', 'external');

            $textSQL = "";

            switch ($textMode) {
                case 'external':
                    $textSQL = "ally_description = :text, ";
                    break;
                case 'internal':
                    $textSQL = "ally_text = :text, ";
                    break;
                case 'apply':
                    $textSQL = "ally_request = :text, ";
                    break;
            }

            $sql = "UPDATE %%ALLIANCE%% SET
			" . $textSQL . "
			ally_tag = :AllianceTag,
			ally_name = :AllianceName,
			ally_owner_range = :AllianceOwnerRange,
			ally_image = :AllianceImage,
			ally_web = :AllianceWeb,
			ally_request_notallow = :AllianceRequestNotAllow,
			ally_max_members = :AllianceMaxMember,
			ally_request_min_points = :AllianceRequestMinPoints,
			ally_stats = :AllianceStats,
			ally_diplo = :AllianceDiplo
			WHERE id = :AllianceID;";

            $db->update($sql, [
                ':AllianceTag' => $this->allianceData['ally_tag'],
                ':AllianceName' => $this->allianceData['ally_name'],
                ':AllianceOwnerRange' => $this->allianceData['ally_owner_range'],
                ':AllianceImage' => $this->allianceData['ally_image'],
                ':AllianceWeb' => $this->allianceData['ally_web'],
                ':AllianceRequestNotAllow' => $this->allianceData['ally_request_notallow'],
                ':AllianceMaxMember' => $this->allianceData['ally_max_members'],
                ':AllianceRequestMinPoints' => $this->allianceData['ally_request_min_points'],
                ':AllianceStats' => $this->allianceData['ally_stats'],
                ':AllianceDiplo' => $this->allianceData['ally_diplo'],
                ':AllianceID' => $this->allianceData['id'],
                ':text' => $text
            ]);
        } else {
            switch ($textMode) {
                case 'internal':
                    $text = $this->allianceData['ally_text'];
                    break;
                case 'apply':
                    $text = $this->allianceData['ally_request'];
                    break;
                default:
                    $text = $this->allianceData['ally_description'];
                    break;
            }
        }

        $this->assign([
            'RequestSelector' => [0 => $LNG['al_requests_allowed'], 1 => $LNG['al_requests_not_allowed']],
            'YesNoSelector' => [1 => $LNG['al_go_out_yes'], 0 => $LNG['al_go_out_no']],
            'textMode' => $textMode,
            'text' => $text,
            'ally_tag' => $this->allianceData['ally_tag'],
            'ally_name' => $this->allianceData['ally_name'],
            'ally_web' => $this->allianceData['ally_web'],
            'ally_image' => $this->allianceData['ally_image'],
            'ally_request_notallow' => $this->allianceData['ally_request_notallow'],
            'ally_members' => $this->allianceData['ally_members'],
            'ally_max_members' => $this->allianceData['ally_max_members'],
            'ally_request_min_points' => $this->allianceData['ally_request_min_points'],
            'ally_owner_range' => $this->allianceData['ally_owner_range'],
            'ally_stats_data' => $this->allianceData['ally_stats'],
            'ally_diplo_data' => $this->allianceData['ally_diplo'],
        ]);

        $this->display('page.alliance.admin.overview.tpl');
    }

    protected function adminClose()
    {
        $USER =& Singleton()->USER;
        if ($this->allianceData['ally_owner'] == $USER['id']) {
            $db = Database::get();

            $sql = "UPDATE %%USERS%% SET ally_id = '0' WHERE ally_id = :AllianceID;";
            $db->update($sql, [
                ':AllianceID' => $this->allianceData['id']
            ]);

            $sql = "UPDATE %%STATPOINTS%% SET id_ally = '0' WHERE id_ally = :AllianceID;";
            $db->update($sql, [
                ':AllianceID' => $this->allianceData['id']
            ]);

            $sql = "DELETE FROM %%STATPOINTS%% WHERE id_owner = :AllianceID AND stat_type = 2;";
            $db->delete($sql, [
                ':AllianceID' => $this->allianceData['id']
            ]);

            $sql = "DELETE FROM %%ALLIANCE%% WHERE id = :AllianceID;";
            $db->delete($sql, [
                ':AllianceID' => $this->allianceData['id']
            ]);

            $sql = "DELETE FROM %%ALLIANCE_REQUEST%% WHERE allianceId = :AllianceID;";
            $db->delete($sql, [
                ':AllianceID' => $this->allianceData['id']
            ]);

            $sql = "DELETE FROM %%DIPLO%% WHERE owner_1 = :AllianceID OR owner_2 = :AllianceID;";
            $db->delete($sql, [
                ':AllianceID' => $this->allianceData['id']
            ]);
        }

        $this->redirectToHome();
    }

    protected function adminTransfer()
    {
        $USER =& Singleton()->USER;

        if ($this->allianceData['ally_owner'] != $USER['id']) {
            $this->redirectToHome();
        }

        $db = Database::get();

        $postleader = HTTP::_GP('newleader', 0);
        if (!empty($postleader)) {
            $sql = "SELECT ally_rank_id FROM %%USERS%% WHERE id = :LeaderID;";
            $Rank = $db->selectSingle($sql, [
                ':LeaderID' => $postleader
            ]);

            $sql = "UPDATE %%USERS%% SET ally_rank_id = :AllyRank WHERE id = :UserID;";
            $db->update($sql, [
                ':UserID' => $USER['id'],
                ':AllyRank' => $Rank['ally_rank_id']
            ]);

            $sql = "UPDATE %%USERS%% SET ally_rank_id = 0 WHERE id = :LeaderID;";
            $db->update($sql, [
                ':LeaderID' => $postleader
            ]);

            $sql = "UPDATE %%ALLIANCE%% SET ally_owner = :LeaderID WHERE id = :AllianceID;";
            $db->update($sql, [
                ':LeaderID' => $postleader,
                ':AllianceID' => $this->allianceData['id']
            ]);

            $this->redirectToHome();
        } else {
            $sql = "SELECT u.id, r.rankName, u.username FROM %%USERS%% u INNER JOIN %%ALLIANCE_RANK%% r"
                . " ON r.rankID = u.ally_rank_id AND r.TRANSFER = 1 WHERE u.ally_id = :allianceId"
                . " AND id != :allianceOwner;";
            $transferUserResult = $db->select($sql, [
                ':allianceOwner' => $this->allianceData['ally_owner'],
                ':allianceId' => $this->allianceData['id']
            ]);

            $transferUserList = [];

            foreach ($transferUserResult as $transferUserRow) {
                $transferUserList[$transferUserRow['id']] = $transferUserRow['username'] . " ["
                    . $transferUserRow['rankName'] . "]";
            }

            $this->assign([
                'transferUserList' => $transferUserList,
            ]);

            $this->display('page.alliance.admin.transfer.tpl');
        }
    }

    protected function adminManageApply()
    {
        $LNG =& Singleton()->LNG;
        $USER =& Singleton()->USER;
        if (!$this->rights['SEEAPPLY'] || !$this->rights['MANAGEAPPLY']) {
            $this->redirectToHome();
        }

        $db = Database::get();

        $sql = "SELECT applyID, u.username, r.time FROM %%ALLIANCE_REQUEST%% r INNER JOIN %%USERS%% u"
            . " ON r.userId = u.id WHERE r.allianceId = :allianceId;";
        $applyResult = $db->select($sql, [
            ':allianceId' => $this->allianceData['id']
        ]);

        $applyList = [];

        foreach ($applyResult as $applyRow) {
            $applyList[] = [
                'username' => $applyRow['username'],
                'id' => $applyRow['applyID'],
                'time' => _date($LNG['php_tdformat'], $applyRow['time'], $USER['timezone']),
            ];
        }

        $this->assign([
            'applyList' => $applyList,
        ]);

        $this->display('page.alliance.admin.manageApply.tpl');
    }

    protected function adminDetailApply()
    {
        $LNG =& Singleton()->LNG;
        $USER =& Singleton()->USER;
        if (!$this->rights['SEEAPPLY'] || !$this->rights['MANAGEAPPLY']) {
            $this->redirectToHome();
        }

        $id = HTTP::_GP('id', 0);

        $db = Database::get();

        $sql = 'SELECT
			r.`applyID`,
			r.`time`,
			r.`text`,
			u.`username`,
			u.`register_time`,
			u.`onlinetime`,
			u.`galaxy`,
			u.`system`,
			u.`planet`,
			CONCAT_WS(\':\', u.`galaxy`, u.`system`, u.`planet`) AS `coordinates`,
			@total_fights := u.`wons` + u.`loos` + u.`draws`,
			@total_fights_percentage := @total_fights / 100,
			@total_fights AS `total_fights`,
			u.`wons`,
			ROUND(u.`wons` / @total_fights_percentage, 2) AS `wons_percentage`,
			u.`loos`,
			ROUND(u.`loos` / @total_fights_percentage, 2) AS `loos_percentage`,
			u.`draws`,
			ROUND(u.`draws` / @total_fights_percentage, 2) AS `draws_percentage`,
			u.`kbmetal`,
			u.`kbcrystal`,
			u.`lostunits`,
			u.`desunits`,
			stat.`tech_rank`,
			stat.`tech_points`,
			stat.`build_rank`,
			stat.`build_points`,
			stat.`defs_rank`,
			stat.`defs_points`,
			stat.`fleet_rank`,
			stat.`fleet_points`,
			stat.`total_rank`,
			stat.`total_points`,
			p.`name`
		FROM
			%%ALLIANCE_REQUEST%% AS r
		LEFT JOIN
			%%USERS%% AS u ON r.userId = u.id
		INNER JOIN
			%%STATPOINTS%% AS stat ON r.userId = stat.id_owner
		LEFT JOIN
			%%PLANETS%% AS p ON p.id = u.id_planet
		WHERE
			applyID = :applyID;';

        $applyDetail = $db->selectSingle($sql, [
            ':applyID' => $id
        ]);

        if (empty($applyDetail)) {
            $this->printMessage($LNG['al_apply_not_exists'], [[
                'label' => $LNG['sys_back'],
                'url' => 'game.php?page=alliance&mode=admin&action=manageApply'
            ]]);
        }

        $applyDetail['text'] = nl2br($applyDetail['text']);
        $applyDetail['kbmetal'] = pretty_number($applyDetail['kbmetal']);
        $applyDetail['kbcrystal'] = pretty_number($applyDetail['kbcrystal']);
        $applyDetail['lostunits'] = pretty_number($applyDetail['lostunits']);
        $applyDetail['desunits'] = pretty_number($applyDetail['desunits']);

        $this->assign([
            'applyDetail' => $applyDetail,
            'apply_time' => _date($LNG['php_tdformat'], $applyDetail['time'], $USER['timezone']),
            'register_time' => _date($LNG['php_tdformat'], $applyDetail['register_time'], $USER['timezone']),
            'onlinetime' => _date($LNG['php_tdformat'], $applyDetail['onlinetime'], $USER['timezone']),
        ]);

        $this->display('page.alliance.admin.detailApply.tpl');
    }

    protected function adminSendAnswerToApply()
    {
        $USER =& Singleton()->USER;
        if (!$this->rights['SEEAPPLY'] || !$this->rights['MANAGEAPPLY']) {
            $this->redirectToHome();
        }

        $db = Database::get();

        $text = makebr(HTTP::_GP('text', '', true));
        $answer = HTTP::_GP('answer', '');
        $applyID = HTTP::_GP('id', 0);

        $sql = "SELECT userId FROM %%ALLIANCE_REQUEST%% WHERE applyID = :applyID;";
        $userId = $db->selectSingle($sql, [
            ':applyID' => $applyID
        ], 'userId');

        // only if alliance request still exist
        if ($userId) {
            $LNG = getLanguage(null, $userId);
            if ($answer == 'yes') {
                $sql = "DELETE FROM %%ALLIANCE_REQUEST%% WHERE applyID = :applyID";
                $db->delete($sql, [
                    ':applyID' => $applyID
                ]);

                $sql = "UPDATE %%USERS%% SET ally_id = :allianceId, ally_register_time = :time, ally_rank_id = 0"
                    . " WHERE id = :userId;";
                $db->update($sql, [
                    ':allianceId' => $this->allianceData['id'],
                    ':time' => TIMESTAMP,
                    ':userId' => $userId
                ]);

                $sql = "UPDATE %%STATPOINTS%% SET id_ally = :allianceId WHERE id_owner = :userId AND stat_type = 1;";
                $db->update($sql, [
                    ':allianceId' => $this->allianceData['id'],
                    ':userId' => $userId
                ]);

                $sql = "UPDATE %%ALLIANCE%% SET ally_members = (SELECT COUNT(*) FROM %%USERS%%"
                    . " WHERE ally_id = :allianceId) WHERE id = :allianceId;";
                $db->update($sql, [
                    ':allianceId' => $this->allianceData['id'],
                ]);

                $text = $LNG['al_hi_the_alliance'] . $this->allianceData['ally_name'] . $LNG['al_has_accepted'] . $text;
                $subject = $LNG['al_you_was_acceted'] . $this->allianceData['ally_name'];
            } else {
                $sql = "DELETE FROM %%ALLIANCE_REQUEST%% WHERE applyID = :applyID";
                $db->delete($sql, [
                    ':applyID' => $applyID
                ]);

                $text = $LNG['al_hi_the_alliance'] . $this->allianceData['ally_name'] . $LNG['al_has_declined'] . $text;
                $subject = $LNG['al_you_was_declined'] . $this->allianceData['ally_name'];
            }

            $senderName = $LNG['al_the_alliance'] . $this->allianceData['ally_name'] . ' ['
                . $this->allianceData['ally_tag'] . ']';
            PlayerUtil::sendMessage($userId, $USER['id'], $senderName, 2, $subject, $text, TIMESTAMP);
        }
        $this->redirectTo('game.php?page=alliance&mode=admin&action=manageApply');
    }

    protected function adminPermissions()
    {
        if (!$this->rights['RANKS']) {
            $this->redirectToHome();
        }

        $sql = "SELECT * FROM %%ALLIANCE_RANK%% WHERE allianceId = :allianceId;";
        $rankResult = Database::get()->select($sql, [
            ':allianceId' => $this->allianceData['id']
        ]);

        $rankList = [];
        foreach ($rankResult as $rankRow) {
            $rankList[$rankRow['rankID']] = $rankRow;
        }

        $availableRanks = [];
        foreach ($this->availableRanks as $rankId => $rankName) {
            if ($this->rights[$rankName]) {
                $availableRanks[$rankId] = $rankName;
            }
        }

        $this->assign([
            'rankList' => $rankList,
            'ownRights' => $this->rights,
            'availableRanks' => $availableRanks,
        ]);

        $this->display('page.alliance.admin.permissions.tpl');
    }

    protected function adminPermissionsSend()
    {
        $LNG =& Singleton()->LNG;
        if (!$this->rights['RANKS']) {
            $this->redirectToHome();
        }

        $newRank = HTTP::_GP('newrank', [], true);
        $delete = HTTP::_GP('deleteRank', 0);
        $rankData = HTTP::_GP('rank', []);

        $db = Database::get();

        if (!empty($newRank['rankName'])) {
            if (!PlayerUtil::isNameValid($newRank['rankName'])) {
                $this->printMessage($LNG['al_invalid_rank_name'], [[
                    'label' => $LNG['sys_back'],
                    'url' => '?page=alliance&mode=admin&action=permission'
                ]]);
            }

            $sql = 'INSERT INTO %%ALLIANCE_RANK%% SET rankName = :rankName, allianceID = :allianceID';
            $params = [
                ':rankName' => $newRank['rankName'],
                ':allianceID' => $this->allianceData['id'],
            ];

            unset($newRank['rankName']);

            foreach ($newRank as $key => $value) {
                if (isset($this->availableRanks[$key]) && $this->rights[$this->availableRanks[$key]]) {
                    $sql .= ', `' . $this->availableRanks[$key] . '` = :' . $this->availableRanks[$key];
                    $params[':' . $this->availableRanks[$key]] = $value == 1 ? 1 : 0;
                }
            }

            $db->insert($sql, $params);
        } else {
            if (!empty($delete)) {
                $sql = "DELETE FROM %%ALLIANCE_RANK%% WHERE rankID = :rankID AND allianceId = :allianceId;";
                $db->delete($sql, [
                    ':allianceId' => $this->allianceData['id'],
                    ':rankID' => $delete
                ]);

                $sql = "UPDATE %%USERS%% SET ally_rank_id = 0 WHERE ally_rank_id = :rankID AND ally_id = :allianceId;";
                $db->update($sql, [
                    ':allianceId' => $this->allianceData['id'],
                    ':rankID' => $delete
                ]);
            } else {
                foreach ($rankData as $rankId => $rowData) {
                    $sql = 'UPDATE %%ALLIANCE_RANK%% SET rankName = :rankName';
                    $params = [
                        ':rankName' => $rowData['rankName'],
                        ':allianceID' => $this->allianceData['id'],
                        ':rankId' => $rankId
                    ];

                    unset($rowData['rankName']);

                    foreach ($this->availableRanks as $key => $value) {
                        if (isset($this->availableRanks[$key]) && $this->rights[$this->availableRanks[$key]]) {
                            $sql .= ', `' . $this->availableRanks[$key] . '` = :' . $this->availableRanks[$key];
                            $params[':' . $this->availableRanks[$key]] = (isset($rowData[$key])) == 1 ? 1 : 0;
                        }
                    }

                    $sql .= ' WHERE rankID = :rankId AND allianceID = :allianceID';

                    $db->update($sql, $params);
                }
            }
        }

        $this->redirectTo('game.php?page=alliance&mode=admin&action=permissions');
    }

    protected function adminMembers()
    {
        $USER =& Singleton()->USER;
        $LNG =& Singleton()->LNG;
        if (!$this->rights['MANAGEUSERS']) {
            $this->redirectToHome();
        }

        $db = Database::get();

        $sql = "SELECT rankID, rankName FROM %%ALLIANCE_RANK%% WHERE allianceId = :allianceId;";
        $rankResult = $db->select($sql, [
            ':allianceId' => $this->allianceData['id'],
        ]);

        $rankList = [$LNG['al_new_member_rank_text']];
        $rankSelectList = $rankList;

        foreach ($rankResult as $rankRow) {
            $hasRankRight = true;
            foreach ($this->availableRanks as $rankName) {
                if (!$this->rights[$rankName]) {
                    $hasRankRight = false;
                    break;
                }
            }

            if ($hasRankRight) {
                $rankSelectList[$rankRow['rankID']] = $rankRow['rankName'];
            }

            $rankList[$rankRow['rankID']] = $rankRow['rankName'];
        }

        $sql = "SELECT DISTINCT u.id, u.username, u.galaxy, u.system, u.planet, u.banaday, u.urlaubs_modus,
            u.ally_register_time, u.onlinetime, u.ally_rank_id, s.total_points
		    FROM %%USERS%% u
		    LEFT JOIN %%STATPOINTS%% as s ON s.stat_type = '1' AND s.id_owner = u.id
		    WHERE ally_id = :allianceId;";

        $memberListResult = $db->select($sql, [
            ':allianceId' => $this->allianceData['id'],
        ]);

        $memberList = [];

        try {
            $USER += $db->selectSingle(
                'SELECT total_points FROM %%STATPOINTS%% WHERE id_owner = :userId AND stat_type = :statType',
                [
                    ':userId' => $USER['id'],
                    ':statType' => 1,
                ]
            );
        } catch (Exception $e) {
            $USER['total_points'] = 0;
        }

        foreach ($memberListResult as $memberListRow) {
            $IsNoobProtec = CheckNoobProtec($USER, $memberListRow, $memberListRow);
            $Class = userStatus($memberListRow, $IsNoobProtec);

            if ($this->allianceData['ally_owner'] == $memberListRow['id']) {
                $memberListRow['ally_rank_id'] = -1;
            }

            $memberList[$memberListRow['id']] = [
                'class' => $Class,
                'username' => $memberListRow['username'],
                'galaxy' => $memberListRow['galaxy'],
                'system' => $memberListRow['system'],
                'planet' => $memberListRow['planet'],
                'register_time' => $memberListRow['ally_register_time'],
                'points' => $memberListRow['total_points'],
                'rankID' => $memberListRow['ally_rank_id'],
                'onlinetime' => floor((TIMESTAMP - $memberListRow['onlinetime']) / 60),
                'kickQuestion' => sprintf($LNG['al_kick_player'], $memberListRow['username'])
            ];
        }

        $this->assign([
            'memberList' => $memberList,
            'rankList' => $rankList,
            'rankSelectList' => $rankSelectList,
            'founder' => empty($this->allianceData['ally_owner_range'])
                ? $LNG['al_founder_rank_text'] : $this->allianceData['ally_owner_range'],
            'al_users_list' => sprintf($LNG['al_users_list'], count($memberList)),
            'canKick' => $this->rights['KICK'],
            'ShortStatus' => [
                'vacation' => $LNG['gl_short_vacation'],
                'banned' => $LNG['gl_short_ban'],
                'inactive' => $LNG['gl_short_inactive'],
                'longinactive' => $LNG['gl_short_long_inactive'],
                'noob' => $LNG['gl_short_newbie'],
                'strong' => $LNG['gl_short_strong'],
                'enemy' => $LNG['gl_short_enemy'],
                'friend' => $LNG['gl_short_friend'],
                'member' => $LNG['gl_short_member'],
            ],
        ]);

        $this->display('page.alliance.admin.members.tpl');
    }

    protected function adminRank()
    {
        $LNG =& Singleton()->LNG;
        if (!$this->rights['MANAGEUSERS']) {
            $this->redirectToHome();
        }

        $userRanks = HTTP::_GP('rank', []);

        $db = Database::get();

        $sql = 'SELECT rankID, ' . implode(', ', $this->availableRanks)
            . ' FROM %%ALLIANCE_RANK%% WHERE allianceID = :allianceId;';
        $rankResult = $db->select($sql, [
            ':allianceId' => $this->allianceData['id']
        ]);
        $rankList = [];
        $rankList[0] = array_combine($this->availableRanks, array_fill(0, count($this->availableRanks), true));

        foreach ($rankResult as $rankRow) {
            $hasRankRight = true;
            foreach ($this->availableRanks as $rankName) {
                if (!$this->rights[$rankName]) {
                    $hasRankRight = false;
                    break;
                }
            }

            if ($hasRankRight) {
                $rankList[$rankRow['rankID']] = $rankRow;
            }
        }

        foreach ($userRanks as $userId => $rankId) {
            if ($userId == $this->allianceData['ally_owner'] || !isset($rankList[$rankId])) {
                continue;
            }

            $sql = 'UPDATE %%USERS%% SET ally_rank_id = :rankID WHERE id = :userId AND ally_id = :allianceId;';
            $db->update($sql, [
                ':allianceId' => $this->allianceData['id'],
                ':rankID' => (int)$rankId,
                ':userId' => (int)$userId
            ]);
        }

        $this->sendJSON($LNG['fl_shortcut_saved']);
    }

    protected function adminMembersKick()
    {
        if (!$this->rights['KICK']) {
            $this->redirectToHome();
        }

        $db = Database::get();

        $id = HTTP::_GP('id', 0);

        $sql = "SELECT ally_id FROM %%USERS%% WHERE id = :id;";
        $kickUserAllianceId = $db->selectSingle($sql, [
            ':id' => $id
        ], 'ally_id');

        # Check, if user is in alliance, see #205
        if (empty($kickUserAllianceId) || $kickUserAllianceId != $this->allianceData['id']) {
            $this->redirectToHome();
        }

        $sql = "UPDATE %%USERS%% SET ally_id = 0, ally_register_time = 0, ally_rank_id = 0 WHERE id = :id;";
        $db->update($sql, [
            ':id' => $id
        ]);

        $sql = "UPDATE %%STATPOINTS%% SET id_ally = 0 WHERE id_owner = :id AND stat_type = 1;";
        $db->update($sql, [
            ':id' => $id
        ]);

        $sql = "UPDATE %%ALLIANCE%% SET ally_members = (SELECT COUNT(*) FROM %%USERS%% WHERE ally_id = :allianceId)"
            . " WHERE id = :allianceId;";
        $db->update($sql, [
            ':id' => $id,
            ':allianceId' => $this->allianceData['id']
        ]);

        $this->redirectTo('game.php?page=alliance&mode=admin&action=members');
    }

    protected function adminDiplomacy()
    {
        if (!$this->rights['DIPLOMATIC']) {
            $this->redirectToHome();
        }

        $db = Database::get();

        $diplomaticList = [
            0 => [
                0 => [],
                1 => [],
                2 => [],
                3 => [],
                4 => [],
                5 => [],
                6 => []
            ],
            1 => [
                0 => [],
                1 => [],
                2 => [],
                3 => [],
                4 => [],
                5 => [],
                6 => []
            ],
            2 => [
                0 => [],
                1 => [],
                2 => [],
                3 => [],
                4 => [],
                5 => [],
                6 => []
            ]
        ];

        $sql = "SELECT d.id, d.level, d.accept, d.owner_1, d.owner_2, a.ally_name FROM %%DIPLO%% d
		INNER JOIN %%ALLIANCE%% a ON IF(:allianceId = d.owner_1, a.id = d.owner_2, a.id = d.owner_1)
		WHERE owner_1 = :allianceId OR owner_2 = :allianceId;";
        $diplomaticResult = $db->select($sql, [
            ':allianceId' => $this->allianceData['id']
        ]);

        foreach ($diplomaticResult as $diplomaticRow) {
            if ($diplomaticRow['level'] == 1 && $diplomaticRow['owner_2'] == $this->allianceData['id']) {
                $diplomaticRow['level'] = 0;
            }
            $own = $diplomaticRow['owner_1'] == $this->allianceData['id'];
            if ($diplomaticRow['accept'] == 1) {
                $diplomaticList[0][$diplomaticRow['level']][$diplomaticRow['id']] = $diplomaticRow['ally_name'];
            } elseif ($own) {
                $diplomaticList[2][$diplomaticRow['level']][$diplomaticRow['id']] = $diplomaticRow['ally_name'];
            } else {
                $diplomaticList[1][$diplomaticRow['level']][$diplomaticRow['id']] = $diplomaticRow['ally_name'];
            }
        }

        $this->assign([
            'diploList' => $diplomaticList,
        ]);

        $this->display('page.alliance.admin.diplomacy.default.tpl');
    }

    protected function adminDiplomacyAccept()
    {
        $USER =& Singleton()->USER;
        if (!$this->rights['DIPLOMATIC']) {
            $this->redirectToHome();
        }

        $db = Database::get();

        $id = HTTP::_GP('id', 0);

        $targetAlliance = $this->getTargetAlliance($id);

        $receivers = $this->getMessageReceivers($targetAlliance['id']);

        foreach ($receivers as $receiver) {
            $lang = getLanguage($receiver['lang']);
            PlayerUtil::sendMessage(
                $receiver['id'],
                $USER['id'],
                $lang['al_circular_alliance'] . $this->allianceData['ally_tag'],
                2,
                $lang['al_diplo_accept_yes'],
                sprintf(
                    $lang['al_diplo_accept_yes_mes'],
                    $lang['al_diplo_level'][(int)$targetAlliance['diplo']],
                    "[" . $this->allianceData['ally_tag'] . "] " . $this->allianceData['ally_name'],
                    "[" . $targetAlliance['ally_tag'] . "] " . $targetAlliance['ally_name']
                ),
                TIMESTAMP
            );
        }

        $sql = "UPDATE %%DIPLO%% SET accept = 1, request_time = :request_time WHERE id = :id"
            . " AND owner_2 = :allianceId";
        $db->update($sql, [
            ':allianceId' => $this->allianceData['id'],
            ':id' => $id,
            ':request_time' => TIMESTAMP
        ]);

        $this->redirectTo('game.php?page=alliance&mode=admin&action=diplomacy');
    }

    protected function adminDiplomacyReject()
    {
        $USER =& Singleton()->USER;
        if (!$this->rights['DIPLOMATIC']) {
            $this->redirectToHome();
        }

        $db = Database::get();

        $id = HTTP::_GP('id', 0);

        $targetAlliance = $this->getTargetAlliance($id);

        $receivers = $this->getMessageReceivers($targetAlliance['id'], true);

        foreach ($receivers as $receiver) {
            $lang = getLanguage($receiver['lang']);
            PlayerUtil::sendMessage(
                $receiver['id'],
                $USER['id'],
                $lang['al_circular_alliance'] . $this->allianceData['ally_tag'],
                2,
                $lang['al_diplo_accept_no'],
                sprintf(
                    $lang['al_diplo_accept_no_mes'],
                    $lang['al_diplo_level'][(int)$targetAlliance['diplo']],
                    "[" . $this->allianceData['ally_tag'] . "] " . $this->allianceData['ally_name'],
                    "[" . $targetAlliance['ally_tag'] . "] " . $targetAlliance['ally_name']
                ),
                TIMESTAMP
            );
        }

        $sql = "DELETE FROM %%DIPLO%% WHERE id = :id AND (owner_1 = :allianceId OR owner_2 = :allianceId);";
        $db->delete($sql, [
            ':allianceId' => $this->allianceData['id'],
            ':id' => $id
        ]);

        $this->redirectTo('game.php?page=alliance&mode=admin&action=diplomacy');
    }

    protected function adminDiplomacyDelete()
    {
        $USER =& Singleton()->USER;
        if (!$this->rights['DIPLOMATIC']) {
            $this->redirectToHome();
        }

        $db = Database::get();

        $id = HTTP::_GP('id', 0);

        $targetAlliance = $this->getTargetAlliance($id);

        if ($targetAlliance['accept'] == 1) {
            $receivers = $this->getMessageReceivers($targetAlliance['id']);

            foreach ($receivers as $receiver) {
                $lang = getLanguage($receiver['lang']);
                PlayerUtil::sendMessage(
                    $receiver['id'],
                    $USER['id'],
                    $lang['al_circular_alliance'] . $this->allianceData['ally_tag'],
                    2,
                    $targetAlliance['diplo'] == 5 ? $lang['al_diplo_war_end'] : $lang['al_diplo_delete'],
                    sprintf(
                        $targetAlliance['diplo'] == 5 ? $lang['al_diplo_war_end_mes'] : $lang['al_diplo_delete_mes'],
                        $lang['al_diplo_level'][(int)$targetAlliance['diplo']],
                        "[" . $this->allianceData['ally_tag'] . "] " . $this->allianceData['ally_name'],
                        "[" . $targetAlliance['ally_tag'] . "] " . $targetAlliance['ally_name']
                    ),
                    TIMESTAMP
                );
            }
        } else {
            $receivers = $this->getMessageReceivers($targetAlliance['id'], true);

            foreach ($receivers as $receiver) {
                $lang = getLanguage($receiver['lang']);
                $level = (int)$targetAlliance['diplo'];
                PlayerUtil::sendMessage(
                    $receiver['id'],
                    $USER['id'],
                    $lang['al_circular_alliance'] . $this->allianceData['ally_tag'],
                    2,
                    $lang['al_diplo_withdraw'],
                    sprintf(
                        $lang['al_diplo_withdraw_mes'],
                        $lang['al_diplo_level'][$level],
                        "[" . $this->allianceData['ally_tag'] . "] " . $this->allianceData['ally_name'],
                        "[" . $targetAlliance['ally_tag'] . "] " . $targetAlliance['ally_name']
                    ),
                    TIMESTAMP
                );
            }
        }

        $sql = "DELETE FROM %%DIPLO%% WHERE id = :id AND (owner_1 = :allianceId OR owner_2 = :allianceId);";
        $db->delete($sql, [
            ':allianceId' => $this->allianceData['id'],
            ':id' => $id
        ]);

        $this->redirectTo('game.php?page=alliance&mode=admin&action=diplomacy');
    }

    protected function adminDiplomacyCreate()
    {
        $USER =& Singleton()->USER;
        $LNG =& Singleton()->LNG;
        if (!$this->rights['DIPLOMATIC']) {
            $this->redirectToHome();
        }

        $db = Database::get();

        $this->initTemplate();
        $this->setWindow('popup');

        $diplomaticMode = HTTP::_GP('diploMode', 0);

        $sql = "SELECT ally_tag,ally_name,id FROM %%ALLIANCE%% WHERE id != :allianceId AND ally_universe = :universe ORDER BY ally_tag ASC;";
        $diplomaticAlly = $db->select($sql, [
            ':allianceId' => $USER['ally_id'],
            ':universe' => Universe::current()
        ]);

        $AllyList = [];
        $IdList = [];
        foreach ($diplomaticAlly as $i) {
            $IdList[] = $i['id'];
            $AllyList[] = $i['ally_name'];
        }

        $diplo_level = $LNG['al_diplo_level'];
        array_shift($diplo_level);
        $this->assign([
            'diploLevel' => $diplo_level,
            'diploMode' => $diplomaticMode,
            'AllyList' => $AllyList,
            'IdList' => $IdList,
        ]);

        $this->display('page.alliance.admin.diplomacy.create.tpl');
    }

    protected function adminDiplomacyCreateProcessor()
    {
        $LNG =& Singleton()->LNG;
        $USER =& Singleton()->USER;
        if (!$this->rights['DIPLOMATIC']) {
            $this->redirectToHome();
        }

        $db = Database::get();

        $id = HTTP::_GP('ally_id', '', UTF8_SUPPORT);

        $targetAlliance = $this->getTargetAlliance($id, true);

        if (empty($targetAlliance)) {
            $this->sendJSON([
                'error' => true,
                'message' => sprintf($LNG['al_diplo_no_alliance'], $targetAlliance['id']),
            ]);
        }

        if (!empty($targetAlliance['diplo'])) {
            $this->sendJSON([
                'error' => true,
                'message' => sprintf($LNG['al_diplo_exists'], $targetAlliance['ally_name']),
            ]);
        }
        if ($targetAlliance['id'] == $this->allianceData['id']) {
            $this->sendJSON([
                'error' => true,
                'message' => $LNG['al_diplo_same_alliance'],
            ]);
        }

        $this->setWindow('ajax');

        $level = HTTP::_GP('level', 0);
        $text = HTTP::_GP('text', '', true);

        if ($level == 5) {
            $receivers = $this->getMessageReceivers($targetAlliance['id']);

            foreach ($receivers as $receiver) {
                $lang = getLanguage($receiver['lang']);

                PlayerUtil::sendMessage(
                    $receiver['id'],
                    $USER['id'],
                    $lang['al_circular_alliance'] . $this->allianceData['ally_tag'],
                    2,
                    $lang['al_diplo_war'],
                    sprintf(
                        $lang['al_diplo_war_mes'],
                        "[" . $this->allianceData['ally_tag'] . "] " . $this->allianceData['ally_name'],
                        "[" . $targetAlliance['ally_tag'] . "] " . $targetAlliance['ally_name'],
                        $lang['al_diplo_level'][$level],
                        $text
                    ),
                    TIMESTAMP
                );
            }
        } else {
            $receivers = $this->getMessageReceivers($targetAlliance['id'], true);

            foreach ($receivers as $receiver) {
                $lang = getLanguage($receiver['lang']);

                PlayerUtil::sendMessage(
                    $receiver['id'],
                    $USER['id'],
                    $lang['al_circular_alliance'] . $this->allianceData['ally_tag'],
                    2,
                    $lang['al_diplo_ask'],
                    sprintf(
                        $lang['al_diplo_ask_mes'],
                        $lang['al_diplo_level'][$level],
                        "[" . $this->allianceData['ally_tag'] . "] " . $this->allianceData['ally_name'],
                        "[" . $targetAlliance['ally_tag'] . "] " . $targetAlliance['ally_name'],
                        $text
                    ),
                    TIMESTAMP
                );
            }
        }

        $sql = "INSERT INTO %%DIPLO%% SET owner_1 = :allianceId, owner_2 = :allianceTargetID, level	= :level,"
            . " accept = :accept, accept_text = :text, universe = :universe, request_time = :request_time";
        $db->insert($sql, [
            ':allianceId' => $USER['ally_id'],
            ':allianceTargetID' => $targetAlliance['id'],
            ':level' => $level,
            ':accept' => $level == 5 ? 1 : 0,
            ':text' => $text,
            ':universe' => Universe::current(),
            ':request_time' => TIMESTAMP,
        ]);

        $this->sendJSON([
            'error' => false,
            'message' => $LNG['al_diplo_create_done'],
        ]);
    }

    private function getTargetAlliance(int $id, bool $ally_id = false)
    {
        $USER =& Singleton()->USER;
        $db = Database::get();

        if ($ally_id) {
            $sql = "SELECT a.id, a.ally_name, a.ally_owner, a.ally_tag, d.level as diplo, d.accept
                FROM %%ALLIANCE%% a
                LEFT JOIN %%DIPLO%% d ON (d.owner_1 = :id AND d.owner_2 = :allianceId) OR (d.owner_2 = :id AND d.owner_1 = :allianceId)
                WHERE a.ally_universe = :universe AND a.id = :id;";

            $targetAlliance = $db->selectSingle($sql, [
                ':allianceId' => $USER['ally_id'],
                ':id' => $id,
                ':universe' => Universe::current()
            ]);
        } else {
            $sql = "SELECT a.id, a.ally_name, a.ally_owner, a.ally_tag, d.level as diplo, d.accept
                FROM %%DIPLO%% d
                JOIN %%ALLIANCE%% a ON a.id = d.owner_1 OR a.id = d.owner_2
                WHERE a.ally_universe = :universe AND d.id = :id AND a.id != :allianceId;";

            $targetAlliance = $db->selectSingle($sql, [
                ':allianceId' => $USER['ally_id'],
                ':id' => $id,
                ':universe' => Universe::current()
            ]);
        }

        return $targetAlliance;
    }

    private function getMessageReceivers(int $targetAllyID, bool $diplomats = false, bool $managers = false)
    {
        $USER =& Singleton()->USER;
        $db = Database::get();

        $sql = "SELECT u.id, u.lang FROM %%USERS%% u";
        if ($diplomats || $managers) {
            $sql = $sql . " JOIN %%ALLIANCE%% a ON u.ally_id = a.id";
            $sql = $sql . " LEFT JOIN %%ALLIANCE_RANK%% r ON u.ally_id = r.allianceID AND u.ally_rank_id = r.rankID";
        }
        if (!$managers) {
            $sql = $sql . " WHERE (u.ally_id = :allianceId OR u.ally_id = :id)";
            if ($diplomats) {
                $sql = $sql . " AND (r.DIPLOMATIC = 1 OR u.id = a.ally_owner)";
            }
            $sql = $sql . ";";

            $receivers = $db->select($sql, [
                ':allianceId' => $USER['ally_id'],
                ':id' => $targetAllyID,
            ]);
        } else {
            $sql = $sql . " WHERE u.ally_id = :allianceId AND (r.MANAGEAPPLY = 1 OR u.id = a.ally_owner);";

            $receivers = $db->select($sql, [
                ':allianceId' => $targetAllyID,
            ]);
        }

        return $receivers;
    }
}
