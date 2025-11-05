<?php

namespace App\Services;

use App\Models\Like;
use Illuminate\Support\Facades\Log;

class LikeService
{
    protected Like $repository;

    public function __construct(Like $repository)
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
            $existing = $this->repository
                ->where('post_id', $data['post_id'])
                ->where('user_id', $data['user_id'])
                ->first();

            if ($existing) {
                return $existing;
            }

            return $this->repository->create($data);
        } catch (\Exception $e) {
            Log::error('Erro ao criar like: ' . $e->getMessage());
            throw $e;
        }
    }

    public function destroy(int $id)
    {
        $like = $this->repository->findOrFail($id);
        return $like->delete();
    }
}
