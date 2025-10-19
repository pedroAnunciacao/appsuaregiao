<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Location;

class LocationController extends Controller
{
    public function index()
    {
        return Location::with('parent')->get()->map(function($l){
            return [
                'id' => $l->id,
                'name' => $l->name,
                'level' => $l->level,
                'parent_id' => $l->parent_id,
                'parent_name' => $l->parent?->name,
                'youtube_url' => $l->youtube_url
            ];
        });
    }

    public function store(Request $request)
    {
        $location = Location::create($request->only(['name','level','parent_id','youtube_url']));
        return response()->json($location);
    }

    public function update(Request $request, $id)
    {
        $location = Location::findOrFail($id);
        $location->update($request->only(['name','youtube_url']));
        return response()->json($location);
    }

    public function destroy($id)
    {
        Location::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }
}

