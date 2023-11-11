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

class Session
{
    private static $obj = null;
    private static $iniSet  = false;
    private $data = null;

    /**
     * Set PHP session settings
     *
     * @return bool
     */

    public static function init()
    {
        if (self::$iniSet === true) {
            return false;
        }
        self::$iniSet = true;

        ini_set('session.use_cookies', '1');
        ini_set('session.use_only_cookies', '1');
        ini_set('session.use_trans_sid', 0);
        ini_set('session.auto_start', '0');
        ini_set('session.serialize_handler', 'php');
        ini_set('session.gc_maxlifetime', SESSION_LIFETIME);
        ini_set('session.gc_probability', '1');
        ini_set('session.gc_divisor', '1000');
        ini_set('session.cookie_httponly', true);
        ini_set('session.save_path', CACHE_PATH . 'sessions');
        ini_set('upload_tmp_dir', CACHE_PATH . 'sessions');

        $HTTP_ROOT = (MODE === 'INSTALL' || MODE === 'UPGRADE') ? dirname(HTTP_ROOT) : HTTP_ROOT;

        session_set_cookie_params(SESSION_LIFETIME, $HTTP_ROOT, null, HTTPS, true);
        session_cache_limiter('nocache');
        session_name('2Moons');

        return true;
    }

    private static function getTempPath()
    {
        return sys_get_temp_dir();
    }


    /**
     * Create an empty session
     *
     * @return String
     */

    public static function getClientIp()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ipAddress = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ipAddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED'])) {
            $ipAddress = $_SERVER['HTTP_X_FORWARDED'];
        } elseif (!empty($_SERVER['HTTP_FORWARDED_FOR'])) {
            $ipAddress = $_SERVER['HTTP_FORWARDED_FOR'];
        } elseif (!empty($_SERVER['HTTP_FORWARDED'])) {
            $ipAddress = $_SERVER['HTTP_FORWARDED'];
        } elseif (!empty($_SERVER['REMOTE_ADDR'])) {
            $ipAddress = $_SERVER['REMOTE_ADDR'];
        } else {
            $ipAddress = 'UNKNOWN';
        }
        return $ipAddress;
    }

    /**
     * Create an empty session
     *
     * @return Session
     */

    public static function create()
    {
        if (!self::existsActiveSession()) {
            self::$obj  = new self();
            register_shutdown_function([self::$obj, 'save']);

            @session_start();
        }

        return self::$obj;
    }

    /**
     * Wake an active session
     *
     * @return Session
     */

    public static function load()
    {
        if (!self::existsActiveSession()) {
            self::init();
            session_start();
            if (isset($_SESSION['obj'])) {
                self::$obj  = unserialize($_SESSION['obj']);
                register_shutdown_function([self::$obj, 'save']);
            } else {
                self::create();
            }
        }

        return self::$obj;
    }

    /**
     * Check if an active session exists
     *
     * @return bool
     */

    public static function existsActiveSession()
    {
        return isset(self::$obj);
    }

    public function __construct()
    {
        self::init();
    }

    public function __sleep()
    {
        return ['data'];
    }

    public function __wakeup()
    {
    }

    public function __set($name, $value)
    {
        $this->data[$name]  = $value;
    }

    public function __get($name)
    {
        if (isset($this->data[$name])) {
            return $this->data[$name];
        } else {
            return null;
        }
    }

    public function __isset($name)
    {
        return isset($this->data[$name]);
    }

    public function save()
    {
        // do not save an empty session
        $sessionId = session_id();
        if (empty($sessionId)) {
            return;
        }

        // sessions require an valid user.
        if (empty($this->data['userId'])) {
            $this->delete();
        }

        $userIpAddress = self::getClientIp();

        if (
            !(
                isset($_GET['page'])
                && $_GET['page'] == "raport"
                && isset($_GET['raport'])
                && count($_GET) == 2
                && MODE === 'INGAME'
            )
        ) {
            $sql    = 'REPLACE INTO %%SESSION%% SET
                sessionID	= :sessionId,
                userID		= :userId,
                lastonline	= :lastActivity,
                created		= :created,
                userIP		= :userAddress;';

            $db     = Database::get();

            $sql_created = 'SELECT created FROM %%SESSION%% WHERE sessionID = :sessionId AND userID = :userId;';

            $created = $db->selectSingle($sql_created, [
                ':sessionId'    => session_id(),
                ':userId'       => $this->data['userId'],
            ], 'created');

            if (empty($created)) {
                $created = time();
            }

            $db->replace($sql, [
                ':sessionId'    => session_id(),
                ':userId'       => $this->data['userId'],
                ':lastActivity' => TIMESTAMP,
                ':created'  => $created,
                ':userAddress'  => $userIpAddress,
            ]);

            $sql = 'UPDATE %%USERS%% SET
                onlinetime	= :lastActivity,
                user_lastip = :userAddress
                WHERE id = :userId;';

            $db->update($sql, [
               ':userAddress'   => $userIpAddress,
               ':lastActivity'  => TIMESTAMP,
               ':userId'        => $this->data['userId'],
            ]);

            // Remove multisessions
            if (PREVENT_MULTISESSIONS == true) {
                $sql    = 'DELETE FROM %%SESSION%% WHERE (userID = :userId AND sessionID != :sessionId);';
                $db->delete($sql, [
                    ':userId'   => $this->data['userId'],
                    ':sessionId'    => session_id(),
                ]);
            }

            // Remove old sessions
            if ($created + SESSION_LIFETIME < time()) {
                $sql    = 'DELETE FROM %%SESSION%% WHERE (userID = :userId AND sessionID = :sessionId);';

                $db->delete($sql, [
                    ':userId'       => $this->data['userId'],
                    ':sessionId'    => session_id(),
                ]);
            }

            $this->checkMultiIP();

            $this->data['lastActivity']  = TIMESTAMP;
            $this->data['sessionId']     = session_id();
            $this->data['userIpAddress'] = $userIpAddress;
            $this->data['requestPath']   = $this->getRequestPath();

            $_SESSION['obj']    = serialize($this);

            @session_write_close();
        }
    }

    public function delete()
    {
        $sql    = 'DELETE FROM %%SESSION%% WHERE sessionID = :sessionId;';
        $db     = Database::get();

        $db->delete($sql, [':sessionId' => session_id()]);

        @session_destroy();
    }

    public function isValidSession()
    {
        // if($this->compareIpAddress($this->data['userIpAddress'], self::getClientIp(), COMPARE_IP_BLOCKS) === false)
        // {
        // return false;
        // }

        if (
            isset($_GET['page'])
            && $_GET['page'] == "raport"
            && isset($_GET['raport'])
            && count($_GET) == 2
            && MODE === 'INGAME'
        ) {
            $this->data['lastActivity'] = time();
        } else {
            if (!isset($_SESSION["obj"])) {
                return false;
            }
        }


        if ($this->data['lastActivity'] < TIMESTAMP - SESSION_LIFETIME) {
            return false;
        }

        $sql = 'SELECT COUNT(*) as record FROM %%SESSION%% WHERE sessionID = :sessionId;';
        $db     = Database::get();

        $sessionCount = $db->selectSingle($sql, [':sessionId' => session_id()], 'record');

        if ($sessionCount == 0) {
            return false;
        }

        return true;
    }

    public function selectActivePlanet()
    {
        $httpData   = HTTP::_GP('cp', 0);

        if (!empty($httpData)) {
            $sql    = 'SELECT id FROM %%PLANETS%% WHERE id = :planetId AND id_owner = :userId;';

            $db = Database::get();
            $planetId   = $db->selectSingle($sql, [
                ':userId'   => $this->data['userId'],
                ':planetId' => $httpData,
            ], 'id');

            if (!empty($planetId)) {
                $this->data['planetId'] = $planetId;
            }
        }
    }

    private function getRequestPath()
    {
        return HTTP_ROOT . (!empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '');
    }

    private function compareIpAddress($ip1, $ip2, $blockCount)
    {
        if (strpos($ip2, ':') !== false && strpos($ip1, ':') !== false) {
            $s_ip = $this->shortIpv6($ip1, $blockCount);
            $u_ip = $this->shortIpv6($ip2, $blockCount);
        } else {
            $s_ip = implode('.', array_slice(explode('.', $ip1), 0, $blockCount));
            $u_ip = implode('.', array_slice(explode('.', $ip2), 0, $blockCount));
        }

        return ($s_ip == $u_ip);
    }

    private function shortIpv6($ip, $length)
    {
        if ($length < 1) {
            return '';
        }

        $blocks = substr_count($ip, ':') + 1;
        if ($blocks < 9) {
            $ip = str_replace('::', ':' . str_repeat('0000:', 9 - $blocks), $ip);
        }
        if ($ip[0] == ':') {
            $ip = '0000' . $ip;
        }
        if ($length < 4) {
            $ip = implode(':', array_slice(explode(':', $ip), 0, 1 + $length));
        }

        return $ip;
    }

    private function checkMultiIP() {

        $db     = Database::get();
        $userIpAddress = self::getClientIp();

        // Check fpr MultiIP
        $sql = "SELECT `id` FROM %%USERS%%
            WHERE `onlinetime` > :lastActivity
            AND `user_lastip` = :userAddress
            AND `id` != :userId
            AND `universe` = :universe;";
        $multis = $db->select($sql, [
            ':userAddress'  => $userIpAddress,
            ':lastActivity' => TIMESTAMP - TIME_24_HOURS,
            ':userId'       => $this->data['userId'],
            ':universe'     => Universe::current(),
        ]);

        if (!empty($multis)) {
            $multis[] = ['id' => $this->data['userId']];

            $sql = "SELECT * FROM %%MULTI%%
                WHERE `multi_ip` = :userAddress
                AND `universe` = :universe;";
            $multiEntry = $db->selectSingle($sql, [
                ':userAddress'  => $userIpAddress,
                ':universe'     => Universe::current(),
            ]);

            if (empty($multiEntry)) {
                $sql = "SELECT mu.`multiID`, count(DISTINCT muc.`userID`) AS anz
                    FROM `uni1_multi_to_users` AS mu
                    JOIN `uni1_multi_to_users` AS muc ON muc.`multiID` = mu.`multiID`
                    WHERE mu.`userID` IN ( ". implode(', ', array_column($multis, 'id')) ." )
                    GROUP BY mu.`multiID`
                    HAVING count(DISTINCT mu.`userID`) >= :usersSize
                    ORDER BY anz ASC;";
                $multiID = $db->selectSingle($sql, [
                    ':usersSize' => count($multis),
                ], 'multiID');

                $sql = "SELECT * FROM %%MULTI%%
                    WHERE `multiID` = :multiID;";
                $multiEntry = $db->selectSingle($sql, [
                    ':multiID'  => $multiID,
                ]);
            }

            if (empty($multiEntry)) {
                $sql = "INSERT INTO %%MULTI%% SET
                    `multi_ip` = :userAddress,
                    `lastActivity` = :lastActivity,
                    `universe` = :universe,
                    `allowed` = 0;";
                $db->insert($sql, [
                    ':userAddress' => $userIpAddress,
                    ':lastActivity' => TIMESTAMP,
                    ':universe' => Universe::current(),
                ]);
                $multiID = $db->lastInsertId();

                $sql = "INSERT INTO %%MULTI_TO_USERS%% SET
                    `multiID` = :multiID,
                    `userID` = :userID,
                    `lastActivity` = :lastActivity,
                    `allowed` = 0;";

                foreach ($multis as $multi) {
                    $db->insert($sql, [
                        ':multiID' => $multiID,
                        ':userID' => $multi['id'],
                        ':lastActivity' => TIMESTAMP,
                    ]);
                }
            } else {
                $multiID = $multiEntry['multiID'];
                $sql = "UPDATE %%MULTI%% SET
                    `multi_ip` = :userAddress,
                    `lastActivity` = :lastActivity
                    WHERE `multiID` = :multiID;";
                $db->update($sql, [
                    ':userAddress'  => $userIpAddress,
                    ':lastActivity' => TIMESTAMP,
                    ':multiID' => $multiID,
                ]);

                $sqlSelect = "SELECT * FROM %%MULTI_TO_USERS%%
                    WHERE `multiID` = :multiID AND `userID` = :userID;";
                $sqlInsert = "INSERT INTO %%MULTI_TO_USERS%% SET
                    `multiID` = :multiID,
                    `userID` = :userID,
                    `lastActivity` = :lastActivity,
                    `allowed` = 0;";
                $sqlUpdate = "UPDATE %%MULTI_TO_USERS%% SET
                    `lastActivity` = :lastActivity
                    WHERE `multiID` = :multiID AND `userID` = :userID;";

                foreach ($multis as $multi) {
                    $result = $db->selectSingle($sqlSelect, [
                        ':multiID' => $multiID,
                        ':userID' => $multi['id'],
                    ]);

                    if (empty($result)) {
                        $db->insert($sqlInsert, [
                            ':multiID' => $multiID,
                            ':userID' => $multi['id'],
                            ':lastActivity' => TIMESTAMP,
                        ]);
                    } else if ($multi['id'] == $this->data['userId']) {
                        $db->update($sqlUpdate, [
                            ':lastActivity' => TIMESTAMP,
                            ':multiID' => $multiID,
                            ':userID' => $multi['id'],
                        ]);
                    }
                }
            }
        }
    }
}
