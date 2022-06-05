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

require_once 'includes/classes/cronjob/CronjobTask.interface.php';

class DailyCronJob implements CronjobTask
{
	function run()
	{
		$this->clearCache();
		$this->reCalculateCronjobs();
		$this->clearEcoCache();
		$this->updateInactiveMines();
	}

	function clearCache()
	{
		ClearCache();
	}
	
	function reCalculateCronjobs()
	{
		Cronjob::reCalculateCronjobs();
	}
	
	function clearEcoCache()
	{
		$sql	= "UPDATE %%PLANETS%% SET eco_hash = '';";
		Database::get()->update($sql);
	}

	function updateInactiveMines() {
		$sql = "UPDATE %%PLANETS%% set metal_mine_porcent = :full, crystal_mine_porcent = :full, deuterium_sintetizer_porcent = :full, solar_plant_porcent = :full, fusion_plant_porcent = :full, solar_satelit_porcent = :full
				WHERE planet_type = :planet AND id_owner IN ( SELECT u.id FROM %%USERS%% AS u WHERE onlinetime < :inactive );";

		Database::get()->update($sql, [
			':full' 	=> 11, //Index of 10 in [] 0 to 10
			':planet'	=> 1,
			':inactive' => TIMESTAMP - INACTIVE,
		]);
	}
}
