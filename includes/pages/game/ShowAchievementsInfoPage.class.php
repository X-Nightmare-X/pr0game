<?php

/**
 *
 * For the full copyright and license information, please view the LICENSE
 *
 * @package 2Moons
 * @author Reflexrecon
 * @copyright 2023 Reflexrecon
 * @licence MIT
 * @version 1.8.0
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
