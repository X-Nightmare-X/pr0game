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

if (!allowedTo(str_replace([dirname(__FILE__), '\\', '/', '.php'], '', __FILE__))) {
    throw new Exception("Permission error!");
}

function ShowNewsPage()
{
    $LNG =& Singleton()->LNG;
    $USER =& Singleton()->USER;
    if (!isset($_GET['action'])) {
        $_GET['action'] = '';
    }
    if ($_GET['action'] == 'send') {
        $edit_id 	= HTTP::_GP('id', 0);
        $title 		= $GLOBALS['DATABASE']->sql_escape(HTTP::_GP('title', '', true));
        $text 		= $GLOBALS['DATABASE']->sql_escape(HTTP::_GP('text', '', true));
        $query		= ($_GET['mode'] == 2) ? "INSERT INTO ".NEWS." (`id` ,`user` ,`date` ,`title` ,`text`) VALUES ( NULL , '".$USER['username']."', '".TIMESTAMP."', '".$title."', '".$text."');" : "UPDATE ".NEWS." SET `title` = '".$title."', `text` = '".$text."', `date` = '".TIMESTAMP."' WHERE `id` = '".$edit_id."' LIMIT 1;";

        $GLOBALS['DATABASE']->query($query);
    } elseif ($_GET['action'] == 'delete' && isset($_GET['id'])) {
        $GLOBALS['DATABASE']->query("DELETE FROM ".NEWS." WHERE `id` = '".HTTP::_GP('id', 0)."';");
    }

    $query = $GLOBALS['DATABASE']->query("SELECT * FROM ".NEWS." ORDER BY id ASC");
    $NewsList = [];
    while ($u = $GLOBALS['DATABASE']->fetch_array($query)) {
        $NewsList[]	= [
            'id'		=> $u['id'],
            'title'		=> $u['title'],
            'date'		=> _date($LNG['php_tdformat'], $u['date'], $USER['timezone']),
            'user'		=> $u['user'],
            'confirm'	=> sprintf($LNG['nws_confirm'], $u['title']),
        ];
    }

    $template	= new template();


    if ($_GET['action'] == 'edit' && isset($_GET['id'])) {
        $News = $GLOBALS['DATABASE']->getFirstRow("SELECT id, title, text FROM ".NEWS." WHERE id = '".$GLOBALS['DATABASE']->sql_escape($_GET['id'])."';");
        $template->assign_vars([
            'mode'			=> 1,
            'nws_head'		=> sprintf($LNG['nws_head_edit'], $News['title']),
            'news_id'		=> $News['id'],
            'news_title'	=> $News['title'],
            'news_text'		=> $News['text'],
        ]);
    } elseif ($_GET['action'] == 'create') {
        $template->assign_vars([
            'mode'			=> 2,
            'nws_head'		=> $LNG['nws_head_create'],
            'news_id'		=> 0,
            'news_title'	=> '',
            'news_text'		=> '',
        ]);
    }

    $template->assign_vars([
        'NewsList'		=> $NewsList,
        'button_submit'	=> $LNG['button_submit'],
        'nws_total'		=> sprintf($LNG['nws_total'], $NewsList && count($NewsList)),
        'nws_news'		=> $LNG['nws_news'],
        'nws_id'		=> $LNG['nws_id'],
        'nws_title'		=> $LNG['nws_title'],
        'nws_date'		=> $LNG['nws_date'],
        'nws_from'		=> $LNG['nws_from'],
        'nws_del'		=> $LNG['nws_del'],
        'nws_create'	=> $LNG['nws_create'],
        'nws_content'	=> $LNG['nws_content'],
        'signalColors'	=> $USER['signalColors'],
    ]);

    $template->show('NewsPage.tpl');
}
