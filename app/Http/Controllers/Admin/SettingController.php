<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;

class SettingController extends Controller
{
    public function show($key)
    {
        $setting = Setting::where('key', $key)->first();
        return response()->json(['url' => $setting->value ?? null]);
    }

    public function update(Request $request, $key)
    {
        $value = $request->input('url') ?? $request->input('value');
        $setting = Setting::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );

        return response()->json(['success' => true, 'url' => $setting->value]);
    }
}

