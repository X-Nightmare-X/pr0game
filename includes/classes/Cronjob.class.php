<?php

/**
 *  2Moons
 *   by Jan-Otto Kröpke 2009-2016
 * For the full copyright and license information, please view the LICENSE
 * @package 2Moons
 * @author Jan-Otto Kröpke <slaver7@gmail.com>
 * @copyright 2009 Lucky
 * @copyright 2016 Jan-Otto Kröpke <slaver7@gmail.com>
 * @licence MIT
 * @version 1.8.0
 * @link https://github.com/jkroepke/2Moons
 */

class Cronjob
{
    public function __construct()
    {
    }

    public static function execute($cronjobID)
    {
        require_once 'includes/classes/class.Discord.php';

        $lockToken = md5(TIMESTAMP);

        $db = Database::get();

        $sql = 'SELECT class FROM %%CRONJOBS%% WHERE isActive = :isActive AND cronjobID = :cronjobId AND `lock` IS NULL;';

        $cronjobClassName = $db->selectSingle($sql, [
            ':isActive' => 1,
            ':cronjobId' => $cronjobID
        ], 'class');

        if (empty($cronjobClassName)) {
            throw new Exception(sprintf("Unknown cronjob %s or cronjob is deactive!", $cronjobID));
        }

        try {
            $db->update('UPDATE %%CRONJOBS%% SET `lock` = :lock WHERE cronjobID = :cronjobId;', [
                ':lock' => $lockToken,
                ':cronjobId' => $cronjobID
            ]);

            $cronjobPath = 'includes/classes/cronjob/' . $cronjobClassName . '.class.php';

            // die hard, if file not exists.
            require_once($cronjobPath);

            /** @var $cronjobObj CronjobTask */
            $cronjobObj = new $cronjobClassName();
            $cronjobObj->run();

            self::reCalculateCronjobs($cronjobID);

            $db->update('UPDATE %%CRONJOBS%% SET `lock` = NULL WHERE cronjobID = :cronjobId;', [
                ':cronjobId' => $cronjobID
            ]);

            $db->insert('INSERT INTO %%CRONJOBS_LOG%% SET `cronjobId` = :cronjobId, `executionTime` = :executionTime, `lockToken` = :lockToken', [
                ':cronjobId' => $cronjobID,
                ':executionTime' => Database::formatDate(TIMESTAMP),
                ':lockToken' => $lockToken
            ]);

            Discord::sendLog($cronjobClassName, ['successful' => true]);
        } catch (Exception $exception) {
            // unlock cronjob
            $db->update('UPDATE %%CRONJOBS%% SET `lock` = :lock WHERE cronjobID = :cronjobId;', [
                ':lock' => null,
                ':cronjobId' => $cronjobID
            ]);

            Discord::sendLog($cronjobClassName, null, $exception);
        }
    }

    public static function getNeedTodoExecutedJobs()
    {
        $sql = 'SELECT cronjobID
		FROM %%CRONJOBS%%
		WHERE isActive = :isActive AND nextTime < :time AND `lock` IS NULL;';

        $cronjobResult = Database::get()->select($sql, [
            ':isActive' => 1,
            ':time' => TIMESTAMP
        ]);

        $cronjobList = [];

        foreach ($cronjobResult as $cronjobRow) {
            $cronjobList[] = $cronjobRow['cronjobID'];
        }

        return $cronjobList;
    }

    public static function getLastExecutionTime($cronjobName)
    {
        $sql = 'SELECT MAX(executionTime) as executionTime FROM %%CRONJOBS_LOG%% INNER JOIN %%CRONJOBS%% USING(cronjobId) WHERE name = :cronjobName;';
        $lastTime = Database::get()->selectSingle($sql, [
            ':cronjobName' => $cronjobName
        ], 'executionTime');

        if (empty($lastTime)) {
            return false;
        }

        return strtotime($lastTime);
    }

    public static function reCalculateCronjobs($cronjobID = null)
    {
        $db = Database::get();

        if (!empty($cronjobID)) {
            $sql = 'SELECT cronjobID, min, hours, dom, month, dow FROM %%CRONJOBS%% WHERE cronjobID = :cronjobId;';
            $cronjobResult = $db->select($sql, [
                ':cronjobId' => $cronjobID
            ]);
        } else {
            $sql = 'SELECT cronjobID, min, hours, dom, month, dow FROM %%CRONJOBS%%;';
            $cronjobResult = $db->select($sql);
        }

        $sql = 'UPDATE %%CRONJOBS%% SET nextTime = :nextTime WHERE cronjobID = :cronjobId;';

        foreach ($cronjobResult as $cronjobRow) {
            $cronTabString = implode(' ', [$cronjobRow['min'], $cronjobRow['hours'], $cronjobRow['dom'], $cronjobRow['month'], $cronjobRow['dow']]);

            $cron = new Cron\CronExpression($cronTabString);
            $nextTime= $cron->getNextRunDate(new DateTime('@' . TIMESTAMP + 60))->getTimestamp();

            $db->update($sql, [
                ':nextTime' => $nextTime,
                ':cronjobId' => $cronjobRow['cronjobID'],
            ]);
        }
    }
}
