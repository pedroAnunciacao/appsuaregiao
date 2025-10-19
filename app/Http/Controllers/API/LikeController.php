<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Like;

class LikeController extends Controller
{
    public function toggle(Request $r, Post $post){
        $like = $post->likes()->where('user_id',$r->user()->id)->first();

        if($like){
            $like->delete();
            return response()->json(['liked'=>false]);
        }

        $post->likes()->create(['user_id'=>$r->user()->id]);
        return response()->json(['liked'=>true]);
    }
}
