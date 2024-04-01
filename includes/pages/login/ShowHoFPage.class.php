<?php

/**
 *  2Moons
 *   by Timo_Ka 2024
 *
 * For the full copyright and license information, please view the LICENSE
 *
 * @package pr0game
 * @copyright 2024 Timo_Ka
 * @licence MIT
 * @version 1.8.0
 * @link https://codeberg.org/pr0game/pr0game
 */

class ShowHofPage extends AbstractLoginPage
{

    public static $requireModule = 0;

    public function __construct()
    {
        parent::__construct();

    }
    
    public function show()
    {

        
        $this->display('page.HoF.default.tpl');

    }

}