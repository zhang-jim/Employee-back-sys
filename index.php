<?php 
require __DIR__ . '/vendor/autoload.php';
date_default_timezone_set('Asia/Taipei');
session_start();

use App\Middlewares\AuthMiddleware; 
use App\Controllers\HomeController;
use App\Controllers\AuthController;
use App\Controllers\MessageController;
use App\Controllers\AttendanceController;
use App\Controllers\DepartmentController;

define('BASE_PATH',__DIR__);
include_once 'routes/Router.php';
include_once 'database/db.php';
include_once 'help.php';

$router = new Router;
$messageController = new MessageController($pdo);
$authController = new AuthController($pdo);
$attendanceController = new AttendanceController($pdo);
$departmentController = new DepartmentController($pdo);
$homeController = new HomeController;

// 不須經過登入驗證的白名單路由
$publicRoutes = ['/','/register','/login','/api/register','/api/login'];
$currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
// 對白名單外的URL進行登入驗證，若未登入則返回錯誤。
if(!in_array($currentPath,$publicRoutes)){
    AuthMiddleware::handle();
}
// 首頁
$router->get('/',function(){
    return view('index');
});
// 控制台
$router->get('/dashboard',[$homeController,'index']);
// 會員路由
$router->get('/login',[$authController,'index']);
$router->post('/api/login',[$authController,'login']);
$router->get('/register',[$authController,'create']);
$router->post('/api/register',[$authController,'store']);
$router->post('/api/logout',[$authController,'logout']);
// 檢視個人資料 
$router->get('/user',[$authController,'showInfo']);
$router->post('/api/user',[$authController,'show']);
// 個人資料編輯
$router->post('/api/user/update',[$authController,'update']);
//重設密碼
$router->post('/api/user/reset-password',[$authController,'resetPassword']);
// 留言路由
$router->get('/message',[$messageController,'index']);
$router->get('/messages',[$messageController,'show']);
$router->get('/messages/create',[$messageController,'create']);
$router->post('/api/messages/create',[$messageController,'store']);
$router->put('/api/messages/{id}',[$messageController,'update']);
$router->delete('/api/messages/{id}',[$messageController,'delete']);
// 打卡紀錄
$router->get('/check-record',[$attendanceController,'show']); 
$router->get('/api/check-record',[$attendanceController,'index']);
$router->post('/api/check-in',[$attendanceController,'store']);
$router->post('/api/check-out',[$attendanceController,'update']);
// 部門路由
$router->get('/departments',[$departmentController,'index']);
$router->post('/api/departments/create',[$departmentController,'store']);
$router->put('/api/departments/{id}',[$departmentController,'update']);
$router->delete('/api/departments/{id}',[$departmentController,'delete']);

$router->dispatch($_SERVER['REQUEST_URI'],$_SERVER['REQUEST_METHOD']);