<?php

namespace App\Helpers;

use Firebase\JWT\JWT;
use Illuminate\Support\Facades\DB;
use App\User;

class jwtAuth {

    public $key;

    public function __construct(){
        $this->key = 'keysecret34433232';
    }

    public function signup($email, $password, $getToken = null) {

    // search if user exist
    $user = User::where([
        'email' => $email,
        'password' => $password
    ])->first();



    // testing if are corrects

    $signup = false;

    if(\is_object($user)){

        $signup = true;
    }

    // generate token with data of user identificated

    if($signup == true) {

        $token = array(
            'sub' => $user->id,
            'email' => $user->email,
            'name' => $user->name,
            'surname' => $user->surname,
            'iat' => time(),
            'exp' => time() +(7 * 24 * 60 * 60)
        );

        $jwt = JWT::encode($token, $this->key, 'HS256');
        $decoded = JWT::decode($jwt, $this->key, ['HS256']);
        if(is_null($getToken)){
            $data =  $jwt;
        }
        else {
            $data = $decoded;
        }

    }
    else{
        $data = array(
            'status' => 'error',
            'message' => 'Login Failed'
        );
    }

    return $data;
  }

}
