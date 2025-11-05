<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\StatisticsService;

class StatisticsController extends Controller
{
    protected StatisticsService $service;

    public function __construct(StatisticsService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $data = $this->service->getDashboardData();
        return view('admin.dashboard', compact('data'));
    }
}
