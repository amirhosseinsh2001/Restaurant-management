<?php

namespace App\Services;

use App\Exceptions\MenuItemException;
use App\Repositories\MenuItemRepository;
use App\Traits\imageUplaodTrait;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;


class MenuItemService
{
    use imageUplaodTrait;
    private string $imageFolder = "menu-items";

    public function __construct(protected MenuItemRepository $menuItemRepository){}
    public function getAll()
    {
        return $this->menuItemRepository->getAllList();
    }

    public function create(array $data)
    {
        $savedImagePath = null;
        try {
            DB::beginTransaction();
            if (isset($data['image_url']) && $data['image_url'] instanceof UploadedFile) {
                $savedImagePath = $this->saveImage($data['image_url'], $this->imageFolder);
                $data['image_url'] = $savedImagePath;
            }
            $menuItem = $this->menuItemRepository->create($data);
            DB::commit();
            return $menuItem;
        }catch (\Throwable $e){
            DB::rollBack();
            if ($savedImagePath) {
                $this->deleteFileByPath($savedImagePath);
            }
            throw $e;
        }
    }

    public function update(array $data, int $id)
    {
        $savedImagePath = null;
        $menuItem = $this->menuItemRepository->findByField('id', $id);
        if (!$menuItem) {
            throw MenuItemException::menuItemNotFound();
        }
        try {
            DB::beginTransaction();
            if (isset($data['image_url']) && $data['image_url']->isValid()) {
//                $this->deleteFile($menuItem, 'image_url');
                $this->deleteFileByPath($menuItem->getRawOriginal('image_url'));
                $savedImagePath = $this->saveImage($data['image_url'], $this->imageFolder);
                $data['image_url'] = $savedImagePath;
            }
            $updatedMenuItem = $this->menuItemRepository->update($data, $id);
            DB::commit();
            return $updatedMenuItem;
        }catch (\Throwable $e){
            DB::rollBack();
            if ($savedImagePath) {
                $this->deleteFileByPath($savedImagePath);
            }
            throw $e;
        }
    }

    public function delete(int $id)
    {
        $menuItem = $this->menuItemRepository->findByField('id', $id);
        if (!$menuItem) {
            throw MenuItemException::menuItemNotFound();
        }
        try {
            DB::beginTransaction();
            $this->menuItemRepository->destroy($id);
            DB::commit();
            $this->deleteFile($menuItem, 'image_url');
            return true;
        }catch (\Throwable $e){
            DB::rollBack();
            throw $e;
        }
    }
}
