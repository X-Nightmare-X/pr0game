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

if (!allowedTo(str_replace([dirname(__FILE__), '\\', '/', '.php'], '', __FILE__))) {
    throw new Exception("Permission error!");
}

function ShowConfigUniPage()
{
    $LNG = &Singleton()->LNG;
    $USER = &Singleton()->USER;
    $config = Config::get(Universe::getEmulated());
    if (!empty($_POST)) {
        $config_before = [
            'uni_name'                  => $config->uni_name,
            'lang'                      => $config->lang,
            'building_speed'            => $config->building_speed,
            'shipyard_speed'            => $config->shipyard_speed,
            'research_speed'            => $config->research_speed,
            'fleet_speed'               => $config->fleet_speed,
            'resource_multiplier'       => $config->resource_multiplier,
            'storage_multiplier'        => $config->storage_multiplier,
            'max_overflow'              => $config->max_overflow,
            'expo_hold_multiplier'      => $config->expo_hold_multiplier,
            'energy_multiplier'         => $config->energy_multiplier,
            'del_user_automatic'        => $config->del_user_automatic,
            'vmode_min_time'            => $config->vmode_min_time,
            'alliance_create_min_points' => $config->alliance_create_min_points,
            'debug'                     => $config->debug,
            'uni_status'                => $config->uni_status,
            'close_reason'              => $config->close_reason,

            'max_elements_build'        => $config->max_elements_build,
            'max_elements_tech'         => $config->max_elements_tech,
            'max_elements_ships'        => $config->max_elements_ships,
            'max_fleet_per_build'       => $config->max_fleet_per_build,
            'min_build_time'            => $config->min_build_time,

            'ref_active'                => $config->ref_active,
            'ref_bonus'                 => $config->ref_bonus,
            'ref_minpoints'             => $config->ref_minpoints,
            'ref_max_referals'          => $config->ref_max_referals,

            'deuterium_cost_galaxy'     => $config->deuterium_cost_galaxy,
            'max_galaxy'                => $config->max_galaxy,
            'max_system'                => $config->max_system,
            'max_planets'               => $config->max_planets,
            'uni_type'                  => $config->uni_type,
            'galaxy_type'               => $config->galaxy_type,
            'planet_creation'           => $config->planet_creation,

            'max_initial_planets'       => $config->max_initial_planets,
            'max_additional_planets'    => $config->max_additional_planets,
            'planets_per_tech'          => $config->planets_per_tech,
            'planet_size_factor'        => $config->planet_size_factor,
            'all_planet_pictures'       => $config->all_planet_pictures,

            'metal_start'               => $config->metal_start,
            'crystal_start'             => $config->crystal_start,
            'deuterium_start'           => $config->deuterium_start,
            'initial_fields'            => $config->initial_fields,
            'initial_temp'              => $config->initial_temp,
            'metal_basic_income'        => $config->metal_basic_income,
            'crystal_basic_income'      => $config->crystal_basic_income,
            'deuterium_basic_income'    => $config->deuterium_basic_income,
            'energy_basic_income'       => $config->energy_basic_income,
            'moon_factor'               => $config->moon_factor,
            'moon_chance'               => $config->moon_chance,
            'debris_moon'               => $config->debris_moon,
            'moon_size_factor'          => $config->moon_size_factor,
            'cascading_moon_chance'     => $config->cascading_moon_chance,

            'factor_university'         => $config->factor_university,
            'silo_factor'               => $config->silo_factor,
            'jumpgate_factor'           => $config->jumpgate_factor,

            'expo_ress_met_chance'      => $config->expo_ress_met_chance,
            'expo_ress_crys_chance'     => $config->expo_ress_crys_chance,
            'expo_ress_deut_chance'     => $config->expo_ress_deut_chance,

            'fleet_debris_percentage'   => $config->fleet_debris_percentage,
            'def_debris_percentage'     => $config->def_debris_percentage,
            'max_fleets_per_acs'        => $config->max_fleets_per_acs,
            'max_participants_per_acs'  => $config->max_participants_per_acs,
            'noob_protection'           => $config->noob_protection,
            'noob_protection_time'      => $config->noob_protection_time,
            'noob_protection_multi'     => $config->noob_protection_multi,
            'adm_attack'                => $config->adm_attack,

            'overview_news_frame'       => $config->overview_news_frame,
            'overview_news_text'        => $config->overview_news_text,

            'trade_allowed_ships'       => $config->trade_allowed_ships,
            'trade_charge'              => $config->trade_charge,
        ];

        $uni_name                       = HTTP::_GP('uni_name', '', true);
        $lang                           = HTTP::_GP('lang', '');
        $building_speed                 = (2500 * HTTP::_GP('building_speed', 0.0));
        $shipyard_speed                 = (2500 * HTTP::_GP('shipyard_speed', 0.0));
        $research_speed                 = (2500 * HTTP::_GP('research_speed', 0.0));
        $fleet_speed                    = (2500 * HTTP::_GP('fleet_speed', 0.0));
        $resource_multiplier            = HTTP::_GP('resource_multiplier', 0.0);
        $storage_multiplier             = HTTP::_GP('storage_multiplier', 0.0);
        $max_overflow                   = HTTP::_GP('max_overflow', 0);
        $expo_hold_multiplier           = HTTP::_GP('expo_hold_multiplier', 0.0);
        $energy_multiplier              = HTTP::_GP('energy_multiplier', 0.0);
        $del_user_automatic             = HTTP::_GP('del_user_automatic', 0);
        $vmode_min_time                 = HTTP::_GP('vmode_min_time', 0);
        $alliance_create_min_points     = HTTP::_GP('alliance_create_min_points', 0);
        $debug                          = isset($_POST['debug']) && $_POST['debug'] == 'on' ? 1 : 0;
        $uni_status                     = HTTP::_GP('uni_status', 0);
        $close_reason                   = HTTP::_GP('close_reason', '', true);

        $max_elements_build             = HTTP::_GP('max_elements_build', 0);
        $max_elements_tech              = HTTP::_GP('max_elements_tech', 0);
        $max_elements_ships             = HTTP::_GP('max_elements_ships', 0);
        $max_fleet_per_build            = max(0, round(HTTP::_GP('max_fleet_per_build', 0.0)));
        $min_build_time                 = HTTP::_GP('min_build_time', 0);

        $ref_active                     = isset($_POST['ref_active']) && $_POST['ref_active'] == 'on' ? 1 : 0;
        $ref_bonus                      = HTTP::_GP('ref_bonus', 0);
        $ref_minpoints                  = HTTP::_GP('ref_minpoints', 0);
        $ref_max_referals               = HTTP::_GP('ref_max_referals', 0);

        $deuterium_cost_galaxy          = HTTP::_GP('deuterium_cost_galaxy', 0);
        $max_galaxy                     = HTTP::_GP('max_galaxy', 0);
        $max_system                     = HTTP::_GP('max_system', 0);
        $max_planets                    = HTTP::_GP('max_planets', 0);
        $uni_type                       = HTTP::_GP('uni_type', 0);
        $galaxy_type                    = HTTP::_GP('galaxy_type', 0);
        $planet_creation                = HTTP::_GP('planet_creation', 0);

        $max_initial_planets            = HTTP::_GP('max_initial_planets', 0);
        $max_additional_planets         = HTTP::_GP('max_additional_planets', 0);
        $planets_per_tech               = HTTP::_GP('planets_per_tech', 0.0);
        $planet_size_factor             = HTTP::_GP('planet_size_factor', 0.0);
        $all_planet_pictures            = isset($_POST['all_planet_pictures']) && $_POST['all_planet_pictures'] == 'on' ? 1 : 0;

        $metal_start                    = HTTP::_GP('metal_start', 0);
        $crystal_start                  = HTTP::_GP('crystal_start', 0);
        $deuterium_start                = HTTP::_GP('deuterium_start', 0);
        $initial_fields                 = HTTP::_GP('initial_fields', 0);
        $initial_temp                   = HTTP::_GP('initial_temp', 0);
        $metal_basic_income             = HTTP::_GP('metal_basic_income', 0);
        $crystal_basic_income           = HTTP::_GP('crystal_basic_income', 0);
        $deuterium_basic_income         = HTTP::_GP('deuterium_basic_income', 0);
        $energy_basic_income            = HTTP::_GP('energy_basic_income', 0);
        $moon_factor                    = HTTP::_GP('moon_factor', 0.0);
        $moon_chance                    = HTTP::_GP('moon_chance', 0);
        $debris_moon                    = isset($_POST['debris_moon']) && $_POST['debris_moon'] == 'on' ? 1 : 0;
        $moon_size_factor               = HTTP::_GP('moon_size_factor', 1.0);
        $cascading_moon_chance          = HTTP::_GP('cascading_moon_chance', 0.0);

        $factor_university              = HTTP::_GP('factor_university', 0);
        $silo_factor                    = HTTP::_GP('silo_factor', 0);
        $jumpgate_factor                = HTTP::_GP('jumpgate_factor', 0);

        $expo_ress_met_chance           = HTTP::_GP('expo_ress_met_chance', 0);
        $expo_ress_crys_chance          = HTTP::_GP('expo_ress_crys_chance', 0);
        $expo_ress_deut_chance          = HTTP::_GP('expo_ress_deut_chance', 0);

        $fleet_debris_percentage        = HTTP::_GP('fleet_debris_percentage', 0);
        $def_debris_percentage          = HTTP::_GP('def_debris_percentage', 0);
        $max_fleets_per_acs             = HTTP::_GP('max_fleets_per_acs', 0);
        $max_participants_per_acs       = HTTP::_GP('max_participants_per_acs', 0);
        $noob_protection                = isset($_POST['noob_protection']) && $_POST['noob_protection'] == 'on' ? 1 : 0;
        $noob_protection_time           = HTTP::_GP('noob_protection_time', 0);
        $noob_protection_multi          = HTTP::_GP('noob_protection_multi', 0);
        $adm_attack                     = isset($_POST['adm_attack']) && $_POST['adm_attack'] == 'on' ? 1 : 0;

        $trade_allowed_ships            = HTTP::_GP('trade_allowed_ships', '');
        $trade_charge                   = HTTP::_GP('trade_charge', 0.0);

        $overview_news_frame            = isset($_POST['newsframe']) && $_POST['newsframe'] == 'on' ? 1 : 0;
        $overview_news_text             = $_POST['NewsText'];

        $config_after = [
            'uni_name'                  => $uni_name,
            'lang'                      => $lang,
            'building_speed'            => $building_speed,
            'shipyard_speed'            => $shipyard_speed,
            'research_speed'            => $research_speed,
            'fleet_speed'               => $fleet_speed,
            'resource_multiplier'       => $resource_multiplier,
            'storage_multiplier'        => $storage_multiplier,
            'max_overflow'              => $max_overflow,
            'expo_hold_multiplier'      => $expo_hold_multiplier,
            'energy_multiplier'         => $energy_multiplier,
            'del_user_automatic'        => $del_user_automatic,
            'vmode_min_time'            => $vmode_min_time,
            'alliance_create_min_points' => $alliance_create_min_points,
            'debug'                     => $debug,
            'uni_status'                => $uni_status,
            'close_reason'              => $close_reason,

            'max_elements_build'        => $max_elements_build,
            'max_elements_tech'         => $max_elements_tech,
            'max_elements_ships'        => $max_elements_ships,
            'max_fleet_per_build'       => $max_fleet_per_build,
            'min_build_time'            => $min_build_time,

            'ref_active'                => $ref_active,
            'ref_bonus'                 => $ref_bonus,
            'ref_minpoints'             => $ref_minpoints,
            'ref_max_referals'          => $ref_max_referals,

            'deuterium_cost_galaxy'     => $deuterium_cost_galaxy,
            'max_galaxy'                => $max_galaxy,
            'max_system'                => $max_system,
            'max_planets'               => $max_planets,
            'uni_type'                  => $uni_type,
            'galaxy_type'               => $galaxy_type,
            'planet_creation'           => $planet_creation,

            'max_initial_planets'       => $max_initial_planets,
            'max_additional_planets'    => $max_additional_planets,
            'planets_per_tech'          => $planets_per_tech,
            'planet_size_factor'        => $planet_size_factor,
            'all_planet_pictures'       => $all_planet_pictures,

            'metal_start'               => $metal_start,
            'crystal_start'             => $crystal_start,
            'deuterium_start'           => $deuterium_start,
            'initial_fields'            => $initial_fields,
            'initial_temp'              => $initial_temp,
            'metal_basic_income'        => $metal_basic_income,
            'crystal_basic_income'      => $crystal_basic_income,
            'deuterium_basic_income'    => $deuterium_basic_income,
            'energy_basic_income'       => $energy_basic_income,
            'moon_factor'               => $moon_factor,
            'moon_chance'               => $moon_chance,
            'debris_moon'               => $debris_moon,
            'moon_size_factor'          => $moon_size_factor,
            'cascading_moon_chance'     => $cascading_moon_chance,

            'factor_university'         => $factor_university,
            'silo_factor'               => $silo_factor,
            'jumpgate_factor'           => $jumpgate_factor,

            'expo_ress_met_chance'      => $expo_ress_met_chance,
            'expo_ress_crys_chance'     => $expo_ress_crys_chance,
            'expo_ress_deut_chance'     => $expo_ress_deut_chance,

            'fleet_debris_percentage'   => $fleet_debris_percentage,
            'def_debris_percentage'     => $def_debris_percentage,
            'max_fleets_per_acs'        => $max_fleets_per_acs,
            'max_participants_per_acs'  => $max_participants_per_acs,
            'noob_protection'           => $noob_protection,
            'noob_protection_time'      => $noob_protection_time,
            'noob_protection_multi'     => $noob_protection_multi,
            'adm_attack'                => $adm_attack,

            'trade_charge'              => $trade_charge,
            'trade_allowed_ships'       => $trade_allowed_ships,

            'overview_news_frame'       => $overview_news_frame,
            'overview_news_text'        => $overview_news_text,
        ];


        if (($uni_status == STATUS_OPEN || $uni_status == STATUS_LOGIN_ONLY) && ($config->uni_status == STATUS_REG_ONLY)) {
            // If login is opened after register only, update all planet timestamps to avoid resource production during closed login times.
            Database::get()->update("UPDATE %%PLANETS%% SET `last_update` = :newTime, `eco_hash` = '' WHERE `universe` = :universe;", [
                ':newTime' => time(),
                ':universe' => Universe::getEmulated(),
            ]);
            // And update all player online times to avoid inactives at universe start.
            Database::get()->update("UPDATE %%USERS%% SET `onlinetime` = :newTime WHERE `universe` = :universe;", [
                ':newTime' => time(),
                ':universe' => Universe::getEmulated(),
            ]);
        } elseif (($uni_status == STATUS_OPEN || $uni_status == STATUS_LOGIN_ONLY) && ($config->uni_status == STATUS_CLOSED)) {
            // If login is opened, update all planet timestamps to avoid resource production during closed login times.
            Database::get()->update("UPDATE %%PLANETS%% SET `last_update` = :newTime, `eco_hash` = '' WHERE `universe` = :universe;", [
                ':newTime' => time(),
                ':universe' => Universe::getEmulated(),
            ]);
        } elseif (($uni_status == STATUS_CLOSED) && ($config->uni_status == STATUS_OPEN)) {
            // Calc ress for all planets when uni login gets closed. Otherwise ress will be lost when reopened.
            $ecoObj = new ResourceUpdate();
            $db = Database::get();
            $db->startTransaction();
            $timestamp = time();
            $sql = "SELECT * FROM %%USERS%% WHERE universe = :universe FOR UPDATE;";
            $users = $db->select($sql, [
                ':universe' => Universe::getEmulated(),
            ]);

            foreach ($users as $user) {
                $sql = "SELECT * FROM %%PLANETS%% WHERE id_owner = :ownerID AND universe = :universe AND planet_type = 1 FOR UPDATE;";
                $planets = $db->select($sql, [
                    ':ownerID' => $user['id'],
                    ':universe' => Universe::getEmulated(),
                ]);

                foreach ($planets as $planet) {
                    $ecoObj->CalcResource($user, $planet, true, $timestamp);
                }
            }
            $db->commit();
        }

        foreach ($config_after as $key => $value) {
            $config->$key = $value;
        }
        $config->save();

        $LOG = new Log(3);
        $LOG->target = 1;
        $LOG->old = $config_before;
        $LOG->new = $config_after;
        $LOG->save();

        if ($config->adm_attack == 0) {
            Database::get()->update("UPDATE %%USERS%% SET `authattack` = '0' WHERE `universe` = :universe;", [
                ':universe' => Universe::getEmulated(),
            ]);
        }
    }

    $template = new template();
    $template->loadscript('../base/jquery.autosize-min.js');
    $template->execscript('$(\'textarea\').autosize();');
    $template->loadscript('../scripts/admin/Percentslider.js');

    $template->assign_vars([
        'uni_name'                      => $config->uni_name,
        'lang'                          => $config->lang,
        'building_speed'                => ($config->building_speed / 2500),
        'shipyard_speed'                => ($config->shipyard_speed / 2500),
        'research_speed'                => ($config->research_speed / 2500),
        'fleet_speed'                   => ($config->fleet_speed / 2500),
        'resource_multiplier'           => $config->resource_multiplier,
        'storage_multiplier'            => $config->storage_multiplier,
        'max_overflow'                  => $config->max_overflow,
        'expo_hold_multiplier'          => $config->expo_hold_multiplier,
        'energy_multiplier'             => $config->energy_multiplier,
        'del_user_automatic'            => $config->del_user_automatic,
        'vmode_min_time'                => $config->vmode_min_time,
        'alliance_create_min_points'    => $config->alliance_create_min_points,
        'debug'                         => $config->debug,
        'uni_status'                    => $config->uni_status,
        'close_reason'                  => $config->close_reason,

        'max_elements_build'            => $config->max_elements_build,
        'max_elements_tech'             => $config->max_elements_tech,
        'max_elements_ships'            => $config->max_elements_ships,
        'max_fleet_per_build'           => $config->max_fleet_per_build,
        'min_build_time'                => $config->min_build_time,

        'ref_active'                    => $config->ref_active,
        'ref_bonus'                     => $config->ref_bonus,
        'ref_minpoints'                 => $config->ref_minpoints,
        'ref_max_referals'              => $config->ref_max_referals,

        'deuterium_cost_galaxy'         => $config->deuterium_cost_galaxy,
        'max_galaxy'                    => $config->max_galaxy,
        'max_system'                    => $config->max_system,
        'max_planets'                   => $config->max_planets,
        'uni_type'                      => $config->uni_type,
        'galaxy_type'                   => $config->galaxy_type,
        'planet_creation'               => $config->planet_creation,

        'max_initial_planets'           => $config->max_initial_planets,
        'max_additional_planets'        => $config->max_additional_planets,
        'planets_per_tech'              => $config->planets_per_tech,
        'planet_size_factor'            => $config->planet_size_factor,
        'all_planet_pictures'           => $config->all_planet_pictures,

        'metal_start'                   => $config->metal_start,
        'crystal_start'                 => $config->crystal_start,
        'deuterium_start'               => $config->deuterium_start,
        'initial_fields'                => $config->initial_fields,
        'initial_temp'                  => $config->initial_temp,
        'metal_basic_income'            => $config->metal_basic_income,
        'crystal_basic_income'          => $config->crystal_basic_income,
        'deuterium_basic_income'        => $config->deuterium_basic_income,
        'energy_basic_income'           => $config->energy_basic_income,
        'moon_factor'                   => $config->moon_factor,
        'moon_chance'                   => $config->moon_chance,
        'debris_moon'                   => $config->debris_moon,
        'moon_size_factor'              => $config->moon_size_factor,
        'cascading_moon_chance'         => $config->cascading_moon_chance,

        'factor_university'             => $config->factor_university,
        'silo_factor'                   => $config->silo_factor,
        'jumpgate_factor'               => $config->jumpgate_factor,

        'expo_ress_met_chance'          => $config->expo_ress_met_chance,
        'expo_ress_crys_chance'         => $config->expo_ress_crys_chance,
        'expo_ress_deut_chance'         => $config->expo_ress_deut_chance,

        'shiips'                        => $config->fleet_debris_percentage,
        'defenses'                      => $config->def_debris_percentage,
        'max_fleets_per_acs'            => $config->max_fleets_per_acs,
        'max_participants_per_acs'      => $config->max_participants_per_acs,
        'noobprot'                      => $config->noob_protection,
        'noobprot2'                     => $config->noob_protection_time,
        'noobprot3'                     => $config->noob_protection_multi,
        'adm_attack'                    => $config->adm_attack,

        'trade_allowed_ships'           => $config->trade_allowed_ships,
        'trade_charge'                  => $config->trade_charge,

        'newsframe'                     => $config->overview_news_frame,
        'NewsTextVal'                   => $config->overview_news_text,

        'Selector'                      => [
            'langs' => $LNG->getAllowedLangs(false),
            'uni_status' => [
                '0' => $LNG['se_uni_status_regopen_gameopen'],
                '1' => $LNG['se_uni_status_regclosed_gameclosed'],
                '2' => $LNG['se_uni_status_regopen_gameclosed'],
                '3' => $LNG['se_uni_status_regclosed_gameopen'],
            ],
            'mail'  => [
                0 => $LNG['se_mail_sel_0'],
                1 => $LNG['se_mail_sel_1'],
                2 => $LNG['se_mail_sel_2'],
            ],
            'encry' => [
                '' => $LNG['se_smtp_ssl_1'],
                'ssl' => $LNG['se_smtp_ssl_2'],
                'tls' => $LNG['se_smtp_ssl_3'],
            ],
            'uni_types' => [
                '0' => $LNG['se_line_uni'],
                '1' => $LNG['se_ring_uni'],
                '2' => $LNG['se_sphere_uni'],
            ],
            'galaxy_types' => [
                '0' => $LNG['se_line_galaxy'],
                '1' => $LNG['se_ring_galaxy'],
                '2' => $LNG['se_sphere_galaxy'],
            ],
            'planet_creations' => [
                '0' => $LNG['se_iter_planet_creation'],
                '1' => $LNG['se_random_planet_creation'],
                '2' => $LNG['se_block_planet_creation'],
            ],
        ],
        'signalColors'                  => $USER['signalColors']
    ]);

    $template->show('ConfigBodyUni.tpl');
}
