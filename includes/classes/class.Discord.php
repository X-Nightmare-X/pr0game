<?php

class Discord
{
    public static function sendException($exception): void
    {
        $config = Config::get();

        if (!isset($config->discord_exceptions_hook) || empty($config->discord_exceptions_hook)) {
            return;
        }

        $USER =& Singleton()->USER;
        if (isset($USER) && is_array($USER) && !empty($USER['id']) && !empty($USER['username'])) {
            $ErrSource = $USER['id'];
            $ErrName = $USER['username'];
        } else {
            $ErrSource = 1;
            $ErrName = 'System';
        }

        self::send($config->discord_exceptions_hook, 
            '**' . $exception->getMessage() . '**' . PHP_EOL .
            '```' .
            'User-Info: ' . $ErrSource . ' ' . $ErrName . PHP_EOL .
            'File: ' . $exception->getFile() . PHP_EOL .
            'Line: ' . $exception->getLine() . PHP_EOL .
            'URL: ' . PROTOCOL . HTTP_HOST . $_SERVER['REQUEST_URI'] . PHP_EOL .
            'Debug Backtrace: ' . PHP_EOL . htmlspecialchars($exception->getTraceAsString()) .
            '```');
    }

    public static function sendLog($title, ?array $data = null, ?Exception $exception = null)
    {
        $config = Config::get();

        if (!isset($config->discord_logs_hook) || empty($config->discord_logs_hook)) {
            return;
        }

        $data = $data ?? (array)$exception;
        $message = '**' . $title . '**' . PHP_EOL . '```';
        foreach ($data ?? [] as $key => $row) {
            if (is_array($row)) {
                $message .= $key . ': ' . json_encode($row) . PHP_EOL;
            } else {
                $message .= $key . ': ' . $row . PHP_EOL;
            }
        }
        $message .= '```';

        self::send($config->discord_logs_hook, $message);
    }

    public static function sendMessage(String $webHookUrl, String $title, array $content)
    {
        if (!isset($webHookUrl) || empty($webHookUrl) || empty($title) || empty($content)) {
            return;
        }

        /**
         * You can grab the discord IDs by turning on developer mode (settings -> (App-Settings) Advanced), and then right clicking on the relevant channel/user and using "copy ID"
         * <#channel_id>
         * <@user_id>
         * <@&role_id> \@role to get ID
         * <:emojiName:emojiId> \:customEmojiName: to get ID
         */

        $message = '**' . $title . '**' . PHP_EOL . '```';
        foreach ($content ?? [] as $key => $row) {
            if (is_array($row)) {
                $message .= $key . ': ' . json_encode($row) . PHP_EOL;
            } else {
                $message .= $key . ': ' . $row . PHP_EOL;
            }
        }
        $message .= '```';

        self::send($webHookUrl, $message);
    }

    private static function send($webHookUrl, $message)
    {
        $json_data = json_encode([
            'content' => $message,
            'username' => 'pr0game',
            'tts' => false,
            'embeds' => []

        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);


        $ch = curl_init($webHookUrl);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-type: application/json']);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_exec($ch);
        curl_close($ch);
    }
}
