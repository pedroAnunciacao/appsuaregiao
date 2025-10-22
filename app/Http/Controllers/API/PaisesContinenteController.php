<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\PaisesContinenteService;
use App\Http\Resources\PaisesContinente\PaisesContinenteResource;

class PaisesContinenteController extends Controller
{
    protected PaisesContinenteService $service;

    public function __construct(PaisesContinenteService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        try {
            $continentes = $this->service->index();
            return PaisesContinenteResource::collection($continentes);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erro ao buscar continentes.'], 500);
        }
    }
}
