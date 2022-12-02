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
    global $LNG, $USER;
    $template   = new template();

    $action     = HTTP::_GP('action', '');
    $universe   = HTTP::_GP('uniID', 0);
    if(isset($USER['id'])) {
		$signalColors = PlayerUtil::player_signal_colors($USER);
	}
	else {
		$signalColors = array('colorPositive' => '#00ff00', 'colorNegative' => '#ff0000', 'colorNeutral' => '#ffd600');
	}
    switch ($action) {
        case 'open':
            $config = Config::get($universe);
            $config->game_disable = 1;
            $config->save();
            break;
        case 'closed':
            $config = Config::get($universe);
            $config->game_disable = 0;
            $config->save();
            break;
        case 'delete':
            if (!empty($universe) && $universe != ROOT_UNI && $universe != Universe::current()) {
                $GLOBALS['DATABASE']->query("DELETE FROM " . ALLIANCE . ", " . ALLIANCE_RANK . ", " . ALLIANCE_REQUEST
                . " USING " . ALLIANCE . "
				LEFT JOIN " . ALLIANCE_RANK . " ON " . ALLIANCE . ".id = " . ALLIANCE_RANK . ".allianceID
				LEFT JOIN " . ALLIANCE_REQUEST . " ON " . ALLIANCE . ".id = " . ALLIANCE_REQUEST . " .allianceID
				WHERE ally_universe = " . $universe . ";");
                $GLOBALS['DATABASE']->query("DELETE FROM " . BANNED . " WHERE universe = " . $universe . ";");
                $GLOBALS['DATABASE']->query("DELETE FROM " . BUDDY . ", " . BUDDY_REQUEST . "
				USING " . BUDDY . "
				LEFT JOIN " . BUDDY_REQUEST . " ON " . BUDDY . ".id = " . BUDDY_REQUEST . ".id
				WHERE " . BUDDY . ".universe = " . $universe . ";");
                $GLOBALS['DATABASE']->query("DELETE FROM " . CONFIG . " WHERE uni = " . $universe . ";");
                $GLOBALS['DATABASE']->query("DELETE FROM " . DIPLO . " WHERE universe = " . $universe . ";");
                $GLOBALS['DATABASE']->query("DELETE FROM " . FLEETS . ", " . FLEETS_EVENT . ", " . AKS . ", "
                . LOG_FLEETS . " USING " . FLEETS . "
				LEFT JOIN " . FLEETS_EVENT . " ON " . FLEETS . ".fleet_id = " . FLEETS_EVENT . ".fleetID
				LEFT JOIN " . AKS . " ON " . FLEETS . ".fleet_group = " . AKS . ".id
				LEFT JOIN " . LOG_FLEETS . " ON " . FLEETS . ".fleet_id = " . LOG_FLEETS . ".fleet_id
				WHERE " . FLEETS . ".fleet_universe = " . $universe . ";");
                $GLOBALS['DATABASE']->query("DELETE FROM " . MESSAGES . " WHERE message_universe = " . $universe . ";");
                $GLOBALS['DATABASE']->query("DELETE FROM " . NOTES . " WHERE universe = " . $universe . ";");
                $GLOBALS['DATABASE']->query("DELETE FROM " . PLANETS . " WHERE universe = " . $universe . ";");
                $GLOBALS['DATABASE']->query("DELETE FROM " . STATPOINTS . " WHERE universe = " . $universe . ";");
                $GLOBALS['DATABASE']->query("DELETE FROM " . TICKETS . ", " . TICKETS_ANSWER . "
				USING " . TICKETS . "
				LEFT JOIN " . TICKETS_ANSWER . " ON " . TICKETS . ".ticketID = " . TICKETS_ANSWER . ".ticketID
				WHERE universe = " . $universe . ";");
                $GLOBALS['DATABASE']->query("DELETE FROM " . TOPKB . " WHERE universe = " . $universe . ";");
                $GLOBALS['DATABASE']->query("DELETE FROM " . USERS . ", " . USERS_ACS . ", " . TOPKB_USERS . ", "
                    . SESSION . ", " . SHORTCUTS . ", " . RECORDS . "
				USING " . USERS . "
				LEFT JOIN " . USERS_ACS . " ON " . USERS . ".id = " . USERS_ACS . ".userID
				LEFT JOIN " . TOPKB_USERS . " ON " . USERS . ".id = " . TOPKB_USERS . ".uid
				LEFT JOIN " . SESSION . " ON " . USERS . ".id = " . SESSION . ".userID
				LEFT JOIN " . SHORTCUTS . " ON " . USERS . ".id = " . SHORTCUTS . ".ownerID
				LEFT JOIN " . RECORDS . " ON " . USERS . ".id = " . RECORDS . ".userID
				LEFT JOIN " . LOSTPASSWORD . " ON " . USERS . ".id = " . LOSTPASSWORD . ".userID
				WHERE " . USERS . ".universe = " . $universe . ";");
                $GLOBALS['DATABASE']->query("DELETE FROM " . USERS_VALID . " WHERE universe = " . $universe . ";");
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
            $universeCount = count(Universe::availableUniverses());
            // Check Multiuniverse Support
            $ch = curl_init();
            if ($universeCount == 1) {
                curl_setopt($ch, CURLOPT_URL, PROTOCOL . HTTP_HOST . HTTP_BASE . "uni" . ROOT_UNI . "/");
            } else {
                curl_setopt($ch, CURLOPT_URL, PROTOCOL . HTTP_HOST . HTTP_BASE);
            }
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
            $configSQL[]    = '`close_reason` = ""';
            $configSQL[]    = '`OverviewNewsText` = "' . $GLOBALS['DATABASE']->escape($config->OverviewNewsText) . '"';

            $GLOBALS['DATABASE']->query("INSERT INTO " . CONFIG . " SET " . implode(', ', $configSQL) . ";");
            $newUniverse    = $GLOBALS['DATABASE']->GetInsertID();

            Config::reload();

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
            $GLOBALS['DATABASE']->query(
                "UPDATE " . USERS . " SET password = '" . $USER['password'] . "' WHERE id = " . $userID . ";"
            );

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

    $uniResult  = $GLOBALS['DATABASE']->query("
    SELECT uni, users_amount, game_disable, energySpeed, halt_speed, resource_multiplier, fleet_speed, building_speed,
    shipyard_speed, research_speed, uni_name, COUNT(DISTINCT inac.id) as inactive, COUNT(planet.id) as planet
	FROM " . CONFIG . " conf
	LEFT JOIN " . USERS . " as inac ON uni = inac.universe AND inac.onlinetime < " . (TIMESTAMP - INACTIVE) . "
	LEFT JOIN " . PLANETS . " as planet ON uni = planet.universe
	GROUP BY conf.uni, inac.universe, planet.universe
	ORDER BY uni ASC;");

    while ($uniRow = $GLOBALS['DATABASE']->fetch_array($uniResult)) {
        $uniList[$uniRow['uni']]    = $uniRow;
    }

    $template->assign_vars([
        'uniList'   => $uniList,
        'SID'       => session_id(),
    ]);

    $template->show('UniversePage.tpl');
}
