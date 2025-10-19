<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\BannedWord;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function index(){
        return Post::with('user','comments','likes')->latest()->paginate(15);
    }

    public function store(Request $r){
        $r->validate([
            'text'=>'nullable|string',
            'media'=>'nullable|file|mimes:jpeg,png,jpg,mp4,mov|max:20480',
            'city'=>'required|string'
        ]);

        if($r->user()->city && strtolower($r->user()->city) !== strtolower($r->city)){
            return response()->json(['error'=>'Você só pode postar na sua cidade selecionada.'],422);
        }

        $banned = BannedWord::pluck('word')->toArray();
        foreach($banned as $w){
            if($r->text && stripos($r->text,$w) !== false)
                return response()->json(['error'=>'Texto contém palavra imprópria.'],422);
        }

        $post = new Post();
        $post->user_id = $r->user()->id;
        $post->text = $r->text;
        $post->city = $r->city;
        $post->state = $r->state ?? null;

        if($r->hasFile('media')){
            $file = $r->file('media');
            $path = $file->store('public/media');
            $post->media_path = Storage::url($path);

            if(str_starts_with($file->getMimeType(),'image')){
                $img = Image::make($file)->fit(600,600)->encode('jpg',75);
                $thumbPath = 'public/media/thumbs/'.uniqid().'.jpg';
                Storage::put($thumbPath,(string)$img);
                $post->thumbnail_path = Storage::url($thumbPath);
            }
        }

        $post->save();
        return response()->json($post,201);
    }
}

