<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Products;
use App\Images;
use Cloudder;

class SearchController extends Controller
{
    public function searchProduct(Request $request){
    	$key = $request['search'];
    	$product = Products::select('products.*', 'types.type_name', 'categories.category_name')->where('product_name', 'like', "%$key%")->orWhere('description', 'like', '%'.$key.'%')->orWhere('type_name', 'like', '%'.$key.'%')->join('types', 'products.products_type_id_foreign', '=', 'types.id')->join('categories', 'categories.id', '=', 'types.types_categories_id_foreign')->get();
        
        if($product != null) {
            foreach ($product as $p) {
                $img = Images::where('images_product_id_foreign', $p->id)->first();
                if($img != null) {
                     $p['url_images'] = Cloudder::show('images/'.$img->url, array("width" => 250, "height" => 250, "crop" => "fill"));
                }
                else {
                    $p['url_images'] = Cloudder::show('images/no-image_bi4whx');
                }
               
            }
        }

    	return response()->json($product);    
    }

    public function searchProductByNameType(Request $request, $typeid) {
    	$key = $request['search'];
    	$product =Products::select('products.*', 'types.type_name', 'categories.category_name')->where('product_name', 'like', "%$key%")->orWhere('description', 'like', '%'.$key.'%')->orWhere('type_name', 'like', '%'.$key.'%')->join('types', 'products.products_type_id_foreign', '=', 'types.id')->join('categories', 'categories.id', '=', 'types.types_categories_id_foreign')->where('products_type_id_foreign', $typeid)->get();
        
        if($product != null) {
            foreach ($product as $p) {
                $img = Images::where('images_product_id_foreign', $p->id)->first();
                if($img != null) {
                     $p['url_images'] = Cloudder::show('images/'.$img->url, array("width" => 250, "height" => 250, "crop" => "fill"));
                }
                else {
                    $p['url_images'] = Cloudder::show('images/no-image_bi4whx');
                }
               
            }
        }

    	return response()->json($product);    
    }

    // public function searchByPrice(Request $request) {
        
    // }

}
