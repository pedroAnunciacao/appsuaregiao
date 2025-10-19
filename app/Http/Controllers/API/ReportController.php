<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Report;

class ReportController extends Controller
{
    public function store(Request $r, Post $post){
        $r->validate(['reason'=>'required|string']);

        $report = $post->reports()->create([
            'user_id'=>$r->user()->id,
            'reason'=>$r->reason
        ]);

        return response()->json($report,201);
    }
}
