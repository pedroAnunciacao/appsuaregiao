<?php

namespace App\Services;

use App\Models\Report;
use Carbon\Carbon;

class ReportService
{
    protected Report $repository;

    public function __construct(Report $repository)
    {
        $this->repository = $repository;
    }

    public function all($filter = null)
    {
        $query = $this->repository->query();

        if ($filter === 'recent') {
            $query->where('created_at', '>=', Carbon::now()->subDays(10));
        }

        if ($filter === 'pending') {
            $query->where('created_at', '<', Carbon::now()->subDays(10))
                ->whereNull('resolved_at');
        }

        return $query->get();
    }

    public function find($id)
    {
        return $this->repository->with('post')->where('id', $id)->get()[0];
    }


    public function reject($id)
    {
        $report = $this->repository->where('id', $id)->get()[0];
        $report->update([
            'status' => 'dismissed',
        ]);
        return $report;
    }


    public function approved($id)
    {
        $report = $this->repository->with('post')->where('id', $id)->get()[0];
        $report->update([
            'status' => 'reviewed',
        ]);
        $report->post->update(['status' => 'blocked']);
        return $report;
    }
}
