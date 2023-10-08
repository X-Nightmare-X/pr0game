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


class ShowAchievementsInfoPage extends AbstractGamePage
{
    #public static $requireModule = MODULE_INFORMATION;

    protected $disableEcoSystem = true;

    public function __construct()
    {
        parent::__construct();
    }

    public function show()
    {
        $elementID  = HTTP::_GP('id', 0);

        $this->setWindow('popup');
        $this->initTemplate();

        $this->assign([
            'id'         => $elementID,
        ]);

        $this->display('page.achievementsInfo.default.tpl');
    }
}
