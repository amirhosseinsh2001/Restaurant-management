<?php

namespace App\Repositories\Core;
use App\Interfaces\Core\CoreInterface;
use Illuminate\Database\Eloquent\Model;

class CoreRepository implements CoreInterface
{
    protected Model $model;
    public function __construct(Model $model)
    {
        $this->model = $model;
    }
    public function findByField($field, $value)
    {
        return $this->model->where($field, $value)->first();
    }
    public function getAllList()
    {
        return $this->model->orderBy('created_at', 'asc')->paginate(10);
    }
}
