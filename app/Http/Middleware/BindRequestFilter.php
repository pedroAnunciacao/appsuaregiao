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
                $params = [
                    'user_id' => $user->id,
                    'pais_regiao_cidade_id' => $user->pais_regiao_cidade_id
                ];
                $request->merge($params);
            }
        }

        return $next($request);
    }
}
