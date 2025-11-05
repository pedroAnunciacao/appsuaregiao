<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Log;

class UserService
{
    protected User $repository;

    public function __construct(User $repository)
    {
        $this->repository = $repository;
    }

    public function all($filters = [])
    {
        $query = $this->repository->query();

        if (isset($filters['is_banned'])) {
            $query->where('is_banned', $filters['is_banned']);
        }

        return $query->get();
    }

    public function store(array $data)
    {
        return $this->repository->create($data);
    }

    public function find(int $id)
    {
        return $this->repository->findOrFail($id);
    }

    public function update(int $id, array $data)
    {
        $user = $this->find($id);
        $user->update($data);
        return $user;
    }

    public function destroy(int $id)
    {
        $user = $this->find($id);
        return $user->delete();
    }

    public function restore(int $id)
    {
        return $this->repository->withTrashed()->find($id)?->restore();
    }

    public function ban(int $id, string $reason, ?string $expires = null)
    {
        $user = $this->find($id);
        $user->update([
            'is_banned' => true,
            'ban_reason' => $reason,
            'ban_expires_at' => $expires,
        ]);
        return $user;
    }

    public function unban(int $id)
    {
        $user = $this->find($id);
        $user->update([
            'is_banned' => false,
            'ban_reason' => null,
            'ban_expires_at' => null,
        ]);
        return $user;
    }
}
