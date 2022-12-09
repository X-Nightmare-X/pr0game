<?php

if (!allowedTo('ShowMultiIPPage')) throw new Exception("Permission error!");

function ShowCommentsPage()
{
    global $LNG, $USER;
    $db = Database::get();

    $sql = 'SELECT uc.id, u.username, a.ally_name, uc.comment, uc.created_at 
    FROM %%USERS_COMMENTS%% AS uc 
    LEFT JOIN %%USERS%% AS u ON u.id = uc.id 
    LEFT JOIN %%ALLIANCE%% AS a ON a.id = u.ally_id 
    WHERE u.universe = :uni ORDER BY created_at DESC;';

    $comments = $db->select($sql, [
        ':uni' => Universe::getEmulated()
    ]);

    foreach ($comments as &$Data) {
        $Data['created_at']	= _date($LNG['php_tdformat'], $Data['created_at']);
    }

    $template	= new template();
	$template->assign_vars(array(
		'comments'	    => $comments,
        'signalColors'  => $USER['signalColors'],
	));
	$template->show('Comments.tpl');
}