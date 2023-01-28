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

class MissionFunctions
{
    public $kill = 0;
    public $_fleet = [];
    public $_upd = [];
    public $_updLog = [];
    public $eventTime = 0;

    protected function UpdateFleet($Option, $Value)
    {
        $this->_fleet[$Option] = $Value;
        $this->_upd[$Option] = $Value;
    }

    protected function UpdateFleetLog($Option, $Value)
    {
        $this->_updLog[$Option] = $Value;
    }

    protected function setState($Value)
    {
        $this->_fleet['fleet_mess'] = $Value;
        $this->_upd['fleet_mess'] = $Value;

        switch ($Value) {
            case FLEET_OUTWARD:
                $this->eventTime = $this->_fleet['fleet_start_time'];
                break;
            case FLEET_RETURN:
                $this->eventTime = $this->_fleet['fleet_end_time'];
                break;
            case FLEET_HOLD:
                $this->eventTime = $this->_fleet['fleet_end_stay'];
                break;
        }
    }

    protected function SaveFleet()
    {
        $targetMission = $this->_fleet['fleet_mission'];
        if ($this->_fleet['fleet_mess'] != FLEET_RETURN && $targetMission != MISSION_COLONISATION && $targetMission != MISSION_EXPEDITION && $targetMission != MISSION_TRADE) {
            // set points of fleet and target owner during arival (SaveFleet is always called on TargetEvent)
            if ($this->_fleet['fleet_group'] != 0) {
                $sql = 'UPDATE %%LOG_FLEETS%% SET 
                    fleet_owner_points = (SELECT total_points FROM %%STATPOINTS%% WHERE id_owner = :fleet_owner AND stat_type = 1),
                    fleet_target_owner_points = (SELECT total_points FROM %%STATPOINTS%% WHERE id_owner = :fleet_target_owner AND stat_type = 1)
                    WHERE `fleet_id` IN (SELECT fleet_id FROM %%FLEETS%% WHERE fleet_group = :acsId);';
                Database::get()->update($sql, [
                    ':fleet_owner'  => $this->_fleet['fleet_owner'],
                    ':fleet_target_owner'  => $this->_fleet['fleet_target_owner'],
                    ':acsId'  => $this->_fleet['fleet_group'],
                ]);
            }
            else {
                $sql = 'UPDATE %%LOG_FLEETS%% SET 
                    fleet_owner_points = (SELECT total_points FROM %%STATPOINTS%% WHERE id_owner = :fleet_owner AND stat_type = 1),
                    fleet_target_owner_points = (SELECT total_points FROM %%STATPOINTS%% WHERE id_owner = :fleet_target_owner AND stat_type = 1)
                    WHERE `fleet_id` = :fleetId;';
                Database::get()->update($sql, [
                    ':fleet_owner'  => $this->_fleet['fleet_owner'],
                    ':fleet_target_owner'  => $this->_fleet['fleet_target_owner'],
                    ':fleetId'  => $this->_fleet['fleet_id'],
                ]);
            }
        }

        if ($this->kill == 1) {
            return;
        }

        $param = [];
        $updateQuery = [];

        foreach ($this->_upd as $Opt => $Val) {
            $updateQuery[] = "`" . $Opt . "` = :" . $Opt;
            $param[':' . $Opt] = $Val;
        }

        if (!empty($updateQuery)) {
            $sql = 'UPDATE %%FLEETS%% SET ' . implode(', ', $updateQuery) . ' WHERE `fleet_id` = :fleetId;';
            $param[':fleetId'] = $this->_fleet['fleet_id'];
            Database::get()->update($sql, $param);

            $sql = 'UPDATE %%FLEETS_EVENT%% SET time = :time WHERE `fleetID` = :fleetId;';
            Database::get()->update($sql, [
                ':time'     => $this->eventTime,
                ':fleetId'  => $this->_fleet['fleet_id']
            ]);
        }

        $param = [];
        $updateQuery = [];

        foreach ($this->_updLog as $Opt => $Val) {
            $updateQuery[] = "`" . $Opt . "` = :" . $Opt;
            $param[':' . $Opt] = $Val;
        }

        if (!empty($updateQuery)) {
            $sql = 'UPDATE %%LOG_FLEETS%% SET ' . implode(', ', $updateQuery) . ' WHERE `fleet_id` = :fleetId;';
            $param[':fleetId'] = $this->_fleet['fleet_id'];
            Database::get()->update($sql, $param);
        }
    }

    protected function UpdatePlanet($userId, $planetId, $time = null)
    {
        $sql = 'SELECT * FROM %%USERS%% WHERE id = :userId;';
        $targetUser = Database::get()->selectSingle($sql, [
            ':userId'   => $userId
        ]);
        $sql = "SELECT * FROM %%PLANETS%% WHERE id = :planetId FOR UPDATE;";
        $planet = Database::get()->selectSingle($sql, [
            ':planetId' => $planetId,
        ]);

        $planetUpdater = new ResourceUpdate();
        $planetUpdater->CalcResource($targetUser, $planet, true, $time);
    }

    protected function RestoreFleet($onStart = true)
    {
        $resource =& Singleton()->resource;

        $fleetData = FleetFunctions::unserialize($this->_fleet['fleet_array']);

        $userId = $onStart == true ? $this->_fleet['fleet_owner'] : $this->_fleet['fleet_target_owner'];
        $planetId = $onStart == true ? $this->_fleet['fleet_start_id'] : $this->_fleet['fleet_end_id'];
        // $time = $onStart == true ? $this->_fleet['fleet_end_time'] : $this->_fleet['fleet_start_time'];

        $this->UpdatePlanet($userId, $planetId);

        $updateQuery = [];

        $param = [
            ':metal'        => $this->_fleet['fleet_resource_metal'],
            ':crystal'      => $this->_fleet['fleet_resource_crystal'],
            ':deuterium'    => $this->_fleet['fleet_resource_deuterium'],
            ':planetId'     => $planetId
        ];

        foreach ($fleetData as $shipId => $shipAmount) {
            $updateQuery[] = "p.`" . $resource[$shipId] . "` = p.`" . $resource[$shipId]
                . "` + :" . $resource[$shipId];
            $param[':' . $resource[$shipId]] = $shipAmount;
        }

        $sql = 'UPDATE %%PLANETS%% as p, %%USERS%% as u SET
		' . implode(', ', $updateQuery) . ',
		p.`metal` = p.`metal` + :metal,
		p.`crystal` = p.`crystal` + :crystal,
		p.`deuterium` = p.`deuterium` + :deuterium
		WHERE p.`id` = :planetId AND u.id = p.id_owner;';

        Database::get()->update($sql, $param);

        $this->KillFleet();
    }

    protected function StoreGoodsToPlanet($onStart = false)
    {
        $sql = 'UPDATE %%PLANETS%% as p, %%USERS%% as u SET
		`metal`			= `metal` + :metal,
		`crystal`		= `crystal` + :crystal,
		`deuterium` 	= `deuterium` + :deuterium
		WHERE p.`id` = :planetId AND u.id = p.id_owner;';

        Database::get()->update($sql, [
            ':metal'        => $this->_fleet['fleet_resource_metal'],
            ':crystal'      => $this->_fleet['fleet_resource_crystal'],
            ':deuterium'    => $this->_fleet['fleet_resource_deuterium'],
            ':planetId'     => ($onStart == true ? $this->_fleet['fleet_start_id'] : $this->_fleet['fleet_end_id'])
        ]);

        $this->UpdateFleet('fleet_resource_metal', '0');
        $this->UpdateFleet('fleet_resource_crystal', '0');
        $this->UpdateFleet('fleet_resource_deuterium', '0');
    }

    protected function KillFleet()
    {
        $this->kill = 1;
        $sql = 'DELETE %%FLEETS%%, %%FLEETS_EVENT%%
		FROM %%FLEETS%% LEFT JOIN %%FLEETS_EVENT%% on fleet_id = fleetId
		WHERE `fleet_id` = :fleetId';

        Database::get()->delete($sql, [
            ':fleetId'  => $this->_fleet['fleet_id']
        ]);
    }

    protected function getLanguage($language = null, $userID = null)
    {
        if (is_null($language) && !is_null($userID)) {
            $sql = 'SELECT lang FROM %%USERS%% WHERE id = :userId;';
            $language = Database::get()->selectSingle($sql, [
                ':userId' => $userID
            ], 'lang');
        }

        $LNG = new Language($language);
        $LNG->includeData(['L18N', 'FLEET', 'TECH', 'CUSTOM']);
        return $LNG;
    }

    function sendAttackReturnMessage() {
        $reslist =& Singleton()->reslist;

        $LNG = $this->getLanguage(NULL, $this->_fleet['fleet_owner']);

        $sql = 'SELECT name FROM %%PLANETS%% WHERE id = :planetId;';
        $planetNameStart = Database::get()->selectSingle($sql, array(
            ':planetId'	=> $this->_fleet['fleet_start_id'],
        ), 'name');

        $sql = 'SELECT name FROM %%PLANETS%% WHERE id = :planetId;';
        $planetNameEnd	= Database::get()->selectSingle($sql, array(
            ':planetId'	=> $this->_fleet['fleet_end_id'],
        ), 'name');

        $Message = sprintf(
            $LNG['sys_fleet_won'],
            $planetNameEnd,
            GetTargetAddressLink($this->_fleet, ''),
            $planetNameStart,
            GetStartAddressLink($this->_fleet, ''),
            pretty_number($this->_fleet['fleet_resource_metal']),
            $LNG['tech'][901],
            pretty_number($this->_fleet['fleet_resource_crystal']),
            $LNG['tech'][902],
            pretty_number($this->_fleet['fleet_resource_deuterium']),
            $LNG['tech'][903]
        );

        $fleetArray = FleetFunctions::unserialize($this->_fleet['fleet_array']);
        $fleetArrayStart = FleetFunctions::unserialize($this->_fleet['fleet_start_array']);

        if (isset($fleetArrayStart)) {
            $fleetAmmount = array_sum($fleetArray);
            $fleetAmmountStart = array_sum($fleetArrayStart);
            if ($fleetAmmountStart != $fleetAmmount) {
                if ($fleetAmmountStart > $fleetAmmount) {
                    $Message .= '<br><br>' . sprintf(
                            $LNG['sys_expe_back_home_ships_lost']
                        );
                }
                foreach ($reslist['fleet'] as $shipId) {
                    if (isset($fleetArrayStart[$shipId]) && isset($fleetArray[$shipId])) {
                        if ($fleetArrayStart[$shipId] != $fleetArray[$shipId]) {
                            $Message .= '<br>' . $LNG['tech'][$shipId] . ': ' . pretty_number(abs($fleetArrayStart[$shipId] - $fleetArray[$shipId]));
                        }
                    } elseif (isset($fleetArrayStart[$shipId])) {
                        $Message .= '<br>' . $LNG['tech'][$shipId] . ': ' . pretty_number(abs($fleetArrayStart[$shipId]));
                    } elseif (isset($fleetArray[$shipId])) {
                        $Message .= '<br>' . $LNG['tech'][$shipId] . ': ' . pretty_number(abs($fleetArray[$shipId]));
                    }
                }
            }
        }

        PlayerUtil::sendMessage($this->_fleet['fleet_owner'], 0, $LNG['sys_mess_tower'], 4, $LNG['sys_mess_fleetback'],
            $Message, $this->_fleet['fleet_end_time'], NULL, 1, $this->_fleet['fleet_universe']);
    }

    static function updateDestroyedAdvancedStats($attacker, $defender, $Element, $amount = 1)
    {
        if ($attacker > 0) {
            $resource =& Singleton()->resource;
            require_once 'includes/classes/Database.class.php';
            $db = Database::get();
        
            $sql = "UPDATE %%ADVANCED_STATS%% SET destroyed_" . $Element . " = destroyed_" . $Element . " + :" . $resource[$Element] . " WHERE userId = :userId";
            $db->update($sql, [
                ':' . $resource[$Element] => $amount,
                ':userId' => $attacker,
            ]);
        } 

        if ($defender > 0) {
            $fleetArray = [$Element => $amount];
            MissionFunctions::updateLostAdvancedStats($defender, $fleetArray);
        }
    }

    static function updateLostAdvancedStats($user, $fleetArray)
    {
        if ($user <= 0) {
            return;
        }

        $resource =& Singleton()->resource;
        require_once 'includes/classes/Database.class.php';
        $db = Database::get();

        $field = [];
        $params = [':userId' => $user];
        foreach ($fleetArray as $Element => $Count) {
            if (empty($field)) {
                $field[] = 'lost_' . $Element . ' = lost_' . $Element . ' + :' . $resource[$Element];
            } else {
                $field[] = ', lost_' . $Element . ' = lost_' . $Element . ' + :' . $resource[$Element];
            }
            $params[':' . $resource[$Element]] = $Count;
        }

        $sql = "UPDATE %%ADVANCED_STATS%% SET " . implode("\n", $field) . " WHERE userId = :userId;";
        $db->update($sql, $params);
    }

    function updateFoundRessAdvancedStats($user, $Element, $amount = 1)
    {
        require_once 'includes/classes/Database.class.php';
        $db = Database::get();
    
        $sql = "UPDATE %%ADVANCED_STATS%% SET found_" . $Element . " = found_" . $Element . " + :" . $Element . " WHERE userId = :userId";
        $db->update($sql, [
            ':' . $Element => $amount,
            ':userId' => $user,
        ]);
    }

    function updateFoundShipsAdvancedStats($user, $fleetArray)
    {
        $resource =& Singleton()->resource;
        require_once 'includes/classes/Database.class.php';
        $db = Database::get();

        $field = [];
        $params = [':userId' => $user];
        foreach ($fleetArray as $Element => $Count) {
            if (empty($field)) {
                $field[] = 'found_' . $Element . ' = found_' . $Element . ' + :' . $resource[$Element];
            } else {
                $field[] = ', found_' . $Element . ' = found_' . $Element . ' + :' . $resource[$Element];
            }
            $params[':' . $resource[$Element]] = $Count;
        }

        $sql = "UPDATE %%ADVANCED_STATS%% SET " . implode("\n", $field) . " WHERE userId = :userId;";
        $db->update($sql, $params);
    }

    static function updateRepairedDefAdvancedStats($user, $Element, $amount = 1)
    {
        require_once 'includes/classes/Database.class.php';
        $db = Database::get();
    
        $sql = "UPDATE %%ADVANCED_STATS%% SET repaired_" . $Element . " = repaired_" . $Element . " + :" . $Element . " WHERE userId = :userId";
        $db->update($sql, [
            ':' . $Element => $amount,
            ':userId' => $user,
        ]);
    }
}
