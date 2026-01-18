<?php

use App\Templating\ViewMiddleware;
require_once dirname(__DIR__, 1) . "/vendor/autoload.php";

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(dirname(__DIR__, 1));
$dotenv->safeLoad();

if ($_ENV['APP_ENV'] !== 'production') {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
}

// start a session  if there is
// none.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once dirname(__DIR__) . '/app/helpers.php';

/**
 * Load application routes
 */
require dirname(__DIR__) . '/app/routes.php';

router()
    ->serveStatic('/static/css/*', dirname(__DIR__) . "/static/css");

router()
    ->serveStatic("/static/js/*", dirname(__DIR__) . "/static/js");

router()
    ->middleware(['GET'], '**/*', new ViewMiddleware(dirname(__DIR__) . '/app/Views'));