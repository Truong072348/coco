<?php
namespace App\Http\Controllers\api;
use App\User;
use App\Products;
use App\Comment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
/**
 * Class CommentsController
 * @package App\Http\Controllers\api
 */
class CommentsController extends Controller
{
    /**
     * @param Request $request
     * @param $userId
     * @param $productId
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveComment(Request $request, $userId, $productId) {
        if(Products::where('id', $productId)->exists() && User::where('id', $userId)->exists()) {
            if ('' == $request['comment']){
                return response()->json(['status' => false]);
            }
            $comments = new Comment;
            $comments->title = $request['title'];
            $comments->comments_user_id_foreign = $userId;
            $comments->comments_product_id_foreign = $productId;
            $comments->comment = $request['comment'];
            $comments->level = 1;
            $comments->save();
            return response()->json(['status' => true]);
        }
        else{
            return response()->json(['status' => false]);
        }
    }
}