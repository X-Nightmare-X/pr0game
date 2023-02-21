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


class ShowErrorPage extends AbstractGamePage
{
    public static $requireModule = 0;

    protected $disableEcoSystem = true;

    public function __construct()
    {
        parent::__construct();
        $this->initTemplate();
    }

    public static function printError($Message, $fullSide = true, $redirect = null)
    {
        $pageObj	= new self();
        $pageObj->printMessage($Message, null, $redirect, $fullSide);
    }

    public function show()
    {
    }
}
