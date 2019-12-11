<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\storeImageRequest;
use App\Images;
use App\Products;
use Validator;
use Cloudder;


class ImageController extends Controller
{
    public function getImages($productid){
        $img_arr = Images::where('images_product_id_foreign', $productid)->get();
    	foreach ($img_arr as $image) {
       		if($image['url'] != null) {
       			$img = Cloudder::show('images/'.$image->url, array("width" => 250, "height" => 250, "crop" => "fill"));
       			$image->url = $img;
       		}
       	}

        return response()->json($img_arr);
    }

    public function store($productid, storeImageRequest $request){
    	
  		while (Products::where('id', $productid)->exists()) {
  			  $Images = new Images;
	        $Images->images_product_id_foreign = $productid;

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

	    	return response()->json(['status' => true]);
  		}

  		return response()->json(['status' => false]);
       
    }

     public function update(Request $request, $id){

    	$images = Images::find($id);
    	
    	if(empty($images)){
    		return response()->json(
                ['status' => false]);
    	}
    	
    	if($request->hasFile('url')){
            $file = $request->file('url');
            $name = $file->getClientOriginalName();
            
            $img = str_random(5)."_".$name;

            Cloudder::upload($file, 'Images/'.$img);

            $images->url = $img;
        }

    	$images->update();

    	return response()->json(['status' => true]);
    }

    public function delete($id) {
    	$images = Images::find($id);
    	Cloudder::destroyImage('images/'.$images->url);
    	$images->delete();

    	return response()->json(['status' => true]);
    }
}
