<?php
try {
    if (file_exists('../vendor/autoload.php')) {
        require("../vendor/autoload.php");
    } else {
        die('Hello, it looks like you did not run: "<code>composer install --no-dev --optimize-autoloader</code>". Please run that and refresh the page');
    }
} catch (Exception $e) {
    die('Hello, it looks like you did not run: <code>composer install --no-dev --optimize-autoloader</code> Please run that and refresh');
}

use MythicalShare\App;
use MythicalShare\Handlers\ConfigHandler;

/**
 * Check if the client area has access to the directory!
 */
if (!is_writable(__DIR__)) {
    App::Crash("We have no access to our client directory. Open the terminal and run: chown -R www-data:www-data /var/www/mythicalshare/*");
    die();
}

/**
 * Check if app is running on https
 */
if (!App::isHTTPS()) {
    App::Crash("We are sorry, but the dash can only run on HTTPS, not HTTP.");
    die();
}

/**
 * System to check for debug mode!
 */
if (!ConfigHandler::get("app", "debug") == null && ConfigHandler::get("app", "debug" == "true")) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else if (!ConfigHandler::get("app", "debug") == null && ConfigHandler::get("app", "debug" == "true")) {
    return null;
} else {
    App::Crash("We are sorry, but your configuration file is missing the required values!");
    die();
}

/**
 * System to load the langauges
 */

$lang = App::getLang();

/**
 * Check if encryption works on this system
 * 
 */
if (!extension_loaded('openssl')) {
    App::Crash("The OpenSSL extension is not installed. Please install it for secure encryption.");
    die();
}

$minKeyLength = 32;
if (strlen(ConfigHandler::get("app", "key")) < $minKeyLength) {
    App::Crash("The encryption key is too short. Please use a key of at least ' . $minKeyLength . ' characters for better security.");
    die();
}

/**
 * MythicalClient Router System
 * 
 * This is the route system for mythicalclient that includes all the routes to the app!
 */
$router = new \Router\Router();
$routesDirectory = __DIR__ . '/../routes/';
$phpFiles = glob($routesDirectory . '*.php');
foreach ($phpFiles as $phpFile) {
    include $phpFile;
}


$router->add("/(.*)", function () {
    require("../api/errors/404.php");
});

try {

    $router->route();
} catch (Exception $e) {
    App::Crash("Failed to start the app route system: " . $e->getMessage());
    die();
}
?>