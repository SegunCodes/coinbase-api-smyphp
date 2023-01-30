<?php

namespace App\Http\Controllers;

use SmyPhp\Core\Controller\Controller;
use App\Models\User;
use SmyPhp\Core\Http\Request;
use SmyPhp\Core\Http\Response;
use SmyPhp\Core\Application;
use App\Http\Middleware\ApiMiddleware;
use App\Http\Requests\CoinbaseRequests;
use SmyPhp\Core\Auth;

class CoinbaseController extends Controller{

    public function __construct(){
        $this->authenticatedMiddleware(new ApiMiddleware(['createAccount', 'update', 'delete', 'getBalance', 'createCardPayment', 'sellOrder', 'buyOrder', 'withdraw', 'deposit']));
    }

    public function createAccount(Request $request, Response $response){
        $client = new CoinbaseRequests;
        $name = $_POST["name"];
        $currency = $_POST["currency"];
        $type = $_POST["type"];
        $primary = $_POST["primary"];
        if (empty($name) || empty($currency) || empty($type) || empty($primary)) {
            return $response->json([
                "success" => false,
                "message" => "all fields are required"
            ], 400);
        }

        // Create a Coinbase account using the Coinbase API
        $account = $client->createAccount([
            'name' => $name,
            'currency' => $currency,
            'type' => $type,
            'primary' => $primary
        ]);

        // Save the Coinbase account ID to the user's record
        $user = new User;
        $update = $user->update([
            'coinbase_account_id' => $account['id']
        ], [
            "id" => Auth::user()
        ]);
        if ($update) {
            return $response->json([
                "success" => true,
                "message" => "Coinbase account created"
            ],200);
        }
    }

    public function update(Request $request, Response $response){
        $accountId = $_POST["accountId"];
        $name = $_POST["name"];
        $currency = $_POST["currency"];
        $type = $_POST["type"];
        $primary = $_POST["primary"];
        if (empty($accountId)) {
            return $response->json([
                "success" => false,
                "message" => "accountId is required"
            ], 400);
        }
        $user = new User;
        $find = $user->findAllWhere([
            'coinbase_account_id' => $accountId,
            'id' => Auth::User()
        ]);
        if(!$find){
            return $response->json([
                "success" => false,
                "message" => "Invalid account id"
            ],400);
        }else{
            $client = new CoinbaseRequests;
            $options = [
                'name' => $name,
                'currency' => $currency,
                'type' => $type,
                'primary' => $primary
            ];

            $account = $client->updateAccount($accountId, $options);
            return $response->json([
                "success" => true,
                "message" => "Coinbase account updated",
                "account" => $account
            ],200);

            return response()->json($account);
        }
    }

    public function delete(Request $request, Response $response){
        $accountId = $_POST["accountId"];
        if (empty($accountId)) {
            return $response->json([
                "success" => false,
                "message" => "account id is required"
            ], 400);
        }
        $user = new User;
        $find = $user->findAllWhere([
            'coinbase_account_id' => $accountId,
            'id' => Auth::User()
        ]);
        if(!$find){
            return $response->json([
                "success" => false,
                "message" => "Invalid account id"
            ],400);
        }else{
            $client = new CoinbaseRequests;
            $result = $client->deleteAccount($accountId);
            return $response->json([
                "success" => true,
                "message" => "account deleted",
                "response" => $result
            ],200);
        }
    }

    public function checkBankSupport(Request $request, Response $response){
        $client = new CoinbaseRequests;
        $paymentMethods = $client->getPaymentMethods();

        $supportedCountries = [];
        foreach ($paymentMethods['data'] as $paymentMethod) {
            if ($paymentMethod['type'] == 'ach_bank_account') {
                $supportedCountries[] = $paymentMethod['country'];
            }
        }

        return $response->json([
            'supported_countries' => $supportedCountries,
            "success" => true,
            "message" => "ok",
        ], 200);
    }

    public function getPrices(Request $request, Response $response){
        $client = new CoinbaseRequests;
        $prices = $client->getPrices([
            'currency_pairs' => ['BTC-USD', 'ETH-USD'],
        ]);
        return $response->json([
            'prices' => $prices,
            "success" => true,
            "message" => "ok",
        ], 200);
    }

    public function getBalance(CRequest $request, Response $response){
        $accountId = $_POST["accountId"];
        if (empty($accountId)) {
            return $response->json([
                "success" => false,
                "message" => "account id is required"
            ], 400);
        }
        $user = new User;
        $find = $user->findAllWhere([
            'coinbase_account_id' => $accountId,
            'id' => Auth::User()
        ]);
        if(!$find){
            return $response->json([
                "success" => false,
                "message" => "Invalid account id"
            ],400);
        }
        $client = new CoinbaseRequests;
        $balance = $client->getBalance($accountId);
        return $response->json([
            "success" => true,
            "message" => "account deleted",
            "balance" => $balance
        ],200);
    }

    public function createCardPayment(Request $request, Response $response){
        $client = new CoinbaseRequests;
        // Add debit card payment method
        $paymentMethodOptions = [
            'type' => 'card',
            'info' => [
                'number' => $_POST['card_number'],
                'exp_month' => $_POST['exp_month'],
                'exp_year' => $_POST['exp_year'],
                'cvv' => $_POST['cvv'],
            ],
        ];
        $paymentMethod = $client->createPaymentMethod($paymentMethodOptions);

        //save payment_method_id to user's record  $paymentMethod["id"]
        $user = new User;
        $update = $user->update([
            'payment_method_id' => $paymentMethod['id']
        ], [
            "id" => Auth::user()
        ]);
        if ($update) {
            return $response->json([
                "success" => true,
                "message" => "OK",
                "payment_method" => $paymentMethod
            ],200);
        }
    }
    
    public function withdraw(Request $request, Response $response){
        $client = new CoinbaseRequests;
        $options = [
            'amount' => $_POST['amount'],
            'currency' => $_POST['currency'],
            'payment_method_id' => $_POST['payment_method_id'],
        ];
        $accountId = $_POST["accountId"];
        if (empty($accountId)) {
            return $response->json([
                "success" => false,
                "message" => "accountId is required"
            ], 400);
        }
        $user = new User;
        $find = $user->findAllWhere([
            'coinbase_account_id' => $accountId,
            'id' => Auth::User()
        ]);
        if(!$find){
            return $response->json([
                "success" => false,
                "message" => "Invalid account id"
            ],400);
        }
        $result = $client->createWithdrawal($accountId ,$options);
        //YOUR TRANSACTION LOGIC CAN COME HERE
        return $response->json([
            "success" => true,
            "response" => $result
        ],200);
    }

    public function deposit(Request $request, Response $response){
        $amount = $_POST['amount'];
        $currency = $_POST['currency'];
        $payment_method_id = $_POST['payment_method_id'];
        $accountId = $_POST["accountId"];
        if (empty($accountId)) {
            return $response->json([
                "success" => false,
                "message" => "accountId is required"
            ], 400);
        }
        $user = new User;
        $find = $user->findAllWhere([
            'coinbase_account_id' => $accountId,
            'id' => Auth::User()
        ]);
        if(!$find){
            return $response->json([
                "success" => false,
                "message" => "Invalid account id"
            ],400);
        }
        $client = new CoinbaseClient();
        $result = $client->deposit($accountId, $amount, $currency, $payment_method_id);
        //YOUR TRANSACTION LOGIC CAN COME HERE
        return $response->json([
            "success" => true,
            "response" => $result
        ],200);
    }

    public function buyOrder(Request $request, Response $response){
        $amount = $_POST['amount'];
        $currency = $_POST['currency'];
        $payment_method_id = $_POST['payment_method_id'];
        $accountId = $_POST["accountId"];
        if (empty($accountId)) {
            return $response->json([
                "success" => false,
                "message" => "accountId is required"
            ], 400);
        }
        $user = new User;
        $find = $user->findAllWhere([
            'coinbase_account_id' => $accountId,
            'id' => Auth::User()
        ]);
        if(!$find){
            return $response->json([
                "success" => false,
                "message" => "Invalid account id"
            ],400);
        }

        // Place a buy order using the Coinbase API
        $client = new CoinbaseClient();
        $order = $client->placeBuyOrder($accountId, $amount, $currency, $payment_method_id);

        // Check if the order was successful
        if ($order['status'] !== 'completed') {
            return $response->json([
                "success" => false,
                "message" => "Order failed"
            ],400);
        }

        //YOUR TRANSACTION LOGIC CAN COME HERE
        return $response->json([
            "success" => true,
            "message" => $order['status']
        ],200);
    }

    public function sellOrder(Request $request,  Response $response){
        $amount = $_POST['amount'];
        $currency = $_POST['currency'];
        $payment_method_id = $_POST['payment_method_id'];
        $accountId = $_POST["accountId"];
        if (empty($accountId)) {
            return $response->json([
                "success" => false,
                "message" => "accountId is required"
            ], 400);
        }
        $user = new User;
        $find = $user->findAllWhere([
            'coinbase_account_id' => $accountId,
            'id' => Auth::User()
        ]);
        if(!$find){
            return $response->json([
                "success" => false,
                "message" => "Invalid account id"
            ],400);
        }

        // Place a sell order using the Coinbase API
        $client = new CoinbaseClient();
        $order = $client->placeSellOrder($accountId, $amount, $currency, $payment_method_id);

        // Check if the order was successful
        if ($order['status'] !== 'completed') {
            return $response->json([
                "success" => false,
                "message" => "Order failed"
            ],400);
        }

         //YOUR TRANSACTION LOGIC CAN COME HERE
         return $response->json([
            "success" => true,
            "message" => $order['status']
        ],200);
    }

}