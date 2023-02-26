<?php

class ShowReportPage extends AbstractGamePage
{
    public static $requireModule = 0;

    public function __construct()
    {
        parent::__construct();
        $this->setWindow('ajax');
    }

    public function show()
    {
        $USER =& Singleton()->USER;

        $humanInteraction = HTTP::_GP('hi', 0);
        $uri = HTTP::_GP('u', '');
        $outline = HTTP::_GP('o', '');
        $timestamp = HTTP::_GP('ts', 0);
        $playerId = $USER['id'];

        $fh = fopen(ROOT_PATH . 'reports.txt', 'a+');
        fwrite($fh, date('Y-m-d H:i:s') . ': ' . $playerId . ';' . (int)$humanInteraction . ';' . urlencode($uri) . ';' . str_replace(';', '', $outline) . ';' . $timestamp . PHP_EOL);
        fclose($fh);

        exit('saved');
    }
}
