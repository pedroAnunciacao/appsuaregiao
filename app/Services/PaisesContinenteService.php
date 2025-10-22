<?php

namespace App\Services;

use App\Models\PaisesContinente;

class PaisesContinenteService
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
            ->get();
    }
}
