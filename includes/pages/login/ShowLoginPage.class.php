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


class ShowLoginPage extends AbstractLoginPage
{
    public static $requireModule = 0;

    public function __construct()
    {
        parent::__construct();
    }

    public function show()
    {
        if (empty($_POST)) {
            HTTP::redirectTo('index.php');
        }

        $LNG =& Singleton()->LNG;
        $db = Database::get();

        $username = HTTP::_GP('username', '', UTF8_SUPPORT);
        $password = HTTP::_GP('password', '', true);
        $universe = Universe::current();

        $sql = 'SELECT `id`, `username`, `password`, `failed_logins` FROM %%USERS%% 
            WHERE `universe` = :universe AND (`username` = :username OR `email` = :username);';
        $loginData = $db->selectSingle($sql, [
            ':universe'	=> $universe,
            ':username'	=> $username,
        ]);

        $sql = 'SELECT * FROM %%USERS_VALID%% 
            WHERE `universe` = :universe AND (`userName` = :username OR `email` = :username);';
        $validationData = $db->selectSingle($sql, [
            ':universe'	=> $universe,
            ':username'	=> $username,
        ]);

        if (!empty($loginData)) {
            if (!password_verify($password, $loginData['password'])) {
                if ($loginData['failed_logins'] < 4) {
                    $sql = 'UPDATE %%USERS%% SET `failed_logins` = `failed_logins` + 1 WHERE `id` = :userId;';
                    $db->update($sql, [':userId' => $loginData['id']]);
                    HTTP::redirectTo('index.php?code=1');
                } else {
                    HTTP::redirectTo('index.php?code=4');
                }
            }

            $sql = 'UPDATE %%USERS%% SET `failed_logins` = 0 WHERE `id` = :userId;';
            $db->update($sql, [':userId' => $loginData['id']]);

            if ($loginData['username'] == $username) {
                $senderName = $LNG['loginUsernamePMSenderName'];
                $subject 	= $LNG['loginUsernamePMSubject'];
                $message 	= sprintf($LNG['loginUsernamePMText']);
    
                PlayerUtil::sendMessage($loginData['id'], 1, $senderName, 50, $subject, $message, TIMESTAMP);
            }

            $session	= Session::create();
            $session->userId		= (int) $loginData['id'];
            $session->universe      = $universe;
            $session->adminAccess	= 0;
            $session->save();

            setcookie('uni', $universe, 2147483647, '/');
            HTTP::redirectTo('game.php');
        } elseif (!empty($validationData) && Config::get()->user_valid == 0) {
            if (!password_verify($password, $validationData['password'])) {
                if ($validationData['failed_logins'] < 4) {
                    $sql = 'UPDATE %%USERS_VALID%% SET `failed_logins` = `failed_logins` + 1 WHERE `validationID` = :validationID;';
                    $db->update($sql, [':validationID' => $validationData['validationID']]);
                    HTTP::redirectTo('index.php?code=1');
                } else {
                    HTTP::redirectTo('index.php?code=4');
                }
            }

            $verifyURL = 'index.php?page=vertify&i=' . $validationData['validationID'] . '&k=' . $validationData['validationKey'];
            HTTP::redirectTo($verifyURL);
        } else {
            HTTP::redirectTo('index.php?code=1');
        }
    }
}
