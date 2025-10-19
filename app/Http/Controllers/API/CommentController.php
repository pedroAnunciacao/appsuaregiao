<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Comment;
use App\Models\Post;

class CommentController extends Controller
{
    // Listar comentários de um post
    public function index(Post $post){
        return $post->comments()->with('user')->latest()->get();
    }

    // Criar comentário em um post
    public function store(Request $r, Post $post){
        $r->validate(['body'=>'required|string']);

        $comment = $post->comments()->create([
            'user_id'=>$r->user()->id,
            'body'=>$r->body
        ]);

        return response()->json($comment,201);
    }

    // Lista todos os comentários (admin)
    public function all()
    {
        return response()->json(Comment::with('user', 'post')->latest()->get());
    }

    // Remove um comentário (admin)
    public function destroy($id)
    {
        $comment = Comment::findOrFail($id);
        $comment->delete();
        return response()->json(['success' => true]);
    }
}
