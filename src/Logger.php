<?php

namespace App;

class Logger
{
    private static string $logFile = __DIR__ . '/../logs/app.log';

    public static function info(string $message): void
    {
        self::writeLog('INFO', $message);
    }

    public static function error(string $message): void
    {
        self::writeLog('ERROR', $message);
    }

    private static function writeLog(string $level, string $message): void
    {
        $time = date('Y-m-d H:i:s');
        $entry = "[$time][$level] $message\n";
        file_put_contents(self::$logFile, $entry, FILE_APPEND);
    }
}
