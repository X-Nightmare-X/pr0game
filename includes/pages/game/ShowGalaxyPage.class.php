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

require_once('includes/classes/class.GalaxyRows.php');

class ShowGalaxyPage extends AbstractGamePage
{
    public static $requireModule = MODULE_RESEARCH;

    public function __construct()
    {
        parent::__construct();
    }

    public function show()
    {
        $USER =& Singleton()->USER;
        $PLANET =& Singleton()->PLANET;
        $resource =& Singleton()->resource;
        $LNG =& Singleton()->LNG;
        $reslist =& Singleton()->reslist;
        $pricelist =& Singleton()->pricelist;
        $config			= Config::get();

        $action 		= HTTP::_GP('action', '');
        $galaxyLeft		= HTTP::_GP('galaxyLeft', '');
        $galaxyRight	= HTTP::_GP('galaxyRight', '');
        $systemLeft		= HTTP::_GP('systemLeft', '');
        $systemRight	= HTTP::_GP('systemRight', '');
        $galaxy			= min(max(HTTP::_GP('galaxy', (int) $PLANET['galaxy']), 1), $config->max_galaxy);
        $system			= min(max(HTTP::_GP('system', (int) $PLANET['system']), 1), $config->max_system);
        $planet			= min(max(HTTP::_GP('planet', (int) $PLANET['planet']), 1), $config->max_planets);
        $type			= HTTP::_GP('type', 1);
        $current		= HTTP::_GP('current', 0);

        if (!empty($galaxyLeft)) {
            $galaxy	= $galaxy - 1;
            if ($galaxy < 1) {
                $galaxy = $config->max_galaxy;
            }
        } elseif (!empty($galaxyRight)) {
            $galaxy	= $galaxy + 1;
            if ($galaxy > $config->max_galaxy) {
                $galaxy = 1;
            }
        }

        if (!empty($systemLeft)) {
            $system	= $system - 1;
            if ($system < 1) {
                $system = $config->max_system;
            }
        } elseif (!empty($systemRight)) {
            $system	= $system + 1;
            if ($system > $config->max_system) {
                $system = 1;
            }
        }

        if ($galaxy != $PLANET['galaxy'] || $system != $PLANET['system']) {
            if ($PLANET['deuterium'] < $config->deuterium_cost_galaxy) {
                $this->printMessage($LNG['gl_no_deuterium_to_view_galaxy'], array(array(
                    'label'	=> $LNG['sys_back'],
                    'url'	=> 'game.php?page=galaxy'
                )));
            } else {
                $resources = [RESOURCE_DEUT => $config->deuterium_cost_galaxy];
                $this->ecoObj->removeResources($PLANET['id'], $resources, $PLANET);
            }
        }

        $targetDefensive    = $reslist['defense'];
        $targetDefensive[]	= 502;
        $missileSelector[0]	= $LNG['gl_all_defenses'];

        foreach ($targetDefensive as $Element) {
            $missileSelector[$Element] = $LNG['tech'][$Element];
        }
        $sql	= 'SELECT total_points
		FROM %%STATPOINTS%%
		WHERE id_owner = :userId AND stat_type = :statType';

        try {
            $USER += Database::get()->selectSingle($sql, [
                ':userId' => $USER['id'],
                ':statType' => 1
            ]) ?: ['total_points' => 0];
        } catch (Exception $exception) {
        }

        $galaxyRows	= new GalaxyRows();
        $galaxyRows->setGalaxy($galaxy);
        $galaxyRows->setSystem($system);
        $Result	= $galaxyRows->getGalaxyData();
        if (gettype($Result) == "NULL") {
            $Result = [];
        }

        $hiddenDebris = ($pricelist[SHIP_PROBE]['cost'][RESOURCE_METAL] + $pricelist[SHIP_PROBE]['cost'][RESOURCE_CRYSTAL]) * ($config->Fleet_Cdr / 100);

        $this->tplObj->loadscript('galaxy.js');
        $this->assign(array(
            'GalaxyRows'				=> $Result,
            'planetcount'				=> sprintf($LNG['gl_populed_planets'], count($Result)),
            'action'					=> $action,
            'galaxy'					=> $galaxy,
            'system'					=> $system,
            'planet'					=> $planet,
            'type'						=> $type,
            'current'					=> $current,
            'maxfleetcount'				=> FleetFunctions::getCurrentFleets($USER['id']),
            'fleetmax'					=> FleetFunctions::getMaxFleetSlots($USER),
            'currentmip'				=> $PLANET[$resource[503]],
            'recyclers'   				=> $PLANET[$resource[209]],
            'spyprobes'   				=> $PLANET[$resource[210]],
            'missile_count'				=> sprintf($LNG['gl_missil_to_launch'], $PLANET[$resource[503]]),
            'spyShips'					=> array(210 => $USER['spio_anz']),
            'settings_fleetactions'		=> $USER['settings_fleetactions'],
            'current_galaxy'			=> $PLANET['galaxy'],
            'current_system'			=> $PLANET['system'],
            'current_planet'			=> $PLANET['planet'],
            'planet_type' 				=> $PLANET['planet_type'],
            'max_planets'               => $config->max_planets,
            'missileSelector'			=> $missileSelector,
            'hiddenDebris'					=> $hiddenDebris,
            'ShortStatus'				=> array(
                'vacation'					=> $LNG['gl_short_vacation'],
                'banned'					=> $LNG['gl_short_ban'],
                'inactive'					=> $LNG['gl_short_inactive'],
                'longinactive'				=> $LNG['gl_short_long_inactive'],
                'noob'						=> $LNG['gl_short_newbie'],
                'strong'					=> $LNG['gl_short_strong'],
                'enemy'						=> $LNG['gl_short_enemy'],
                'friend'					=> $LNG['gl_short_friend'],
                'member'					=> $LNG['gl_short_member'],
            ),
        ));

        $this->display('page.galaxy.default.tpl');
    }
}
