<?php

namespace App\Services;

use App\Models\PaisesContinente;

class ContinenteService
{
    protected PaisesContinente $repository;

    public function __construct(PaisesContinente $repository)
    {
        $this->repository = $repository;
    }

    public function index()
    {
        return $this->repository
            ->orderBy('nome')
            ->with('paises')
            ->get();
    }
}
