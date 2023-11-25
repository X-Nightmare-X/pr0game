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

class ShowIndexPage extends AbstractLoginPage
{
    public function __construct()
    {
        parent::__construct();
        $this->setWindow('light');
    }

    public function show()
    {
        $LNG =& Singleton()->LNG;
        $db = Database::get();

        $referralID = HTTP::_GP('ref', 0);
        if (!empty($referralID)) {
            $this->redirectTo('index.php?page=register&referralID=' . $referralID);
        }

        $universeSelect = [];
        $universeSelected = Universe::current();

        foreach (Universe::availableUniverses() as $uniId) {
            $config = Config::get($uniId);
            if ($config->uni_status == STATUS_CLOSED) {
                $universeSelect[$uniId] = $config->uni_name . $LNG['uni_closed'];
            } elseif ($config->uni_status == STATUS_REG_ONLY) {
                $sql = "SELECT COUNT(id) FROM %%USERS%% WHERE universe = :universe AND authlevel = 0;";
                $registered = $db->select($sql, [
                    ':universe' => $uniId,
                ]);
                $universeSelect[$uniId] = $config->uni_name . $LNG['uni_reg_open'] . sprintf($LNG['uni_reg_users'], $registered);
            } elseif ($config->uni_status == STATUS_LOGIN_ONLY) {
                $sql = "SELECT COUNT(id) FROM %%USERS%% WHERE universe = :universe AND authlevel = 0 AND onlinetime > :active;";
                $active = $db->select($sql, [
                    ':universe' => $uniId,
                    ':active' => TIMESTAMP - INACTIVE,
                ]);
                $universeSelect[$uniId] = $config->uni_name . $LNG['uni_reg_closed'] . sprintf($LNG['uni_active_users'], $active);
                $universeSelected = $uniId;
            } else {
                $sql = "SELECT COUNT(id) FROM %%USERS%% WHERE universe = :universe AND authlevel = 0 AND onlinetime > :active;";
                $active = $db->select($sql, [
                    ':universe' => $uniId,
                    ':active' => TIMESTAMP - INACTIVE,
                ]);
                $universeSelect[$uniId] = $config->uni_name . sprintf($LNG['uni_active_users'], $active);
                $universeSelected = $uniId;
            }
        }

        $Code = HTTP::_GP('code', 0);
        $loginCode = false;
        if (isset($LNG['login_error_' . $Code])) {
            $loginCode = $LNG['login_error_' . $Code];
        }

        if (isset($_COOKIE['uni']) && !empty($_COOKIE['uni'])) {
            $universeSelected = (int) $_COOKIE['uni'];
        }
        $sql = "SELECT count(*) as count FROM %%CONFIG%% WHERE recaptchaPubKey != ''";
        $countcaptchakey = $db->selectSingle($sql, [], 'count');
        $this->assign([
            'universeSelect'    => $universeSelect,
            'universeSelected'  => $universeSelected,
            'code'              => $loginCode,
            'descHeader'        => sprintf($LNG['loginWelcome'], $config->game_name),
            'descText'          => sprintf($LNG['loginServerDesc'], $config->game_name),
            'gameInformations'  => explode("\n", $LNG['gameInformations']),
            'loginInfo'         => sprintf($LNG['loginInfo'], '<a href="index.php?page=rules&lang=' . ($GLOBALS['_COOKIE']['lang'] ?? 'de') . '">' . $LNG['menu_rules'] . '</a>'),
            'countcaptchakey'   => $countcaptchakey,
        ]);


        $this->display('page.index.default.tpl');
    }
}
