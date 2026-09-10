<?php

namespace App\Exceptions;

use App\Traits\apiResponseTrait;
use Exception;

class OrderException extends Exception
{
    use apiResponseTrait;
    public function render()
    {
        $statusCode = is_numeric($this->getCode()) && $this->getCode() >= 400 && $this->getCode() < 600
            ? $this->getCode()
            : 400;

        return $this->errorResponse($this->getMessage(), $statusCode);
    }
}
