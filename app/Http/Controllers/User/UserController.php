<?php

namespace App\Http\Controllers;

use SmyPhp\Core\Controller\Controller;
use App\Models\User;
use SmyPhp\Core\Http\Request;
use SmyPhp\Core\Http\Response;
use SmyPhp\Core\Application;
use SmyPhp\Core\DatabaseModel;

class UserController extends Controller{

    public function register(Request $request, Response $response){
        $user = new User();
        $name = $_POST["name"];
        $email = $_POST["email"];
        $password = $_POST["password"];
        $cpassword = $_POST["cpassword"];
        if (empty($name) || empty($email) || empty($password) || empty($cpassword)) {
            return $response->json([
                "success" => false,
                "message" => "all fields are required"
            ], 400);
        }
        //check if email already exists
        $emailExist = $user->findOne([
            'email' => $email
        ]);
        if (!empty($emailExist)) {
            return $response->json([
                "success" => false,
                "message" => "user already exists"
            ], 400);
        }
        if ($password != $cpassword) {
            return $response->json([
                "success" => false,
                "message" => "passwords do not match"
            ], 400);
        }
        $hashPassword = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = DatabaseModel::prepare("INSERT INTO users SET name = :name, email = :email, password = :password");
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hashPassword);
        if($stmt->execute()){
            return $response->json([
                "success" => true,
                "message" => "user created"
            ], 200);
        }else{
            return $response->json([
                "success" => false,
                "message" => "unexpected error"
            ], 500);
        }
    }

    public function login(Request $request, Response $response){
        $loginUser = new User();        
        $email = $_POST["email"];
        $password = $_POST["password"];
        if (empty($email) || empty($password)) {
            return $response->json([
                "success" => false,
                "message" => "all fields are required"
            ], 400);
        }
        //check if email already exists
        $user = $loginUser->findOne([
            'email' => $email
        ]);
        if (!empty($user)) {
            if(!password_verify($password, $user->password)){
                return $response->json([
                    "success" => false,
                    "message" => "incorrect password"
                ], 400);
            }
            //create token
            $data = [
                "id" => $user->id,
                "email" => $user->email,
                "created_at" => time(),
                "expires_at" => time() + (86400 * 7) // 7 days 
            ];
            $token = Token::sign($data); // jwt authentication token
            unset($user->password);
            unset($user->errors);
            return $response->json([
                "success" => true,
                "token" => $token,
                "data" => $user,
                "message" => "user logged in"
            ], 200);
        }else{
            return $response->json([
                "success" => false,
                "message" => "user not found"
            ], 400);
        }
    }

}