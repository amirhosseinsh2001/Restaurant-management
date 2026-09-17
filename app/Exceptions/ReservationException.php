<?php

namespace App\Exceptions;

use App\Traits\apiResponseTrait;
use Exception;

class ReservationException extends Exception
{
    use apiResponseTrait;

    public function __construct(
        string $message,
        int $code = 400,
        protected array $errors = [],
        protected array $meta = [],
    ) {
        parent::__construct($message, $code);
    }

    public static function deskReserved(array $suggestion)
    {
        return new self(__('messages.desks.desk_reserved'), 429, [],['suggestion' => $suggestion]);
    }

    public static function deskIsNotAvaiable()
    {
        return new self(__('messages.desks.desk_is_not_available'), 429, []);
    }

    public static function deskCapacityNotFit(array $suggestion)
    {
        return new self(__('messages.desks.desk_capacity_not_fit'), 429, [],['suggestion' => $suggestion]);
    }

    public function render()
    {
        $statusCode = is_numeric($this->getCode()) && $this->getCode() >= 400 && $this->getCode() < 600
            ? $this->getCode()
            : 400;
        return $this->errorResponse($this->getMessage(), $statusCode, $this->errors, $this->meta);
    }
}
