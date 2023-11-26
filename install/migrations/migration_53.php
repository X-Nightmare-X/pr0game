<?php

require_once __DIR__.'/vendor/autoload.php';

define('MODE', 'UPGRADE');
define('ROOT_PATH', str_replace('\\', '/', dirname(__FILE__)) . '/');
set_include_path(ROOT_PATH);

require 'includes/common.php';

try {
    createTempTables();
    processSimulations();
    processRecent();
    
    $unis = Universe::availableUniverses();
    foreach($unis as $uni) {
        processTop($uni);
    }
    
    deleteTables();
    renameTempTables();
} catch (\Throwable $th) {
    error_log('Error: ' . $th->getMessage() . '.');
}

function createTempTables()
{
    $db = Database::get();

    $sql = "CREATE TABLE `" . DB_PREFIX . "raports_temp` (
        `rid` varchar(32) NOT NULL,
        `raport` longtext NOT NULL,
        `time` int(11) NOT NULL,
        `attacker` varchar(255) NOT NULL DEFAULT '',
        `defender` varchar(255) NOT NULL DEFAULT '',
        PRIMARY KEY (`rid`),
        KEY `time` (`time`)
    ) ENGINE = InnoDB DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;";
    $db->nativeQuery($sql);

    $sql = "CREATE TABLE `" . DB_PREFIX . "topkb_temp` (
        `rid` varchar(32) NOT NULL,
        `units` double(50,0) unsigned NOT NULL,
        `result` varchar(1) NOT NULL,
        `time` int(11) NOT NULL,
        `universe` tinyint(3) unsigned NOT NULL,
        `galaxy` tinyint(3) unsigned NULL DEFAULT NULL,
        `memorial` tinyint(1) unsigned NOT NULL DEFAULT '0',
        KEY `time` (`universe`,`rid`,`time`)
    ) ENGINE = InnoDB DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;";
    $db->nativeQuery($sql);

    $sql = "CREATE TABLE `" . DB_PREFIX . "users_to_topkb_temp` (
        `rid` varchar(32) NOT NULL,
        `uid` int(11) NOT NULL,
        `username` varchar(128) NOT NULL,
        `role` tinyint(1) NOT NULL,
        KEY `rid` (`rid`,`role`)
    ) ENGINE = InnoDB DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;";
    $db->nativeQuery($sql);
}

function processSimulations()
{
    $db = Database::get();
    // Last 3 days of sims/pirate/aliens
    $sql = "SELECT * FROM %%RW%% WHERE `defender` = '' AND `time` > :threeDays;";
    $simulations = $db->select($sql, [
        ':threeDays' => TIMESTAMP - TIME_72_HOURS,
    ]);

    foreach ($simulations as $report) {
        $sql = "INSERT INTO `" . DB_PREFIX . "raports_temp` (`rid`, `raport`, `time`, `attacker`, `defender`)
            VALUES (:rid, :raport, :time, :attacker, :defender);";
        try {
            $db->insert($sql, [
                ':rid'      => $report['rid'],
                ':raport'   => $report['raport'],
                ':time'     => $report['time'],
                ':attacker' => $report['attacker'],
                ':defender' => $report['defender'],
            ]);
        } catch (\Throwable $th) {
            error_log('Query: [' . $sql . '] failed. Error: ' . $th->getMessage() . '. Skipped');
        }
    }
}

function processRecent()
{
    $db = Database::get();
    $sql = "SELECT * FROM %%TOPKB%% WHERE `time` > :sevenDays;";
    $recent = $db->select($sql, [
        ':sevenDays' => TIMESTAMP - TIME_1_WEEK,
    ]);

    insertValues($recent);
}

function processTop($universe)
{
    $db = Database::get();
    $sql = "SELECT * FROM %%TOPKB%% WHERE `universe` = :universe ORDER BY `units` LIMIT 150;";
    $top = $db->select($sql, [
        ':universe' => $universe,
    ]);

    insertValues($top);
}

function insertValues(array $topkbs)
{
    $db = Database::get();
    foreach ($topkbs as $topkb) {
        $sql = "INSERT INTO `" . DB_PREFIX . "topkb_temp` (`rid`, `units`, `result`, `time`, `universe`, `galaxy`, `memorial`)
            VALUES (:rid, :units, :result, :time, :universe, :galaxy, :memorial);";
        try {
            $db->insert($sql, [
                ':rid'      => $topkb['rid'],
                ':units'    => $topkb['units'],
                ':result'   => $topkb['result'],
                ':time'     => $topkb['time'],
                ':universe' => $topkb['universe'],
                ':galaxy'   => $topkb['galaxy'],
                ':memorial' => $topkb['memorial'],
            ]);
        } catch (\Throwable $th) {
            error_log('Query: [' . $sql . '] failed. Error: ' . $th->getMessage() . '. Skipped');
        }

        $sql = "SELECT * FROM %%TOPKB_USERS%% WHERE `rid` = :rid;";
        $recentUsers = $db->select($sql, [
            ':rid' => $topkb['rid'],
        ]);
    
        foreach ($recentUsers as $user) {
            $sql = "INSERT INTO `" . DB_PREFIX . "users_to_topkb_temp` (`rid`, `uid`, `username`, `role`)
                VALUES (:rid, :uid, :username, :role);";
            try {
                $db->insert($sql, [
                    ':rid'      => $user['rid'],
                    ':uid'      => $user['uid'],
                    ':username' => $user['username'],
                    ':role'     => $user['role'],
                ]);
            } catch (\Throwable $th) {
                error_log('Query: [' . $sql . '] failed. Error: ' . $th->getMessage() . '. Skipped');
            }
        }
    
        $sql = "SELECT * FROM %%RW%% WHERE `rid` = :rid;";
        $report = $db->selectSingle($sql, [
            ':rid' => $topkb['rid'],
        ]);
    
        $sql = "INSERT INTO `" . DB_PREFIX . "raports_temp` (`rid`, `raport`, `time`, `attacker`, `defender`)
            VALUES (:rid, :raport, :time, :attacker, :defender);";
        try {
            $db->insert($sql, [
                ':rid'      => $report['rid'],
                ':raport'   => $report['raport'],
                ':time'     => $report['time'],
                ':attacker' => $report['attacker'],
                ':defender' => $report['defender'],
            ]);
        } catch (\Throwable $th) {
            error_log('Query: [' . $sql . '] failed. Error: ' . $th->getMessage() . '. Skipped');
        }
    }
}

function deleteTables()
{
    $db = Database::get();

    $sql = "DROP TABLE `" . DB_PREFIX . "raports`;";
    $db->nativeQuery($sql);

    $sql = "DROP TABLE `" . DB_PREFIX . "topkb`;";
    $db->nativeQuery($sql);

    $sql = "DROP TABLE `" . DB_PREFIX . "users_to_topkb`;";
    $db->nativeQuery($sql);
}

function renameTempTables()
{
    $db = Database::get();

    $sql = "RENAME TABLE `" . DB_PREFIX . "raports_temp` TO `" . DB_PREFIX . "raports`;";
    $db->nativeQuery($sql);

    $sql = "RENAME TABLE `" . DB_PREFIX . "topkb_temp` TO `" . DB_PREFIX . "topkb`;";
    $db->nativeQuery($sql);

    $sql = "RENAME TABLE `" . DB_PREFIX . "users_to_topkb_temp` TO `" . DB_PREFIX . "users_to_topkb`;";
    $db->nativeQuery($sql);
}