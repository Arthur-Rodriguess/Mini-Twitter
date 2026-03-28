<?php

require_once __DIR__ . "/../vendor/autoload.php";

session_start();

$router = require_once __DIR__ . "/../routes.php";

$router->run();