<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\PaisesRegiaoService;
use App\Http\Resources\PaisesRegiao\PaisesRegiaoResource;
use Illuminate\Http\Request;

class PaisesRegiaoController extends Controller
{
    protected PaisesRegiaoService $service;

    public function __construct(PaisesRegiaoService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        try {
            $paisId = $request->query('pais_id');
            $regioes = $this->service->index($paisId);
            return PaisesRegiaoResource::collection($regioes);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erro ao buscar regiões.'], 500);
        }
    }
}
