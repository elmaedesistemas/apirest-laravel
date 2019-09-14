<?php

namespace App\Http\Controllers;

use App\Providers\JwtAuthServiceProvider;
use Illuminate\Http\Request;
use App\User;

class UserController extends Controller
{
    //
    public function register(Request $request) {

        // Collect data of user for POST
        $json = $request->input('json', null);
        $params = json_decode($json);
        $params_array = json_decode($json, true);

        // Testing if data is empty
        if(!empty($params_array) && !empty($params)) {

            // Clean data
            $params_array = array_map('trim', $params_array);

            // Validate data

            $validate = \Validator::make($params_array, [
                'name' => 'required|alpha',
                'surname' => 'required|alpha',
                'email' => 'required|email|unique:users',
                'password' => 'required'
            ]);

            if($validate->fails()) {
                $data = array (
                    'status' => 'error',
                    'code' => 404,
                    'message' => 'The user is not created successfully',
                    'errors' => $validate->errors()
                );
            }
            else {

              // cyfrating password
                $pwd = hash('sha256', $params->password);

              // Testing if user exist now = in validation->email(unique:users)

              //Create user

              $user = new User();
              $user->name = $params_array['name'];
              $user->surname = $params_array['surname'];
              $user->email = $params_array['email'];
              $user->password = $pwd;

              //Save user

              $user->save();

              $data = array(
                'status' => 'success',
                'code' => 200,
                'message' => 'The user is created succesfully'
              );

            }

            return response()->json($data, 200);

        } //endif
        else {
            $data = array(
                'status' => 'error',
                'code' => 404,
                'message' => 'The Data is Wrong'
            );
        } // End of Testing data empty
    } // End of Register

    public function login(Request $request) {

        $jwtAuth = new \JwtAuth();

        // Reveice data for post

        $json = $request->input('json', null);
        $params = \json_decode($json);
        $params_array = json_decode($json, true);

        //validate the data

        $validate = \Validator::make($params_array, [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if($validate->fails()) {
            $signup = array (
                'status' => 'error',
                'code' => 404,
                'message' => 'The user is not logged successfully',
                'errors' => $validate->errors()
            );
        }
        else {

          // cyfrating password
            $pwd = hash('sha256', $params->password);

          //return token or data
            $signup = $jwtAuth->signup($params->email, $pwd);
            if(isset($params->getToken)){
                $signup = $jwtAuth->signup($params->email, $pwd, true);
            }



          $data = array(
            'status' => 'success',
            'code' => 200,
            'message' => 'The user is logged succesfully'
          );

        }

        return response()->json($data, 200);


        $email = 'bmejia2404@gmail.com';
        $password = 'admin2801';
        $pwd = hash('sha256', $password);

        return response()->json($signup, 200);
    }

    public function update(Request $request){

        $token = $request->header('Authorization');
        $jwtAuth = new \JwtAuth();
        $checkToken = $jwtAuth->checkToken($token);

        if($checkToken){
            echo '<h1> Login Succesfully </h1>';
        }else {
            echo '<h1> Login Incorrect. :(</h1>';
        }
        die();
    }
}
