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

        $db = Database::get();

        $username = HTTP::_GP('username', '', UTF8_SUPPORT);
        $password = HTTP::_GP('password', '', true);
        $universe = Universe::current();

        $sql = "SELECT id, password FROM %%USERS%% WHERE universe = :universe AND username = :username;";
        $loginData = $db->selectSingle($sql, [
            ':universe'	=> $universe,
            ':username'	=> $username
        ]);

        $sql = "SELECT * FROM %%USERS_VALID%% WHERE universe = :universe AND userName = :username;";
        $validationData = $db->selectSingle($sql, [
            ':universe'	=> $universe,
            ':username'	=> $username
        ]);

        if (!empty($loginData)) {
            if (!password_verify($password, $loginData['password'])) {
                HTTP::redirectTo('index.php?code=1');
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
                HTTP::redirectTo('index.php?code=1');
            }

            $verifyURL = 'index.php?page=vertify&i=' . $validationData['validationID'] . '&k=' . $validationData['validationKey'];
            HTTP::redirectTo($verifyURL);
        } else {
            HTTP::redirectTo('index.php?code=1');
        }
    }
}
