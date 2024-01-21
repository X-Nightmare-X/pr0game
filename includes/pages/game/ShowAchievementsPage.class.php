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


class ShowAchievementsPage extends AbstractGamePage
{
    #public static $requireModule = MODULE_TECHTREE;

    public function __construct()
    {
        parent::__construct();
    }

    public function show()
    {
        $USER =& Singleton()->USER;
        $LNG =& Singleton()->LNG;
        require_once 'includes/classes/Achievement.class.php';

        $Messages		    = $USER['messages'];
        $allAchievements    = Achievement::getAchievementList();
        $userAchievements   = Achievement::getUserAchievements($USER['id']);
        $this->assign([
            'allAchievements'		=> $allAchievements,
            'userAchievements'		=> $userAchievements,
            'notImplemented'		=> NOT_IMPLEMENTED_ACHIEVEMENTS,
            'messages'			    => ($Messages > 0) ? (($Messages == 1) ? $LNG['ov_have_new_message'] : sprintf($LNG['ov_have_new_messages'], pretty_number($Messages))) : false,
            'message_type'          => $USER['showMessageCategory'] === 1 ? $USER['message_type'] : false,
        ]);

        $this->display('page.achievements.default.tpl');
    }
}
