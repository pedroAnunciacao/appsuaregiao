<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;

class BindRequestFilter
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->header('X-Content')) {
            $user = User::find($request->header('X-Content'));

            if ($user) {
                if ($request->header('X-region_selected')) {
                    $paisRegiaoCidadeId =  $request->header('X-region_selected');
                } else {
                    $paisRegiaoCidadeId = $user->pais_regiao_cidade_id;
                }

                $params = [
                    'user_id' => $user->id,
                    'pais_regiao_cidade_id' => $paisRegiaoCidadeId
                ];
                $request->merge($params);
            }
        }

        return $next($request);
    }
}
