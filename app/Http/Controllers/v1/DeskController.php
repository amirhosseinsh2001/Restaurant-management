<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\createDeskRequest;
use App\Http\Requests\updateDeskRequest;
use App\Models\Desk;
use App\Services\DeskService;

class DeskController extends Controller
{
    public function __construct(protected DeskService $deskService)
    {}

    public function index()
    {
        $result = $this->deskService->getAll();
        return $this->successResponse($result, __('messages.desks.desks_listed_successfully'), 200);
    }

    public function store(createDeskRequest $request)
    {
        $data = $request->validated();
        $result = $this->deskService->create($data);
        return $this->successResponse($result, __('messages.desks.desk_created_successfully'), 201);
    }

    public function update(updateDeskRequest $request, $id)
    {
        $data = $request->validated();
        $result = $this->deskService->update($data, $id);
        return $this->successResponse($result, __('messages.desks.desk_updated_successfully'), 200);
    }

    public function destroy($id)
    {
        $result = $this->deskService->delete($id);
        return $this->successResponse($result, __('messages.desks.desk_deleted_successfully'), 200);
    }
}
