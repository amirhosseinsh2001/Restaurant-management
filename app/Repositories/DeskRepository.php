<?php

namespace App\Repositories;

use App\Models\Desk;
use App\Repositories\Core\CoreRepository;
use Illuminate\Support\Facades\DB;

class DeskRepository extends CoreRepository
{
    public function __construct(Desk $model)
    {
        parent::__construct($model);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }
    public function update(array $data, int $id)
    {
        $desk = $this->findByField("id", $id);
        if (!$desk)
        {
            return null;
        }
        $desk->update($data);
        return $desk->refresh();
    }
    public function destroy(int $id)
    {
        $desk = $this->findByField("id", $id);
        return $this->model->destroy($desk->id);
    }

    public function findAvailableTable(string $reservationDate, string $startTime, string $endTime, int $guestCount)
    {
        return $this->model->query()
            ->where('capacity', '>=', $guestCount)
            ->whereDoesntHave('reservations', function ($query) use ($reservationDate, $startTime, $endTime) {
                $query->where('reservation_date', $reservationDate)
                    ->where('start_time', '<', $endTime)
                    ->where('end_time', '>', $startTime);
            })
            ->orderBy('capacity')
            ->lockForUpdate()
            ->first();
    }

    public function suggestAvailableDesks(string $reservationDate, string $startTime, string $endTime, int $guestCount)
    {
        return $this->model->query()
            ->where('capacity', '>=', $guestCount)
            ->where('status', 'available')
            ->whereDoesntHave('reservations', function ($query) use ($reservationDate, $startTime, $endTime) {
                $query->where('reservation_date', $reservationDate)
                    ->where('start_time', '<', $endTime)
                    ->where('end_time', '>', $startTime);
            })
            ->orderBy('capacity')
            ->select('id', 'desk_number', 'capacity')
            ->limit(5)
            ->get();
    }
}
