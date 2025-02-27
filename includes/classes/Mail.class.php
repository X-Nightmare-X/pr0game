<?php

use PHPMailer\PHPMailer\PHPMailer;

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

class Mail
{
    public static function send($mailTarget, $mailTargetName, $mailSubject, $mailContent)
    {
        $mail	= self::getMailObject();

        $mail->CharSet	= 'UTF-8';
        $mail->Subject	= $mailSubject;
        $mail->Body		= $mailContent;
        $mail->AddAddress($mailTarget, $mailTargetName);
        try {
            $mail->Send();
        } catch (Exception $e) {
            error_log($mail->ErrorInfo);
        }
    }

    public static function multiSend($mailTargets, $mailSubject, $mailContent = null)
    {
        $mail	= self::getMailObject();

        $mail->Subject	= $mailSubject;

        foreach ($mailTargets as $address => $data) {
            $content = isset($data['body']) ? $data['body'] : $mailContent;

            $mail->AddAddress($address, $data['username']);
            $mail->MsgHTML($content);
            $mail->Send();
            $mail->ClearAddresses();
        }
    }

    private static function getMailObject()
    {
        $mail               = new PHPMailer(true);

        $config				= Config::get();

        if ($config->mail_use == 2) {
            $mail->IsSMTP();
            $mail->SMTPSecure       = $config->smtp_ssl;
            $mail->Host             = $config->smtp_host;
            $mail->Port             = $config->smtp_port;

            if ($config->smtp_user != '') {
                $mail->SMTPAuth         = true;
                $mail->Username         = $config->smtp_user;
                $mail->Password         = $config->smtp_pass;
            }
        } elseif ($config->mail_use == 0) {
            $mail->IsMail();
        } else {
            throw new Exception("sendmail is deprecated, use SMTP instead!");
        }

        $mailFromAddress	= $config->smtp_sendmail;
        $mailFromName		= $config->game_name;

        $mail->CharSet	= 'UTF-8';
        $mail->SetFrom($mailFromAddress, $mailFromName);

        return $mail;
    }
}
