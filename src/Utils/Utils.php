<?php

namespace CleantalkHubGitKeeper\Utils;

class Utils
{
    /**
     * @param string $key
     * @return mixed
     * @throws \Exception
     */
    public static function getEnv($key)
    {
        if ( ! isset($_ENV[$key]) ) {
            // Check the config file values against of config example and pass the live data into it.
            throw new \Exception("Env variable `{$key}` not found.", 501);
        }

        return $_ENV[$key];
    }
}
