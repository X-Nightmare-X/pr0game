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

function userStatus($data, $noob_protection = false)
{
    $Array = [];

    if (isset($data['banaday']) && $data['banaday'] > TIMESTAMP) {
        $Array[] = 'banned';
    }

    if (isset($data['urlaubs_modus']) && $data['urlaubs_modus'] == 1) {
        $Array[] = 'vacation';
    }

    if (isset($data['onlinetime']) && $data['onlinetime'] < TIMESTAMP - INACTIVE_LONG) {
        $Array[] = 'longinactive';
    }

    if (isset($data['onlinetime']) && $data['onlinetime'] < TIMESTAMP - INACTIVE) {
        $Array[] = 'inactive';
    }

    if ($noob_protection && $noob_protection['NoobPlayer']) {
        $Array[] = 'noob';
    }

    if ($noob_protection && $noob_protection['StrongPlayer']) {
        $Array[] = 'strong';
    }

    return $Array;
}

function getLanguage($language = null, $userID = null)
{
    if (is_null($language) && !is_null($userID)) {
        $language = Database::get()->selectSingle('SELECT lang FROM %%USERS%% WHERE id = :id;', [
            ':id' => $userID
        ])['lang'];
    }

    $LNG = new Language($language);
    $LNG->includeData(['L18N', 'FLEET', 'TECH', 'CUSTOM', 'INGAME']);
    return $LNG;
}

function getPlanets($USER)
{
    if (isset($USER['PLANETS'])) {
        return $USER['PLANETS'];
    }

    $order = $USER['planet_sort_order'] == 1 ? "DESC" : "ASC";

    $sql = "SELECT id, name, galaxy, system, planet, planet_type, image, b_building, b_building_id
			FROM %%PLANETS%% WHERE id_owner = :userId AND destruyed = :destruyed ORDER BY ";

    switch ($USER['planet_sort']) {
        case 0:
            $sql .= 'id ' . $order;
            break;
        case 1:
            $sql .= 'galaxy ' . $order . ', system ' . $order . ', planet ' . $order . ', planet_type ' . $order;
            break;
        case 2:
            $sql .= 'name ' . $order;
            break;
    }

    $planetsResult = Database::get()->select($sql, [
        ':userId' => $USER['id'],
        ':destruyed' => 0
    ]);

    $planetsList = [];

    foreach ($planetsResult as $planetRow) {
        $planetsList[$planetRow['id']] = $planetRow;
    }

    return $planetsList;
}

function get_timezone_selector()
{
    // New Timezone Selector, better support for changes in tzdata (new russian timezones, e.g.)
    // http://www.php.net/manual/en/datetimezone.listidentifiers.php

    $timezones = [];
    $timezone_identifiers = DateTimeZone::listIdentifiers();

    foreach ($timezone_identifiers as $value) {
        if (preg_match('/^(America|Antartica|Arctic|Asia|Atlantic|Europe|Indian|Pacific|Australia)\//', $value)) {
            $ex = explode('/', $value); //obtain continent,city
            $city = isset($ex[2]) ? $ex[1] . ' - ' . $ex[2] : $ex[1]; //in case a timezone has more than one
            $timezones[$ex[0]][$value] = str_replace('_', ' ', $city);
        }
    }
    return $timezones;
}

function locale_date_format($format, $time, $LNG = null)
{
    // Workaround for locale Names.

    if (!isset($LNG)) {
        $LNG =& Singleton()->LNG;
    }

    $weekDay = date('w', $time);
    $months = date('n', $time) - 1;

    $format = str_replace(['D', 'M'], ['$D$', '$M$'], $format);
    $format = str_replace('$D$', addcslashes($LNG['week_day'][$weekDay], 'A..z'), $format);
    $format = str_replace('$M$', addcslashes($LNG['months'][$months], 'A..z'), $format);

    return $format;
}

function _date($format, $time = null, $toTimeZone = null, $LNG = null)
{
    if (!isset($time)) {
        $time = TIMESTAMP;
    }

    if (isset($toTimeZone)) {
        $date = new DateTime();
        if (method_exists($date, 'setTimestamp')) {    // PHP > 5.3
            $date->setTimestamp($time);
        } else {
            // PHP < 5.3
            $tempDate = getdate((int)$time);
            $date->setDate($tempDate['year'], $tempDate['mon'], $tempDate['mday']);
            $date->setTime($tempDate['hours'], $tempDate['minutes'], $tempDate['seconds']);
        }

        $time -= $date->getOffset();
        try {
            $date->setTimezone(new DateTimeZone($toTimeZone));
        } catch (Exception $e) {
        }
        $time += $date->getOffset();
    }

    $format = locale_date_format($format, $time, $LNG);
    return date($format, $time);
}

function ValidateAddress($address)
{
    return filter_var($address, FILTER_VALIDATE_EMAIL) !== false;
}

function message($mes, $dest = "", $time = "3", $topnav = false)
{
    require_once('includes/classes/class.template.php');
    $template = new template();
    $template->message($mes, $dest, $time, !$topnav);
    exit;
}

function CalculateMaxPlanetFields($planet)
{
    $resource =& Singleton()->resource;
    return $planet['field_max'] + floor($planet[$resource[33]] * FIELDS_BY_TERRAFORMER) + ($planet[$resource[41]]
        * FIELDS_BY_MOONBASIS_LEVEL);
}

function pretty_time($seconds)
{
    $LNG =& Singleton()->LNG;
    $day = $seconds / 86400;
    $day = floor($day);
    $hour = $seconds / 3600;
    $hour = floor($hour);
    $hour = $hour % 24;
    $minute = $seconds / 60;
    $minute = floor($minute);
    $minute = $minute % 60;
    $second = floor($seconds);
    $second = $seconds % 60;

    $time = '';

    if ($day > 0) {
        $time .= sprintf('%d%s ', $day, $LNG['short_day']);
    }

    return $time . sprintf(
        '%02d%s %02d%s %02d%s',
        $hour,
        $LNG['short_hour'],
        $minute,
        $LNG['short_minute'],
        $second,
        $LNG['short_second']
    );
}

function pretty_fly_time($seconds)
{
    $hour = floor($seconds / 3600);
    $minute = floor($seconds / 60);
    $minute = $minute % 60;
    $second = floor($seconds);
    $second = $second % 60;

    return sprintf('%02d:%02d:%02d', $hour, $minute, $second);
}

function getRestTimeFormat($seconds, $shorten = false)
{
    $timethresh = [31536000, 2592000, 604800, 86400, 3600, 60, 1]; // y,m,w,d,h,m,s
    $ax = [0, 0, 0, 0, 0, 0, 0];
    $fmt = ["Y", "M", "W", "d", "h", "m", "s"];
    $longest = -1;
    foreach ($timethresh as $t => $v) {
        $inside = floor($seconds / $v);
        if ($inside == 0 && $longest == $t - 1) {
            $longest = $t;
        }
        $seconds -= $inside * $v;
        $ax[$t] = $inside;
    }
    $rstr = "";
    $longest = $longest + 1;
    for ($i = $longest; $i < 7 && ($i - $longest < 3 || $shorten != true); $i++) {
        if ($i > 3) {
            $rstr = $rstr . " " . sprintf('%02d', $ax[$i]) . $fmt[$i] . " ";
        } else {
            $rstr = $rstr . " " . $ax[$i] . $fmt[$i] . " ";
        }
    }
    return trim($rstr);
}

function GetStartAddressLink($FleetRow, $FleetType = '')
{
    return '<a href="game.php?page=galaxy&amp;galaxy=' . $FleetRow['fleet_start_galaxy'] . '&amp;system='
        . $FleetRow['fleet_start_system'] . '" class="' . $FleetType . '">[' . $FleetRow['fleet_start_galaxy'] . ':'
        . $FleetRow['fleet_start_system'] . ':' . $FleetRow['fleet_start_planet'] . ']</a>';
}

function GetTargetAddressLink($FleetRow, $FleetType = '')
{
    return '<a href="game.php?page=galaxy&amp;galaxy=' . $FleetRow['fleet_end_galaxy'] . '&amp;system='
        . $FleetRow['fleet_end_system'] . '" class="' . $FleetType . '">[' . $FleetRow['fleet_end_galaxy'] . ':'
        . $FleetRow['fleet_end_system'] . ':' . $FleetRow['fleet_end_planet'] . ']</a>';
}

function BuildPlanetAddressLink($CurrentPlanet)
{
    return '<a href="game.php?page=galaxy&amp;galaxy=' . $CurrentPlanet['galaxy'] . '&amp;system='
        . $CurrentPlanet['system'] . '">[' . $CurrentPlanet['galaxy'] . ':' . $CurrentPlanet['system'] . ':'
        . $CurrentPlanet['planet'] . ']</a>';
}

function pretty_number($n, $dec = 0)
{
    if (is_numeric($n)) {
        return number_format($n, $dec, ',', '.');
    } else {
        return $n;
    }
}

function GetUserByID($userId, $GetInfo = "*")
{
    if (is_array($GetInfo)) {
        $GetOnSelect = implode(', ', $GetInfo);
    } else {
        $GetOnSelect = $GetInfo;
    }

    $sql = 'SELECT ' . $GetOnSelect . ' FROM %%USERS%% WHERE id = :userId';

    $User = Database::get()->selectSingle($sql, [
        ':userId' => $userId
    ]);

    return $User;
}

function makebr($text)
{
    // XHTML FIX for PHP 5.3.0
    // Danke an Meikel

    $BR = "<br>\n";
    if (version_compare(PHP_VERSION, "5.3.0", ">=")) {
        return nl2br($text, false);
    }
    return strtr($text, ["\r\n" => $BR, "\r" => $BR, "\n" => $BR]);
}

function CheckNoobProtec($OwnerPlayer, $TargetPlayer, $Player)
{
    $config = Config::get();
    if (
        $config->noob_protection == 0
        || $config->noob_protection_time == 0
        || $config->noob_protection_multi == 0
        || $Player['banaday'] > TIMESTAMP
        || $Player['onlinetime'] < TIMESTAMP - INACTIVE
    ) {
        return ['NoobPlayer' => false, 'StrongPlayer' => false];
    }

    return [
        'NoobPlayer' => (
            /* WAHR:
                Wenn Spieler mehr als 25000 Punkte hat UND
                Wenn ZielSpieler weniger als 80% der Punkte des Spieler hat.
                ODER weniger als 5.000 hat.
            */
            // Addional Comment: Letzteres ist eigentlich sinnfrei, bitte testen.a
            ($TargetPlayer['total_points'] <= $config->noob_protection_time) && // Default: 25.000
            ($OwnerPlayer['total_points'] > $TargetPlayer['total_points'] * $config->noob_protection_multi)
        ),
        'StrongPlayer' => (
            /* WAHR:
                Wenn Spieler weniger als 5000 Punkte hat UND
                Mehr als das funfache der eigende Punkte hat
            */
            ($OwnerPlayer['total_points'] < $config->noob_protection_time) && // Default: 5.000
            ($OwnerPlayer['total_points'] * $config->noob_protection_multi < $TargetPlayer['total_points'])
        ),
    ];
}

function shortly_number($number, $decial = null)
{
    $negate = $number < 0 ? -1 : 1;
    $number = abs($number);
    $unit = ["", "K", "M", "B", "T", "Q", "Q+", "S", "S+", "O", "N"];
    $key = 0;

    if ($number >= 1000000) {
        ++$key;
        while ($number >= 1000000) {
            ++$key;
            $number = $number / 1000000;
        }
    } elseif ($number >= 1000) {
        ++$key;
        $number = $number / 1000;
    }

    if (!is_numeric($decial)) {
        $decial = ((int)(((int)$number != $number) && $key != 0 && $number != 0 && $number < 100));
    }
    return pretty_number($negate * $number, $decial) . '&nbsp;' . $unit[$key];
}

function floatToString($number, $Pro = 0, $output = false)
{
    if ($output) {
        return str_replace(",", ".", sprintf("%." . $Pro . "f", $number));
    }
    return sprintf("%." . $Pro . "f", $number);
}

function getNumber($from, $default = 0)
{
    return is_numeric($from) ? $from : $default;
}

function isModuleAvailable($ID, $universe = 0)
{
    $config = Config::get($universe);
    $modules = $config->moduls;
    
    if (!isset($modules[$ID])) {
        $e = new \Exception;
        error_log('Unknown module ID: ' . $ID);
        error_log('\n' . $e->getTraceAsString());
    }

    return isset($modules[$ID]) ? $modules[$ID] == 1 : true;
}

function isJuliaInstalled() : bool
{
    $result = exec('julia -v');
    return $result;
}

function isJuliaRunning() : bool
{
    if (!isJuliaInstalled())
        return false;
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, "http://127.0.0.1:8100/ping");
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($curl);
    $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);

    return $result == 'alive';
}

function startJulia()
{
    if (!isJuliaInstalled())
        return;

    $path = __DIR__ . '/../juliaBattleEngine/';
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        // extension=php_com_dotnet required
        $WshShell = new COM('WScript.Shell'); 
        $oExec = $WshShell->Run('julia '. $path . 'wsm_v3.jl ' . $path . 'shipinfos.csv 8100', 0, false);
        error_log($oExec); // 0 = success
    } else {
        error_log('julia '. $path . 'wsm_v3.jl ' . $path . 'shipinfos.csv 8100 > /dev/null 2>&1 &');
        exec('julia '. $path . 'wsm_v3.jl ' . $path . 'shipinfos.csv 8100 > /dev/null 2>&1 &');
    }
}

function exportJuliaShipData()
{
    $sql = "SELECT CONCAT(
                v.`elementID`, ';', v.`attack`, ';', v.`defend`, ';',
                TRUNCATE((v.`cost901` + v.`cost902`) / 10, 0), ';', IF(v.`class` = 200, 1, 0), ';',
                IFNULL(GROUP_CONCAT(r.`rapidfireID`, ',', r.`shoots` SEPARATOR '-'), '')
            ) AS csvLine
        FROM uni1_vars v
        LEFT JOIN uni1_vars_rapidfire r ON r.`elementID` = v.`elementID`
        WHERE v.`class` IN (200, 400)
        GROUP BY v.`elementID`";
    $result = Database::get()->select($sql);

    $firstLine = true;
    foreach ($result as $line) {
        if ($firstLine) {
            $firstLine = false;
            file_put_contents('juliaBattleEngine/shipinfos.csv', $line['csvLine'] . PHP_EOL);
        }
        file_put_contents('juliaBattleEngine/shipinfos.csv', $line['csvLine'] . PHP_EOL, FILE_APPEND);
    }
}

function restartJulia() : bool
{
    if (!isJuliaRunning()) {
        startJulia();
    }
    if (!isJuliaRunning()) {
        return false;
    }
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, "http://127.0.0.1:8100/updateships");
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($curl);
    curl_close($curl);
    return $result == 'done';
}

function ClearCache()
{
    $DIRS = ['cache/', 'cache/templates/'];
    foreach ($DIRS as $DIR) {
        $FILES = array_diff(scandir($DIR), ['..', '.', '.htaccess']);
        foreach ($FILES as $FILE) {
            if (is_dir(ROOT_PATH . $DIR . $FILE)) {
                continue;
            }

            unlink(ROOT_PATH . $DIR . $FILE);
        }
    }


    $template = new template();
    $template->clearAllCache();


    require_once 'includes/classes/Cronjob.class.php';
    Cronjob::reCalculateCronjobs();

    $sql = 'UPDATE %%PLANETS%% SET eco_hash = :ecoHash;';
    Database::get()->update($sql, [
        ':ecoHash' => ''
    ]);
    clearstatcache();

    // Julia BallteEngine ship export and restart
    if (isJuliaInstalled()) {
        exportJuliaShipData();
        restartJulia();
    }

    /* does no work on git.

    // Find currently Revision

    $REV = 0;

    $iterator = new RecursiveDirectoryIterator(ROOT_PATH);
    foreach(new RecursiveIteratorIterator($iterator, RecursiveIteratorIterator::CHILD_FIRST) as $file) {
        if (false == $file->isDir()) {
            $CONTENT    = file_get_contents($file->getPathname());

            preg_match('!\$'.'Id: [^ ]+ ([0-9]+)!', $CONTENT, $match);

            if(isset($match[1]) && is_numeric($match[1]))
            {
                $REV    = max($REV, $match[1]);
            }
        }
    }

    $config->version    = $version[0].'.'.$version[1].'.'.$REV;
    */

    $config = Config::get();
    $version = explode('.', $config->version);
    $config->version = $version[0] . '.' . $version[1] . '.' . 'git';
    $config->saveGlobalKeys();
}

function allowedTo($side)
{
    $USER =& Singleton()->USER;
    return ($USER['authlevel'] == AUTH_ADM || (isset($USER['rights']) && $USER['rights'][$side] == 1));
}

function getRandomString()
{
    return md5(uniqid());
}

function isVacationMode($USER)
{
    return ($USER['urlaubs_modus'] == 1) ? true : false;
}

function clearGIF()
{
    header('Cache-Control: no-cache');
    header('Content-type: image/gif');
    header('Content-length: 43');
    header('Expires: 0');
    echo '\x47\x49\x46\x38\x39\x61\x01\x00\x01\x00\x80\x00\x00\x00\x00\x00\x00\x00\x00\x21\xF9\x04\x01\x00\x00\x00\x00'
        . '\x2C\x00\x00\x00\x00\x01\x00\x01\x00\x00\x02\x02\x44\x01\x00\x3B';
    exit;
}

/*
 * Handler for exceptions
 *
 * @param object
 * @return Exception
 */
function exceptionHandler($exception)
{
    /** @var $exception ErrorException|Exception */

    if (!headers_sent()) {
        if (!class_exists('HTTP', false)) {
            require_once('includes/classes/HTTP.class.php');
        }

        HTTP::sendHeader('HTTP/1.1 503 Service Unavailable');
    }

    if (method_exists($exception, 'getSeverity')) {
        $errno = $exception->getSeverity();
    } else {
        $errno = E_USER_ERROR;
    }

    $errorType = [
        E_ERROR => 'ERROR',
        E_WARNING => 'WARNING',
        E_PARSE => 'PARSING ERROR',
        E_NOTICE => 'NOTICE',
        E_CORE_ERROR => 'CORE ERROR',
        E_CORE_WARNING => 'CORE WARNING',
        E_COMPILE_ERROR => 'COMPILE ERROR',
        E_COMPILE_WARNING => 'COMPILE WARNING',
        E_USER_ERROR => 'USER ERROR',
        E_USER_WARNING => 'USER WARNING',
        E_USER_NOTICE => 'USER NOTICE',
        E_STRICT => 'STRICT NOTICE',
        E_RECOVERABLE_ERROR => 'RECOVERABLE ERROR',
        E_DEPRECATED => 'DEPRECATED',
        E_USER_DEPRECATED => 'USER DEPRECATED',
        E_ALL => 'ALL',
    ];

    if (file_exists(ROOT_PATH . 'install/VERSION')) {
        $version = file_get_contents(ROOT_PATH . 'install/VERSION') . ' (FILE)';
    } else {
        $version = 'UNKNOWN';
    }
    $gameName = '-';

    if (MODE !== 'INSTALL') {
        try {
            $config = Config::get();
            $gameName = $config->game_name;
            $version = $config->version;
        } catch (ErrorException $e) {
        }
    }


    $DIR = (MODE === 'INSTALL' || MODE === 'UPGRADE') ? '..' : '.';
    ob_start();
    echo '<!DOCTYPE html>
<!--[if lt IE 7 ]> <html lang="de" class="no-js ie6"> <![endif]-->
<!--[if IE 7 ]>    <html lang="de" class="no-js ie7"> <![endif]-->
<!--[if IE 8 ]>    <html lang="de" class="no-js ie8"> <![endif]-->
<!--[if IE 9 ]>    <html lang="de" class="no-js ie9"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--> <html lang="de" class="no-js"> <!--<![endif]-->
<head>
	<title>' . $gameName . ' - Error</title>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8">
	<link rel="stylesheet" type="text/css" href="' . $DIR . '/styles/resource/css/base/boilerplate.css">
	<link rel="stylesheet" type="text/css" href="' . $DIR . '/styles/resource/css/ingame/main.css">
	<link rel="stylesheet" type="text/css" href="' . $DIR . '/styles/resource/css/base/jquery.css">
	<link rel="stylesheet" type="text/css" href="' . $DIR . '/styles/theme/gow/formate.css">
	<link rel="shortcut icon" href="./favicon.ico" type="image/x-icon">
	<script type="text/javascript">
	var ServerTimezoneOffset = -3600;
	var serverTime 	= new Date(2012, 2, 12, 14, 43, 36);
	var startTime	= serverTime.getTime();
	var localTime 	= serverTime;
	var localTS 	= startTime;
	var Gamename	= document.title;
	var Ready		= "Fertig";
	var Skin		= "' . $DIR . '/styles/theme/gow/";
	var Lang		= "de";
	var head_info	= "Information";
	var auth		= 3;
	var days 		= ["So","Mo","Di","Mi","Do","Fr","Sa"]
	var months 		= ["Jan","Feb","Mar","Apr","Mai","Jun","Jul","Aug","Sep","Okt","Nov","Dez"] ;
	var tdformat	= "[M] [D] [d] [H]:[i]:[s]";
	var queryString	= "";
    var relativeTime = Math.floor(Date.now() / 1000);

    setInterval(function() {
	if(relativeTime < Math.floor(Date.now() / 1000)) {
	    serverTime.setSeconds(serverTime.getSeconds()+1);
	    relativeTime++;
	}
    }, 1);
	</script>
	<script type="text/javascript" src="' . $DIR . '/scripts/base/jquery.js"></script>
	<script type="text/javascript" src="' . $DIR . '/scripts/base/jquery.ui.js"></script>
	<script type="text/javascript" src="' . $DIR . '/scripts/base/jquery.cookie.js"></script>
	<script type="text/javascript" src="' . $DIR . '/scripts/base/jquery.fancybox.js"></script>
	<script type="text/javascript" src="' . $DIR . '/scripts/base/jquery.validationEngine.js"></script>
	<script type="text/javascript" src="' . $DIR . '/scripts/base/tooltip.js"></script>
	<script type="text/javascript" src="' . $DIR . '/scripts/game/base.js"></script>
</head>
<body id="overview" class="full">

<div style="padding: 50px; max-width: 50%; margin: 0 auto">
    <h2>Ein Fehler ist aufgetreten</h2>
    <p>
        Unser Entwicklungs-Team wurde bereits von dem Fehler in Kenntnis gesetzt und wird ihn zeitnah beheben.<br>
        Sofern zusätzliche Informationen zu Verfügung stellen, um den Fehler zu reproduzieren, melde diese bitte im
        Discord-Channel #bugs.
    </p>
    <p>
        This error has been reported to our dev-team and it will be fixed soon.<br>
        In case you are able to provide additional information in order to reproduce this issue, please send us a
        message within our Discord channel #bugs.
    </p>

    <a class="button" href="/game.php">Zurück zur Startseite</a>
</div>

<!--
Message: ' . $exception->getMessage() . '
File: ' . $exception->getFile() . '
Line: ' . $exception->getLine() . '
URL: ' . PROTOCOL . HTTP_HOST . $_SERVER['REQUEST_URI'] . '
PHP-Version: ' . PHP_VERSION . '
PHP-API: ' . php_sapi_name() . '
pr0game Version: ' . $version . '

Debug Backtrace:
' . makebr(htmlspecialchars($exception->getTraceAsString())) . '
-->
</body>
</html>';

    if (MODE !== 'INSTALL') {
        require_once 'includes/classes/class.Discord.php';
        Discord::sendException($exception);
    }

    echo str_replace(['\\', ROOT_PATH, substr(ROOT_PATH, 0, 15)], ['/', '/', 'FILEPATH '], ob_get_clean());

    $errorText = date("[d-M-Y H:i:s]", TIMESTAMP) . ' ' . $errorType[$errno] . ': "'
        . strip_tags($exception->getMessage()) . "\"\r\n";
    $errorText .= 'File: ' . $exception->getFile() . ' | Line: ' . $exception->getLine() . "\r\n";
    $errorText .= 'URL: ' . PROTOCOL . HTTP_HOST . $_SERVER['REQUEST_URI'] . ' | Version: ' . $version . "\r\n";
    $errorText .= "Stack trace:\r\n";
    $errorText .= str_replace(
        ROOT_PATH,
        '/',
        htmlspecialchars(str_replace('\\', '/', $exception->getTraceAsString()))
    ) . "\r\n";

    if (is_writable('includes/error.log')) {
        file_put_contents('includes/error.log', $errorText, FILE_APPEND);
    }

    /* Debug via Support Ticket */
    $config = Config::get();
    if ($config->debug) {
        $USER =& Singleton()->USER;
        if (isset($USER) && is_array($USER) && !empty($USER['id']) && !empty($USER['username'])) {
            $ErrSource = $USER['id'];
            $ErrName = $USER['username'];
        } else {
            $ErrSource = 0;
            $ErrName = 'System';
        }
        require 'includes/classes/class.SupportTickets.php';
        $ticketObj = new SupportTickets();
        $ticketID = $ticketObj->createTicket($ErrSource, 9, $errorType[$errno]);
        $ticketObj->createAnswer($ticketID, $ErrSource, $ErrName, $errorType[$errno], $errorText, 0);
    }
}

/*
 *
 * @throws ErrorException
 *
 * @return bool If its an hidden error.
 *
 */
function errorHandler($errno, $errstr, $errfile, $errline)
{
    if (!($errno & error_reporting())) {
        return false;
    }

    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
}

// "workaround" for PHP version pre 5.3.0
if (!function_exists('array_replace_recursive')) {
    function array_replace_recursive()
    {
        if (!function_exists('recurse')) {
            function recurse($array, $array1)
            {
                foreach ($array1 as $key => $value) {
                    // create new key in $array, if it is empty or not an array
                    if (!isset($array[$key]) || (isset($array[$key]) && !is_array($array[$key]))) {
                        $array[$key] = [];
                    }

                    // overwrite the value in the base array
                    if (is_array($value)) {
                        $value = recurse($array[$key], $value);
                    }
                    $array[$key] = $value;
                }
                return $array;
            }
        }

        // handle the arguments, merge one by one
        $args = func_get_args();
        $array = $args[0];
        if (!is_array($array)) {
            return $array;
        }
        $count = count($args);
        for ($i = 1; $i < $count; ++$i) {
            if (is_array($args[$i])) {
                $array = recurse($array, $args[$i]);
            }
        }
        return $array;
    }
}
