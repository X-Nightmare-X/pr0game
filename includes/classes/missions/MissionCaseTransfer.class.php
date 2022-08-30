<?php

class MissionCaseTransfer extends MissionFunctions implements Mission
{
	function __construct($Fleet)
	{
		$this->_fleet	= $Fleet;
	}

	function TargetEvent()
	{
        $db = Database::get();

        $sql = 'SELECT * FROM %%PLANETS%% WHERE id = :planetId;';
        $targetPlanet = $db->selectSingle($sql, [
            ':planetId' => $this->_fleet['fleet_end_id'],
        ]);

        // return fleet if target planet deleted
        if ($targetPlanet == false) {
            $this->setState(FLEET_RETURN);
            $this->SaveFleet();
            return;
        }

		$sql = 'SELECT name FROM %%PLANETS%% WHERE `id` = :planetId;';

		$startPlanetName	= $db->selectSingle($sql, array(
			':planetId'	=> $this->_fleet['fleet_start_id']
		), 'name');

		$LNG			= $this->getLanguage(NULL, $this->_fleet['fleet_owner']);

		$Message		= sprintf($LNG['sys_transfer_mess_owner'],
			$targetPlanet['name'], GetTargetAddressLink($this->_fleet, ''),
			pretty_number($this->_fleet['fleet_resource_metal']), $LNG['tech'][901],
			pretty_number($this->_fleet['fleet_resource_crystal']), $LNG['tech'][902],
			pretty_number($this->_fleet['fleet_resource_deuterium']), $LNG['tech'][903]
		);


		$fleet = FleetFunctions::unserialize($this->_fleet['fleet_array']);

		foreach($fleet as $elementID => $amount) {
			$Message   	.= '<br>'.$LNG['tech'][$elementID].': '.pretty_number($amount);
		}

		PlayerUtil::sendMessage($this->_fleet['fleet_owner'], 0, $LNG['sys_mess_tower'], 5,
			$LNG['sys_mess_transport'], $Message, $this->_fleet['fleet_start_time'], NULL, 1, $this->_fleet['fleet_universe']);

		$LNG			= $this->getLanguage(NULL, $this->_fleet['fleet_target_owner']);
		$Message        = sprintf($LNG['sys_transfer_mess_user'],
			$startPlanetName, GetStartAddressLink($this->_fleet, ''),
			$targetPlanetName, GetTargetAddressLink($this->_fleet, ''),
			pretty_number($this->_fleet['fleet_resource_metal']), $LNG['tech'][901],
			pretty_number($this->_fleet['fleet_resource_crystal']), $LNG['tech'][902],
			pretty_number($this->_fleet['fleet_resource_deuterium']), $LNG['tech'][903]
		);

		foreach($fleet as $elementID => $amount) {
			$Message   	.= '<br>'.$LNG['tech'][$elementID].': '.pretty_number($amount);
		}

		PlayerUtil::sendMessage($this->_fleet['fleet_target_owner'], 0, $LNG['sys_mess_tower'], 5,
			$LNG['sys_mess_transport'], $Message, $this->_fleet['fleet_start_time'], NULL, 1, $this->_fleet['fleet_universe']);

		$this->StoreGoodsToPlanet();
		$this->RestoreFleet(false);
	}

	function EndStayEvent()
	{
		return;
	}

	function ReturnEvent()
	{
		$LNG				= $this->getLanguage(NULL, $this->_fleet['fleet_owner']);

		$Message     		= sprintf($LNG['sys_stat_mess'],
			GetStartAddressLink($this->_fleet, ''),
			pretty_number($this->_fleet['fleet_resource_metal']), $LNG['tech'][901],
			pretty_number($this->_fleet['fleet_resource_crystal']), $LNG['tech'][902],
			pretty_number($this->_fleet['fleet_resource_deuterium']), $LNG['tech'][903]
		);

		PlayerUtil::sendMessage($this->_fleet['fleet_owner'], 0, $LNG['sys_mess_tower'], 4, $LNG['sys_mess_fleetback'],
			$Message, $this->_fleet['fleet_end_time'], NULL, 1, $this->_fleet['fleet_universe']);
			
		$this->RestoreFleet();
	}
}
