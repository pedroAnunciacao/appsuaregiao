<?php

namespace App\Services;

use App\Models\Comment;
use Illuminate\Support\Facades\Log;

class CommentService
{
    protected Comment $repository;

    public function __construct(Comment $repository)
    {
        $this->repository = $repository;
    }

    public function all()
    {
        return $this->repository->with('post')->get();
    }

    public function store(array $data)
    {
        try {
            return $this->repository->create($data);
        } catch (\Exception $e) {
            Log::error('Erro ao criar comentário: ' . $e->getMessage());
            throw $e;
        }
    }

    public function show(int $id)
    {
        return $this->repository->with('post')->findOrFail($id);
    }

    public function update(int $id, array $data)
    {
        $comment = $this->repository->findOrFail($id);
        $comment->update($data);
        return $comment;
    }

    public function destroy(int $id)
    {
        $comment = $this->repository->findOrFail($id);
        return $comment->delete();
    }
}
