<?php

namespace App\Http\Controllers;

use App\Providers\JwtAuthServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
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
        if(!empty($params_array) && !empty($params_array)) {

            // Clean data
            $params_array = array_map('trim', $params_array);

            // Validate data

            $validate = \Validator::make($params_array, [
                'name' => 'required|alpha',
                'username' => 'required|alpha',
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

              //  encrrypt password
                $pwd = hash('sha256', $params->password);

              // Testing if user exist now = in validation->email(unique:users)

              
              //Create user
              $user = new User();
              $user->name = $params_array['name'];
              $user->username = $params_array['username'];
              $user->email = $params_array['email'];
              $user->password = $pwd;
              $user->role = 'ROLE_USER';

              //Save user

              $user->save();

              $data = array(
                'status' => 'success',
                'code' => 200,
                'message' => 'The user is created succesfully',
                'user' => $user
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
        $params_array = \json_decode($json, true);

          //   //validate the data


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

          //  encrrypt password
            $pwd = hash('sha256', $params->password);

            //return token or data
            $signup = $jwtAuth->signup($params->email, $pwd);

            if(isset($params->getToken)){
                $signup = $jwtAuth->signup($params->email, $pwd, true);
            }
        }

        return response()->json($signup, 200);
    

        //return response()->json($signup, 200);
    }

    public function update(Request $request){

       

        // collect data for post
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);

        if($checkToken && !empty($params_array)){

            //Update user

            // Out user identified
            $user = $jwtAuth->checkToken($token, true);

            // validate data
            $validate = \Validator::make($params_array, [
                'name' => 'required|alpha',
                'username' => 'required|alpha',
                'email' => 'required|email|unique:users,'.$user->sub
            ]);

            // remove fields that i dont want to update

            unset($params_array['id']);
            unset($params_array['role']);
            unset($params_array['created_at']);
            unset($params_array['remember_token']);


            // Update user in database
            $user_update = User::where('id', $user->sub)->update($params_array);

            //return array
            $data = array(
                'code' => 200,
                'status' => 'success',
                'message' => 'user is updated successfully',
                'user' => $user,
                'changes' => $params_array

            );

            return response()->json($data, $data['code']);
            
        }else {

            // Message Error
            $data = array(
                'code' => 500,
                'status' => 'error',
                'message' => 'user not identified successfully'
            );

        return response()->json($data, $data['code']);
        }
    }

    public function upload(Request $request){

        // collect data of request
        $image = $request->file('file0');


        //validate photo

        $validate = \Validator::make($request->all(), [
            'file0' => 'required|image|mimes: jpg,jpeg,png'

        ]);

        // save photo
        if(!$image || $validate->fails()){

            $data = array(
                'code' => 500,
                'status' => 'error',
                'message' => 'Error to charge photo'
            );

        } else {
            $image_name = time().$image->getClientOriginalName();
            \Storage::disk('users')->put($image_name, \File::get($image));

            $data = array(
                'code' => 200,
                'status' => 'success',
                'image' => $image_name
            );
        }

        return response()->json($data, $data['code']);
    }

    public function getImage($filename) {
        $isset = \Storage::disk('users')->exists($filename);
        if($isset){
            $file = \Storage::disk('users')->get($filename);
            return new Response($file, 200);
        } else {
            $data = array(
                'code' => 404,
                'status' => 'error',
                'message' => 'Picture not found'
            );
        }
        
    }

    public function detail($id) {
        $user = User::find($id);

        if(is_object($user)){
            $data = array (
                'code' => 200,
                'status' => 'success',
                'user' => $user
            );

        } else {

            $data = array(
                'code' => 500,
                'status' => 'error',
                'message' => 'User is not exist'
            );    
        }
        
        return response()->json($data, $data['code']);
    }
}
