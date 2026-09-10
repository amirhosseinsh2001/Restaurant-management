<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\bestReportRequest;
use App\Http\Requests\createReservationRequest;
use App\Http\Requests\suggestDeskRequest;
use App\Models\Reservation;
use App\Services\ReservationService;
use Illuminate\Support\Facades\Auth;

class ReservationController extends Controller
{
    public function __construct(protected ReservationService  $reservationService){}

    public function index()
    {
    }

    public function store(createReservationRequest $request)
    {
        $userId = Auth::user()->id;
        $result = $this->reservationService->makeReservation($request->validated(), $userId);
        return $this->successResponse($result, __('messages.reservations.desk_reserved_successfully'), 200);
    }

    public function suggest(suggestDeskRequest $request)
    {
        $data = $request->validated();
        $result = $this->reservationService->getAvailableDesks($data);
        return $this->successResponse($result, __('messages.reservations.available_desks_load_successfully'), 200);
    }

    public function confirmed($id)
    {
        $result = $this->reservationService->markAsConfirmed($id);
        return $this->successResponse($result, 'رزرو تایید شد', 200);
    }

    public function completed($id)
    {
        $result = $this->reservationService->markAsCompleted($id);
        return $this->successResponse($result, 'رزرو کامل شد', 200);
    }

    public function cancelled($id)
    {
        $result = $this->reservationService->markAsCancelled($id);
        return $this->successResponse($result, 'رزرو لفو شد', 200);
    }
}
