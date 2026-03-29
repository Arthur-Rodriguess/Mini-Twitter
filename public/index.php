<?php

use MiniTwitter\Core\Response;

session_start();

require_once __DIR__ . "/../vendor/autoload.php";

$router = require_once __DIR__ . "/../routes.php";

try {
    $router->run();
} catch (Exception $e) {
    $code = $e->getCode() ?: 500;
    Response::json(["error" => $e->getMessage()], $code);
}