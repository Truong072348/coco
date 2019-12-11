<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\storeProductRequest;
use App\Http\Requests\updateProductRequest;
use Illuminate\Support\Facades\Gate;
use Validator;
use App\Products;
use App\Images;
use App\Categories;
use App\Types;
use App\Orders;
use App\Shops;
use Auth;
use Cloudder;

class ProductController extends Controller
{
    public function index(){
    	$product = Products::all();
        return response()->json($product);
    }

    public function show($productid){

        if(Products::where('id', $productid)->exists()){
            $product = Products::select('products.*','shop_name')->where('products.id', $productid)->join('shops','shops.id', '=' ,'products.products_shop_id_foreign')->first();
            $img_arr = Images::where('images_product_id_foreign', $productid)->get();
            $type = Types::where('id', $product->products_type_id_foreign)->first();
            foreach ($img_arr as $image) {
                 if($image['url'] != null) {
                     $img = Cloudder::show('images/'.$image->url, array("width" => 250, "height" => 250, "crop" => "fill"));
                     $image->url = $img;
                 }
            }
            $categories = Categories::where('id', $type->types_categories_id_foreign)->first();
          

            return response()->json(['info' => $product, 'images' => $img_arr, 'categoryID' => $categories->id]);

        }

        return response()->json(['status' => false]);
    }

    public function store(storeProductRequest $request){

        $product = Products::create($request->all());
        $Images = new Images;
        $Images->images_product_id_foreign = $product->id;


        if($request->hasFile('url')){
            $file = $request->file('url');
            $name = $file->getClientOriginalName();
            
            $img = str_random(5)."_".$name;
           	Cloudder::upload($file, 'Images/'.$img);
            $Images->url = $img;
        
        } else {
            $Images->url = 'no-image_bi4whx';
        }

        $Images->save();

    	return response()->json(['status' => true, 'productID' => $product->id]);
    }
	

    public function update(updateProductRequest $request, $id){
    	
        $product = Products::find($id);
        // return Auth::user()->email;
        
 
        // $bool = Gate::allows('products-update', $userid);
        // return response()->json($bool);

        if(empty($product)){
            return response()->json(['status' => false]);
        }


        $input = $request->all();
        $product->update($input);

        return response()->json(['status'=>true]);

       

    }


    public function delete($id) {

    	while (Images::where('images_product_id_foreign', $id)->exists() && Orders::where('orders_user_id_foreign', $id)->exists()) {
            return response()->json(
                [
                    'status' => false
                ]);
        }
        $img_arr = Images::where('images_product_id_foreign', $id)->get();
        foreach ($img_arr as $img) {    
            $img->delete();   
        }

        while (Products::where('id', $id)->exists()) {
           $product = Products::find($id);
            $product->delete();

            return response()->json(['status' => true]);
        }

        return response()->json(['status' => false]);
    	
    }

    public function getByType($typeid){
        $product_list = Products::where('products_type_id_foreign', $typeid)->get();
        return response()->json($product_list);
    }

    public function getByShop($shopid){
        $product_list = Products::where('products_shop_id_foreign', $shopid)->get();
        return response()->json($product_list);
    }

    public function getByCategory($categoryid){
        $product_list = Products::select('products.id', 'products.product_name', 'products.price')->join('types', 'products.products_type_id_foreign', '=', 'types.id')->join('categories', 'categories.id', '=', 'types.types_categories_id_foreign')->where('categories.id', $categoryid)->get();
        return response()->json($product_list);
    }


}
