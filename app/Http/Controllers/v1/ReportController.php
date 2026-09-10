<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\bestReportRequest;
use App\Services\ReportService;

class ReportController extends Controller
{
    public function __construct(protected ReportService $reportService){}

    public function report_best_desks(bestReportRequest $request)
    {
        $data = $request->validated();
        $result = $this->reportService->bestSellerDesks($data);
        return $this->successResponse($result, __('messages.reports.best_seller_report'), 200);
    }

    public function report_best_items(bestReportRequest $request)
    {
        $data = $request->validated();
        $result = $this->reportService->bestSellerItems($data);
        return $this->successResponse($result, __('messages.reports.best_seller_report'), 200);
    }
}
