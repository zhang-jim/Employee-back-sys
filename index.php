<?php 
include_once 'Routes/Router.php';
include_once 'Controller/MessageController.php';
include_once 'database/db.php';

$router = new Router;
$messageController = new MessageController($pdo);

$router->get('/',[$messageController,'index']);
$router->get('/messages',[$messageController,'show']);
$router->get('/messages/create',[$messageController,'create']);
$router->post('/messages/create',[$messageController,'store']);
$router->put('/messages/{id}',[$messageController,'update']);
$router->delete('/messages/{id}',[$messageController,'delete']);

$router->dispatch($_SERVER['REQUEST_URI'],$_SERVER['REQUEST_METHOD']);