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
    public function run()
    {
        $this->clearCache();
        $this->reCalculateCronjobs();
        $this->clearEcoCache();
        $universes = Universe::availableUniverses();
        foreach ($universes as $universe) {
            $uni = $universe['uni'];
            $this->cancelVacation($uni);
            $this->updateInactiveMines($uni);
            $this->emptyInactiveAllianceAndBuddy($uni);
        }
        // $this->cleanReports($universes);
        $this->eraseIPAdresses();
    }

    public function clearCache()
    {
        ClearCache();
    }

    public function reCalculateCronjobs()
    {
        Cronjob::reCalculateCronjobs();
    }

    public function clearEcoCache()
    {
        $sql	= "UPDATE %%PLANETS%% SET eco_hash = '';";
        Database::get()->update($sql);
    }

    public function eraseIPAdresses()
    {
        $sql = "UPDATE %%MULTI%% SET multi_ip = 'cleared' WHERE lastActivity < :oneMonth;";
        Database::get()->update($sql, [':oneMonth' => TIMESTAMP - TIME_1_MONTH]);
    }

    public function cancelVacation($universe)
    {
        if (isModuleAvailable(MODULE_VMODE_KICK, $universe)) {
            $sql = "SELECT id, b_tech_planet, b_tech, b_tech_id, b_tech_queue, urlaubs_until, universe FROM %%USERS%%
                    WHERE urlaubs_modus = 1 AND onlinetime < :inactive AND bana = 0 AND universe = :universe;";
            $players = Database::get()->select($sql, [
                ':inactive' => TIMESTAMP - INACTIVE_LONG,
                ':universe' => $universe
            ]);
            foreach ($players as $player) {
                PlayerUtil::disable_vmode($player);
                PlayerUtil::clearPlanets($player);
            }
        }
    }

    public function emptyInactiveAllianceAndBuddy($universe) {
        if (isModuleAvailable(MODULE_EMPTY_BUDDY, $universe)) {
            $db = Database::get();
            $sql = "SELECT u.id FROM %%USERS%% AS u WHERE urlaubs_modus = 0 AND onlinetime < :inactive AND universe = :universe;";
            $players = $db->select($sql, [
                ':inactive' => TIMESTAMP - INACTIVE,
                ':universe' => $universe
            ]);
            foreach ($players as $player) {
                $sql = 'SELECT id, ally_id FROM %%USERS%% WHERE id = :userId;';
                $userData = $db->selectSingle($sql, [
                    ':userId'   => $player['id']
                ]);
                PlayerUtil::removeFromAlliance($userData);
                PlayerUtil::removeFromBuddy($player['id']);
            }
        }
    }

    public function updateInactiveMines($universe)
    {
        $sql = "UPDATE %%PLANETS%% set metal_mine_porcent = :full, crystal_mine_porcent = :full, deuterium_sintetizer_porcent = :full, solar_plant_porcent = :full, fusion_plant_porcent = :full, solar_satelit_porcent = :full
				WHERE planet_type = :planet AND id_owner IN (SELECT u.id FROM %%USERS%% AS u WHERE urlaubs_modus = 0 AND onlinetime < :inactive AND universe = :universe);";

        Database::get()->update($sql, [
            ':full' 	=> 10, // 10 = 100%
            ':planet'	=> 1,
            ':inactive' => TIMESTAMP - INACTIVE,
            ':universe' => $universe
        ]);
    }

    /**
     * Deletes old battle reports depending on config settings and unit size for battle hall
     *
     * @param array $universes
     * @return void
     */
    private function cleanReports(array $universes)
    {
        $db = Database::get();

        $config	= Config::get(ROOT_UNI);
        $del_before = TIMESTAMP - ($config->del_oldstuff * 86400); // Global setting: Delete messages/reports after X days

        foreach ($universes as $universe) {
            // Select lowest units to keep per universe
            $sql = "SELECT `units` FROM %%TOPKB%% WHERE `universe` = :universe AND `memorial` = 0 ORDER BY `units` DESC LIMIT 1 OFFSET :topKbLimLow;";
            $units = $db->selectSingle($sql, [
                ':universe' => $universe,
                ':topKbLimLow'  => TOPKB_LIMIT-1,
            ], 'units');

            if (!empty($units)) {
                // Delete lower battlehall KBs
                $sql = "DELETE FROM %%TOPKB%% WHERE `universe` = :universe AND `units` < :units AND `memorial` = 0;";
                $db->delete($sql. [
                    ':universe' => $universe,
                    ':units' => $units,
                ]);
            }
        }

        // Delete simulations/expofights older than 3 days and KBs older than config setting and not in Battlehall
        $sql = "DELETE FROM %%RW%% WHERE 
            ( `defender` = '' AND `time` < :threeDays ) OR
            ( `defender` != '' AND `time` < :oldStuff AND `rid` NOT IN ( SELECT `rid` FROM %%TOPKB%% ) );";
        $db->delete($sql, [
            ':threeDays' => TIMESTAMP - TIME_72_HOURS, // Keep only last 3 days of sims/pirate/aliens
            ':oldStuff' => TIMESTAMP - $del_before,
        ]);

        // Delete unused user_to_topkb
        $sql = "DELETE FROM %%TOPKB_USERS%% WHERE `rid` NOT IN ( SELECT `rid` FROM %%TOPKB%% );";
        $db->delete($sql);
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
