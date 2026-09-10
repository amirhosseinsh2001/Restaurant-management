<?php

namespace App\Repositories;

use App\Models\Category;
use App\Repositories\Core\CoreRepository;

class CategoryRepository extends CoreRepository
{
    public function __construct(Category $model)
    {
        parent::__construct($model);
    }

    public function create($data)
    {
        return Category::create($data);
    }
    public function update(array $data, int $id)
    {
        $category = $this->findByField("id", $id);
        if (!$category)
        {
            return null;
        }
        $category->update($data);
        return $category->refresh();
    }
    public function destroy(int $id)
    {
        $category = $this->findByField("id", $id);
        return Category::destroy($category->id);
    }
}
