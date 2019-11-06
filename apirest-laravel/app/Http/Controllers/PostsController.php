<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Helpers\JWTAuth;
use App\Posts;

class PostsController extends Controller
{
    //
    public function __construct() {
        $this->middleware('api.auth', ['except' => ['index', 'show']]);
    }

    public function index() {

         // got all categories of database
         $posts = Posts::all()->load('categories');

         return response()->json([
             'code' => 200,
             'status' => 'success',
             'posts' => $posts
         ], 200);
    }

    public function show($id) {

        $post = Posts::find($id)->load('categories');

        if(is_object($post)){
            $data = [
                'code' => 200,
                'status' => 'success',
                'posts' => $post
            ];
        } else {

            $data = [
                'code' => 404,
                'status' => 'error',
                'message' => 'The post is not exists'
            ];
        }

        return response()->json($data, $data['code']);
    }

    public function store(Request $request) {

        //collect data for post
        $json = $request->input('json', null);
        $params = json_decode($json);
        $params_array = json_decode($json, true);

        // validate data
        if(!empty($params_array)){
          $jwtAuth = new JwtAuth();
          $token = $request->header('Authorization', null);
          $user = $jwtAuth->checkToken($token, true);


        $validate = \Validator::make($params_array, [
            'category_id' => 'required',
            'title' => 'required',
            'content' => 'required',
            'image' => 'required'
        ]);

        if($validate->fails()){
          $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'The post has not been saved, data is missing.'
              ];

        } else {
                    
        // save post
        $post = new Posts();
        $post->user_id = $user->sub;
        $post->category_id = $params->category_id;
        $post->title = $params->title;
        $post->content = $params->content;
        $post->image = $params->image;
        $post->save();

          $data = [
              'code' => 200,
              'status' => 'success',
              'message' => 'The post has been saved.'
            ];
        }

        } else {
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'The post has not been saved, data is missing.'
              ];
        }

            // return response
        return response()->json($data, $data['code']);

    }

    public function update($id, Request $request) {
         //collect data for post
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);

        $data = [
            'code' => 500,
            'status' => 'error',
            'message' => 'Write a category, please.'
        ];

        if(!empty($params_array)){

            // validate data
    
            $validate = \Validator::make($params_array, [
                'title' => 'required',
                'content' => 'required',
                'category_id' => 'required'
            ]);

            if($validate->fails()){
                $data['errors'] = $validate->errors();
                
                return response()->json($data, $data['code']);
            }
    
            // remove data
            unset($params_array['id']);
            unset($params_array['user_id']);
            unset($params_array['created_at']);
            unset($params_array['user']);
    
            // update register CATEGORY
            $post = Posts::where('id', $id)->update($params_array);
            $data = [
                'code' => 200,
                'status' => 'success',
                'category' => $params_array
            ];
    
        }
            
                // return result
                return response()->json($data, $data['code']);
    
        }
    }

