<?php

namespace App\Http\Requests;

class CoinbaseRequests{
    protected $client;

    public function __construct(){
        $this->client = new GuzzleHttp\Client([
            'base_uri' => 'https://api.coinbase.com/v2/',
            'headers' => [
                'Authorization' => 'Bearer '.env('COINBASE_API_KEY'),
                'Content-Type' => 'application/json',
            ],
        ]);
    }

    //create an account
    public function createAccount($options){
        $response = $this->client->post('accounts', [
            'json' => $options,
        ]);

        return json_decode((string) $response->getBody(), true);
    }

    //get all account info for a user
    public function getAccounts(){
        $response = $this->client->get('accounts');

        return json_decode((string) $response->getBody(), true);
    }

    //get specific account info for a user
    public function getAccount($accountId){
        $response = $this->client->get("accounts/{$accountId}");

        return json_decode((string) $response->getBody(), true);
    }

    //update account
    public function updateAccount($accountId, $options){
        $response = $this->client->patch("accounts/{$accountId}", [
            'json' => $options,
        ]);

        return json_decode((string) $response->getBody(), true);
    }

    //delete account
    public function deleteAccount($accountId){
        $response = $this->client->delete("accounts/{$accountId}");

        return json_decode((string) $response->getBody(), true);
    }

    //create payment methods
    public function createPaymentMethod($options){
        $response = $this->client->post('payment-methods', [
            'json' => $options,
        ]);

        return json_decode((string) $response->getBody(), true);
    }

    //all payment methods
    public function getPaymentMethods(){
        $response = $this->client->get('payment-methods');

        return json_decode((string) $response->getBody(), true);
    }

    //single payment method
    // public function getPaymentMethod($payment_method_id){
    //     $response = $this->client->get("payment-methods/{$payment_method_id}");

    //     return json_decode((string) $response->getBody(), true);
    // }

    //create withdrawal
    public function createWithdrawal($accountId ,$options){
        $response = $this->client->post("accounts/$accountId/withdrawals", [
            'json' => $options,
        ]);

        return json_decode((string) $response->getBody(), true);
    }

    public function deposit($accountId ,$amount, $currency, $payment_method_id){
        $response = $this->client->post("accounts/$accountId/deposits", [
            'json' => [
                'amount' => $amount,
                'currency' => $currency,
                'payment_method_id' => $payment_method_id,
            ],
        ]);

        return json_decode((string) $response->getBody(), true);
    }

    public function getBuyPrices($currency, $options = []){
        $response = $this->client->get("prices/{$currency}/buy", [
            'query' => $options,
        ]);

        return json_decode((string) $response->getBody(), true);
    }

    public function getSellPrices($currency, $options = []){
        $response = $this->client->get("prices/{$currency}/sell", [
            'query' => $options,
        ]);

        return json_decode((string) $response->getBody(), true);
    }

    public function getPrices($options = []){
        $response = $this->client->get('prices', [
            'query' => $options,
        ]);

        return json_decode((string) $response->getBody(), true);
    }

    public function placeSellOrder($accountId, $amount, $currency, $payment_method_id){
        $response = $this->client->post("accounts/$accountId/buys", [
            'json' => [
                'amount' => $amount,
                'currency' => $currency,
                'payment_method_id' => $payment_method_id
            ],
        ]);

        return json_decode((string) $response->getBody(), true);
    }

    public function placeBuyOrder($accountId, $amount, $currency, $payment_method_id){
        $response = $this->client->post("accounts/$accountId/buys", [
            'json' => [
                'amount' => $amount,
                'currency' => $currency,
                'payment_method_id' => $payment_method_id
            ],
        ]);

        return json_decode((string) $response->getBody(), true);
    }

    public function getBalance($accountId){
        $response = $this->client->get("accounts/{$accountId}/balance");

        return json_decode((string) $response->getBody(), true);
    }



}

