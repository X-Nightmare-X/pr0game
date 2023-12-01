<?php

/**
 *  2Moons
 *   by Timo_Ka 2023
 *
 * For the full copyright and license information, please view the LICENSE
 *
 * @package pr0game
 * @copyright 2023 Timo_Ka
 * @licence MIT
 * @version 1.8.0
 * @link https://codeberg.org/pr0game/pr0game
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