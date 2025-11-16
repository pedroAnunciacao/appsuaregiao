<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\ContinenteService;
use App\Http\Resources\Continentes\ContinentesResource;

class ContinenteController extends Controller
{
    protected ContinenteService $service;

    public function __construct(ContinenteService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        try {
            $continentes = $this->service->index();
            return ContinentesResource::collection($continentes);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erro ao buscar continentes.'], 500);
        }
    }
}
