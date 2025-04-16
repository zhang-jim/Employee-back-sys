<?php 
require __DIR__ . '/vendor/autoload.php';

use App\Controllers\HomeController;
use App\Controllers\AuthController;
use App\Controllers\MessageController;

define('BASE_PATH',__DIR__);
include_once 'Routes/Router.php';
include_once 'database/db.php';
include_once 'help.php';

$router = new Router;
$messageController = new MessageController($pdo);
$authController = new AuthController($pdo);
$homeController = new HomeController;
// 首頁
$router->get('/',[$homeController,'index']);
// 會員路由
$router->get('/login',[$authController,'index']);
$router->post('/login',[$authController,'login']);
$router->get('/register',[$authController,'create']);
$router->post('/register',[$authController,'store']);
$router->post('/logout',[$authController,'logout']);
// 留言路由
$router->get('/message',[$messageController,'index']);
$router->get('/messages',[$messageController,'show']);
$router->get('/messages/create',[$messageController,'create']);
$router->post('/messages/create',[$messageController,'store']);
$router->put('/messages/{id}',[$messageController,'update']);
$router->delete('/messages/{id}',[$messageController,'delete']);

$router->dispatch($_SERVER['REQUEST_URI'],$_SERVER['REQUEST_METHOD']);