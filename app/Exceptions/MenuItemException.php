<?php

namespace App\Exceptions;

use App\Traits\apiResponseTrait;
use Exception;
use Illuminate\Http\JsonResponse;

class MenuItemException extends Exception
{
    use apiResponseTrait;
    public static function menuItemExist()
    {
        return new self(__('messages.menus.menu_exist'), 429);
    }

    public static function menuItemNotFound()
    {
        return new self(__('messages.menus.menu_not_found'), 429);
    }

    public function render($request): JsonResponse
    {
        $statusCode = is_numeric($this->getCode()) && $this->getCode() >= 400 && $this->getCode() < 600
            ? $this->getCode()
            : 400;

        return $this->errorResponse($this->getMessage(), $statusCode);
    }
}
