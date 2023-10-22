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

if ($USER['authlevel'] != AUTH_ADM || $_GET['sid'] != session_id()) {
    throw new Exception("Permission error!");
}

function ShowUniversePage()
{
    $LNG =& Singleton()->LNG;
    $USER =& Singleton()->USER;
    $template   = new template();

    require_once('includes/classes/Database.class.php');
    $db = Database::get();

    $action     = HTTP::_GP('action', '');
    $universe   = HTTP::_GP('uniID', 0);
    switch ($action) {
        case 'open':
            $config = Config::get($universe);
            $config->uni_status = STATUS_OPEN;
            $config->save();
            break;
        case 'closed':
            $config = Config::get($universe);
            $config->uni_status = STATUS_CLOSED;
            $config->save();
            break;
        case 'delete':
            if (!empty($universe) && $universe != ROOT_UNI && $universe != Universe::current()) {
                $sql = "DELETE FROM %%ALLIANCE%%, %%ALLIANCE_RANK%%, %%ALLIANCE_REQUEST%%
                    USING %%ALLIANCE%%
                    LEFT JOIN %%ALLIANCE_RANK%% ON %%ALLIANCE%%.`id` = %%ALLIANCE_RANK%%.`allianceID`
                    LEFT JOIN %%ALLIANCE_REQUEST%% ON %%ALLIANCE%%.`id` = %%ALLIANCE_REQUEST%%.`allianceID`
                    WHERE `ally_universe` = :universe;";
                $db->delete($sql, [':universe' => $universe]);

                $sql = "DELETE FROM %%BANNED%% WHERE `universe` = :universe;";
                $db->delete($sql, [':universe' => $universe]);

                $sql = "DELETE FROM %%BUDDY%%, %%BUDDY_REQUEST%%
                    USING %%BUDDY%%
                    LEFT JOIN %%BUDDY_REQUEST%% ON %%BUDDY%%.`id` = %%BUDDY_REQUEST%%.`id`
                    WHERE %%BUDDY%%.`universe` = :universe;";
                $db->delete($sql, [':universe' => $universe]);

                $sql = "DELETE FROM %%CONFIG%% WHERE `uni` = :universe;";
                $db->delete($sql, [':universe' => $universe]);

                $sql = "DELETE FROM %%DIPLO%% WHERE `universe` = :universe;";
                $db->delete($sql, [':universe' => $universe]);

                $sql = "DELETE FROM %%FLEETS%%, %%FLEETS_EVENT%%, %%AKS%%, %%LOG_FLEETS%%, %%TRADES%%
                    USING %%FLEETS%%
                    LEFT JOIN %%FLEETS_EVENT%% ON %%FLEETS%%.`fleet_id` = %%FLEETS_EVENT%%.`fleetID`
                    LEFT JOIN %%AKS%% ON %%FLEETS%%.`fleet_id` = %%AKS%%.`id`
                    LEFT JOIN %%LOG_FLEETS%% ON %%FLEETS%%.`fleet_id` = %%LOG_FLEETS%%.`fleet_id`
                    LEFT JOIN %%TRADES%% ON %%FLEETS%%.`fleet_id` = %%TRADES%%.`seller_fleet_id`
                    WHERE %%FLEETS%%.`fleet_universe` = :universe;";
                $db->delete($sql, [':universe' => $universe]);

                $sql = "DELETE FROM %%LOG_FLEETS%% WHERE `fleet_universe` = :universe;";
                $db->delete($sql, [':universe' => $universe]);

                $sql = "DELETE FROM %%MESSAGES%% WHERE `message_universe` = :universe;";
                $db->delete($sql, [':universe' => $universe]);

                $sql = "DELETE FROM %%NOTES%% WHERE `universe` = :universe;";
                $db->delete($sql, [':universe' => $universe]);

                $sql = "DELETE FROM %%PLANETS%% WHERE `universe` = :universe;";
                $db->delete($sql, [':universe' => $universe]);

                $sql = "DELETE FROM %%STATPOINTS%% WHERE `universe` = :universe;";
                $db->delete($sql, [':universe' => $universe]);

                $sql = "DELETE FROM %%TICKETS%%, %%TICKETS_ANSWER%%
                    USING %%TICKETS%%
                    LEFT JOIN %%TICKETS_ANSWER%% ON %%TICKETS%%.`ticketID` = %%TICKETS_ANSWER%%.`ticketID`
                    WHERE `universe` = :universe;";
                $db->delete($sql, [':universe' => $universe]);

                $sql = "DELETE FROM %%TOPKB%% WHERE universe = :universe;";
                $db->delete($sql, [':universe' => $universe]);

                $sql = "DELETE FROM %%USERS%%, %%USERS_ACS%%, %%TOPKB_USERS%%, %%SESSION%%, %%SHORTCUTS%%, %%RECORDS%%, %%USERS_TO_ACHIEVEMENTS%%, %%USERS_COMMENTS%%, %%RECAPTCHA%%, %%ADVANCED_STATS%%
                    USING %%USERS%%
                    LEFT JOIN %%USERS_ACS%% ON %%USERS%%.`id` = %%USERS_ACS%%.`userID`
                    LEFT JOIN %%TOPKB_USERS%% ON %%USERS%%.`id` = %%TOPKB_USERS%%.`uid`
                    LEFT JOIN %%SESSION%% ON %%USERS%%.`id` = %%SESSION%%.`userID`
                    LEFT JOIN %%SHORTCUTS%% ON %%USERS%%.`id` = %%SHORTCUTS%%.`ownerID`
                    LEFT JOIN %%RECORDS%% ON %%USERS%%.`id` = %%RECORDS%%.`userID`
                    LEFT JOIN %%LOSTPASSWORD%% ON %%USERS%%.`id` = %%LOSTPASSWORD%%.`userID`
                    LEFT JOIN %%USERS_TO_ACHIEVEMENTS%% ON %%USERS%%.`id` = %%USERS_TO_ACHIEVEMENTS%%.`userID`
                    LEFT JOIN %%USERS_COMMENTS%% ON %%USERS%%.`id` = %%USERS_COMMENTS%%.`id`
                    LEFT JOIN %%RECAPTCHA%% ON %%USERS%%.`id` = %%RECAPTCHA%%.`userID`
                    LEFT JOIN %%ADVANCED_STATS%% ON %%USERS%%.`id` = %%ADVANCED_STATS%%.`userId`
                    WHERE %%USERS%%.`universe` = :universe;";
                $db->delete($sql, [':universe' => $universe]);

                $sql = "DELETE FROM %%USERS_VALID%% WHERE universe = :universe;";
                $db->delete($sql, [':universe' => $universe]);

                $sql = "DELETE FROM %%MARKETPLACE%% WHERE `universeId` = :universe;";
                $db->delete($sql, [':universe' => $universe]);

                if (Universe::getEmulated() == $universe) {
                    Universe::setEmulated(Universe::current());
                }

                if (count(Universe::availableUniverses()) == 2) {
                    // Hack The Session
                    setcookie(session_name(), session_id(), SESSION_LIFETIME, HTTP_BASE, null, HTTPS, true);
                    HTTP::redirectTo("../admin.php?reload=r");
                }
            }
            break;
        case 'create':
            $universeCount = max(Universe::availableUniverses());
            // Check Multiuniverse Support
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, PROTOCOL . HTTP_HOST . HTTP_BASE . "uni" . ($universeCount + 1) . "/");
            curl_setopt($ch, CURLOPT_HTTPGET, true);
            curl_setopt($ch, CURLOPT_AUTOREFERER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (compatible; pr0game/" . Config::get()->VERSION
                . "; +https://pr0game.com)");
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8",
                "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.3",
                "Accept-Language: de-DE,de;q=0.8,en-US;q=0.6,en;q=0.4",
            ]);
            if (str_contains(HTTP_HOST, '127.0.0.1') || str_contains(HTTP_HOST, 'localhost')) {
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            }
            curl_exec($ch);
            $httpCode   = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);

            curl_close($ch);
            if ($httpCode != 302) {
                $template = new template();
                $template->message(str_replace(
                    [
                        '{NGINX-CODE}'
                    ],
                    [
                        #'rewrite '.HTTP_ROOT.'uni[0-9]+/?(.*)?$ '.HTTP_ROOT.'$2 break;'
                        'rewrite /(.*)/?uni[0-9]+/?(.*) /$1/$2 break;'
                    ],
                    $LNG->getTemplate('createUniverseInfo')
                )
                . '<a href="javascript:window.history.back();"><button>' . $LNG['uvs_back'] . '</button></a>'
                . '<a href="javascript:window.location.reload();"><button>' . $LNG['uvs_reload'] . '</button></a>');
                exit;
            }

            $config = Config::get();

            $configSQL  = [];
            foreach (Config::getGlobalConfigKeys() as $basicConfigKey) {
                $configSQL[]    = '`' . $basicConfigKey . '` = "' . $config->$basicConfigKey . '"';
            }

            $configSQL[]    = '`uni_name` = "' . $LNG['fcm_universe'] . ' ' . ($universeCount + 1) . '"';
            $configSQL[]    = '`uni_status` = "1"';
            $configSQL[]    = '`close_reason` = "The universe is being set up. Please wait."';
            $configSQL[]    = '`OverviewNewsText` = "' . $config->OverviewNewsText . '"';

            $sql = "INSERT INTO %%CONFIG%% SET " . implode(', ', $configSQL) . ";";
            $db->insert($sql);
            $newUniverse = $db->lastInsertId();

            Config::reload();
            $sql = "INSERT INTO %%MARKETPLACE%% (`universeId`) VALUES (:universe);";
            $db->insert($sql, [
                ':universe' => $newUniverse,
            ]);
            list($userID, $planetID) = PlayerUtil::createPlayer(
                $newUniverse,
                $USER['username'],
                '',
                $USER['email'],
                $USER['lang'],
                1,
                1,
                1,
                null,
                AUTH_ADM
            );

            $sql = "UPDATE %%USERS%% SET `password` = :pass WHERE id = :userID;";
            $db->update($sql, [
                ':pass' => $USER['password'],
                ':userID' => $userID,
            ]);

            if ($universeCount === 1) {
                // Hack The Session
                setcookie(
                    session_name(),
                    session_id(),
                    SESSION_LIFETIME,
                    HTTP_ROOT . 'uni' . $USER['universe'] . '/',
                    null,
                    HTTPS,
                    true
                );
                HTTP::redirectTo("uni" . $USER['universe'] . "/admin.php?reload=r");
            }
            break;
    }

    $uniList = [];

    $sql = "SELECT `uni`, `users_amount`, `uni_status`, `energySpeed`, `halt_speed`, `resource_multiplier`, `fleet_speed`, `building_speed`,
                `shipyard_speed`, `research_speed`, `uni_name`, COUNT(DISTINCT inac.`id`) as inactive, COUNT(planet.`id`) as planet
	    FROM %%CONFIG%% conf
	    LEFT JOIN %%USERS%% as inac ON `uni` = inac.`universe` AND inac.`onlinetime` < :inactive
	    LEFT JOIN %%PLANETS%% as planet ON `uni` = planet.`universe`
	    GROUP BY conf.`uni`, inac.`universe`, planet.`universe`
	    ORDER BY uni ASC;";
    $uniResult = $db->select($sql, [':inactive' => TIMESTAMP - INACTIVE]);

    foreach ($uniResult as $uniRow) {
        $uniList[$uniRow['uni']] = $uniRow;
    }

    $template->assign_vars([
        'uniList'       => $uniList,
        'SID'           => session_id(),
        'signalColors'  => $USER['signalColors']
    ]);

    $template->show('UniversePage.tpl');
}
