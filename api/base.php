<?php 
use MythicalShare\Handlers\ConfigHandler;
header('Content-type: application/json');
ini_set("display_errors", 0);
ini_set("display_startup_errors", 0);
if (!is_writable(__DIR__)) {
    http_response_code(500);
    $rsp = array(
        "code" => 500,
        "error" => "The server is not ready to handle the request.",
        "message" => "We have no write permission for our home directory. Please update the permission by executing this in the server shell: chown -R www-data:www-data /var/www/mythicalshare/ && chown -R www-data:www-data /var/www/mythicalshare/*"
    );
    die(json_encode($rsp, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
}
if (ConfigHandler::get("app","maintenance") == "true") { 
    http_response_code(500);
    $rsp = array(
        "code" => 500,
        "error" => "The server is not ready to handle the request.",
        "message" => "We are so sorry but our api is down for maintenance"
    );
    die(json_encode($rsp, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
}

?>