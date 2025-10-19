<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Location;
use App\Models\PaisRegiaoCidade;

class LocationController extends Controller
{
    // Retorna o link do vídeo para país/estado/cidade
    public function getTvLink(Request $request)
    {
        $country = $request->query('country');
        $state = $request->query('state');
        $city = $request->query('city');

        // Busca por cidade, depois estado, depois país
        $query = Location::query();

        if ($country) {
            $query->where('name', $country)->where('level', 'country');
        }

        if ($state) {
            $query->orWhere(function ($q) use ($state) {
                $q->where('name', $state)->where('level', 'state');
            });
        }

        if ($city) {
            $query->orWhere(function ($q) use ($city) {
                $q->where('name', $city)->where('level', 'city');
            });
        }

        $location = $query->orderByDesc('level')->first();
        $url = $location ? $location->youtube_url : null;

        return response()->json(['url' => $url]);
    }

    // CRUD para locations (admin)
    public function index()
    {
        // Retorna todos os registros de pais_regiao_cidades
        return response()->json(PaisRegiaoCidade::all());
    }

    public function store(Request $request)
    {
        $location = PaisRegiaoCidade::create($request->all());
        return response()->json($location, 201);
    }

    public function update(Request $request, $id)
    {
        $location = PaisRegiaoCidade::findOrFail($id);
        $location->update($request->all());
        return response()->json($location);
    }

    public function destroy($id)
    {
        $location = PaisRegiaoCidade::findOrFail($id);
        $location->delete();
        return response()->json(['success' => true]);
    }

    // Retorna todos os países
    public function paises()
    {
        $paises = PaisRegiaoCidade::select('pais_id')->distinct()->get();
        return response()->json($paises);
    }

    // Retorna todos os estados distintos (Brasil)
    public function estados()
    {
        $estados = PaisRegiaoCidade::select('uf')->distinct()->pluck('uf');
        return response()->json($estados);
    }

    // Retorna cidades dinamicamente por país e estado
    public function cidades(Request $request, $uf = null)
    {
        $country = $request->query('country');
        $state = $request->query('state');

        // Permite buscar tanto por query string quanto por parâmetro de rota
        $uf = $uf ?? $state;

        if ($uf) {
            $cidades = PaisRegiaoCidade::where('uf', $uf)->pluck('nome');
        } elseif ($country) {
            $cidades = PaisRegiaoCidade::where('pais_id', $country)->pluck('nome');
        } else {
            $cidades = [];
        }

        return response()->json($cidades);
    }

    // Salva localização do usuário
    public function salvarLocalizacao(Request $request)
    {
        $user = auth()->user();
        $user->country = $request->country;
        $user->state = $request->state;
        $user->city = $request->city;
        $user->save();

        return response()->json(['success' => true]);
    }

    // Retorna todas as regiões de um país
    public function regioes($pais_id)
    {
        $regioes = PaisRegiaoCidade::where('pais_id', $pais_id)
            ->select('regiao_id')
            ->distinct()
            ->get();

        return response()->json($regioes);
    }

    // Salva ou atualiza o link do vídeo para país/estado/cidade
    public function saveTvLink(Request $request)
    {
        $country = $request->input('country');
        $state = $request->input('state');
        $city = $request->input('city');
        $url = $request->input('url');

        // Define nível e nome
        if ($city) {
            $level = 'city';
            $name = $city;
            $parent_id = null;
        } elseif ($state) {
            $level = 'state';
            $name = $state;
            $parent_id = null;
        } else {
            $level = 'country';
            $name = $country;
            $parent_id = null;
        }

        // Busca ou cria location
        $location = Location::firstOrNew(['name' => $name, 'level' => $level]);
        $location->youtube_url = $url;
        $location->parent_id = $parent_id;
        $location->save();

        return response()->json(['success' => true, 'location' => $location]);
    }
}
