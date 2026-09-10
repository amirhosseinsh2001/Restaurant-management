<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\createmenuRequest;
use App\Http\Requests\updatemenuRequest;
use App\Services\MenuItemService;



class MenuItemController extends Controller
{
    public function __construct(protected MenuItemService $menuItemService){}
    public function index()
    {
        $result = $this->menuItemService->getAll();
        return $this->successResponse($result, __('messages.menus.menus_listed_successfully'), 200);
    }
    public function store(createmenuRequest $request)
    {
        $data = $request->validated();
        $result = $this->menuItemService->create($data);
        return $this->successResponse($result, __('messages.menus.menu_created_successfully'), 201);
    }
    public function update(updatemenuRequest $request, $id)
    {
        $data = $request->validated();
        $result = $this->menuItemService->update($data, $id);
        return $this->successResponse($result, __('messages.menus.menu_updated_successfully'), 200);
    }
    public function destroy($id)
    {
        $result = $this->menuItemService->delete($id);
        return $this->successResponse($result, __('messages.menus.menu_deleted_successfully'), 200);
    }
}
