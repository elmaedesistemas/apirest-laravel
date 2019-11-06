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

    if(is_object($user)){

        $signup = true;
    }

    // generate token with data of user identificated

    if($signup) {

        $token = array(
            'sub' => $user->id,
            'email' => $user->email,
            'name' => $user->name,
            'username' => $user->username,
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

  public function checkToken($jwt, $getIdentity = false){

    $auth = false;

    try{
        $jwt = str_replace('""', '', $jwt);
        $decoded = JWT::decode($jwt, $this->key, ['HS256']);
    }catch(\UnexpectedValueException $e){
        $auth = false;
    }catch(\DomainException $e){
        $auth = false;
    }

    if(!empty($decoded) && is_object($decoded) && isset($decoded->sub)){
        $auth = true;
    }else {
        $auth = false;
    }

    if($getIdentity){
        return $decoded;
    }

    return $auth;
  }
}
