<?php

namespace App\Repositories;

use App\Models\Reservation;
use App\Repositories\Core\CoreRepository;

class ReservationRepository extends CoreRepository
{
    public function __construct(Reservation $model)
    {
        parent::__construct($model);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function isTableAvailable(int $deskId, string $reservationDate, string $startTime, string $endTime)
    {
        return !$this->model->where('desk_id', $deskId)
            ->where('reservation_date', $reservationDate)
            ->where('start_time', '<', $endTime)
            ->where('end_time', '>', $startTime)
            ->exists();
    }

    public function updateStatus(string $data, int $id)
    {
        $reservation = $this->findByField('id', $id);
        $reservation->update(['status' => $data]);
        return $reservation->refresh();
    }
}
