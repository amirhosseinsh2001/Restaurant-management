<?php

namespace App\Services;

use App\Exceptions\ReservationException;
use App\Http\Resources\deskSuggestionResource;
use App\Models\Reservation;
use App\Repositories\Core\CoreRepository;
use App\Repositories\DeskRepository;
use App\Repositories\ReservationRepository;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ReservationService extends CoreRepository
{
    public function __construct(protected ReservationRepository $reservationRepository, protected DeskRepository $deskRepository, Reservation $model)
    {
        parent::__construct($model);
    }

    public function makeReservation(array $data, int $userId)
    {
        try {
            DB::beginTransaction();
            $start = Carbon::parse($data['start_time']);
            $duration = $data['duration'] ?? 120;
            $end = (clone $start)->addMinutes($duration);
            if (isset($data['desk_id'])) {
                $desk = $this->deskRepository->findByField('id', $data['desk_id']);
                if ($desk->capacity < $data['guests_count']) {
                    throw new ReservationException("ظرفیت میز کافی نیست.", 422);
                }
                //TODO: Add suggestion for below condition.
                if ($desk->capacity > $data['guests_count']) {
                    $suggestions = $this->getAvailableDesks($data);
                    throw ReservationException::deskCapacityNotFit(
                        deskSuggestionResource::collection($suggestions)->resolve(),
                    );
                }
                $isAvailable = $this->reservationRepository->isTableAvailable(
                    $data['desk_id'],
                    $data['reservation_date'],
                    $start->format('H:i:s'),
                    $end->format('H:i:s'),
                );
                if (!$isAvailable) {
//                    $suggestions = $this->deskRepository->suggestAvailableDesks(
//                        $data['reservation_date'],
//                        $start->format('H:i:s'),
//                        $end->format('H:i:s'),
//                        $data['guests_count']
//                    );
                    $suggestions = $this->getAvailableDesks($data);
                    throw ReservationException::deskReserved(
                        deskSuggestionResource::collection($suggestions)->resolve(),
                    );
                }
                $deskId = $data['desk_id'];
            } else {
                $desk = $this->deskRepository->findAvailableTable(
                    $data['reservation_date'],
                    $start->format('H:i:s'),
                    $end->format('H:i:s'),
                    $data['guests_count'],
                );
                if (!$desk) {
                    throw ReservationException::deskIsNotAvaiable();
                }
                $deskId = $desk->id;
            }
            $data = [
                'user_id' => $userId,
                'desk_id' => $deskId,
                'reservation_date' => $data['reservation_date'],
                'start_time' => $start,
                'end_time' => $end,
                'guests_count' => $data['guests_count'],
                'status' => 'pending',
            ];
            $reservation = $this->reservationRepository->create($data);
            DB::commit();
            return $reservation;
        }catch (\throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function getAvailableDesks(array $data)
    {
        $start = Carbon::parse($data['start_time']);
        $duration = $data['duration'] ?? 120;
        $end = (clone $start)->addMinutes($duration);
        return $this->deskRepository->suggestAvailableDesks(
            $data['reservation_date'],
            $start->format('H:i:s'),
            $end->format('H:i:s'),
            $data['guests_count']
        );
    }

    public function markAsConfirmed(int $id)
    {
        $reservation = $this->reservationRepository->findByField('id', $id);
        if ($reservation->status !== 'pending') {
            throw new ReservationException('فقط رزرو در حال انتظار تایید می شود');
        }
        $res = $this->reservationRepository->updateStatus('confirmed', $reservation->id);
        return $res;
    }

    public function markAsCompleted(int $id)
    {
        $reservation = $this->reservationRepository->findByField('id', $id);
        if ($reservation->status !== 'confirmed') {
            throw new ReservationException('فقط رزرو پذیرفته شده تکمیل می شود');
        }
        $res = $this->reservationRepository->updateStatus('completed', $reservation->id);
        return $res;
    }

    public function markAsCancelled(int $id)
    {
        $reservation = $this->reservationRepository->findByField('id', $id);
        if ($reservation->status !== 'completed') {
            throw new ReservationException('فقط سفارش در حال انتظار قابلیت لغو دارد');
        }
        $res = $this->reservationRepository->updateStatus('cancelled', $reservation->id);
        return $res;
    }
}
