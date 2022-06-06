<?php

/**
 * pr0game powered by steemnova
 * trade calculations for marketplace
 * (c) 2022 havoc
 * 
 */

class MarketManager {

	private $_restype_metal = 1;
	private $_restype_crystal = 2;
	private $_restype_deuterium = 3;

	private $_pushRatio = 0.5;

	private $_mkVolume = [];
	private $_mdVolume = [];
	private $_cdVolume = [];

	private function getMetSales($expectedrestype)
	{
		$db = Database::get();
		$sql = 'SELECT
			ex_resource_amount as amount,
			seller.fleet_resource_metal as metal
			FROM %%TRADES%%
			JOIN %%LOG_FLEETS%% seller ON seller.fleet_id = seller_fleet_id
			JOIN %%LOG_FLEETS%% buyer ON buyer.fleet_id = buyer_fleet_id
			WHERE transaction_type = 0
			AND seller.fleet_resource_crystal = 0
			AND seller.fleet_resource_deuterium = 0
			AND buy_time >= CURDATE() - INTERVAL 7 DAY
			AND filter_visibility != 1
			AND ex_resource_type = :exprestype
			ORDER BY buy_time DESC;';

		$trades = $db->select($sql, [':exprestype' => $expectedrestype]);

		return $trades;
	}
	
	private function getCrysSales($expectedrestype)
	{
		$db = Database::get();
		$sql = 'SELECT
			ex_resource_amount as amount,
			seller.fleet_resource_crystal as crystal
			FROM %%TRADES%%
			JOIN %%LOG_FLEETS%% seller ON seller.fleet_id = seller_fleet_id
			JOIN %%LOG_FLEETS%% buyer ON buyer.fleet_id = buyer_fleet_id
			WHERE transaction_type = 0
			AND seller.fleet_resource_metal = 0
			AND seller.fleet_resource_deuterium = 0
			AND buy_time >= CURDATE() - INTERVAL 7 DAY
			AND filter_visibility != 1
			AND ex_resource_type = :exprestype
			ORDER BY buy_time DESC;';
		
		$trades = $db->select($sql, [':exprestype' => $expectedrestype]);
		
		return $trades;
	}
	
	private function getDeutSales($expectedrestype)
	{
		$db = Database::get();
		$sql = 'SELECT
			ex_resource_amount as amount,
			seller.fleet_resource_deuterium as deuterium
			FROM %%TRADES%%
			JOIN %%LOG_FLEETS%% seller ON seller.fleet_id = seller_fleet_id
			JOIN %%LOG_FLEETS%% buyer ON buyer.fleet_id = buyer_fleet_id
			WHERE transaction_type = 0
			AND seller.fleet_resource_crystal = 0
			AND seller.fleet_resource_metal = 0
			AND buy_time >= CURDATE() - INTERVAL 7 DAY
			AND filter_visibility != 1
			AND ex_resource_type = :exprestype
			ORDER BY buy_time DESC;';
		
		$trades = $db->select($sql, [':exprestype' => $expectedrestype]);
		
		return $trades;
	}
	
	private function getTotalMetCrysVolume()
	{
		if ($this->_mkVolume != []) return $this->_mkVolume;

		$allTrades = [
				'metal' => 0,
				'crystal' => 0,
		];

		$kristrades = $this->getMetSales($this->_restype_crystal);
		
		foreach ($kristrades as $trade)
		{
			$allTrades['metal'] += $trade['metal'];
			$allTrades['crystal'] += $trade['amount'];
		}
		
		$mettrades = $this->getCrysSales($this->_restype_metal);
		
		foreach ($mettrades as $trade)
		{
			$allTrades['metal'] += $trade['amount'];
			$allTrades['crystal'] += $trade['crystal'];
		}

		return $allTrades;
	}
	
	private function getTotalMetDeutVolume()
	{
		if ($this->_mdVolume != []) return $this->_mdVolume;

		$allTrades = [
			'metal' => 0,
			'deuterium' => 0,
		];
		
		$deutrades = $this->getMetSales($this->_restype_deuterium);
		
		foreach ($deutrades as $trade)
		{
			$allTrades['metal'] += $trade['metal'];
			$allTrades['deuterium'] += $trade['amount'];
		}
		
		$mettrades = $this->getDeutSales($this->_restype_metal);
		
		foreach ($mettrades as $trade)
		{
			$allTrades['metal'] += $trade['amount'];
			$allTrades['deuterium'] += $trade['deuterium'];
		}
		
		$this->_mdVolume = $allTrades;
		return $allTrades;
	}

	private function getTotalCrysDeutVolume()
	{
		if ($this->_cdVolume != []) return $this->_cdVolume;
		$allTrades = [
				'crystal' => 0,
				'deuterium' => 0,
		];
		
		$deutrades = $this->getCrysSales($this->_restype_deuterium);
		
		foreach ($deutrades as $trade)
		{
			$allTrades['crystal'] += $trade['crystal'];
			$allTrades['deuterium'] += $trade['amount'];
		}
		
		$crystrades = $this->getDeutSales($this->_restype_crystal);
		
		foreach ($crystrades as $trade)
		{
			$allTrades['crystal'] += $trade['amount'];
			$allTrades['deuterium'] += $trade['deuterium'];
		}
		
		$this->_cdVolume = $allTrades;
		return $allTrades;
	}
	
	public function getMetCrysRatio()
	{
		$trades = $this->getTotalMetCrysVolume();
		return ($trades['metal'] / $trades['crystal']);
	}

	public function getMetDeutRatio()
	{
		$trades = $this->getTotalMetDeutVolume();
		return ($trades['metal'] / $trades['deuterium']);
	}
	
	public function getCrysDeutRatio()
	{
		$trades = $this->getTotalCrysDeutVolume();
		return ($trades['crystal'] / $trades['deuterium']);
	}

	public function getAllTradeRatios()
	{
		$result = [
				'mc' => $this->getMetCrysRatio(),
				'md' => $this->getMetdeutRatio(),
				'cd' => $this->getCrysDeutRatio(),
		];
		
		$result['cd2'] = ($result['mc'] / $result['md']);
		
		return $result;
	}
}