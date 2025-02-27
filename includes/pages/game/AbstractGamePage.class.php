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

abstract class AbstractGamePage
{
    /**
     * reference of the template object
     * @var template
     */
    protected $tplObj;

    /**
     * reference of the template object
     * @var ResourceUpdate
     */
    protected $ecoObj;
    protected $window;
    protected $disableEcoSystem = false;

    protected function __construct()
    {
        if (!AJAX_REQUEST) {
            $this->setWindow('full');
            if (!$this->disableEcoSystem) {
                $USER =& Singleton()->USER;
                $PLANET =& Singleton()->PLANET;
                if (!empty($USER) && !empty($PLANET)) {
                    $db = Database::get();
                    $db->startTransaction();
                    $sql    = "SELECT * FROM %%PLANETS%% WHERE id = :planetId FOR UPDATE;";
                    $p = $db->selectSingle($sql, [
                        ':planetId' => $PLANET['id'],
                    ]);
                    $sql    = "SELECT * FROM %%USERS%% WHERE id = :userId FOR UPDATE;";
                    $u   = $db->selectSingle($sql, [
                        ':userId'   => $USER['id'],
                    ]);

                    $this->ecoObj = new ResourceUpdate();
                    list($USER, $PLANET) = $this->ecoObj->CalcResource($USER, $PLANET, true);
                    $db->commit();
                }
            }
            $this->initTemplate();
        } else {
            $this->setWindow('ajax');
        }
    }

    protected function initTemplate()
    {
        if (isset($this->tplObj)) {
            return true;
        }

        $this->tplObj = new template();
        [$tplDir] = $this->tplObj->getTemplateDir();
        $this->tplObj->setTemplateDir($tplDir . 'game/');
        return true;
    }

    protected function setWindow($window)
    {
        $this->window = $window;
    }

    protected function getWindow()
    {
        return $this->window;
    }

    protected function getQueryString()
    {
        $queryString = [];
        $page = HTTP::_GP('page', '');

        if (!empty($page)) {
            $queryString['page'] = $page;
        }

        $mode = HTTP::_GP('mode', '');
        if (!empty($mode)) {
            $queryString['mode'] = $mode;
        }

        $id = HTTP::_GP('id', '');
        if (!empty($id)) {
            $queryString['id'] = $id;
        }

        return http_build_query($queryString);
    }

    protected function getCronjobsTodo()
    {
        require_once 'includes/classes/Cronjob.class.php';

        $this->assign([
            'cronjobs' => Cronjob::getNeedTodoExecutedJobs()
        ]);
    }

    protected function getNavigationData()
    {
        $PLANET =& Singleton()->PLANET;
        $LNG =& Singleton()->LNG;
        $USER =& Singleton()->USER;
        $THEME =& Singleton()->THEME;
        $resource =& Singleton()->resource;
        $reslist =& Singleton()->reslist;
        $config = Config::get();

        $PlanetSelect = [];

        if ($USER['bana'] == 1) {
            echo $LNG['banned_message'];
            die();
        }

        if (!isset($USER['PLANETS'])) {
            $USER['PLANETS'] = getPlanets($USER);
        }

        foreach ($USER['PLANETS'] as $PlanetQuery) {
            $PlanetSelect[$PlanetQuery['id']] = $PlanetQuery['name'] . (($PlanetQuery['planet_type'] == 3) ? " (" . $LNG['fcm_moon'] . ")" : "") . " [" . $PlanetQuery['galaxy'] . ":" . $PlanetQuery['system'] . ":" . $PlanetQuery['planet'] . "]";
        }

        $resourceTable = [];
        $resourceSpeed = $config->resource_multiplier;
        foreach ($reslist['resstype'][1] as $resourceID) {
            $resourceTable[$resourceID]['name'] = $resource[$resourceID];
            $resourceTable[$resourceID]['current'] = floor($PLANET[$resource[$resourceID]]);
            $resourceTable[$resourceID]['max'] = $PLANET[$resource[$resourceID] . '_max'];
            if ($USER['urlaubs_modus'] == 1 || $PLANET['planet_type'] != 1) {
                $resourceTable[$resourceID]['production'] = $PLANET[$resource[$resourceID] . '_perhour'];
            } else {
                $resourceTable[$resourceID]['production'] = $PLANET[$resource[$resourceID] . '_perhour'] + $config->{$resource[$resourceID] . '_basic_income'} * $resourceSpeed;
            }
        }

        foreach ($reslist['resstype'][2] as $resourceID) {
            $resourceTable[$resourceID]['name'] = $resource[$resourceID];
            $resourceTable[$resourceID]['used'] = $PLANET[$resource[$resourceID] . '_used'];
            $resourceTable[$resourceID]['max'] = $PLANET[$resource[$resourceID]];
        }

        foreach ($reslist['resstype'][3] as $resourceID) {
            $resourceTable[$resourceID]['name'] = $resource[$resourceID];
            $resourceTable[$resourceID]['current'] = $USER[$resource[$resourceID]];
        }

        $themeSettings = $THEME->getStyleSettings();

        $commit = '';
        $commitShort = '';
        if (file_exists('.git/FETCH_HEAD')) {
            $commit = explode('	', file_get_contents('.git/FETCH_HEAD'))[0];
            $commitShort = substr($commit, 0, 7);
        }

        $avatar = 'styles/resource/images/user.png';
        if (Session::load()->data !== null) {
            try {
                $avatar = json_decode(Session::load()->data->account->json_metadata)->profile->profile_image;
            } catch (Exception $e) {
            }
        }

        $sql = "SELECT COUNT(r.applyID) AS applies
            FROM %%ALLIANCE_REQUEST%% r
            JOIN %%USERS%% u ON u.ally_id = r.allianceID
            JOIN %%ALLIANCE%% a ON a.id = u.ally_id
            LEFT JOIN %%ALLIANCE_RANK%% ra ON ra.allianceID = u.ally_id AND ra.rankID = u.ally_rank_id
            WHERE r.allianceID = :allianceID AND u.id = :userID AND (u.id = a.ally_owner OR ra.MANAGEAPPLY = 1);";
        $allyApplyRequests = Database::get()->selectSingle($sql, [
            ':allianceID' => $USER['ally_id'],
            ':userID' => $USER['id']
        ], 'applies');

        $sql = "SELECT COUNT(d.id) AS diplos
            FROM %%DIPLO%% d
            JOIN %%USERS%% u ON u.ally_id = d.owner_2
            JOIN %%ALLIANCE%% a ON a.id = u.ally_id
            LEFT JOIN %%ALLIANCE_RANK%% ra ON ra.allianceID = u.ally_id AND ra.rankID = u.ally_rank_id
            WHERE d.owner_2 = :allianceID AND accept = 0 AND u.id = :userID AND (u.id = a.ally_owner OR ra.DIPLOMATIC = 1);";
        $allyDiploRequests = Database::get()->selectSingle($sql, [
            ':allianceID' => $USER['ally_id'],
            ':userID' => $USER['id']
        ], 'diplos');

        $allyrequests = $allyApplyRequests + $allyDiploRequests;

        $vacation = false;
        if ($USER['urlaubs_modus']) {
            if ($USER['urlaubs_until'] > TIMESTAMP) {
                $vacation = sprintf($LNG['tn_vacation_mode_until'], _date($LNG['php_tdformat'], $USER['urlaubs_until'], $USER['timezone']));
            } else {
                $vacation = $LNG['tn_vacation_mode'];
            }
            if (isModuleAvailable(MODULE_VMODE_KICK, $USER['universe'])) {
                $vacation .= $LNG['tn_vacation_mode_kick'];
            }
        }

        $this->assign([
            'PlanetSelect' => $PlanetSelect,
            'new_message' => $USER['messages'],
            'new_allyrequests' => $allyrequests,
            'commit' => $commit,
            'commitShort' => $commitShort,
            'vacation' => $vacation,
            'delete' => $USER['db_deaktjava'] ? sprintf($LNG['tn_delete_mode'], _date($LNG['php_tdformat'], $USER['db_deaktjava'] + ($config->del_user_manually * 86400)), $USER['timezone']) : false,
            'current_pid' => $PLANET['id'],
            'image' => $PLANET['image'],
            'username' => $USER['username'],
            'avatar' => $avatar,
            'resourceTable' => $resourceTable,
            'shortlyNumber' => $themeSettings['TOPNAV_SHORTLY_NUMBER'],
            'uni_status' => $config->uni_status,
            'hasBoard' => filter_var($config->forum_url, FILTER_VALIDATE_URL),
            'hasAdminAccess' => !empty(Session::load()->adminAccess),
            'hasGate' => $PLANET[$resource[43]] > 0,
            'discordUrl' => DISCORD_URL,
        ]);
    }

    protected function getPageData()
    {
        $USER =& Singleton()->USER;
        $THEME =& Singleton()->THEME;
        if ($this->getWindow() === 'full') {
            $this->getNavigationData();
            $this->getCronjobsTodo();
        }

        $dateTimeServer = new DateTime("now");
        if (isset($USER['timezone'])) {
            try {
                $dateTimeUser = new DateTime("now", new DateTimeZone($USER['timezone']));
            } catch (Exception $e) {
                $dateTimeUser = $dateTimeServer;
            }
        } else {
            $dateTimeUser = $dateTimeServer;
        }

        $config = Config::get();

        $this->assign([
            'vmode' => $USER['urlaubs_modus'],
            'authlevel' => $USER['authlevel'],
            'userID' => $USER['id'],
            'bodyclass' => $this->getWindow(),
            'game_name' => $config->game_name,
            'uni_name' => $config->uni_name,
            'use_google_analytics' => $config->use_google_analytics,
            'google_analytics_key' => $config->google_analytics_key,
            'debug' => $config->debug,
            'version' => $config->version,
            'date' => explode("|", date('Y\|n\|j\|G\|i\|s\|Z', TIMESTAMP)),
            'isPlayerCardActive' => isModuleAvailable(MODULE_PLAYERCARD),
            'REV' => substr($config->version, -4),
            'Offset' => $dateTimeUser->getOffset() - $dateTimeServer->getOffset(),
            'queryString' => $this->getQueryString(),
            'themeSettings' => $THEME->getStyleSettings(),
        ]);
    }

    protected function printMessage($message, $redirectButtons = null, $redirect = null, $fullSide = true)
    {
        $this->assign([
            'message' => $message,
            'redirectButtons' => $redirectButtons,
        ]);

        if (isset($redirect)) {
            $this->tplObj->gotoside($redirect[0], $redirect[1]);
        }

        if (!$fullSide) {
            $this->setWindow('popup');
        }

        $this->display('error.default.tpl');
    }

    protected function updatePlanetTime()
    {
        global $PLANET;
        if (!isset($PLANET['id'])) {
            return;
        }
        $db = Database::get();
        $sql = "UPDATE %%PLANETS%% SET last_update = :lastUpdateTime WHERE id = :planetId";
        $db->update($sql, [
            ':lastUpdateTime' => TIMESTAMP,
            ':planetId' => $PLANET['id'],
        ]);
    }

    protected function assign($array, $nocache = true)
    {
        $this->tplObj->assign_vars($array, $nocache);
    }

    protected function display($file)
    {
        $THEME =& Singleton()->THEME;
        $LNG =& Singleton()->LNG;
        $USER =& Singleton()->USER;
        // $this->updatePlanetTime();

        if ($this->getWindow() !== 'ajax') {
            $this->getPageData();
        }
        $config = Config::get();
        $captchakey = $config->recaptcha_pub_key;
        $this->assign([
            'lang'              => $LNG->getLanguage(),
            'dpath'             => $THEME->getTheme(),
            'scripts'           => $this->tplObj->jsscript,
            'execscript'        => implode("\n", $this->tplObj->script),
            'basepath'          => PROTOCOL . HTTP_HOST . HTTP_BASE,
            'TIMEZONESTRING'    => $USER['timezone'],
            'signalColors'      => $USER['signalColors'],
            'captchakey'        => $captchakey,
        ]);
        $this->assign([
            'LNG' => $LNG
        ], false);

        $this->tplObj->display('extends:layout.' . $this->getWindow() . '.tpl|' . $file);
        exit;
    }

    protected function sendJSON($data)
    {
        // $this->updatePlanetTime();
        echo json_encode($data);
        exit;
    }

    protected function redirectTo($url)
    {
        // $this->updatePlanetTime();
        HTTP::redirectTo($url);
        exit;
    }
}
