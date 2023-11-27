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

class ShowTeamPage extends AbstractGamePage
{

    public function __construct()
    {
        parent::__construct();

    }

    public function show()
    {

        $universe = Universe::current();
        $db = Database::get();

        $sql = "SELECT `id`, `username` FROM %%USERS%% WHERE authlevel >= 1 and `universe` = :universe";
        $admins = $db->select($sql,[
            ':universe' => $universe,
        ]);

        $idDawnOfTheUwe     = "0";
        $idAdman            = "0";
        $idHackbrett        = "0";
        $idMasterspiel      = "0";
        $idReflexrecon      = "0";
        $idTimoKa           = "0";

        foreach ($admins as $user) {
            switch (strtolower($user["username"])){
                case strtolower("DawnOfTheUwe"):
                    $idDawnOfTheUwe = $user["id"];
                    break;
                case strtolower("Adman"):
                    $idAdman = $user["id"];
                    break;
                case strtolower("Masterspiel"):
                    $idMasterspiel = $user["id"];
                    break;
                case strtolower("reflexrecon"):
                    $idReflexrecon = $user["id"];
                    break;
                case strtolower("timo_ka"):
                    $idTimoKa = $user["id"];
                    break;
                default:
                    break;
            }
        }

        $LNG =& Singleton()->LNG;

        $this->assign([
            'idDawnOfTheUwe'		=> $idDawnOfTheUwe,
            'idAdman'		        => $idAdman,
            'idHackbrett'		    => $idHackbrett,
            'idMasterspiel'		    => $idMasterspiel,
            'idReflexrecon'		    => $idReflexrecon,
            'idTimoKa'		        => $idTimoKa,
        ]);
        $this->display('page.team.default.tpl');

    }

}