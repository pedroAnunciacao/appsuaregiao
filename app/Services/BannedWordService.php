<?php

namespace App\Services;

use App\Models\BannedWord;

class BannedWordService
{
    protected BannedWord $repository;

    public function __construct(BannedWord $repository)
    {
        $this->repository = $repository;
    }

    public function all()
    {
        return $this->repository->all();
    }

    public function store(array $data)
    {
        return $this->repository->create($data);
    }

    public function find($id)
    {
        return $this->repository->findOrFail($id);
    }

    public function update($id, array $data)
    {
        $word = $this->find($id);
        $word->update($data);
        return $word;
    }

    public function destroy($id)
    {
        $word = $this->find($id);
        return $word->delete();
    }
}
