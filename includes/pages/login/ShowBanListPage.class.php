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

class ShowBanListPage extends AbstractLoginPage
{
    public static $requireModule = MODULE_BANLIST;

    public function __construct()
    {
        parent::__construct();
    }

    public function show()
    {
        $LNG =& Singleton()->LNG;

        $db = Database::get();

        $page  		= HTTP::_GP('side', 1);

        $sql = "SELECT COUNT(*) as count FROM %%BANNED%% WHERE universe = :universe ORDER BY time DESC;";
        $banCount = $db->selectSingle($sql, [
            ':universe'	=> Universe::current(),
        ], 'count');

        $maxPage	= ceil($banCount / BANNED_USERS_PER_PAGE);
        $page		= max(1, min($page, $maxPage));

        $sql = "SELECT * FROM %%BANNED%% WHERE universe = :universe ORDER BY time DESC LIMIT :offset, :limit;";
        $banResult = $db->select($sql, [
            ':universe'	=> Universe::current(),
            ':offset'	=> (($page - 1) * BANNED_USERS_PER_PAGE),
            ':limit'	=> BANNED_USERS_PER_PAGE
        ]);

        $banList	= [];

        foreach ($banResult as $banRow) {
            $banList[]	= [
                'player'	=> $banRow['who'],
                'theme'		=> $banRow['theme'],
                'from'		=> _date($LNG['php_tdformat'], $banRow['time'], Config::get()->timezone),
                'to'		=> _date($LNG['php_tdformat'], $banRow['longer'], Config::get()->timezone),
                'admin'		=> $banRow['author'],
                'mail'		=> $banRow['email'],
                'info'		=> sprintf($LNG['bn_writemail'], $banRow['author']),
            ];
        }

        $universeSelect	= $this->getUniverseSelector();

        $this->assign([
            'universeSelect'	=> $universeSelect,
            'banList'			=> $banList,
            'banCount'			=> $banCount,
            'page'				=> $page,
            'maxPage'			=> $maxPage,
        ]);

        $this->display('page.banList.default.tpl');
    }
}
