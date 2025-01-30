<?php

namespace CleantalkHubGitKeeper\Utils;

class Logger
{
    /**
     * @param string $message
     * @return void
     */
    public static function log($message)
    {
        try {
            if ( Utils::getEnv('DEBUG') ) {
                $log = date('Y-m-d H:i:s') . ' ' . $message . PHP_EOL;
                file_put_contents(dirname(__DIR__, 2) . '/log.txt', $log, FILE_APPEND);
            }
        } catch (\Exception $e) {
            // Nothing. Just catch an exception here.
        }
    }
}
