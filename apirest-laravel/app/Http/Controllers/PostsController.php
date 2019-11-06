<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Helpers\JWTAuth;
use App\Posts;

class PostsController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('api.auth', ['except' => [
                'index',
                'show',
                'getImage',
                'getPostsByCategory',
                'getPostsByUser'
            ]]);
    }

    public function index()
    {

        // got all categories of database
        $posts = Posts::all()->load('categories');

        return response()->json([
            'code' => 200,
            'status' => 'success',
            'posts' => $posts
        ], 200);
    }

    public function show($id)
    {

        $post = Posts::find($id)->load('categories');

        if (is_object($post)) {
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

    public function store(Request $request)
    {

        //collect data for post
        $json = $request->input('json', null);
        $params = json_decode($json);
        $params_array = json_decode($json, true);

        // validate data
        if (!empty($params_array)) {
            $user = $this->getIdentity($request);


            $validate = \Validator::make($params_array, [
                'category_id' => 'required',
                'title' => 'required',
                'content' => 'required',
                'image' => 'required'
            ]);

            if ($validate->fails()) {
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

    public function update($id, Request $request)
    {
        //collect data for post
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);

        $data = [
            'code' => 500,
            'status' => 'error',
            'message' => 'Write a category, please.'
        ];

        if (!empty($params_array)) {

            // validate data

            $validate = \Validator::make($params_array, [
                'title' => 'required',
                'content' => 'required',
                'category_id' => 'required'
            ]);

            if ($validate->fails()) {
                $data['errors'] = $validate->errors();

                return response()->json($data, $data['code']);
            }

            // remove data
            unset($params_array['id']);
            unset($params_array['user_id']);
            unset($params_array['created_at']);
            unset($params_array['user']);

            // get user identified
            $user = $this->getIdentity($request);

            // search register
            $post = Posts::where('id', $id)
                ->where('user_id', $user->sub) - first();

            if (!empty($post) && is_object($post)) {

                // update register

                $post->updateorCreate($params_array);

                // return something

                $data = [
                    'code' => 200,
                    'status' => 'success',
                    'post' => $post
                ];
            }


            /* // update register CATEGORY
            $where = [
                'id' => $id,
                'user_id' => $user->sub,
            ];
            $post = Posts::updateOrCreate($where, $params_array);
            */
            $data = [
                'code' => 200,
                'status' => 'success',
                'posts' => $post,
                'changes' => $params_array
            ];
        }

        // return result
        return response()->json($data, $data['code']);
    }

    public function destroy($id, Request $request)
    {

        //get user identified
        $user = $this->getIdentity($request);

        // get register
        $post = Posts::where('id', $id)
            ->where('user_id', $user->sub) - first();

        if (empty($post)) {
            $data = [
                'code' => 404,
                'status' => 'error',
                'message' => 'The post is not exist.'
            ];
        } else {

            // delete
            $post->delete();

            // return something
            $data = [
                'code' => 200,
                'status' => 'success',
                'post' => $post
            ];
        }

        return response()->json($data, $data['code']);
    }

    private function getIdentity($request) {
        $jwtAuth = new JwtAuth();
        $token = $request->header('Authorization', null);
        $user = $jwtAuth->checkToken($token, true);

        return $user;
    }

    public function upload(Request $request) {
        // collect the picture
        $image = $request->file('file0');


        // validate image

        $validate = \Validator::make($request->all(), [
            'file0' => 'required|image|mimes:jpg,jpeg,png'
        ]);

        // save image
        if(!image || $validate->fails()){
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'Error to charge the picture'
            ];
        } else {
            $image_name = time().$image->getClientOriginalName();

            \Storage::disk('images')->put($image_name, \File::get($image));

            $data = [
                'code' => 200,
                'status' => 'success',
                'image' => $image_name
            ];

        }

        // return data
        return response()->json($data, $data['code']);
    }

    public function getImage($filename) {
        $isset = \Storage::disk('images')->exists($filename);
        if($isset){
            $file = \Storage::disk('images')->get($filename);
            return new Response($file, 200);
        } else {
            $data = array(
                'code' => 404,
                'status' => 'error',
                'message' => 'Picture not found'
            );
        }
        
    }

    public function getPostsByCategory($id) {
        $posts = Posts::where('category_id', $id)->get();

        return response()->json([
            'status' => 'success',
            'posts' => $posts
        ], 200);
    }


    public function getPostsByUser($id) {
        $posts = Posts::where('user_id', $id)->get();

        return response()->json([
            'status' => 'success',
            'posts' => $posts
        ], 200);
    }

}
