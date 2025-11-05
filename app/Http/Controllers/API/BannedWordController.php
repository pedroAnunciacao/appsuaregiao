<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\BannedWordService;
use Illuminate\Http\Request;
use App\Http\Resources\BannedWord\BannedWordResource;

class BannedWordController extends Controller
{
    protected BannedWordService $service;

    public function __construct(BannedWordService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $words = $this->service->all();
        return view('banned_words.index', ['bannedWords' => BannedWordResource::collection($words)]);
    }

    public function create()
    {
        return view('banned_words.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate(['word' => 'required|string|max:255']);
        $this->service->store($data);
        return redirect('/banned-words')->with('success', 'Palavra criada!');
    }

    public function edit($id)
    {
        $word = $this->service->find($id);
        return view('banned_words.edit', ['bannedWord' => $word]);
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate(['word' => 'required|string|max:255']);
        $this->service->update($id, $data);
        return redirect('/banned-words')->with('success', 'Atualizada!');
    }

    public function destroy($id)
    {
        $this->service->destroy($id);
        return redirect('/banned-words')->with('success', 'Removida!');
    }
}
