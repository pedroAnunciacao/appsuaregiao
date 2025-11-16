<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\PaisesRegiaoService;
use App\Http\Resources\Regiao\RegiaoResource;
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
            $regioes = $this->service->index();
            return RegiaoResource::collection($regioes);
        } catch (\Exception $e) {
            dd($e);
            return response()->json(['message' => 'Erro ao buscar regiões.'], 500);
        }
    }
}
