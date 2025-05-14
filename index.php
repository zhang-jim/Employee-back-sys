<?php
require __DIR__ . '/vendor/autoload.php';
date_default_timezone_set('Asia/Taipei');
session_start();

use Dotenv\Dotenv;
use Routes\Router;

define('BASE_PATH', __DIR__);
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

include_once 'database/db.php';
include_once 'help.php';
include_once 'routes/Router.php';

$router = new Router();

require 'routes/web.php';

// 開始分派
$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
