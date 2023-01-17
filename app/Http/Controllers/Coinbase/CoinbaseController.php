<?php

namespace App\Http\Controllers;

use SmyPhp\Core\Controller\Controller;
use App\Models\User;
use SmyPhp\Core\Http\Request;
use SmyPhp\Core\Http\Response;
use SmyPhp\Core\Application;
use App\Http\Middleware\ApiMiddleware;

class CoinbaseController extends Controller{

    public function __construct(){
        $this->authenticatedMiddleware(new ApiMiddleware(['']));
    }

}