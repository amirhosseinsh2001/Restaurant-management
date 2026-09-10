<?php

namespace App\Repositories;

use App\Models\MenuItem;
use App\Repositories\Core\CoreRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Collection;

class MenuItemRepository extends CoreRepository
{
    public function __construct(MenuItem $model)
    {
        parent::__construct($model);
    }

    public function getByIds(array $ids)
    {
        return $this->model->whereIn('id', $ids)->get();
    }

    public function create($data)
    {
        return $this->model->create($data);
    }

    public function update(array $data, int $id)
    {
        $menuItem = $this->findByField("id", $id);
        if (!$menuItem)
        {
            return null;
        }
        $menuItem->update($data);
        return $menuItem->refresh();
    }
    public function destroy(int $id)
    {
        $menuItem = $this->findByField("id", $id);
        return $this->model->destroy($menuItem->id);
    }
}
