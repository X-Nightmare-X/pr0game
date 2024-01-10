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
        'VERSION', 'stat', 'stat_level', 'stat_last_update', 'stat_settings',
        'stat_update_time', 'stat_last_db_update', 'stats_fly_lock', 'cron_lock',
        'game_name', 'ttf_file', 'timezone', 'dst', 'git_issues_link',
        'del_oldstuff', 'del_user_manually', 'sendmail_inactive', 'del_user_sendmail',
        'mail_active', 'mail_use', 'smtp_sendmail', 'smail_path', 'smtp_host',
        'smtp_ssl', 'smtp_port', 'smtp_user', 'smtp_pass',
        'message_delete_behavior', 'message_delete_days', 'ga_active', 'ga_key',
        'disclamerAddress', 'disclamerPhone', 'disclamerMail', 'disclamerNotice',
        'recaptchaPrivKey', 'recaptchaPubKey'
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
        $configRow = $db->selectSingle('SELECT * FROM %%CONFIG%%;');
        $universeResult = $db->select('SELECT * FROM %%CONFIG_UNIVERSE%%;');
        foreach ($universeResult as $universeRow) {
            $modulesResult = $db->select('SELECT * FROM %%CONFIG_UNIVERSE_MODULES%% WHERE `uni` = :universe;', [':universe' => $universeRow['uni']]);
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
                        ':state' => 1,
                    ]);
                    $moduls[$moduleKey] = 1;
                }
            }
            $universeRow['modules'] = $moduls;
            $universeRow = array_merge($universeRow, $configRow);
            self::$instances[$universeRow['uni']] = new self($universeRow);
            Universe::add($universeRow['uni']);
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
            if (!in_array($columnName, self::$globalConfigKeys)) {
                $updateData[] = '`' . $columnName . '` = :' . $columnName;
                $params[':' . $columnName] = $this->configData[$columnName];
            }
        }
        $params[':universe'] = $this->configData['uni'];

        $sql = 'UPDATE %%CONFIG%% SET '.implode(', ', $updateData).' WHERE `UNI` = :universe';
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

        foreach (Universe::availableUniverses() as $universeId) {
            $params[':universe'] = $universeId;
            
            $sql = 'UPDATE %%CONFIG%% SET '.implode(', ', $updateData).' WHERE `UNI` = :universe';
            $db->update($sql, $params);
        }

        $this->updateRecords = [];
        return true;
    }

    public static function getAll()
    {
        throw new Exception("Config::getAll is deprecated!");
    }
}
