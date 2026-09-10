<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\createCategoryRequest;
use App\Http\Requests\updateCategoryRequest;
use App\Services\CategoryService;

class CategoryController extends Controller
{
    public function __construct(protected CategoryService $categoryService)
    {
    }

    public function index()
    {
        $result = $this->categoryService->getAll();
        return $this->successResponse($result, __('messages.categories.categories_listed_successfully'), 200);
    }

    public function store(createCategoryRequest $request)
    {
        $data = $request->validated();
        $result = $this->categoryService->create($data);
        return $this->successResponse($result, __('messages.categories.category_created_successfully'), 201);
    }

    public function update(updateCategoryRequest $request, $id)
    {
        $data = $request->validated();
        $result = $this->categoryService->update($data, $id);
        return $this->successResponse($result, __('messages.categories.category_updated_successfully'), 200);

    }

    public function destroy($id)
    {
        $result = $this->categoryService->delete($id);
        return $this->successResponse($result, __('messages.categories.category_deleted_successfully'), 200);
    }
}
