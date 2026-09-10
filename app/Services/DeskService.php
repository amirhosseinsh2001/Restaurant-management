<?php

namespace App\Services;

use App\Exceptions\DeskException;
use App\Models\Desk;
use App\Repositories\DeskRepository;
use Illuminate\Support\Facades\Auth;

class DeskService
{
    public function __construct(protected DeskRepository $repository){}
    public function getAll()
    {
//        $desks = Desk::paginate(10);
//        return $desks;
        return $this->repository->getAllList();
    }

    public function create(array $data)
    {
        $deskExist = $this->repository->findByField('desk_number', $data['desk_number']);
        if ($deskExist) {
            throw DeskException::deskExist();
        }
        $desk = $this->repository->create($data);
        return $desk;
    }

    public function update(array $data, int $id)
    {
        $desk = $this->repository->update($data, $id);
        if (!$desk) {
            throw DeskException::deskNotFound();
        }
        return $desk;
    }

    public function delete(int $id)
    {
        $desk = $this->repository->destroy($id);
        return $desk;
    }
}
