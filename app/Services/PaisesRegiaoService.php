<?php

namespace App\Services;

use App\Models\PaisesRegiao;

class PaisesRegiaoService
{
    protected PaisesRegiao $repository;

    public function __construct(PaisesRegiao $repository)
    {
        $this->repository = $repository;
    }

    public function index($paisId = null)
    {
        $query = $this->repository->query();

        if ($paisId) {
            $query->where('pais_id', $paisId);
        }

        return $query->orderBy('nome')->get();
    }
}
