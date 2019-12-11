<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Requests\registerRequest;
use App\Http\Requests\userRequest;
use App\Http\Requests\creatShopRequest;
use App\Http\Controllers\Controller;
use App\User;
use App\Shops;
use App\Products;
use Validator;
use Cloudder;
use Auth;

class UserController extends Controller
{
    public $successStatus = 200;

    public function index(){
        
        $user_arr = User::all();

       	foreach ($user_arr as $user) {
       		if($user['url_images'] != null) {
       			$img = Cloudder::show('avatar/'.$user->url_images, array("width" => 250, "height" => 250, "crop" => "fill"));
       			$user->url_images = $img;
       		}
       	}

        return response()->json($user_arr);
    }

    public function login()
    {   
        
        if (Auth::attempt(
            [
                'email' => request('email'),
                'password' => request('password')
            ]

        )) {
            $user = Auth::user();
            // $success['token'] = $user->createToken('token')->accessToken;
            while (Shops::where('shops_user_id_foreign', $user->id)->exists()) {
                $shop = Shops::where('shops_user_id_foreign', $user->id)->first();
                return response()->json(
                [
                    'success' => 'successfully',
                    'userid' => $user->id,
                    'shopID' => $shop->id
                ],
                200
                
                );
            }
            
            return response()->json(
                [
                    'success' => 'successfully',
                    'userid' => $user->id,
                    'shopID' => 0
                ],
                200
                
            );
        }
        else {
            return response()->json(
                [
                    'faile' => 'Unauthorised'
                ], 400);
        }
    }

    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */
    public function register(registerRequest $request)
    {

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        
        $user = new User;
        $user->name = $input['name'];
        $user->email = $input['email'];
        $user->password = $input['password'];
        $user->phone = $input['phone'];
        $user->birthday = $input['birthday'];
        $user->sex = $input['sex'];
        $user->url_images = $input['sex'] == 0 ? 'nvTa3_female-define_jjhyfx' : 'male-define_ubnxt4';
        // $user->remember_token = Str::random(60);
        $user->save();


        // $success['token'] = $user->createToken('MyApp')->accessToken;
        $success['name'] = $user->name;

        return response()->json(
            [
                'status' => true, 'userid' => $user->id
            ],
            $this->successStatus
        );

    }

    public function getUser($id){
    	$user = User::find($id);
        $user->url_images = Cloudder::show('avatar/'.$user->url_images, array("width" => 250, "height" => 250, "crop" => "fill"));
        while (Shops::where('shops_user_id_foreign', $id)->exists()) {
                $shop = Shops::where('shops_user_id_foreign', $id)->first();
                $user['shopID'] = $shop->id;
                return  response()->json($user);
        }

        $user['shopID'] = 0;
    	
        return response()->json($user);
    }

    public function update(userRequest $request, $id){
        $user = User::find($id);
        
        $input = $request->all();
        $user->name = $input['name'];
        $user->phone = $input['phone'];
        $user->birthday = $input['birthday'];
        $user->sex = $input['sex'];
        $user->address = $input['address'];
        $user->update();
       
        return response()->json(['status'=> true]);
    }

    public function updateAvatar(Request $request, $id) {
        while (User::where('id', $id)->exists()) {
           $user = User::find($id);

            if($request->hasFile('url_images')){
                $file = $request->file('url_images');
                $name = $file->getClientOriginalName();
                
                $img = str_random(5)."_".$name;
                Cloudder::upload($file, 'avatar/'.$img);
                $user->url_images = $img;
            } 

            $user->update();
            return response()->json(['status'=> true]);
        }

        return response()->json(['status'=> false]);
        
    }

    public function createShop(creatShopRequest $request, $userid){

        while (Shops::where('shops_user_id_foreign', $userid)->exists()) {
            return response()->json(
                [
                    'error' => 'You have a shop'
                ], 400);
        }

        $shop = new Shops;
        $shop->shop_name = $request['shop_name'];
        $shop->shops_user_id_foreign = $userid;
        $shop->save();

        return response()->json(['shop' => $shop, 'status' => true]);        
    }

    public function getShop($user){

    	while (Shops::where('shops_user_id_foreign', $user)->exists()) {
            $info = Shops::where('shops_user_id_foreign', $user)->first();
        	$product = Products::where('products_shop_id_foreign', '=', $info->id)->get();

 	        return response()->json(['info' => $info , 'products' => $product]);

        }
    	
    	 return response()->json(['status' => false], 400);
    }
}
