<?php

namespace App\Services;

use App\Models\PaisRegioCidade;

class PaisRegioCidadeService
{
    protected PaisRegioCidade $repository;

    public function __construct(PaisRegioCidade $repository)
    {
        $this->repository = $repository;
    }

    public function index($regiaoId = null, $perPage = 50)
    {
        $query = $this->repository->query();

        if ($regiaoId) {
            $query->where('regiao_id', $regiaoId);
        }

        return $query->orderBy('nome')->paginate($perPage);
    }
}
