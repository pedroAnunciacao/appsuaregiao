<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\PostService;
use App\Http\Resources\Post\PostResource;
use App\Http\Requests\Post\StorePostRequest;
use App\Http\Requests\Post\UpdatePostRequest;

class PostController extends Controller
{
    protected PostService $service;

    public function __construct(PostService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $post = $this->service->index();
        return PostResource::collection($post);
    }

    public function store(StorePostRequest $request)
    {
        try {
            $post = $this->service->store($request);
            return new PostResource($post);
        } catch (\Exception $e) {
            dd($e);
            return response()->json(['message' => 'Erro ao criar post.'], 500);
        }
    }

    public function update(UpdatePostRequest $request)
    {
        $post = $this->service->update($request);
        return new PostResource($post);
    }

    public function delete(int $id)
    {
        $post = $this->service->destroy($id);
        return new PostResource($post);
    }


    public function show(int $id)
    {
        $post = $this->service->show($id);
        return new PostResource($post);
    }


    public function restore(int $id)
    {
        $post = $this->service->restore($id);
        return new PostResource($post);
    }
}
