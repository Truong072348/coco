<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;
use App\Orders;
use App\Products;
use App\DetailsOrder;
use App\Histories;
use App\User;
use Cloudder;

class OrderController extends Controller
{
    
	public function getOrder($orderid){
		while(Orders::where('id', $orderid)->exists()) {
			$order = Orders::where('id', $orderid)->first();
            $user = User::where('id', $order->orders_user_id_foreign)->first();
            $img = Cloudder::show('avatar/'.$user->url_images, array("width" => 250, "height" => 250, "crop" => "fill"));
            $partner['name'] =  $user->name;
            $partner['avatar'] = $img; 

			return response()->json(['order' => $order, 'partner' => $partner]);
		}

		return response()->json(['status' => false]);
	}

	public function getOrderByUser($userid){
		while(Orders::where('orders_user_id_foreign', $userid)->exists()) {
			$order = Orders::where('orders_user_id_foreign', $userid)->get();

			return response()->json($order);
		}

		return response()->json(['status' => false]);
	}

	public function getDetailOrder($orderid){
		while(DetailsOrder::where('details_order_oders_id_foreign', $orderid)->exists()) {
			$order = DetailsOrder::where('details_order_oders_id_foreign', $orderid)->get();

			return response()->json($order);
		}

		return response()->json(['status' => false]);
	}

    public function store(Request $request, $user){
    	$validator = Validator::make($request->all(),
            [
                'adress' => 'required',
                'costs'=>'required'
            ]
        );

        if ($validator->fails()) {
            return response()->json(
                [
                    'error' => $validator->errors()
                ], 400);
        }
    	
        $order = new Orders;
        $order->orders_user_id_foreign = $user;
        $order->coupon = '0';
        $order->adresss = $request['adress'];
        if($request['note'] === null) {
            $order->note = 'Tiêu chuẩn';
        } else {
            $order->note = $request['note'];
        }

        $order->costs = $request['costs'];
        $order->status = 'Đang xử lý';
        $order->save();

        $history = new Histories;
        $history->histories_order_id_foreign = $order->id;
        $history->histories_user_id_foreign = $user;
        $history->save();

    	return response()->json($order);
    }

    public function updateStatusOrder($orderid, $statusid){
        while (Orders::where('id', $orderid)->exists()) {
            $order = Orders::find($orderid);
            switch ($statusid) {
                case '1':
                    $order->status = 'Đang gửi hàng';
                    $order->update();
                    break;
                 case '2':
                    $order->status = 'Đã nhận hàng';
                    $order->update();
                case '3':
                    $order->status = 'Đã hủy';
                    $order->update();
                    break;
                case '4':
                    $order->status = 'Hoàn thành';
                    $order->update();
                    break;
                
                default:
                    $order->status = 'Đang xử lý';
                    $order->update();
                    break;
            }

            return response()->json(['status' => true]);
        }


        return response()->json(['status' => true]);
    }


    public function addProductOrder(Request $request, $order, $product){

    	$price = Products::where('id', $product)->first();
    	$detail = new DetailsOrder;
    	$detail->details_order_product_id_foreign = $product;
    	$detail->details_order_oders_id_foreign = $order;
    	$detail->quantity = $request->quantity;
    	$detail->price = $price->price;
    	$detail->save();

    	return response()->json(['status' => true]);
    }

    public function updateCostOrder($orderid) {

    	$detail_arr = DetailsOrder::where('details_order_oders_id_foreign', $orderid)->get();
    	$total = 0;
    	foreach ($detail_arr as $detail) {
       		if($detail['price'] != 0) {
       			$total += $detail->price * $detail->quantity;
       		}
       	}

       	while(Orders::where('id', $orderid)->exists()) {
			$order = Orders::where('id', $orderid)->first();

			$order->costs = $total;
			$order->update();
			return response()->json(['status' => true]);
		}


    	return response()->json(['status' => false]);
    }
}
