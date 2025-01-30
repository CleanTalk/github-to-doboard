<?php

/**
 * This file must be unique named and placed in the root of the web server.
 *
 * This file contains main logic check incoming data from GitHub.
 */

use CleantalkHubGitKeeper\HubGitKeeper;
use CleantalkHubGitKeeper\Utils\Logger;

// @ToDo Need to implement additional check incoming data by secret key

$event = $_SERVER['HTTP_X_GITHUB_EVENT'] ?? null;
$content = file_get_contents( "php://input" );

if( ! $content || ! $event ){
    die(403);
}

try {
    $content = json_decode($content, true, 512, JSON_THROW_ON_ERROR);
} catch (Exception $e) {
    http_response_code(403);
    exit('Data decoding error: ' . $e->getMessage());
}

global $app_dir;
$app_dir = dirname(__DIR__);

if ( ! file_exists($app_dir . '/vendor/autoload.php') ) {
    http_response_code(501);
    exit('Autoload file not exists. Are you sure you run `composer install`?');
}

require $app_dir . '/vendor/autoload.php';

try {
    $dotenv = Dotenv\Dotenv::createImmutable($app_dir, '.config');
    $dotenv->load();
    $app = new HubGitKeeper($event, $content);
    $response = $app->run();
    $response->send();
} catch (Exception $e) {
    Logger::log($e->getMessage());
    Logger::log($e->getTraceAsString());
    http_response_code($e->getCode());
    exit($e->getMessage());
}
