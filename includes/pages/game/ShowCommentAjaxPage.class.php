<?php

class ShowCommentAjaxPage extends AbstractGamePage
{
    public function __construct()
    {
        parent::__construct();
        $this->setWindow('ajax');
    }

    public function show()
    {
        $USER =& Singleton()->USER;

        $comment = HTTP::_GP('comment', 0);

        $db = Database::get();

        $sql = 'INSERT INTO %%USERS_COMMENTS%% SET
		user		= :user,
		comment		= :comment,
		created_at	= :timestamp;';

        $db->insert($sql, [
            ':user'			=> $USER['id'],
            ':comment'		=> $comment,
            ':created_at'	=> TIMESTAMP
        ]);
    }
}
