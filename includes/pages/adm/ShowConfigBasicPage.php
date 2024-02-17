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

function ShowConfigBasicPage()
{
    $LNG =& Singleton()->LNG;
    $USER =& Singleton()->USER;
    $config = Config::get(Universe::getEmulated());
    if (!empty($_POST)) {
        $config_before = [
            'game_name'                 => $config->game_name,
            'timezone'                  => $config->timezone,
            'dst'                       => $config->dst,
            'forum_url'                 => $config->forum_url,
            'git_issues_link'           => $config->git_issues_link,
            'ttf_file'                  => $config->ttf_file,

            'discord_logs_hook'         => $config->discord_logs_hook,
            'discord_exceptions_hook'   => $config->discord_exceptions_hook,
            'discord_tickets_hook'      => $config->discord_tickets_hook,

            'mail_active'               => $config->mail_active,
            'smtp_sendmail'             => $config->smtp_sendmail,
            'mail_use'                  => $config->mail_use,
            'smail_path'                => $config->smail_path,
            'smtp_ssl'                  => $config->smtp_ssl,
            'smtp_host'                 => $config->smtp_host,
            'smtp_port'                 => $config->smtp_port,
            'smtp_user'                 => $config->smtp_user,
            'smtp_pass'                 => $config->smtp_pass,

            'del_oldstuff'              => $config->del_oldstuff,
            'del_user_manually'         => $config->del_user_manually,
            'user_valid'                => $config->user_valid,
            'sendmail_inactive'         => $config->sendmail_inactive,
            'del_user_sendmail'         => $config->del_user_sendmail,

            'message_delete_behavior'   => $config->message_delete_behavior,
            'message_delete_days'       => $config->message_delete_days,

            'google_analytics_key'      => $config->google_analytics_key,
            'use_google_analytics'      => $config->use_google_analytics,

            'recaptcha_priv_key'        => $config->recaptcha_priv_key,
            'recaptcha_pub_key'         => $config->recaptcha_pub_key,
            
            'disclamer_address'         => $config->disclamer_address,
            'disclamer_phone'           => $config->disclamer_phone,
            'disclamer_mail'            => $config->disclamer_mail,
            'disclamer_notice'          => $config->disclamer_notice,
            
            'stat_settings'             => $config->stat_settings,
            'stat'                      => $config->stat,
            'stat_level'                => $config->stat_level,
        ];

        $game_name                  = HTTP::_GP('game_name', '', true);
        $timezone                   = HTTP::_GP('timezone', '');
        $dst                        = HTTP::_GP('dst', 2);
        $forum_url                  = HTTP::_GP('forum_url', '', true);
        $git_issues_link            = HTTP::_GP('git_issues_link', '', true);
        $ttf_file                   = HTTP::_GP('ttf_file', '');

        $discord_logs_hook          = HTTP::_GP('discord_logs_hook', '');
        $discord_exceptions_hook    = HTTP::_GP('discord_exceptions_hook', '');
        $discord_tickets_hook       = HTTP::_GP('discord_tickets_hook', '');
        
        $mail_active                = isset($_POST['mail_active']) && $_POST['mail_active'] == 'on' ? 1 : 0;
        $smtp_sendmail              = HTTP::_GP('smtp_sendmail', '', true);
        $mail_use                   = HTTP::_GP('mail_use', 0);
        $smail_path                 = HTTP::_GP('smail_path', '');
        $smtp_ssl                   = HTTP::_GP('smtp_ssl', '');
        $smtp_host                  = HTTP::_GP('smtp_host', '', true);
        $smtp_port                  = HTTP::_GP('smtp_port', 0);
        $smtp_user                  = HTTP::_GP('smtp_user', '', true);
        $smtp_pass                  = HTTP::_GP('smtp_pass', '', true);

        $del_oldstuff               = HTTP::_GP('del_oldstuff', 0);
        $del_user_manually          = HTTP::_GP('del_user_manually', 0);
        $user_valid                 = isset($_POST['user_valid']) && $_POST['user_valid'] == 'on' ? 1 : 0;
        $sendmail_inactive          = isset($_POST['sendmail_inactive']) && $_POST['sendmail_inactive'] == 'on' ? 1 : 0;
        $del_user_sendmail          = HTTP::_GP('del_user_sendmail', 0);
        
        $message_delete_behavior    = HTTP::_GP('message_delete_behavior', 0);
        $message_delete_days        = HTTP::_GP('message_delete_days', 0);

        $use_google_analytics       = isset($_POST['use_google_analytics']) && $_POST['use_google_analytics'] == 'on' ? 1 : 0;
        $google_analytics_key       = HTTP::_GP('google_analytics_key', '', true);
        
        $recaptcha_priv_key         = HTTP::_GP('recaptcha_priv_key', '', true);
        $recaptcha_pub_key          = HTTP::_GP('recaptcha_pub_key', '', true);

        $disclaimerAddress          = HTTP::_GP('disclaimerAddress', '', true);
        $disclaimerPhone            = HTTP::_GP('disclaimerPhone', '', true);
        $disclaimerMail             = HTTP::_GP('disclaimerMail', '', true);
        $disclaimerNotice           = HTTP::_GP('disclaimerNotice', '', true);

        $stat_settings              = HTTP::_GP('stat_settings', 0);
        $stat                       = HTTP::_GP('stat', 0);
        $stat_level                 = HTTP::_GP('stat_level', 0);

        $config_after = [
            'game_name'                 => $game_name,
            'timezone'                  => $timezone,
            'dst'                       => $dst,
            'forum_url'                 => $forum_url,
            'git_issues_link'           => $git_issues_link,
            'ttf_file'                  => $ttf_file,

            'discord_logs_hook'         => $discord_logs_hook,
            'discord_exceptions_hook'   => $discord_exceptions_hook,
            'discord_tickets_hook'      => $discord_tickets_hook,
            
            'mail_active'               => $mail_active,
            'smtp_sendmail'             => $smtp_sendmail,
            'mail_use'                  => $mail_use,
            'smail_path'                => $smail_path,
            'smtp_ssl'                  => $smtp_ssl,
            'smtp_host'                 => $smtp_host,
            'smtp_port'                 => $smtp_port,
            'smtp_user'                 => $smtp_user,
            'smtp_pass'                 => $smtp_pass,

            'del_oldstuff'              => $del_oldstuff,
            'del_user_manually'         => $del_user_manually,
            'user_valid'                => $user_valid,
            'sendmail_inactive'         => $sendmail_inactive,
            'del_user_sendmail'         => $del_user_sendmail,

            'message_delete_behavior'   => $message_delete_behavior,
            'message_delete_days'       => $message_delete_days,

            'google_analytics_key'      => $google_analytics_key,
            'use_google_analytics'      => $use_google_analytics,
            
            'recaptcha_priv_key'        => $recaptcha_priv_key,
            'recaptcha_pub_key'         => $recaptcha_pub_key,

            'disclamer_address'         => $disclaimerAddress,
            'disclamer_phone'           => $disclaimerPhone,
            'disclamer_mail'            => $disclaimerMail,
            'disclamer_notice'          => $disclaimerNotice,
            
            'stat_settings'             => $stat_settings,
            'stat'                      => $stat,
            'stat_level'                => $stat_level,
        ];

        foreach ($config_after as $key => $value) {
            $config->$key	= $value;
        }
        $config->saveGlobalKeys();

        $LOG = new Log(3);
        $LOG->target = 0;
        $LOG->old = $config_before;
        $LOG->new = $config_after;
        $LOG->save();
    }

    $TimeZones		= get_timezone_selector();

    $template	= new template();
    $template->loadscript('../base/jquery.autosize-min.js');
    $template->execscript('$(\'textarea\').autosize();');

    $template->assign_vars([
        'game_name'                 => $config->game_name,
        'timezone'                  => $config->timezone,
        'dst'                       => $config->dst,
        'forum_url'                 => $config->forum_url,
        'git_issues_link'           => $config->git_issues_link,
        'ttf_file'                  => $config->ttf_file,

        'discord_logs_hook'         => $config->discord_logs_hook,
        'discord_exceptions_hook'   => $config->discord_exceptions_hook,
        'discord_tickets_hook'      => $config->discord_tickets_hook,
        
        'mail_active'               => $config->mail_active,
        'smtp_sendmail'             => $config->smtp_sendmail,
        'mail_use'                  => $config->mail_use,
        'smail_path'                => $config->smail_path,
        'smtp_ssl'                  => $config->smtp_ssl,
        'smtp_host'                 => $config->smtp_host,
        'smtp_port'                 => $config->smtp_port,
        'smtp_user'                 => $config->smtp_user,
        'smtp_pass'                 => $config->smtp_pass,

        'del_oldstuff'              => $config->del_oldstuff,
        'del_user_manually'         => $config->del_user_manually,
        'user_valid'                => $config->user_valid,
        'sendmail_inactive'         => $config->sendmail_inactive,
        'del_user_sendmail'         => $config->del_user_sendmail,

        'message_delete_behavior'   => $config->message_delete_behavior,
        'message_delete_days'       => $config->message_delete_days,

        'google_analytics_key'      => $config->google_analytics_key,
        'use_google_analytics'      => $config->use_google_analytics,
        
        'recaptcha_priv_key'        => $config->recaptcha_priv_key,
        'recaptcha_pub_key'         => $config->recaptcha_pub_key,

        'disclaimerAddress'         => $config->disclamer_address,
        'disclaimerPhone'           => $config->disclamer_phone,
        'disclaimerMail'            => $config->disclamer_mail,
        'disclaimerNotice'          => $config->disclamer_notice,
        
        'stat_level'                => $config->stat_level,
        'stat'                      => $config->stat,
        'stat_settings'             => $config->stat_settings,

        'signalColors'              => $USER['signalColors'],
        'Selector'                  => [
            'timezone' => $TimeZones,
            'dst' => [0 => $LNG['op_dst_mode_sel'][0], 1 => $LNG['op_dst_mode_sel'][1], 2 => $LNG['op_dst_mode_sel'][2]], // 0 = no, 1 = yes, 2 = auto
            'mail' => [0 => $LNG['se_mail_sel_0'], 2 => $LNG['se_mail_sel_2']], // 1 = sendmail, is deprecated, use SMTP (2) instead
            'encry' => ['' => $LNG['se_smtp_ssl_1'], 'ssl' => $LNG['se_smtp_ssl_2'], 'tls' => $LNG['se_smtp_ssl_3']],
            'message_delete_behavior' => [0 => $LNG['se_message_delete_behavior_0'], 1 => $LNG['se_message_delete_behavior_1']],
            'stat' => [1 => $LNG['cs_yes'], 2 => $LNG['cs_no_view'], 0 => $LNG['cs_no']],
        ],
    ]);

    $template->show('ConfigBasicBody.tpl');
}
