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
require_once('includes/classes/Universe.class.php');

class Config
{
    protected $configData = [];
    protected $updateRecords = [];
    protected static $instances = [];


    /**
     * Global configkeys
     *
     * These keys will be changed for every univers of this server!
     */
    protected static $globalConfigKeys	= [
        'game_name',
        'version',
        'timezone',
        'dst',
        'forum_url',
        'git_issues_link',
        'ttf_file',

        'discord_logs_hook',
        'discord_exceptions_hook',
        'discord_tickets_hook',
        
        'mail_active',
        'smtp_sendmail',
        'mail_use',
        'smail_path',
        'smtp_ssl',
        'smtp_host',
        'smtp_port',
        'smtp_user',
        'smtp_pass',
        
        'del_oldstuff',
        'del_user_manually',
        'user_valid',
        'sendmail_inactive',
        'del_user_sendmail',
        
        'message_delete_behavior',
        'message_delete_days',
        
        'use_google_analytics',
        'google_analytics_key',
        
        'recaptcha_priv_key',
        'recaptcha_pub_key',
        
        'disclamer_address',
        'disclamer_phone',
        'disclamer_mail',
        'disclamer_notice',

        'stat_settings',
        'stat',
        'stat_level',
    ];

    /**
     * Global configkeys
     *
     * These keys will be changed for every univers of this server!
     */
    public static function getGlobalConfigKeys()
    {
        return self::$globalConfigKeys;
    }

    /**
     * Return an config object for the requested universe
     *
     * @param int $universe Universe ID
     *
     * @return Config
     */
    public static function get($universe = 0)
    {
        if (empty(self::$instances)) {
            self::generateInstances();
        }

        if ($universe === 0) {
            $universe = Universe::current();
        }

        if (!isset(self::$instances[$universe])) {
            throw new Exception("Unknown universe id: ".$universe);
        }

        return self::$instances[$universe];
    }

    public static function reload()
    {
        self::generateInstances();
    }

    private static function generateInstances()
    {
        $db     = Database::get();
        $sql = 'SELECT * FROM %%CONFIG%%;';
        $configRow = $db->selectSingle($sql);
        try {
            $sql = 'SELECT * FROM %%CONFIG_UNIVERSE%%;';
            $universeResult = $db->select($sql);
            foreach ($universeResult as $universeRow) {
                $sql = 'SELECT * FROM %%CONFIG_UNIVERSE_MODULES%% WHERE `uni` = :universe;';
                $modulesResult = $db->select($sql, [
                    ':universe' => $universeRow['uni'],
                ]);
                $moduls = [];
                foreach ($modulesResult as $modulesRow) {
                    $moduls[$modulesRow['module']] = $modulesRow['state'];
                }
                $modules =& Singleton()->modules;
                foreach ($modules as $moduleKey => $moduleName) {
                    if (!isset($moduls[$moduleKey])) {
                        $sql = 'INSERT INTO %%CONFIG_UNIVERSE_MODULES%% (`uni`, `module`, `module_name`, `state`)
                            VALUES (:uni, :module, :module_name, :state)';
                        $db->insert($sql, [
                            ':uni' => $universeRow['uni'],
                            ':module' => $moduleKey,
                            ':module_name' => $moduleName,
                            ':state' => 0,
                        ]);
                        $moduls[$moduleKey] = 0;
                    }
                }
                $universeRow['moduls'] = $moduls;
                $universeRow = array_merge($universeRow, $configRow);
                self::$instances[$universeRow['uni']] = new self($universeRow);
                Universe::add($universeRow['uni']);
            }
        } catch (\Throwable $th) {
            //keep old way for migrations
            $sql = 'SELECT `uni`, `VERSION` AS `version`, `sql_revision`, `users_amount`, `building_speed`, `shipyard_speed`, `research_speed`,
                `fleet_speed`, `resource_multiplier`, `storage_multiplier`, `message_delete_behavior`, `message_delete_days`, `halt_speed` AS `expo_hold_multiplier`,
                `Fleet_Cdr` AS `fleet_debris_percentage`, `Defs_Cdr` AS `def_debris_percentage`, `initial_fields`, `uni_name`, `game_name`, `uni_status`, `close_reason`,
                `metal_basic_income`, `crystal_basic_income`, `deuterium_basic_income`, `energy_basic_income`, `LastSettedGalaxyPos` AS `last_galaxy_pos`,
                `LastSettedSystemPos` AS `last_system_pos`, `LastSettedPlanetPos` AS `last_planet_pos`, `noobprotection` AS `noob_protection`,
                `noobprotectiontime` AS `noob_protection_time`, `noobprotectionmulti` AS `noob_protection_multi`, `forum_url`, `adm_attack`, `debug`,
                `lang`, `stat`, `stat_level`, `stat_last_update`, `stat_settings`, `stat_update_time`, `stat_last_db_update`, `stats_fly_lock`,
                `cron_lock`, `OverviewNewsFrame` AS `overview_news_frame`, `OverviewNewsText` AS `overview_news_text`, `min_build_time`, `mail_active`, `mail_use`,
                `smtp_host`, `smtp_port`, `smtp_user`, `smtp_pass`, `smtp_ssl`, `smtp_sendmail`, `smail_path`, `user_valid`, `ga_active` AS `use_google_analytics`,
                `ga_key` AS `google_analytics_key`, `moduls`, `trade_allowed_ships`, `trade_charge`, `max_galaxy`, `max_system`, `max_planets`,
                `planet_factor` AS `planet_size_factor`, `all_planet_pictures`, `max_elements_build`, `max_elements_tech`, `max_elements_ships`,
                `min_player_planets` AS `max_initial_planets`, `planets_tech` AS `max_additional_planets`, `planets_per_tech`, `max_fleet_per_build`, `deuterium_cost_galaxy`,
                `max_overflow`, `moon_factor`, `moon_chance`, `moonSizeFactor` AS `moon_size_factor`, `cascading_moon_chance`, `factor_university`, `max_fleets_per_acs`,
                `max_participants_per_acs`, `debris_moon`, `vmode_min_time`, `jumpgate_factor`, `metal_start`, `crystal_start`, `deuterium_start`, `ttf_file`, `git_issues_link`,
                `ref_active`, `ref_bonus`, `ref_minpoints`, `ref_max_referals`, `del_oldstuff`, `del_user_manually`, `del_user_automatic`, `del_user_sendmail`,
                `sendmail_inactive`, `silo_factor`, `timezone`, `dst`, `energySpeed` AS `energy_multiplier`, `disclamerAddress` AS `disclamer_address`,
                `disclamerPhone` AS `disclamer_phone`, `disclamerMail` AS `disclamer_mail`, `disclamerNotice` AS `disclamer_notice`, `alliance_create_min_points`, `uni_type`,
                `galaxy_type`, `planet_creation`, `expo_ress_met_chance`, `expo_ress_crys_chance`, `expo_ress_deut_chance`, `initial_temp`,
                `recaptchaPrivKey` AS `recaptcha_priv_key`, `recaptchaPubKey` AS `recaptcha_pub_key`
                FROM %%CONFIG%%;';
            $configResult = $db->nativeQuery($sql);
            foreach ($configResult as $configRow) {
                self::$instances[$configRow['uni']] = new self($configRow);
                Universe::add($configRow['uni']);
            }
        }
    }

    public function __construct($configData)
    {
        $this->configData = $configData;
    }

    public function __get($key)
    {
        if (!isset($this->configData[$key])) {
            throw new UnexpectedValueException(sprintf("Unknown configuration key %s!", $key));
        }

        return $this->configData[$key];
    }

    public function __set($key, $value)
    {
        if (!isset($this->configData[$key])) {
            throw new UnexpectedValueException(sprintf("Unknown configuration key %s!", $key));
        }
        $this->updateRecords[]  = $key;
        $this->configData[$key] = $value;
    }

    public function __isset($key)
    {
        return isset($this->configData[$key]);
    }

    public function save()
    {
        if (empty($this->updateRecords)) {
            // Do nothing here.
            return true;
        }

        $updateData = [];
        $params = [];
        foreach ($this->updateRecords as $columnName) {
            if (!in_array($columnName, self::$globalConfigKeys) && $columnName != 'moduls') {
                $updateData[] = '`' . $columnName . '` = :' . $columnName;
                $params[':' . $columnName] = $this->configData[$columnName];
            }
        }
        $params[':universe'] = $this->configData['uni'];

        $sql = 'UPDATE %%CONFIG_UNIVERSE%% SET '.implode(', ', $updateData).' WHERE `UNI` = :universe';
        Database::get()->update($sql, $params);

        $this->updateRecords = [];
        return true;
    }

    public function saveGlobalKeys()
    {
        if (empty($this->updateRecords)) {
            // Do nothing here.
            return true;
        }

        $db = Database::get();

        $updateData = [];
        $params = [];

        foreach ($this->updateRecords as $columnName) {
            if (in_array($columnName, self::$globalConfigKeys)) {
                $updateData[] = '`' . $columnName . '` = :' . $columnName;
                $params[':' . $columnName] = $this->configData[$columnName];
            }
        }

        $sql = 'UPDATE %%CONFIG%% SET '.implode(', ', $updateData).';';
        $db->update($sql, $params);

        $this->updateRecords = [];
        return true;
    }

    public function saveModules()
    {
        foreach ($this->configData['moduls'] as $modulKey => $modulValue) {
            $sql = 'UPDATE %%CONFIG_UNIVERSE_MODULES%% SET `state` = :state 
                WHERE `uni` = :universe AND `module` = :module;';
            Database::get()->update($sql, [
                ':universe' => $this->configData['uni'],
                ':module' => $modulKey,
                ':state' => $modulValue,
            ]);
        }
        return true;
    }

    public static function getAll()
    {
        throw new Exception("Config::getAll is deprecated!");
    }
}
