<?php

namespace App\Services;

use App\Exceptions\CategoryException;
use App\Repositories\CategoryRepository;

class CategoryService
{
    public function __construct(protected CategoryRepository $repository){}
    public function getAll()
    {
        return $this->repository->getAllList();
    }

    public function create(array $data)
    {
        $categoryExist = $this->repository->findByField('name', $data['name']);
        if ($categoryExist) {
            throw CategoryException::categoryExist();
        }
        $category = $this->repository->create($data);
        return $category;
    }

    public function update(array $data, int $id)
    {
        $category = $this->repository->update($data, $id);
        if (!$category) {
            throw CategoryException::categoryNotFound();
        }
        return $category;
    }

    public function delete(int $id)
    {
        $category = $this->repository->destroy($id);
        return $category;
    }
}
