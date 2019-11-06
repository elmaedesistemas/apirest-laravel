<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Categories;

class CategoriesController extends Controller
{
    //

    public function __construct() {
        $this->middleware('api.auth', ['except' => ['index', 'show']]);
    }

    public function index(){
        
        // got all categories of database
        $categories = Categories::all();

        return response()->json([
            'code' => 200,
            'status' => 'success',
            'categories' => $categories
        ]);

    }

    public function show($id){
        
        $category = Categories::find($id);


        if(is_object($category)){
            $data = [
                'code' => 200,
                'status' => 'success',
                'category' => $category
            ];
        } else {
         
            $data = [
                'code' => 404,
                'status' => 'error',
                'message' => 'Caegory not exist'
            ];
        }

        return response()->json($data, $data['code']);
    }

    public function store(Request $request){

        // collect data for post
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);

        //validate data
    if(!empty($params_array)) {
        $validate = \Validator::make($params_array, [
            'name' => 'required'
        ]);

        //save category

        if($validate->fails()){
            $data = [
                'code' => 500,
                'status' => 'error',
                'message' => 'Category no save'
            ];
        } else {
            $category = new Categories();
            $category->name = $params_array['name'];
            $category->save();

            $data = [
                'code' => 200,
                'status' => 'success',
                'category' => $category
            ];
        }
    } else {

            $data = [
                'code' => 500,
                'status' => 'error',
                'message' => 'Write a category, please.'
            ];
        }

            //return result
            return response()->json($data, $data['code']);
    }

    public function update($id, Request $request){

        //collect data for post
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);

        
    if(!empty($params_array)){

        // validate data

        $validate = \Validator::make($params_array, [
            'name' => 'required'
        ]);

        // remove data
        unset($params_array['id']);
        unset($params_array['created_at']);

        // update register CATEGORY
        $category = Categories::where('id', $id)->update($params_array);
        $data = [
            'code' => 200,
            'status' => 'success',
            'category' => $params_array
        ];

    } else {

            $data = [
                'code' => 500,
                'status' => 'error',
                'message' => 'Write a category, please.'
            ];
        }
        
            // return result
            return response()->json($data, $data['code']);

    }
}
