<?php

namespace App\Services;

use App\Models\Pais;

class PaisService
{
    protected Pais $repository;

    public function __construct(Pais $repository)
    {
        $this->repository = $repository;
    }

    public function index($continenteId = null)
    {
        $query = $this->repository->query();

        if ($continenteId) {
            $query->where('continente_id', $continenteId);
        }

        return $query->orderBy('nome')->get();
    }
}
