<?php

require dirname(__DIR__).'/config/app.php';
use App\Http\Controllers\User\UserController;


/*
|--------------------------------------------------------------------------
| Routes
|--------------------------------------------------------------------------
|
| Here is where you can register routes for your application. 
|
*/

$app->router->get('/', function(){
    return "Hello world";
});
$app->router->post('/user/register', [UserController::class, 'register']);
$app->router->post('/user/login', [UserController::class, 'login']);

$app->run();