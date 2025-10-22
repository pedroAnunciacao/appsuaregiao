<?php

namespace App\Services;

use App\Http\Requests\Post\StorePostRequest;
use App\Http\Requests\Post\UpdatePostRequest;
use App\Models\Post;
use Illuminate\Http\Request;
use App\Models\BannedWord;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

class PostService
{
    protected Post $repository;

    public function __construct(Post $repository)
    {
        $this->repository = $repository;
    }

    public function index()
    {
        return $this->repository->with('user', 'comments', 'likes')->latest()->paginate(15);
    }

    public function store(StorePostRequest $request)
    {

        // if($r->user()->city && strtolower($r->user()->city) !== strtolower($r->city)){
        //     return response()->json(['error'=>'Você só pode postar na sua cidade selecionada.'],422);
        // }

        // $banned = BannedWord::pluck('word')->toArray();
        // foreach($banned as $w){
        //     if($r->text && stripos($r->text,$w) !== false)
        //         return response()->json(['error'=>'Texto contém palavra imprópria.'],422);
        // }

        $path = null;
        if ($request->hasFile('thumbnail_path') && $request->file('thumbnail_path')->isValid()) {
            $file = $request->file('thumbnail_path');
            $filename = time() . '_' . $file->getClientOriginalName();

            $file->storeAs('post', $filename, 'public');
            $path = asset('storage/post/' . $filename);
        }

        $postData = $request->validated();
        $postData['thumbnail_path'] = $path;
        $post = $this->repository->create($postData);

        return $post;
    }


    public function show(int $id)
    {
        $post = $this->repository->findOrFail($id);
        return $post->load(['user', 'comments', 'likes']);
    }


    public function update(UpdatePostRequest $request)
    {
        $path = null;
        if ($request->hasFile('thumbnail_path') && $request->file('thumbnail_path')->isValid()) {
            $file = $request->file('thumbnail_path');
            $filename = time() . '_' . $file->getClientOriginalName();

            $file->storeAs('post', $filename, 'public');
            $path = asset('storage/post/' . $filename);
        }

        $postData = $request->validated();
        $post = $this->repository->findOrFail($postData['id']);
        $post->update($postData);
        return $post;
    }

    public function destroy(int $id)
    {
        $post = $this->repository->findOrFail($id);
        $post->delete();
        return $post;
    }

    public function restore(int $id)
    {
        $post = $this->repository->withTrashed()->findOrFail($id);
        $post->restore();
        return $post;
    }
}
