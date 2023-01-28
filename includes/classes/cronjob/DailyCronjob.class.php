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
		$this->cancelVacation();
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

	function cancelVacation() {
		$sql = "SELECT id, b_tech_planet, b_tech, b_tech_id, b_tech_queue, urlaubs_until, universe FROM %%USERS%%
				WHERE urlaubs_modus = 1 AND onlinetime < :inactive AND bana = 0;";
		$players = Database::get()->select($sql, [
			':inactive' => TIMESTAMP - INACTIVE_LONG,
		]);

		foreach ($players as $player) {
			PlayerUtil::disable_vmode($player);
		}
	}

	function updateInactiveMines() {
		$sql = "UPDATE %%PLANETS%% set metal_mine_porcent = :full, crystal_mine_porcent = :full, deuterium_sintetizer_porcent = :full, solar_plant_porcent = :full, fusion_plant_porcent = :full, solar_satelit_porcent = :full
				WHERE planet_type = :planet AND id_owner IN ( SELECT u.id FROM %%USERS%% AS u WHERE urlaubs_modus = 0 AND onlinetime < :inactive );";

		Database::get()->update($sql, [
			':full' 	=> 10, // 10 = 100%
			':planet'	=> 1,
			':inactive' => TIMESTAMP - INACTIVE,
		]);
	}
}


/* 
	Enable to debug 
	includes/classes/Universe.class.php set var $currentUniverse to 1 instead of NULL
*/

// define('MODE', 'INSTALL');
// require 'includes/constants.php';
// require 'includes/classes/Database.class.php';
// require 'includes/classes/Cache.class.php';
// require 'includes/vars.php';
// require 'includes/classes/PlayerUtil.class.php';
// require 'includes/classes/class.PlanetRessUpdate.php';
// require 'includes/classes/Config.class.php';
// require 'includes/classes/Universe.class.php';
// Config::get(1);
// define('TIMESTAMP', time());
// $class = new DailyCronJob();
// $class->cancelVacation();
