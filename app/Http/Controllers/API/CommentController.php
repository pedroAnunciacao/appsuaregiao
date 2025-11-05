<?php

namespace App\Http\Controllers\API;

use App\Services\CommentService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CommentController extends Controller
{
    protected CommentService $service;

    public function __construct(CommentService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $comments = $this->service->all();
        return response()->json($comments);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'post_id' => 'required|exists:posts,id',
            // 'user_id' => 'required|integer',
            'body' => 'required|string|max:1000',
        ]);

        $comment = $this->service->store($data);
        return response()->json($comment, 201);
    }

    public function show(int $id)
    {
        $comment = $this->service->show($id);
        return response()->json($comment);
    }

    public function update(Request $request, int $id)
    {
        $data = $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $comment = $this->service->update($id, $data);
        return response()->json($comment);
    }

    public function destroy(int $id)
    {
        $this->service->destroy($id);
        return response()->json(['message' => 'Comentário removido com sucesso']);
    }
}
