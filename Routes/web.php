<?php

use App\Controllers\HomeController;
use App\Controllers\AuthController;
use App\Controllers\MessageController;
use App\Controllers\AttendanceController;
use App\Controllers\DepartmentController;
use App\Middlewares\AuthMiddleware;

$homeController = new HomeController;
$authController = new AuthController($pdo);
$messageController = new MessageController($pdo);
$attendanceController = new AttendanceController($pdo);
$departmentController = new DepartmentController($pdo);

// 登入驗證白名單
$publicRoutes = ['/', '/register', '/login', '/api/register', '/api/login', '/auth/verify'];
$currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
// 對白名單外的URL進行登入驗證，若未登入則返回錯誤。
if (!in_array($currentPath, $publicRoutes)) {
    AuthMiddleware::handle();
}
// 控制台
$router->get('/', fn() => view('index'));
$router->get('/dashboard', [$homeController, 'index']);
// Auth
$router->get('/login', [$authController, 'index']);
$router->post('/api/login', [$authController, 'login']);
$router->get('/register', [$authController, 'create']);
$router->post('/api/register', [$authController, 'store']);
$router->post('/api/logout', [$authController, 'logout']);
$router->get('/user', [$authController, 'showInfo']);
$router->post('/api/user', [$authController, 'show']);
$router->post('/api/user/update', [$authController, 'update']);
$router->post('/api/user/reset-password', [$authController, 'resetPassword']);
$router->get('/auth/verify', [$authController, 'verify']);
// Message
$router->get('/message',[$messageController,'index']);
$router->get('/messages',[$messageController,'show']);
$router->get('/messages/create',[$messageController,'create']);
$router->post('/api/messages/create',[$messageController,'store']);
$router->put('/api/messages/{id}',[$messageController,'update']);
$router->delete('/api/messages/{id}',[$messageController,'delete']);
// Attendance
$router->get('/check-record',[$attendanceController,'show']); 
$router->get('/api/check-record',[$attendanceController,'index']);
$router->post('/api/check-in',[$attendanceController,'store']);
$router->post('/api/check-out',[$attendanceController,'update']);
// Department
$router->get('/departments',[$departmentController,'index']);
$router->post('/api/departments/create',[$departmentController,'store']);
$router->put('/api/departments/{id}',[$departmentController,'update']);
$router->delete('/api/departments/{id}',[$departmentController,'delete']);
