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
        if (!$desk) {
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

    private function baseAvailableQuery($date, $start, $end, $guestCount)
    {
        return $this->model->query()
            ->where('status', 'available')
            ->where('capacity', '>=', $guestCount)
            ->availableBetween($date, $start, $end);
    }

    public function findAvailableTable(string $reservationDate, string $startTime, string $endTime, int $guestCount)
    {
//        return $this->model->query()
//            ->where('capacity', '>=', $guestCount)
//            ->whereDoesntHave('reservations', function ($query) use ($reservationDate, $startTime, $endTime) {
//                $query->where('reservation_date', $reservationDate)
//                    ->where('start_time', '<', $endTime)
//                    ->where('end_time', '>', $startTime);
//            })
//            ->orderBy('capacity')
//            ->lockForUpdate()
//            ->first();
        return DB::transaction(function () use ($reservationDate, $startTime, $endTime, $guestCount) {

            $desk = $this->baseAvailableQuery($reservationDate, $startTime, $endTime, $guestCount)
                ->orderBy('capacity')
                ->lockForUpdate()
                ->first();

            return $desk;
        });
    }

    public function suggestAvailableDesks(string $reservationDate, string $startTime, string $endTime, int $guestCount)
    {
//        return $this->model->query()
//            ->where('capacity', '>=', $guestCount)
//            ->where('status', 'available')
//            ->whereDoesntHave('reservations', function ($query) use ($reservationDate, $startTime, $endTime) {
//                $query->where('reservation_date', $reservationDate)
//                    ->where('start_time', '<', $endTime)
//                    ->where('end_time', '>', $startTime);
//            })
//            ->orderByRaw('capacity - ? ASC', [$guestCount])
//            ->select('id', 'desk_number', 'capacity')
//            ->limit(5)
//            ->get();
//        return $this->baseAvailableQuery($reservationDate, $startTime, $endTime, $guestCount)
//            ->orderByRaw('capacity - ? ASC', [$guestCount])
//            ->select('id', 'desk_number', 'capacity')
//            ->limit(5)
//            ->get();
        return $this->baseAvailableQuery($reservationDate, $startTime, $endTime, $guestCount)
            ->where('capacity', '<=', $guestCount + 4)
            ->select([
                'id',
                'desk_number',
                'capacity',

                DB::raw("
                      (
                        (
                            ABS(capacity - {$guestCount}) * 2
                            + (capacity - {$guestCount}) * 3
                        ) as score
                 ")
            ])
            ->orderBy('score')
            ->limit(5)
            ->get();
    }
}
