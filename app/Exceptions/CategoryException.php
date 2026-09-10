<?php

namespace App\Exceptions;

use App\Traits\apiResponseTrait;
use Exception;
use Illuminate\Http\JsonResponse;

class CategoryException extends Exception
{
    use apiResponseTrait;
    public static function categoryExist()
    {
        return new self(__('messages.categories.category_exist'), 429);
    }

    public static function categoryNotFound()
    {
        return new self(__('messages.categories.category_not_found'), 429);
    }

    public function render($request): JsonResponse
    {
        $statusCode = is_numeric($this->getCode()) && $this->getCode() >= 400 && $this->getCode() < 600
            ? $this->getCode()
            : 400;

        return $this->errorResponse($this->getMessage(), $statusCode);
    }
}
