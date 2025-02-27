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

class CleanerCronjob implements CronjobTask
{
    public function run()
    {
        $config	= Config::get(ROOT_UNI);

        $unis	= Universe::availableUniverses();

        //Delete old messages
        $del_before 	= TIMESTAMP - ($config->del_oldstuff * 86400);
        $del_inactive 	= TIMESTAMP - ($config->del_user_automatic * 86400);
        $del_deleted 	= TIMESTAMP - ($config->del_user_manually * 86400);
        $del_messages 	= TIMESTAMP - ($config->message_delete_days * 86400);

        if ($del_inactive === TIMESTAMP) {
            $del_inactive = 2147483647; // Tue Jan 19 2038 04:14:07 GMT+0100
        }

        $sql	= 'DELETE FROM %%MESSAGES%% WHERE `message_time` < :time;';
        Database::get()->delete($sql, [
            ':time'	=> $del_before
        ]);

        $sql	= 'DELETE FROM %%ALLIANCE%% WHERE `ally_members` = 0;';
        Database::get()->delete($sql);

        $sql	= 'DELETE FROM %%PLANETS%% WHERE `destruyed` < :time AND `destruyed` != 0;';
        Database::get()->delete($sql, [
            ':time'	=> TIMESTAMP
        ]);

        $sql	= 'DELETE FROM %%SESSION%% WHERE `lastonline` < :time;';
        Database::get()->delete($sql, [
            ':time'	=> TIMESTAMP - SESSION_LIFETIME
        ]);

        $sql	= 'DELETE FROM %%FLEETS_EVENT%% WHERE fleetID NOT IN (SELECT fleet_id FROM %%FLEETS%%);';
        Database::get()->delete($sql);

        $sql	= 'DELETE FROM %%LOG_FLEETS%% WHERE `start_time` < :time;';
        Database::get()->delete($sql, [
            ':time'	=> $del_before
        ]);

        $sql	= 'UPDATE %%USERS%% SET `email_2` = `email` WHERE `setmail` < :time;';
        Database::get()->update($sql, [
            ':time'	=> TIMESTAMP
        ]);

        $sql	= 'UPDATE %%PLANETS%% SET `tf_active` = 0 WHERE `tf_active` = 1;';
        Database::get()->update($sql);

        $sql	= 'DELETE FROM %%PHALANX_LOG%% WHERE `phalanx_time` < :time;';
        Database::get()->delete($sql, [
            ':time'	=> $del_before
        ]);

        $sql	= 'DELETE FROM %%PHALANX_FLEETS%% WHERE `phalanx_log_id` NOT IN (SELECT `id` FROM %%PHALANX_LOG%%)';
        Database::get()->delete($sql);

        foreach($unis as $uni)
        {
            $config	= Config::get($uni);
            $status = $config->uni_status;
            if ($status == STATUS_REG_ONLY) {
                continue;
            }

            $del_inactive 	= TIMESTAMP - ($config->del_user_automatic * 86400);
            $del_deleted 	= TIMESTAMP - ($config->del_user_manually * 86400);
            $sql	= 'SELECT `id` FROM %%USERS%% WHERE `authlevel` = :authlevel AND universe = :uni
		    AND ((`db_deaktjava` != 0 AND `db_deaktjava` < :timeDeleted) OR `onlinetime` < :timeInactive);';

            $deleteUserIds = Database::get()->select($sql, [
                ':authlevel'	=> AUTH_USR,
                ':timeDeleted'	=> $del_deleted,
                ':timeInactive'	=> $del_inactive,
                ':uni'			=> $uni
            ]);

            if (!empty($deleteUserIds)) {
                foreach ($deleteUserIds as $dataRow) {
                    PlayerUtil::deletePlayer($dataRow['id']);
                }
            }
        }

        // do not delete hall of fame
        // foreach($unis as $uni)
        // {
        // $sql	= 'SELECT units FROM %%TOPKB%% WHERE `universe` = :universe ORDER BY units DESC LIMIT 99,1;';

        // $battleHallLowest	= Database::get()->selectSingle($sql, array(
        // ':universe'	=> $uni
        // ),'units');

        // if(!is_null($battleHallLowest))
        // {
        // $sql	= 'DELETE %%TOPKB%%, %%TOPKB_USERS%%
        // FROM %%TOPKB%%
        // INNER JOIN %%TOPKB_USERS%% USING (rid)
        // WHERE `universe` = :universe AND `units` < :battleHallLowest;';

        // Database::get()->delete($sql, array(
        // ':universe'			=> $uni,
        // ':battleHallLowest'	=> $battleHallLowest
        // ));
        // }
        // }

        // do not delete combat reports
        // $sql	= 'DELETE FROM %%RW%% WHERE `time` < :time AND `rid` NOT IN (SELECT `rid` FROM %%TOPKB%%);';
        // Database::get()->delete($sql, array(
        // ':time'	=> $del_before
        // ));

        $sql	= 'DELETE FROM %%MESSAGES%% WHERE `message_deleted` < :time;';
        Database::get()->delete($sql, [
            ':time'	=> $del_messages
        ]);
    }
}
