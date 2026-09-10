<?php

namespace App\Services;

use App\Repositories\ReportRepository;
use Illuminate\Support\Carbon;

class ReportService
{
    public function __construct(protected ReportRepository $reportRepository){}

    public function bestSellerDesks(array $data)
    {
        $start = Carbon::parse($data['start_date']);
        $end = Carbon::parse($data['end_date']);
        return $this->reportRepository->bestDesksReport(
            $start->format('Y-m-d'),
            $end->format('Y-m-d'),
        );
    }

    public function bestSellerItems(array $data)
    {
        $start = Carbon::parse($data['start_date']);
        $end = Carbon::parse($data['end_date']);
        return $this->reportRepository->bestMenuItemsReport(
            $start->format('Y-m-d'),
            $end->format('Y-m-d'),
        );
    }
}
