<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;

use App\Services\UserService;
use Illuminate\Http\Request;
use App\Http\Resources\User\UserResource;

class UserController extends Controller
{
    protected UserService $service;

    public function __construct(UserService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $users = $this->service->all($request->query());
        return view('users.index', ['users' => UserResource::collection($users)]);
    }

    public function create()
    {
        return view('users.create');
    }


        public function login()
    {
        return view('auth.login');
    }


    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);

        $this->service->store($data);
        return redirect('/users')->with('success', 'Usuário criado!');
    }

    public function edit($id)
    {
        $user = $this->service->find($id);
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $dataRequest = $request->all();
        $data = [];
        $data['name']  = $dataRequest['name'];
        $data['email'] = $dataRequest['email'];
        if(!empty($dataRequest['password'])){
        $data['password'] = $dataRequest['password'];

        }
        $this->service->update($id, $data);
        return redirect('/users')->with('success', 'Usuário atualizado!');
    }

    public function destroy($id)
    {
        $this->service->destroy($id);
        return redirect('/users')->with('success', 'Usuário removido!');
    }

    public function restore($id)
    {
        $this->service->restore($id);
        return redirect('/users')->with('success', 'Usuário restaurado!');
    }

    public function ban(Request $request, $id)
    {
        $this->service->ban($id, $request->input('ban_reason'), $request->input('ban_expires_at'));
        return redirect('/users')->with('success', 'Usuário banido!');
    }

    public function unban($id)
    {
        $this->service->unban($id);
        return redirect('/users')->with('success', 'Usuário desbanido!');
    }
}
