<?php

/*
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
/**
 * Calculates missile attacks
 *
 */
class MissionCaseMIP extends MissionFunctions implements Mission
{
    public function __construct($Fleet)
    {
        $this->_fleet	= $Fleet;
    }

    public function TargetEvent()
    {
        $resource =& Singleton()->resource;
        $reslist =& Singleton()->reslist;
        $db	= Database::get();

        $sqlFields	= [];
        $elementIDs	= array_merge($reslist['defense'], $reslist['missile']);

        foreach ($elementIDs as $elementID) {
            $sqlFields[]	= '%%PLANETS%%.`' . $resource[$elementID] . '`';
        }

        $sql = 'SELECT lang, defence_tech,
		%%PLANETS%%.id, name, id_owner, ' . implode(', ', $sqlFields) . '
		FROM %%PLANETS%%
		INNER JOIN %%USERS%% ON id_owner = %%USERS%%.id
		WHERE %%PLANETS%%.id = :planetId;';

        $targetData	= $db->selectSingle($sql, [
            ':planetId'	=> $this->_fleet['fleet_end_id']
        ]);

        // kill fleet if target planet deleted
        if ($targetData == false) {
            $fleetArray = FleetFunctions::unserialize($this->_fleet['fleet_array']);
            MissionFunctions::updateLostAdvancedStats($this->_fleet['fleet_owner'], $fleetArray);
            $this->KillFleet();
            return;
        }

        if ($this->_fleet['fleet_end_type'] == 3) {
            $sql	= 'SELECT ' . $resource[502] . ' FROM %%PLANETS%% WHERE id_luna = :moonId;';
            $targetData[$resource[502]]	= $db->selectSingle($sql, [
                ':moonId'	=> $this->_fleet['fleet_end_id']
            ], $resource[502]);
        }

        $sql		= 'SELECT lang, military_tech FROM %%USERS%% WHERE id = :userId;';
        $senderData	= $db->selectSingle($sql, [
            ':userId'	=> $this->_fleet['fleet_owner']
        ]);

        if (
            !in_array($this->_fleet['fleet_target_obj'], array_merge($reslist['defense'], $reslist['missile']))
            || $this->_fleet['fleet_target_obj'] == 502
            || $this->_fleet['fleet_target_obj'] == 0
        ) {
            $primaryTarget	= 401;
        } else {
            $primaryTarget	= $this->_fleet['fleet_target_obj'];
        }

        $targetDefensive    = [];

        foreach ($elementIDs as $elementID) {
            $targetDefensive[$elementID]	= $targetData[$resource[$elementID]];
        }

        unset($targetDefensive[502]);

        $senderData['LNG']	= $this->getLanguage($senderData['lang']);
        $targetData['LNG']	= $this->getLanguage($targetData['lang']);

        $senderData['MSG'] = false;
        $targetData['MSG'] = false;

        if ($targetData[$resource[502]] >= $this->_fleet['fleet_amount']) {
            $senderData['MSG'] 	= $senderData['LNG']['sys_irak_no_att'];
            $targetData['MSG']	= $targetData['LNG']['sys_irak_no_att'];
            $where 		= $this->_fleet['fleet_end_type'] == 3 ? 'id_luna' : 'id';

            $sql		= 'UPDATE %%PLANETS%% SET ' . $resource[502] . ' = ' . $resource[502] . ' - :amount WHERE ' . $where . ' = :planetId;';

            $db->update($sql, [
                ':amount'	=> $this->_fleet['fleet_amount'],
                ':planetId'	=> $targetData['id']
            ]);
            MissionFunctions::updateDestroyedAdvancedStats($this->_fleet['fleet_target_owner'], $this->_fleet['fleet_owner'], 503, $this->_fleet['fleet_amount']);
            MissionFunctions::updateLostAdvancedStats($this->_fleet['fleet_target_owner'], [502 => $this->_fleet['fleet_amount']]);
        } else {
            if ($targetData[$resource[502]] > 0) {
                $where 	= $this->_fleet['fleet_end_type'] == 3 ? 'id_luna' : 'id';
                $sql	= 'UPDATE %%PLANETS%% SET ' . $resource[502] . ' = :amount WHERE ' . $where . ' = :planetId;';

                $db->update($sql, [
                    ':amount'	=> 0,
                    ':planetId'	=> $targetData['id']
                ]);

                MissionFunctions::updateDestroyedAdvancedStats($this->_fleet['fleet_target_owner'], $this->_fleet['fleet_owner'], 503, $targetData[$resource[502]]);
                MissionFunctions::updateLostAdvancedStats($this->_fleet['fleet_target_owner'], [502 => $targetData[$resource[502]]]);
            }

            $targetDefensive = array_filter($targetDefensive);

            if (!empty($targetDefensive)) {
                require_once 'includes/classes/missions/functions/calculateMIPAttack.php';
                $result   	= calculateMIPAttack(
                    $targetData["defence_tech"],
                    $senderData["military_tech"],
                    $this->_fleet['fleet_amount'],
                    $targetDefensive,
                    $primaryTarget,
                    $targetData[$resource[502]]
                );

                $result		= array_filter($result);


                $senderData['MSG'] 	= sprintf($senderData['LNG']['sys_irak_def'], $targetData[$resource[502]]) . '<br><br>';
                $targetData['MSG']	= sprintf($targetData['LNG']['sys_irak_def'], $targetData[$resource[502]]) . '<br><br>';

                ksort($result, SORT_NUMERIC);

                foreach ($result as $Element => $destroy) {
                    $senderData['MSG'] 	.= sprintf('%s (- %d)<br>', $senderData['LNG']['tech'][$Element], $destroy);
                    $targetData['MSG']	.= sprintf('%s (- %d)<br>', $targetData['LNG']['tech'][$Element], $destroy);

                    $sql	= 'UPDATE %%PLANETS%% SET ' . $resource[$Element] . ' = ' . $resource[$Element] . ' - :amount WHERE id = :planetId;';
                    $db->update($sql, [
                        ':planetId' => $targetData['id'],
                        ':amount'	=> $destroy
                    ]);
                    MissionFunctions::updateDestroyedAdvancedStats($this->_fleet['fleet_owner'], $this->_fleet['fleet_target_owner'], $Element, $destroy);
                }
                MissionFunctions::updateLostAdvancedStats($this->_fleet['fleet_owner'], [503 => $this->_fleet['fleet_amount'] - $targetData[$resource[502]]]);
            } else {
                MissionFunctions::updateLostAdvancedStats($this->_fleet['fleet_owner'], [503 => $this->_fleet['fleet_amount']]);
                $senderData['MSG'] 	= $senderData['LNG']['sys_irak_no_def'];
                $targetData['MSG']	= $targetData['LNG']['sys_irak_no_def'];
            }
        }

        $sql		= 'SELECT name FROM %%PLANETS%% WHERE id = :planetId;';
        $planetName	= Database::get()->selectSingle($sql, [
            ':planetId'	=> $this->_fleet['fleet_start_id'],
        ], 'name');

        $ownerLink			= $planetName . " " . GetStartAddressLink($this->_fleet);
        $targetLink 		= $targetData['name'] . " " . GetTargetAddressLink($this->_fleet);
        $senderData['MSG'] 	= sprintf($senderData['LNG']['sys_irak_mess'], $this->_fleet['fleet_amount'], $ownerLink, $targetLink) . $senderData['MSG'];
        $targetData['MSG']	= sprintf($targetData['LNG']['sys_irak_mess'], $this->_fleet['fleet_amount'], $ownerLink, $targetLink) . $targetData['MSG'];

        PlayerUtil::sendMessage(
            $this->_fleet['fleet_owner'],
            0,
            $senderData['LNG']['sys_mess_tower'],
            3,
            $senderData['LNG']['sys_irak_subject'],
            $senderData['MSG'],
            $this->_fleet['fleet_start_time'],
            null,
            1,
            $this->_fleet['fleet_universe']
        );

        PlayerUtil::sendMessage(
            $this->_fleet['fleet_target_owner'],
            0,
            $targetData['LNG']['sys_mess_tower'],
            3,
            $targetData['LNG']['sys_irak_subject'],
            $targetData['MSG'],
            $this->_fleet['fleet_start_time'],
            null,
            1,
            $this->_fleet['fleet_universe']
        );

        $this->KillFleet();
    }

    public function EndStayEvent()
    {
        return;
    }

    public function ReturnEvent()
    {
        return;
    }
}
