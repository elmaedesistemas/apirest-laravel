<?php

namespace App\Http\Controllers;

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
                $pwd = \password_hash($params->password, PASSWORD_BCRYPT, ['cost' => 4]);

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
        echo 'logeo';
    }
}
