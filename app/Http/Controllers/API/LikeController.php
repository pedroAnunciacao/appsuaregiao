<?php

namespace App\Http\Controllers\API;

use App\Services\LikeService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LikeController extends Controller
{
    protected LikeService $service;

    public function __construct(LikeService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $likes = $this->service->all();
        return response()->json($likes);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'post_id' => 'required|exists:posts,id',
            'user_id' => 'required|integer',
        ]);

        $like = $this->service->store($data);
        return response()->json($like, 201);
    }

    public function destroy(int $id)
    {
        $this->service->destroy($id);
        return response()->json(['message' => 'Like removido com sucesso']);
    }
}
