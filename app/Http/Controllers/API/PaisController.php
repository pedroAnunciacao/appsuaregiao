<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\PaisService;
use App\Http\Resources\Pais\PaisResource;
use Illuminate\Http\Request;

class PaisController extends Controller
{
    protected PaisService $service;

    public function __construct(PaisService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        try {
            $continenteId = $request->query('continente_id');
            $paises = $this->service->index($continenteId);
            return PaisResource::collection($paises);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erro ao buscar países.'], 500);
        }
    }
}
