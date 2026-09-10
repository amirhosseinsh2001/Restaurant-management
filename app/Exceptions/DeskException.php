<?php

namespace App\Exceptions;

use App\Traits\apiResponseTrait;
use Exception;
use Illuminate\Http\JsonResponse;

class DeskException extends Exception
{
    use apiResponseTrait;
    public static function deskExist()
    {
        return new self(__('messages.desks.desk_exist'), 429);
    }

    public static function deskNotFound()
    {
        return new self(__('messages.desks.desk_not_found'), 429);
    }

    public function render($request): JsonResponse
    {
        $statusCode = is_numeric($this->getCode()) && $this->getCode() >= 400 && $this->getCode() < 600
            ? $this->getCode()
            : 400;

        return $this->errorResponse($this->getMessage(), $statusCode);
    }
}
