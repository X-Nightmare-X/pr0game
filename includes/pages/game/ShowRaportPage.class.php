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

class ShowRaportPage extends AbstractGamePage
{
	public static $requireModule = 0;
	
	protected $disableEcoSystem = true;

	function __construct() 
	{
		parent::__construct();
	}
	
	private function BCWrapperPreRev2321($combatReport, $isDefender)
	{
		if(isset($combatReport['moon']['desfail']))
		{
			$combatReport['moon']	= array(
				'moonName'				=> $combatReport['moon']['name'],
				'moonChance'			=> $combatReport['moon']['chance'],
				'moonDestroySuccess'	=> !$combatReport['moon']['desfail'],
				'fleetDestroyChance'	=> $combatReport['moon']['chance2'],
				'fleetDestroySuccess'	=> !$combatReport['moon']['fleetfail']
			);			
		}
		elseif(isset($combatReport['moon'][0]))
		{
			$combatReport['moon']	= array(
				'moonName'				=> $combatReport['moon'][1],
				'moonChance'			=> $combatReport['moon'][0],
				'moonDestroySuccess'	=> !$combatReport['moon'][2],
				'fleetDestroyChance'	=> $combatReport['moon'][3],
				'fleetDestroySuccess'	=> !$combatReport['moon'][4]
			);			
		}
		
		if(isset($combatReport['simu']))
		{
			$combatReport['additionalInfo'] = $combatReport['simu'];
		}
		
		if(isset($combatReport['debris'][0]))
		{
            $combatReport['debris'] = array(
                901	=> $combatReport['debris'][0],
                902	=> $combatReport['debris'][1]
            );
		}
		
		if (!empty($combatReport['steal']['metal']))
		{
			$combatReport['steal'] = array(
				901	=> $combatReport['steal']['metal'],
				902	=> $combatReport['steal']['crystal'],
				903	=> $combatReport['steal']['deuterium']
			);
		}

		if (!$isDefender) {
			$combatReport['repaired'] = [];
		}
		
		return $combatReport;
	}
	
	function show() 
	{
		global $LNG, $USER;
		
		$LNG->includeData(array('FLEET'));		
		$this->setWindow('popup');

		$db = Database::get();

		$RID		= HTTP::_GP('raport', '');

		$sql = "SELECT raport,attacker,defender FROM %%RW%% WHERE rid = :reportID;";
		$reportData = $db->selectSingle($sql, array(
			':reportID'	=> $RID
		));

		if(empty($reportData)) {
			$this->printMessage($LNG['sys_raport_not_found']);
		}
		
		// empty is BC for pre r2484
		$attackers = explode(",", $reportData['attacker']);
		$defenders = explode(",", $reportData['defender']);
		$isAttacker = empty($reportData['attacker']) || in_array($USER['id'], $attackers);
		$isDefender = empty($reportData['defender']) || in_array($USER['id'], $defenders);

		$showDetails = $isAttacker || $isDefender;
		$sql = "SELECT ally_id FROM %%USERS%% WHERE id = :pID;";
		$allyID = $db->selectSingle($sql, array(
			':pID'	=> $USER['id']
		), 'ally_id');
		if (!$showDetails && $allyID > 0) {
			foreach ($attackers as $atterID) {
				$aID = $db->selectSingle($sql, array(
					':pID'	=> $atterID
				), 'ally_id');
				if ($allyID == $aID) {
					$showDetails = true;
					break;
				}
			}
			if (!$showDetails) {
				foreach ($defenders as $defferID) {
					$aID = $db->selectSingle($sql, array(
						':pID'	=> $defferID
					), 'ally_id');
					if ($allyID == $aID) {
						$showDetails = true;
						break;
					}
				}
			}
		}

		$Info = null;
		if (!$showDetails) {
			$sql = "SELECT 
			raport, time,
			(
				SELECT
				GROUP_CONCAT(username SEPARATOR ' & ') as attacker
				FROM %%USERS%%
				WHERE id IN (SELECT uid FROM %%TOPKB_USERS%% WHERE %%TOPKB_USERS%%.rid = %%RW%%.rid AND role = 1)
			) as attacker,
			(
				SELECT
				GROUP_CONCAT(username SEPARATOR ' & ') as defender
				FROM %%USERS%%
				WHERE id IN (SELECT uid FROM %%TOPKB_USERS%% WHERE %%TOPKB_USERS%%.rid = %%RW%%.rid AND role = 2)
			) as defender
			FROM %%RW%%
			WHERE rid = :reportID;";
			$reportData = $db->selectSingle($sql, array(
				':reportID'	=> $RID
			));

			$Info		= array($reportData["attacker"], $reportData["defender"]);
		}

		if(empty($reportData)) {
			$this->printMessage($LNG['sys_raport_not_found']);
		}

		$combatReport			= unserialize($reportData['raport']);
		if (!$isDefender && $combatReport['result'] == 'r' && count($combatReport['rounds']) <= 2) {
			$this->printMessage($LNG['sys_raport_lost_contact']);
		}
		
		$combatReport['time']	= _date($LNG['php_tdformat'], $combatReport['time'], $USER['timezone']);
		$combatReport			= $this->BCWrapperPreRev2321($combatReport, $isDefender);
		
		$this->assign(array(
			'Raport'	=> $combatReport,
			'Info'		=> $Info,
			'pageTitle'	=> $LNG['sys_mess_attack_report']
		));
		
		$this->display('shared.mission.raport.tpl');
	}
}
