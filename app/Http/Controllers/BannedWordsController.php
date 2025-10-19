<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BannedWord;

class BannedWordsController extends Controller
{
    public function index()
    {
        return response()->json(BannedWord::all());
    }

    public function store(Request $request)
    {
        $word = BannedWord::create($request->all());
        return response()->json($word, 201);
    }

    public function destroy($id)
    {
        $word = BannedWord::findOrFail($id);
        $word->delete();
        return response()->json(['success' => true]);
    }
}
