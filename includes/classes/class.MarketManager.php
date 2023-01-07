<?php

/**
 * pr0game powered by steemnova
 * trade calculations for marketplace
 * (c) 2022 havoc
 * 
 */
require_once "includes\classes\Universe.class.php";
class MarketManager
{

	private $_restype_metal = 1;
	private $_restype_crystal = 2;
	private $_restype_deuterium = 3;

	private $_mkVolume = [];
	private $_mdVolume = [];
	private $_cdVolume = [];
	private $_universe = Universe::current();


	private $_pushTolerance = 0.3; // accepted market volatility percentage, not triggering push check

	public function __construct($universe = Universe::current())
	{
		$this->_universe =  $universe;
	}

	// return useful default values for when there are no trades
	private function getDefaultRatio($expectedrestype)
	{
		if ($expectedrestype == $this->_restype_metal) return 3;
		if ($expectedrestype == $this->_restype_crystal) return 2;
		else return 1;
	}

	/**
	 * the following 3 functions get a 7 day trade list of all trades on a single resource pair
	 */
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
			AND seller.fleet_universe = :universe
			ORDER BY buy_time DESC;';

		$trades = $db->select($sql, [
			':exprestype' => $expectedrestype,
			':universe' => $this->_universe,
		]);

		if (empty($trades)) {
			$trades[] = [
				'amount' => $this->getDefaultRatio($expectedrestype),
				'metal' => $this->getDefaultRatio($this->_restype_metal),
			];
		}

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
			AND seller.fleet_universe = :universe
			ORDER BY buy_time DESC;';

		$trades = $db->select($sql, [
			':exprestype' => $expectedrestype,
			':universe' => $this->_universe,
		]);

		if (empty($trades)) {
			$trades[] = [
				'amount' => $this->getDefaultRatio($expectedrestype),
				'crystal' => $this->getDefaultRatio($this->_restype_crystal),
			];
		}

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
			AND seller.fleet_universe = :universe
			ORDER BY buy_time DESC;';

		$trades = $db->select($sql, [
			':exprestype' => $expectedrestype,
			':universe' => $this->_universe,
		]);

		if (empty($trades)) {
			$trades[] = [
				'amount' => $this->getDefaultRatio($expectedrestype),
				'deuterium' => $this->getDefaultRatio($this->_restype_deuterium),
			];
		}
		return $trades;
	}

	/**
	 * next 3: calculate total trade volume for a given resource pair
	 */
	private function getTotalMetCrysVolume()
	{
		if ($this->_mkVolume != []) return $this->_mkVolume;

		$allTrades = [
			'metal' => 0,
			'crystal' => 0,
		];

		$kristrades = $this->getMetSales($this->_restype_crystal);

		foreach ($kristrades as $trade) {
			$allTrades['metal'] += $trade['metal'];
			$allTrades['crystal'] += $trade['amount'];
		}

		$mettrades = $this->getCrysSales($this->_restype_metal);

		foreach ($mettrades as $trade) {
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

		foreach ($deutrades as $trade) {
			$allTrades['metal'] += $trade['metal'];
			$allTrades['deuterium'] += $trade['amount'];
		}

		$mettrades = $this->getDeutSales($this->_restype_metal);

		foreach ($mettrades as $trade) {
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

		foreach ($deutrades as $trade) {
			$allTrades['crystal'] += $trade['crystal'];
			$allTrades['deuterium'] += $trade['amount'];
		}

		$crystrades = $this->getDeutSales($this->_restype_crystal);

		foreach ($crystrades as $trade) {
			$allTrades['crystal'] += $trade['amount'];
			$allTrades['deuterium'] += $trade['deuterium'];
		}

		$this->_cdVolume = $allTrades;
		return $allTrades;
	}

	/**
	 * aaand calculate the ratio based on that.
	 */
	private function getMetCrysRatio()
	{
		$trades = $this->getTotalMetCrysVolume();
		return ($trades['metal'] / $trades['crystal']);
	}

	private function getMetDeutRatio()
	{
		$trades = $this->getTotalMetDeutVolume();
		return ($trades['metal'] / $trades['deuterium']);
	}

	private function getCrysDeutRatio()
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

		return $result;
	}

	public function getReferenceRatios()
	{
		$ratios = $this->getAllTradeRatios();

		$result = [
			'metal' => round($ratios['md'], 1),
			'crystal' => round($ratios['cd'], 1),
			'deuterium' => 1,
		];

		return $result;
	}

	/**
	 * @param array ['metal' => x, 'crystal' => y, 'deuterium' => z]
	 * round AND int-cast because only int-cast just cuts off decimals
	 */
	public function convertToMetal($input)
	{
		if (!array_key_exists('metal', $input)) $input['metal'] = 0;
		if (!array_key_exists('crystal', $input)) $input['crystal'] = 0;
		if (!array_key_exists('deuterium', $input)) $input['deuterium'] = 0;

		return (int) round($input['metal']
			+ ($input['crystal'] * $this->getMetCrysRatio())
			+ ($input['deuterium'] * $this->getMetDeutRatio()));
	}

	/** @param aray [901, 902, 903] mkd **/
	public function convertToMetalNumeric($input)
	{
		$output = [
			'metal' => $input[901],
			'crystal' => $input[902],
			'deuterium' => $input[903],
		];

		return $this->convertToMetal($output);
	}

	/** @param array [1, 2, 3] mkd - matching exp_res_type **/
	public function convertExpResTypeToMetal($expResType, $expResAmount)
	{
		$result = [];

		switch ($expResType) {
			case 1:
				$result['metal'] = $expResAmount;
				break;
			case 2:
				$result['crystal'] = $expResAmount;
				break;
			case 3:
				$result['deuterium'] = $expResAmount;
				break;
			default:
				throw new Exception('unexpected resource');
		}

		return $this->convertToMetal($result);
	}

	/**
	 * @param $fleetArray format:
	 * [
	 * 		202 => 5, // kt?
	 * 		203 => 4, // gt?
	 * ]
	 * @return int | default: MSE. $useScore = true: score
	 */
	public function getFleetValue($fleetArray, $useScore = false)
	{
		global $pricelist;

		$totalCostArray = [
			901 => 0,
			902 => 0,
			903 => 0,
		];

		foreach ($fleetArray as $ship => $count) {
			$totalCostArray[901] += $pricelist[$ship]['cost'][901] * $count;
			$totalCostArray[902] += $pricelist[$ship]['cost'][902] * $count;
			$totalCostArray[903] += $pricelist[$ship]['cost'][903] * $count;
		}

		if ($useScore) // return value in points, not metal
		{
			return ($totalCostArray[901] + $totalCostArray[902]) / Config::get()->stat_settings;
		}

		$overallCost = $this->convertToMetalNumeric($totalCostArray);
		return $overallCost;
	}

	/** todo: player class. find or create. **/
	private function getScoreByPlayerId($playerid)
	{
		$db = Database::get();
		$sql = "SELECT
					u.username,
					s.total_points
				FROM %%USERS%% u
				LEFT JOIN %%STATPOINTS%% s ON s.id_owner = u.id AND s.stat_type = 1
				WHERE u.id = :playerID AND u.universe = :universe;";
		$query = $db->selectSingle($sql, array(
			':universe'	=> $this->_universe,
			':playerID'	=> $playerid,
		));

		if (!$query) {
			throw new Exception('the requested player does not exist');
		}

		return $query['total_points'] ?: 0;
	}

	/**
	 * @param $offer & $ask: suggest metal standard units
	 * @param fleetarray optional: check if fleet size is push-relevant
	 * @return bool
	 */
	public function isPush(int $offer, int $ask, int $sellerId, int $buyerId, int $fleetScore = 0)
	{
		$sellerScore = $this->getScoreByPlayerId($sellerId);
		$buyerScore = $this->getScoreByPlayerId($buyerId);

		// special case: buyer is weaker, but becomes stronger when receiving fleet
		$specialFleetPush = ($fleetScore != 0
			&& ($sellerScore > $buyerScore)
			&& ($sellerScore - $fleetScore < $buyerScore + $fleetScore));

		$highestValue = ($offer > $ask) ? $offer : $ask;
		$margin = $highestValue * (1 - $this->_pushTolerance);

		// seller <= buyer - too cheap?
		if ($sellerScore <= $buyerScore || $specialFleetPush) {
			return ($ask < $margin);
		} else // too expensive?
		{
			return ($offer < $margin);
		}
	}

	public function isFleetPush($fleetArray, int $ask, int $sellerId, int $buyerId)
	{
		$fleetScore = $this->getFleetValue($fleetArray, true);
		$fleetValue = $this->getFleetValue($fleetArray);
		return $this->isPush($fleetValue, $ask, $sellerId, $buyerId, $fleetScore);
	}
}
