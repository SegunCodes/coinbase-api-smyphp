<?php

require dirname(__DIR__).'/config/app.php';
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\Coinbase\CoinbaseController;


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
$app->router->post('/coinbase/create-account', [CoinbaseController::class, 'createAccount']);
$app->router->post('/coinbase/update-account', [CoinbaseController::class, 'update']);
$app->router->post('/coinbase/delete-account', [CoinbaseController::class, 'delete']);
$app->router->get('/coinbase/supported-countries', [CoinbaseController::class, 'checkBankSupport']);
$app->router->get('/coinbase/get-prices', [CoinbaseController::class, 'getPrices']);
$app->router->post('/coinbase/get-balance', [CoinbaseController::class, 'getBalance']);
$app->router->post('/coinbase/card-payment-method', [CoinbaseController::class, 'createCardPayment']);
$app->router->post('/coinbase/withdraw', [CoinbaseController::class, 'withdraw']);
$app->router->post('/coinbase/deposit', [CoinbaseController::class, 'deposit']);
$app->router->post('/coinbase/buy-order', [CoinbaseController::class, 'buyOrder']);
$app->router->post('/coinbase/sell-order', [CoinbaseController::class, 'sellOrder']);

$app->run();