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

class ShowBattleHallPage extends AbstractGamePage
{
	public static $requireModule = MODULE_BATTLEHALL;

	function __construct()
    {
		parent::__construct();
	}

	private function assembleSelectors()
	{
		global $LNG;

		$Selectors = [];
		$Selectors['memorial'] = [
			MEMORIAL_ALL => $LNG['tkb_all'],
			MEMORIAL_EXCLUDE => $LNG['tkb_exclude'],
			MEMORIAL_ONLY => $LNG['tkb_only'],
		];

		$Selectors['timeframe'] = [
			TIMEFRAME_ALL => $LNG['tkb_all'],
			TIMEFRAME_24H => $LNG['tkb_day'],
			TIMEFRAME_1WK => $LNG['tkb_week'],
			TIMEFRAME_1MTH => $LNG['tkb_month'],
		];

		$Selectors['diplomacy'] = [
			DIPLOMACY_ALL => $LNG['tkb_all'],
			DIPLOMACY_ALLIANCE => $LNG['pl_ally'],
			DIPLOMACY_SELF => $LNG['tkb_self'],
		];

		$maxgala = Config::get()->max_galaxy;

		$Selectors['galaxy'] = [
			0 => $LNG['tkb_all'],
		];

		for ($curgala = 1; $curgala <= $maxgala; $curgala++)
		{
			$Selectors['galaxy'][$curgala] = $curgala;
		}

		return $Selectors;
	}

	function show()
	{
		global $USER, $LNG;
    	require_once 'includes/classes/class.BattleHallFilter.php';

		$memorial = HTTP::_GP('memorial', 1);
		$timeframe = HTTP::_GP('timeframe', 0);
		$diplomacy = HTTP::_GP('diplomacy', 0);
		$galaxy = HTTP::_GP('galaxy', 0);

		$filters = [
			'memorial' => $memorial,
			'timeframe' => $timeframe,
			'diplomacy' => $diplomacy,
			'galaxy' => $galaxy,
		];

		$pBHFilter = new BattleHallFilter($filters);
		$top = $pBHFilter->getTopKBs();

		$TopKBList	= array();
		foreach($top as $data)
		{
			$TopKBList[]	= array(
				'result'	=> $data['result'],
				'date'		=> _date($LNG['php_tdformat'], $data['time'], $USER['timezone']),
				'time'		=> TIMESTAMP - $data['time'],
				'units'		=> $data['units'],
				'rid'		=> $data['rid'],
				'attacker'	=> $data['attacker'],
				'defender'	=> $data['defender'],
			);
		}

		$Selectors = $this->assembleSelectors();

		$this->assign(array(
			'TopKBList'		=> $TopKBList,
			'Selectors'		=> $Selectors,
			'memorial'		=> $memorial,
			'timeframe'		=> $timeframe,
			'diplomacy'		=> $diplomacy,
			'galaxy'		=> $galaxy,
		));

		$this->display('page.battleHall.default.tpl');
	}
}
