<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use Illuminate\Http\Request;
use App\Http\Resources\Reports\ReportResource;

class ReportController extends Controller
{
    protected ReportService $service;

    public function __construct(ReportService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $reports = $this->service->all($request->query('filter'));
        return view('reports.index', ['reports' => ReportResource::collection($reports)]);
    }

    public function show($id)
    {
        $report = $this->service->find($id);
        return view('reports.show', compact('report'));
    }


        public function reject($id)
    {
        $report = $this->service->reject($id);
        return view('reports.show', compact('report'));
    }


        public function approved($id)
    {
        $report = $this->service->approved($id);
        return view('reports.show', compact('report'));
    }

}
