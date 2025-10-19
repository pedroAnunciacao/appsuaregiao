<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    /**
     * Banir usuário
     */
    public function banUser(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->is_banned = true;
        $user->ban_reason = $request->input('reason');
        $user->ban_expires_at = $request->input('expires_at'); // null para permanente
        $user->save();
        return response()->json(['success' => true]);
    }

    /**
     * Desbanir usuário
     */
    public function unbanUser($id)
    {
        $user = User::findOrFail($id);
        $user->is_banned = false;
        $user->ban_reason = null;
        $user->ban_expires_at = null;
        $user->save();
        return response()->json(['success' => true]);
    }

    /**
     * Remover usuário (soft delete)
     */
    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return response()->json(['success' => true]);
    }

    /**
     * Listar usuários
     */
    public function listUsers()
    {
        return User::all();
    }

    /**
     * Atualiza localização do usuário logado
     */
    public function setLocation(Request $request)
    {
        $user = $request->user();
        $user->country = $request->input('country');
        $user->state = $request->input('state');
        $user->city = $request->input('city');
        // Se quiser salvar região, adicione $user->region = $request->input('region');
        $user->save();
        return response()->json(['success' => true]);
    }

    /**
     * Retorna os dados do usuário logado, incluindo cidade e país
     * e verifica se o usuário está em sua própria cidade
     */
    public function getUserData(Request $request)
{
    $user = $request->user();

    // Se não houver usuário via cookie, tenta autenticar pelo token Bearer
    if (!$user) {
        $token = $request->bearerToken();
        if ($token) {
            $accessToken = \Laravel\Sanctum\PersonalAccessToken::findToken($token);
            if ($accessToken) {
                $user = $accessToken->tokenable;
                auth()->setUser($user);
            }
        }
    }

    if (!$user) {
        return response()->json([
            'error' => 'Usuário não autenticado'
        ], 401);
    }

    $selectedCity = $request->query('selectedCity', null);

    $userCity = $user->city ? strtolower(trim($user->city)) : null;
    $normalizedSelectedCity = $selectedCity ? strtolower(trim($selectedCity)) : null;

    $isInOwnCity = ($userCity && $normalizedSelectedCity && $userCity === $normalizedSelectedCity);

    return response()->json([
        'id' => $user->id,
        'name' => $user->name,
        'email' => $user->email,
        'city' => $user->city,
        'state' => $user->state,
        'country' => $user->country,
        'isInOwnCity' => $isInOwnCity,
        'isAdmin' => $user->is_admin ?? false
    ]);
}
    /**
     * Retorna os posts da cidade do usuário
     */
    public function getUserCityPosts(Request $request)
    {
        $user = $request->user();

        if (!$user->city) {
            return response()->json(['error' => 'Usuário não possui cidade definida'], 400);
        }

        $posts = Post::with('user', 'comments', 'likes')
                    ->where('city', $user->city)
                    ->latest()
                    ->paginate(15);

        return response()->json($posts);
    }
}
