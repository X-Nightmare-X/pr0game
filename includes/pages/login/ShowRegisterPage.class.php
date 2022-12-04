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

class ShowRegisterPage extends AbstractLoginPage
{
    function __construct()
    {
        parent::__construct();
    }

    function show()
    {
        global $LNG;
        $universeSelect = [];
        $referralData = ['id' => 0, 'name' => ''];
        $accountName = "";

        foreach (Universe::availableUniverses() as $uniId) {
            $config = Config::get($uniId);
            if ($config->uni_status == STATUS_CLOSED) {
                $universeSelect[$uniId] = $config->uni_name . $LNG['uni_closed'];
            }
            elseif ($config->uni_status == STATUS_REG_ONLY) {
                $universeSelect[$uniId] = $config->uni_name . $LNG['uni_reg_open'];
            }
            elseif ($config->uni_status == STATUS_LOGIN_ONLY) {
                $universeSelect[$uniId] = $config->uni_name . $LNG['uni_reg_closed'];
            }
            else {
                $universeSelect[$uniId] = $config->uni_name;
            }
        }

        $config = Config::get();
        if ($config->ref_active == 1 && !empty($referralID)) {
            $db = Database::get();

            $sql = "SELECT username FROM %%USERS%% WHERE id = :referralID AND universe = :universe;";
            $referralAccountName = $db->selectSingle($sql, [
                ':referralID' => $referralID,
                ':universe' => Universe::current()
            ], 'username');

            if (!empty($referralAccountName)) {
                $referralData = ['id' => $referralID, 'name' => $referralAccountName];
            }
        }

        $this->assign([
            'referralData' => $referralData,
            'accountName' => $accountName,
            'universeSelect' => $universeSelect,
            'registerPasswordDesc' => sprintf($LNG['registerPasswordDesc'], 6),
            'registerRulesDesc' => sprintf($LNG['registerRulesDesc'], '<a href="index.php?page=rules&lang=' . ($GLOBALS['_COOKIE']['lang'] ?? 'de') . '">' . $LNG['menu_rules'] . '</a>')
        ]);

        $this->display('page.register.default.tpl');
    }

    function send()
    {
        global $LNG;
        $config = Config::get();

        if ($config->uni_status == STATUS_CLOSED || $config->uni_status == STATUS_LOGIN_ONLY) {
            $this->printMessage($LNG['registerErrorUniClosed'], [[
                'label' => $LNG['registerBack'],
                'url' => 'javascript:window.history.back()',
            ]]);
        }

        $userName = HTTP::_GP('username', '', UTF8_SUPPORT);
        $password = HTTP::_GP('password', '', true);
        $password2 = HTTP::_GP('passwordReplay', '', true);
        $mailAddress = HTTP::_GP('email', '');
        $mailAddress2 = HTTP::_GP('emailReplay', '');
        $rulesChecked = HTTP::_GP('rules', 0);
        $language = HTTP::_GP('lang', '');

        $referralID = HTTP::_GP('referralID', 0);

        $errors = [];

        if (empty($userName)) {
            $errors[] = $LNG['registerErrorUsernameEmpty'];
        }

        if (!PlayerUtil::isNameValid($userName)) {
            $errors[] = $LNG['registerErrorUsernameChar'];
        }

        if (strlen($password) < 6) {
            $errors[] = sprintf($LNG['registerErrorPasswordLength'], 6);
        }

        if ($password != $password2) {
            $errors[] = $LNG['registerErrorPasswordSame'];
        }

        if (!PlayerUtil::isMailValid($mailAddress)) {
            $errors[] = $LNG['registerErrorMailInvalid'];
        }

        if (empty($mailAddress)) {
            $errors[] = $LNG['registerErrorMailEmpty'];
        }

        if ($mailAddress != $mailAddress2) {
            $errors[] = $LNG['registerErrorMailSame'];
        }

        if ($rulesChecked != 1) {
            $errors[] = $LNG['registerErrorRules'];
        }

        $db = Database::get();

        $sql = "SELECT (
				SELECT COUNT(*)
				FROM %%USERS%%
				WHERE universe = :universe
				AND username = :userName
			) + (
				SELECT COUNT(*)
				FROM %%USERS_VALID%%
				WHERE universe = :universe
				AND username = :userName
			) as count;";

        $countUsername = $db->selectSingle($sql, [
            ':universe' => Universe::current(),
            ':userName' => $userName,
        ], 'count');

        $sql = "SELECT (
			SELECT COUNT(*)
			FROM %%USERS%%
			WHERE universe = :universe
			AND (
				email = :mailAddress
				OR email_2 = :mailAddress
			)
		) + (
			SELECT COUNT(*)
			FROM %%USERS_VALID%%
			WHERE universe = :universe
			AND email = :mailAddress
		) as count;";

        $countMail = $db->selectSingle($sql, [
            ':universe' => Universe::current(),
            ':mailAddress' => $mailAddress,
        ], 'count');

        if ($countUsername != 0) {
            $errors[] = $LNG['registerErrorUsernameExist'];
        }

        if ($countMail != 0) {
            $errors[] = $LNG['registerErrorMailExist'];
        }

        if (!empty($errors)) {
            $this->printMessage(implode("<br>\r\n", $errors), [[
                'label' => $LNG['registerBack'],
                'url' => 'javascript:window.history.back()',
            ]]);
        }

        if ($config->ref_active == 1 && !empty($referralID)) {
            $sql = "SELECT COUNT(*) as state FROM %%USERS%% WHERE id = :referralID AND universe = :universe;";
            $Count = $db->selectSingle($sql, [
                ':referralID' => $referralID,
                ':universe' => Universe::current()
            ], 'state');

            if ($Count == 0) {
                $referralID = 0;
            }
        } else {
            $referralID = 0;
        }

        $validationKey = md5(uniqid('2m'));

        $sql = "INSERT INTO %%USERS_VALID%% SET
				`userName` = :userName,
				`validationKey` = :validationKey,
				`password` = :password,
				`email` = :mailAddress,
				`date` = :timestamp,
				`ip` = :remoteAddr,
				`language` = :language,
				`universe` = :universe,
				`referralID` = :referralID;";


        $db->insert($sql, [
            ':userName' => $userName,
            ':validationKey' => $validationKey,
            ':password' => PlayerUtil::cryptPassword($password),
            ':mailAddress' => $mailAddress,
            ':timestamp' => TIMESTAMP,
            ':remoteAddr' => Session::getClientIp(),
            ':language' => $language,
            ':universe' => Universe::current(),
            ':referralID' => $referralID,
        ]);

        $validationID = $db->lastInsertId();
        $verifyURL = 'index.php?page=vertify&i=' . $validationID . '&k=' . $validationKey;
        
        if (isset($_COOKIE['uni'])) {
            setcookie('uni', '', -1, '/');
            unset($_COOKIE['uni']);
        }

        if ($config->user_valid == 0) {
            $this->redirectTo($verifyURL);
        } else {
            require 'includes/classes/Mail.class.php';
            $MailRAW = $LNG->getTemplate('email_vaild_reg');
            $MailContent = str_replace([
                '{USERNAME}',
                '{PASSWORD}',
                '{GAMENAME}',
                '{VERTIFYURL}',
                '{GAMEMAIL}',
            ], [
                $userName,
                $password,
                $config->game_name . ' - ' . $config->uni_name,
                HTTP_PATH . $verifyURL,
                $config->smtp_sendmail,
            ], $MailRAW);

            $subject = sprintf($LNG['registerMailVertifyTitle'], $config->game_name);
            Mail::send($mailAddress, $userName, $subject, $MailContent);

            $this->printMessage($LNG['registerSendComplete']);
        }
    }
}
