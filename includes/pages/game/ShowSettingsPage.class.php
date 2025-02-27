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

class ShowSettingsPage extends AbstractGamePage
{
    public static $requireModule = 0;

    public function __construct()
    {
        parent::__construct();
    }

    public function show()
    {
        $USER =& Singleton()->USER;
        $LNG =& Singleton()->LNG;
        if ($USER['urlaubs_modus'] == 1) {
            $this->assign([
                'vacationUntil'         => _date($LNG['php_tdformat'], $USER['urlaubs_until'], $USER['timezone']),
                'delete'                => $USER['db_deaktjava'],
                'canVacationDisbaled'   => $USER['urlaubs_until'] < TIMESTAMP,
            ]);

            $this->display('page.settings.vacation.tpl');
        } else {
            $db = Database::get();
            $sql = "SELECT prioMission1 as 'type_mission_1', prioMission2 as 'type_mission_2', 
            prioMission3 as 'type_mission_3',prioMission4 as 'type_mission_4', 
            prioMission5 as 'type_mission_5',prioMission6 as 'type_mission_6',
            prioMission7 as 'type_mission_7',prioMission8 as 'type_mission_8', 
            prioMission9 as 'type_mission_9', prioMission17 as 'type_mission_17'
            FROM %%USERS%% WHERE universe = :universe AND id = :userID ;";
            $missionprios =$db->selectSingle($sql, [
                ':universe' => Universe::current(),
                ':userID'   => $USER['id'],
            ]);

            $this->assign([
                'Selectors'         => [
                    'timezones' => get_timezone_selector(),
                    'Sort' => [
                        0 => $LNG['op_sort_normal'],
                        1 => $LNG['op_sort_koords'],
                        2 => $LNG['op_sort_abc'],
                    ],
                    'SortUpDown' => [
                        0 => $LNG['op_sort_up'],
                        1 => $LNG['op_sort_down'],
                    ],
                    'Skins' => Theme::getAvalibleSkins(),
                    'lang' => $LNG->getAllowedLangs(false),
                ],
                'adminProtection'               => $USER['authattack'],
                'discord_id'                    => $USER['discord_id'],
                'discord_hook'                  => $USER['discord_hook'],
                'userAuthlevel'                 => $USER['authlevel'],
                'changeNickTime'                => ($USER['uctime'] + USERNAME_CHANGETIME) - TIMESTAMP,
                'username'                      => $USER['username'],
                'email'                         => $USER['email'],
                'permaEmail'                    => $USER['email_2'],
                'userLang'                      => $USER['lang'],
                'theme'                         => $USER['dpath'],
                'planetSort'                    => $USER['planet_sort'],
                'planetOrder'                   => $USER['planet_sort_order'],
                'spycount'                      => $USER['spio_anz'],
                'fleetActions'                  => $USER['settings_fleetactions'],
                'timezone'                      => $USER['timezone'],
                'delete'                        => $USER['db_deaktjava'],
                'showMessageCategory'           => $USER['showMessageCategory'],
                'queueMessages'                 => $USER['hof'],
                'spyMessagesMode'               => $USER['spyMessagesMode'],
                'galaxySpy'                     => $USER['settings_esp'],
                'galaxyBuddyList'               => $USER['settings_bud'],
                'galaxyMissle'                  => $USER['settings_mis'],
                'galaxyMessage'                 => $USER['settings_wri'],
                'blockPM'                       => $USER['settings_blockPM'],
                'userid'                        => $USER['id'],
                'ref_active'                    => Config::get()->ref_active,
                'SELF_URL'                      => PROTOCOL . HTTP_HOST . HTTP_ROOT,
                'colors'                        => $USER['colors'],
                'signalColors'                  => $USER['signalColors'],
                'defaultColors'                 => PlayerUtil::player_colors(),
                'defaultSignalColors'           => PlayerUtil::player_signal_colors(),
                'stb_small_ress'                => $USER['stb_small_ress'],
                'stb_med_ress'                  => $USER['stb_med_ress'],
                'stb_big_ress'                  => $USER['stb_big_ress'],
                'stb_small_time'                => $USER['stb_small_time'],
                'stb_med_time'                  => $USER['stb_med_time'],
                'stb_big_time'                  => $USER['stb_big_time'],
                'stb_enabled'                   => $USER['stb_enabled'],
                'missionPrios'                  => $missionprios,
                'recordsOptIn'                  => $USER['records_optIn'],
                'publish_achievement'           => $USER['publish_achievement'],
                'show_all_buildable_elements'   => $USER['show_all_buildable_elements'],
                'missing_requirements_opacity'  => $USER['missing_requirements_opacity'],
                'missing_resources_opacity'     => $USER['missing_resources_opacity'],
            ]);

            $this->display('page.settings.default.tpl');
        }
    }

    public function toggleVacation()
    {
        $LNG =& Singleton()->LNG;
        $USER =& Singleton()->USER;
        $PLANET =& Singleton()->PLANET;
        if ($USER['urlaubs_modus'] == 1) {
            if ($USER['urlaubs_until'] > TIMESTAMP) {
                $this->printMessage($LNG['op_cant_deactivate_vacation_mode'], [
                    [
                        'label' => $LNG['sys_forward'],
                        'url'   => 'game.php?page=settings',
                    ],
                ]);
            }
            
            $db = Database::get();
            $db->startTransaction();

            $sql = "SELECT id FROM %%USERS%% WHERE universe = :universe AND id = :userID FOR UPDATE;";
            $db->selectSingle($sql, [
                ':universe' => Universe::current(),
                ':userID'   => $USER['id'],
            ]);

            $sql = "SELECT id FROM %%PLANETS%% WHERE universe = :universe AND id_owner = :userID FOR UPDATE;";
            $db->select($sql, [
                ':universe' => Universe::current(),
                ':userID'   => $USER['id'],
            ]);

            PlayerUtil::disable_vmode($USER, $PLANET);
            $db->commit();

            $this->printMessage($LNG['op_options_vacation_deactivated'], [
                [
                    'label' => $LNG['sys_forward'],
                    'url'   => 'game.php?page=settings',
                ],
            ]);
        } else {
            if (!$this->checkVMode()) {
                $this->printMessage($LNG['op_cant_activate_vacation_mode'], [
                    [
                        'label' => $LNG['sys_back'],
                        'url'   => 'game.php?page=settings',
                    ]
                ]);
            }

            PlayerUtil::enable_vmode($USER, $PLANET);

            $text = $LNG['op_options_vacation_activated'];
            $text .= '<br>' . $LNG['op_options_no_other_settings_changed'];
            if (isModuleAvailable(MODULE_VMODE_KICK, $USER['universe'])) {
                $text .= $LNG['tn_vacation_mode_kick'];
            }
            $this->printMessage($text, [
                [
                    'label' => $LNG['sys_forward'],
                    'url'   => 'game.php?page=settings',
                ]
            ]);
        }
    }

    public function toggleDelete()
    {
        $LNG =& Singleton()->LNG;
        $USER =& Singleton()->USER;

        $db = Database::get();
        $db->startTransaction();

        $sql = "SELECT id FROM %%USERS%% WHERE universe = :universe AND id = :userID FOR UPDATE;";
        $db->selectSingle($sql, [
            ':universe' => Universe::current(),
            ':userID'   => $USER['id'],
        ]);

        if ($USER['db_deaktjava'] === 0) {
            $sql = "UPDATE %%USERS%% SET db_deaktjava = :timestamp WHERE id = :userID;";
            $db->update($sql, [
                ':userID'       => $USER['id'],
                ':timestamp'    => TIMESTAMP,
            ]);
            $db->commit();

            $text = $LNG['op_options_deletion_activated'];
            if ($USER['urlaubs_modus'] == 0) { // only in standard settings menu
                $text .= '<br>' . $LNG['op_options_no_other_settings_changed'];
            }
            $this->printMessage($text, [
                [
                    'label' => $LNG['sys_forward'],
                    'url'   => 'game.php?page=settings',
                ]
            ]);
        } else {
            $sql = "UPDATE %%USERS%% SET db_deaktjava = 0 WHERE id = :userID;";
            $db->update($sql, [':userID'   => $USER['id']]);
            $db->commit();

            $text = $LNG['op_options_deletion_deactivated'];
            if ($USER['urlaubs_modus'] == 0) { // only in standard settings menu
                $text .= '<br>' . $LNG['op_options_no_other_settings_changed'];
            }
            $this->printMessage($text, [
                [
                    'label' => $LNG['sys_forward'],
                    'url'   => 'game.php?page=settings',
                ]
            ]);
        }
    }

    private function checkVMode()
    {
        $USER =& Singleton()->USER;
        $PLANET =& Singleton()->PLANET;
        /*
        if (!empty($USER['b_tech']) || !empty($PLANET['b_building']) || !empty($PLANET['b_hangar'])) {
            return false;
        }
        */
        $db = Database::get();

        $sql = "SELECT COUNT(*) as state FROM %%FLEETS%% WHERE `fleet_owner` = :userID FOR UPDATE;";
        $fleets = $db->selectSingle($sql, [':userID' => $USER['id']], 'state');

        if ($fleets != 0) {
            return false;
        }

        $db->startTransaction();
        $sql = "SELECT * FROM %%PLANETS%% WHERE id_owner = :userID AND id != :planetID AND destruyed = 0 FOR UPDATE;";
        $query = $db->select($sql, [
            ':userID'   => $USER['id'],
            ':planetID' => $PLANET['id'],
        ]);

        foreach ($query as $CPLANET) {
            list($USER, $CPLANET) = $this->ecoObj->CalcResource($USER, $CPLANET, true);
            /*
            if (!empty($CPLANET['b_building']) || !empty($CPLANET['b_hangar'])) {
                $db->commit();
                return false;
            }
            */
            unset($CPLANET);
        }

        $db->commit();
        return true;
    }

    public function send()
    {
        $USER =& Singleton()->USER;
        $LNG =& Singleton()->LNG;
        $THEME =& Singleton()->THEME;
        $adminprotection    = HTTP::_GP('adminprotection', 0);
        $discord_id         = HTTP::_GP('discord_id', '');
        $discord_hook       = HTTP::_GP('discord_hook', '');

        $username           = HTTP::_GP('username', $USER['username'], UTF8_SUPPORT);
        $password           = HTTP::_GP('password', '');

        $newpassword        = HTTP::_GP('newpassword', '');
        $newpassword2       = HTTP::_GP('newpassword2', '');

        $email              = HTTP::_GP('email', $USER['email']);

        $timezone           = HTTP::_GP('timezone', '');
        $language           = HTTP::_GP('language', '');

        $planetSort         = HTTP::_GP('planetSort', 0);
        $planetOrder        = HTTP::_GP('planetOrder', 0);

        $theme              = HTTP::_GP('theme', $THEME->getThemeName());

        $showMessageCategory = HTTP::_GP('showMessageCategory', 0);
        $queueMessages      = HTTP::_GP('queueMessages', 0);
        $spyMessagesMode    = HTTP::_GP('spyMessagesMode', 0);
        
        $show_all_buildable_elements = HTTP::_GP('show_all_buildable_elements', 0);
        $missing_requirements_opacity = HTTP::_GP('missing_requirements_opacity', 0);
        $missing_resources_opacity = HTTP::_GP('missing_resources_opacity', 0);

        $spycount           = HTTP::_GP('spycount', 1.0);
        $fleetactions       = HTTP::_GP('fleetactions', 5);

        $galaxySpy          = HTTP::_GP('galaxySpy', 0);
        $galaxyMessage      = HTTP::_GP('galaxyMessage', 0);
        $galaxyBuddyList    = HTTP::_GP('galaxyBuddyList', 0);
        $galaxyMissle       = HTTP::_GP('galaxyMissle', 0);
        $blockPM            = HTTP::_GP('blockPM', 0);

        $colorMission2friend = HTTP::_GP('colorMission2friend', '#ff00ff');

        $colorMission1Own = HTTP::_GP('colorMission1Own', '#66cc33');
        $colorMission2Own = HTTP::_GP('colorMission2Own', '#339966');
        $colorMission3Own = HTTP::_GP('colorMission3Own', '#5bf1c2');
        $colorMission4Own = HTTP::_GP('colorMission4Own', '#cf79de');
        $colorMission5Own = HTTP::_GP('colorMission5Own', '#80a0c0');
        $colorMission6Own = HTTP::_GP('colorMission6Own', '#ffcc66');
        $colorMission7Own = HTTP::_GP('colorMission7Own', '#c1c1c1');
        $colorMission7ReturnOwn = HTTP::_GP('colorMission7OwnReturn', '#cf79de');
        $colorMission8Own = HTTP::_GP('colorMission8Own', '#ceff68');
        $colorMission9Own = HTTP::_GP('colorMission9Own', '#ffff99');
        $colorMission10Own = HTTP::_GP('colorMission10Own', '#ffcc66');
        $colorMission15Own = HTTP::_GP('colorMission15Own', '#5bf1c2');
        $colorMission16Own = HTTP::_GP('colorMission16Own', '#5bf1c2');
        $colorMission17Own = HTTP::_GP('colorMission17Own', '#5bf1c2');
        $colorMissionReturnOwn = HTTP::_GP('colorMissionReturnOwn', '#6e8eea');

        $colorMission1Foreign = HTTP::_GP('colorMission1Foreign', '#ff0000');
        $colorMission2Foreign = HTTP::_GP('colorMission2Foreign', '#aa0000');
        $colorMission3Foreign = HTTP::_GP('colorMission3Foreign', '#00ff00');
        $colorMission4Foreign = HTTP::_GP('colorMission4Foreign', '#ad57bc');
        $colorMission5Foreign = HTTP::_GP('colorMission5Foreign', '#3399cc');
        $colorMission6Foreign = HTTP::_GP('colorMission6Foreign', '#ff6600');
        $colorMission7Foreign = HTTP::_GP('colorMission7Foreign', '#00ff00');
        $colorMission8Foreign = HTTP::_GP('colorMission8Foreign', '#acdd46');
        $colorMission9Foreign = HTTP::_GP('colorMission9Foreign', '#dddd77');
        $colorMission10Foreign = HTTP::_GP('colorMission10Foreign', '#ff6600');
        $colorMission15Foreign = HTTP::_GP('colorMission15Foreign', '#39d0a0');
        $colorMission16Foreign = HTTP::_GP('colorMission16Foreign', '#39d0a0');
        $colorMission17Foreign = HTTP::_GP('colorMission17Foreign', '#39d0a0');
        $colorMissionReturnForeign = HTTP::_GP('colorMissionReturnForeign', '#6e8eea');

        $colorStaticTimer = HTTP::_GP('colorStaticTimer', '#ffff00');
        $colorPositive = HTTP::_GP('colorPositive', '#00ff00');
        $colorNegative = HTTP::_GP('colorNegative', '#ff0000');
        $colorNeutral = HTTP::_GP('colorNeutral', '#ffd600');

        $stb_small_ress     = HTTP::_GP('stb_small_ress', 500);
        $stb_med_ress       = HTTP::_GP('stb_med_ress', 3000);
        $stb_big_ress       = HTTP::_GP('stb_big_ress', 7000);
        $stb_small_time     = HTTP::_GP('stb_small_time', 1);
        $stb_med_time       = HTTP::_GP('stb_med_time', 2);
        $stb_big_time       = HTTP::_GP('stb_big_time', 3);
        $stb_enabled        = HTTP::_GP('stb_enabled', 0);

        $prio1 = HTTP::_GP('type_mission_1', 1);
        $prio2 = HTTP::_GP('type_mission_2', 2);
        $prio3 = HTTP::_GP('type_mission_3', 0);
        $prio4 = HTTP::_GP('type_mission_4', 3);
        $prio5 = HTTP::_GP('type_mission_5', 4);
        $prio6 = HTTP::_GP('type_mission_6', 5);
        $prio7 = HTTP::_GP('type_mission_7', 6);
        $prio8 = HTTP::_GP('type_mission_8', 7);
        $prio9 = HTTP::_GP('type_mission_9', 8);
        $prio17 =HTTP::_GP('type_mission_17', 9);
        $recordsOptIn = HTTP::_GP('recordsOptIn', 0);

        $publish_achievement = HTTP::_GP('publish_achievement', 0);

        // Vertify

        if ($USER['authlevel'] != AUTH_USR) {
            $adminprotection = ($adminprotection == 1) ? $USER['authlevel'] : 0;
        } else {
            $adminprotection = 0;
            $discord_id = '';
            $discord_hook = '';
        }

        $spycount           = min(max(round($spycount), 1), 4294967295);
        $fleetactions       = min(max($fleetactions, 1), 99);

        $language = array_key_exists($language, $LNG->getAllowedLangs(false)) ? $language : $LNG->getLanguage();
        $theme = array_key_exists($theme, Theme::getAvalibleSkins()) ? $theme : $THEME->getThemeName();

        $db = Database::get();
        $db->startTransaction();

        $sql = "SELECT * FROM %%USERS%% WHERE universe = :universe AND id = :id FOR UPDATE;";
        $db->select($sql, [
            ':universe' => Universe::current(),
            ':id' => $USER['id'],
        ]);
        $sql = "SELECT * FROM %%PLANETS%% WHERE universe = :universe AND id_owner = :id FOR UPDATE;";
        $db->select($sql, [
            ':universe' => Universe::current(),
            ':id' => $USER['id'],
        ]);

        if (!empty($username) && $USER['username'] != $username) {
            if (!PlayerUtil::isNameValid($username)) {
                $this->printMessage($LNG['op_user_name_no_alphanumeric'], [
                    [
                        'label' => $LNG['sys_back'],
                        'url'   => 'game.php?page=settings',
                    ],
                ]);
            } elseif ($USER['uctime'] >= TIMESTAMP - USERNAME_CHANGETIME) {
                $this->printMessage($LNG['op_change_name_pro_week'], [
                    [
                        'label' => $LNG['sys_back'],
                        'url'   => 'game.php?page=settings',
                    ],
                ]);
            } else {
                $sql = "SELECT
					(SELECT COUNT(*) FROM %%USERS%% WHERE universe = :universe AND username = :username) +
					(SELECT COUNT(*) FROM %%USERS_VALID%% WHERE universe = :universe AND username = :username)
				AS count";
                $Count = $db->selectSingle($sql, [
                    ':universe' => Universe::current(),
                    ':username' => $username,
                ], 'count');

                if (!empty($Count)) {
                    $this->printMessage(sprintf($LNG['op_change_name_exist'], $username), [
                        [
                            'label' => $LNG['sys_back'],
                            'url'   => 'game.php?page=settings',
                        ],
                    ]);
                } else {
                    $sql = "UPDATE %%USERS%% SET username = :username, uctime = :timestamp WHERE id = :userID;";
                    $db->update($sql, [
                        ':username' => $username,
                        ':userID'   => $USER['id'],
                        ':timestamp' => TIMESTAMP,
                    ]);

                    Session::load()->delete();
                }
            }
        }

        if (
            !empty($newpassword)
            && !empty($password)
            && password_verify($password, $USER['password'])
            && $newpassword == $newpassword2
        ) {
            $newpass     = PlayerUtil::cryptPassword($newpassword);
            $sql = "UPDATE %%USERS%% SET password = :newpass WHERE id = :userID;";
            $db->update($sql, [
                ':newpass'  => $newpass,
                ':userID'   => $USER['id'],
            ]);
            Session::load()->delete();
        }

        if (!empty($email) && $email != $USER['email']) {
            if (!password_verify($password, $USER['password'])) {
                $this->printMessage($LNG['op_need_pass_mail'], [
                    [
                        'label' => $LNG['sys_back'],
                        'url'   => 'game.php?page=settings',
                    ],
                ]);
            } elseif (!ValidateAddress($email)) {
                $this->printMessage($LNG['op_not_vaild_mail'], [
                    [
                        'label' => $LNG['sys_back'],
                        'url'   => 'game.php?page=settings',
                    ],
                ]);
            } else {
                $sql = "SELECT"
                    . " (SELECT COUNT(*) FROM %%USERS%% WHERE id != :userID AND universe = :universe AND (email ="
                    . " :email OR email_2 = :email)) + (SELECT COUNT(*) FROM %%USERS_VALID%% WHERE universe = :universe"
                    . " AND email = :email) as count";
                $Count = $db->selectSingle($sql, [
                    ':universe' => Universe::current(),
                    ':userID'   => $USER['id'],
                    ':email'    => $email,
                ], 'count');

                if (!empty($Count)) {
                    $this->printMessage(sprintf($LNG['op_change_mail_exist'], $email), [
                        [
                            'label' => $LNG['sys_back'],
                            'url'   => 'game.php?page=settings',
                        ],
                    ]);
                } else {
                    $sql = "UPDATE %%USERS%% SET email = :email, setmail = :time WHERE id = :userID;";
                    $db->update($sql, [
                        ':email'    => $email,
                        ':time'     => (TIMESTAMP + 604800),
                        ':userID'   => $USER['id'],
                    ]);
                }
            }
        }

        $sql =  "UPDATE %%USERS%% SET
		dpath					        = :theme,
		timezone				        = :timezone,
		planet_sort				        = :planetSort,
		planet_sort_order		        = :planetOrder,
		spio_anz				        = :spyCount,
		settings_fleetactions	        = :fleetActions,
		settings_esp			        = :galaxySpy,
		settings_wri			        = :galaxyMessage,
		settings_bud			        = :galaxyBuddyList,
		settings_mis			        = :galaxyMissle,
		settings_blockPM		        = :blockPM,
		authattack				        = :adminProtection,
		discord_id				        = :discord_id,
		discord_hook				    = :discord_hook,
		lang					        = :language,
        showMessageCategory             = :showMessageCategory,
		hof						        = :queueMessages,
		spyMessagesMode			        = :spyMessagesMode,
        show_all_buildable_elements     = :show_all_buildable_elements,
        missing_requirements_opacity    = :missing_requirements_opacity,
        missing_resources_opacity       = :missing_resources_opacity,
        colorMission2friend             = :colorMission2friend,
        colorMission1Own                = :colorMission1Own,
        colorMission2Own                = :colorMission2Own,
        colorMission3Own                = :colorMission3Own,
        colorMission4Own                = :colorMission4Own,
        colorMission5Own                = :colorMission5Own,
        colorMission6Own                = :colorMission6Own,
        colorMission7Own                = :colorMission7Own,
        colorMission7OwnReturn          = :colorMission7OwnReturn,
        colorMission8Own                = :colorMission8Own,
        colorMission9Own                = :colorMission9Own,
        colorMission10Own               = :colorMission10Own,
        colorMission15Own               = :colorMission15Own,
        colorMission16Own               = :colorMission16Own,
        colorMission17Own               = :colorMission17Own,
        colorMissionReturnOwn           = :colorMissionReturnOwn,
        colorMission1Foreign            = :colorMission1Foreign,
        colorMission2Foreign            = :colorMission2Foreign,
        colorMission3Foreign            = :colorMission3Foreign,
        colorMission4Foreign            = :colorMission4Foreign,
        colorMission5Foreign            = :colorMission5Foreign,
        colorMission6Foreign            = :colorMission6Foreign,
        colorMission7Foreign            = :colorMission7Foreign,
        colorMission8Foreign            = :colorMission8Foreign,
        colorMission9Foreign            = :colorMission9Foreign,
        colorMission10Foreign           = :colorMission10Foreign,
        colorMission15Foreign           = :colorMission15Foreign,
        colorMission16Foreign           = :colorMission16Foreign,
        colorMission17Foreign           = :colorMission17Foreign,
        colorMissionReturnForeign       = :colorMissionReturnForeign,
        colorStaticTimer                = :colorStaticTimer,
        colorPositive                   = :colorPositive,
        colorNegative                   = :colorNegative,
        colorNeutral                    = :colorNeutral,
        prioMission1                    = :prioMission1,
        prioMission2                    = :prioMission2,
        prioMission3                    = :prioMission3,
        prioMission4                    = :prioMission4,
        prioMission5                    = :prioMission5,
        prioMission6                    = :prioMission6,
        prioMission7                    = :prioMission7,
        prioMission8                    = :prioMission8,
        prioMission9                    = :prioMission9,
        prioMission17                   = :prioMission17,
        records_optIn                   = :records_optIn,
        stb_small_ress                  = :stb_small_ress,
        stb_med_ress                    = :stb_med_ress,
        stb_big_ress                    = :stb_big_ress,
        stb_small_time                  = :stb_small_time,
        stb_med_time                    = :stb_med_time,
        stb_big_time                    = :stb_big_time,
        stb_enabled                     = :stb_enabled,
        publish_achievement             = :publish_achievement
		WHERE id = :userID;";
        $db->update($sql, [
            ':theme'                        => $theme,
            ':timezone'                     => $timezone,
            ':planetSort'                   => $planetSort,
            ':planetOrder'                  => $planetOrder,
            ':spyCount'                     => $spycount,
            ':fleetActions'                 => $fleetactions,
            ':galaxySpy'                    => $galaxySpy,
            ':galaxyMessage'                => $galaxyMessage,
            ':galaxyBuddyList'              => $galaxyBuddyList,
            ':galaxyMissle'                 => $galaxyMissle,
            ':blockPM'                      => $blockPM,
            ':adminProtection'              => $adminprotection,
            ':discord_id'                   => $discord_id,
            ':discord_hook'                 => $discord_hook,
            ':language'                     => $language,
            ':showMessageCategory'          => $showMessageCategory,
            ':queueMessages'                => $queueMessages,
            ':spyMessagesMode'              => $spyMessagesMode,
            ':show_all_buildable_elements'  => $show_all_buildable_elements,
            ':missing_requirements_opacity' => $missing_requirements_opacity,
            ':missing_resources_opacity'    => $missing_resources_opacity,
            ':userID'                       => $USER['id'],
            ':colorMission2friend'          => $colorMission2friend,
            ':colorMission1Own'             => $colorMission1Own,
            ':colorMission2Own'             => $colorMission2Own,
            ':colorMission3Own'             => $colorMission3Own,
            ':colorMission4Own'             => $colorMission4Own,
            ':colorMission5Own'             => $colorMission5Own,
            ':colorMission6Own'             => $colorMission6Own,
            ':colorMission7Own'             => $colorMission7Own,
            ':colorMission7OwnReturn'       => $colorMission7ReturnOwn,
            ':colorMission8Own'             => $colorMission8Own,
            ':colorMission9Own'             => $colorMission9Own,
            ':colorMission10Own'            => $colorMission10Own,
            ':colorMission15Own'            => $colorMission15Own,
            ':colorMission16Own'            => $colorMission16Own,
            ':colorMission17Own'            => $colorMission17Own,
            ':colorMissionReturnOwn'        => $colorMissionReturnOwn,
            ':colorMission1Foreign'         => $colorMission1Foreign,
            ':colorMission2Foreign'         => $colorMission2Foreign,
            ':colorMission3Foreign'         => $colorMission3Foreign,
            ':colorMission4Foreign'         => $colorMission4Foreign,
            ':colorMission5Foreign'         => $colorMission5Foreign,
            ':colorMission6Foreign'         => $colorMission6Foreign,
            ':colorMission7Foreign'         => $colorMission7Foreign,
            ':colorMission8Foreign'         => $colorMission8Foreign,
            ':colorMission9Foreign'         => $colorMission9Foreign,
            ':colorMission10Foreign'        => $colorMission10Foreign,
            ':colorMission15Foreign'        => $colorMission15Foreign,
            ':colorMission16Foreign'        => $colorMission16Foreign,
            ':colorMission17Foreign'        => $colorMission17Foreign,
            ':colorMissionReturnForeign'    => $colorMissionReturnForeign,
            ':colorStaticTimer'             => $colorStaticTimer,
            ':colorPositive'                => $colorPositive,
            ':colorNegative'                => $colorNegative,
            ':colorNeutral'                 => $colorNeutral,
            ':prioMission1'                 => $prio1,
            ':prioMission2'                 => $prio2,
            ':prioMission3'                 => $prio3,
            ':prioMission4'                 => $prio4,
            ':prioMission5'                 => $prio5,
            ':prioMission6'                 => $prio6,
            ':prioMission7'                 => $prio7,
            ':prioMission8'                 => $prio8,
            ':prioMission9'                 => $prio9,
            ':prioMission17'                => $prio17,
            ':records_optIn'                => $recordsOptIn,
            ':stb_small_ress'               => $stb_small_ress,
            ':stb_med_ress'                 => $stb_med_ress,
            ':stb_big_ress'                 => $stb_big_ress,
            ':stb_small_time'               => $stb_small_time,
            ':stb_med_time'                 => $stb_med_time,
            ':stb_big_time'                 => $stb_big_time,
            ':stb_enabled'                  => $stb_enabled,
            ':publish_achievement'          => $publish_achievement
        ]);

        $db->commit();

        require_once 'includes/classes/achievements/MiscAchievement.class.php';
        MiscAchievement::checkSettingsAchievements($USER['id'], $recordsOptIn);

        $this->printMessage($LNG['op_options_changed'], [
            [
                'label' => $LNG['sys_forward'],
                'url'   => 'game.php?page=settings',
            ]
        ]);
    }
}
