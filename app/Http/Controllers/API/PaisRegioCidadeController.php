<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\PaisRegioCidadeService;
use App\Http\Resources\PaisRegioCidade\PaisRegioCidadeResource;
use Illuminate\Http\Request;

class PaisRegioCidadeController extends Controller
{
    protected PaisRegioCidadeService $service;

    public function __construct(PaisRegioCidadeService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        try {
            $regiaoId = $request->query('regiao_id');
            $perPage = $request->query('per_page', 5000);
            
            $cidades = $this->service->index($regiaoId, $perPage);
            return PaisRegioCidadeResource::collection($cidades);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erro ao buscar cidades.'], 500);
        }
    }
}
