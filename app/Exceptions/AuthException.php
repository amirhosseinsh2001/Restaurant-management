<?php

namespace App\Exceptions;

use App\Traits\apiResponseTrait;
use Exception;
use Illuminate\Http\JsonResponse;

class AuthException extends Exception
{
    use apiResponseTrait;

    public static function failLoadUserInfo()
    {
        return new self(__('messages.failed_load_user_information'), 429);
    }

    public static function otpAlreadySent(int $seconds = 0): self
    {
//        return new self(__('messages.otp_already_sent'), 429);
        $message = __('messages.otp_already_sent', ['seconds' => $seconds]);

        return new self($message, 429);
    }

    public static function userAlreadyExists(string $field): self
    {
//        return new self(__('messages.user_already_exists'), 429);
        if ($field == 'email') {
            $field = 'ایمیل';
        }
        if ($field == 'phone') {
            $field = 'موبایل';
        }
        $message = __('messages.user_already_exists', ['field' => $field]);

        return new self($message, 429);
    }

    public static function otpExpired()
    {
        return new self(__('messages.otp_expired'), 429);
    }

    public static function loginWithEmailFailed(): self
    {
        return new self(__('messages.user_email_not_found'), 429);
    }


    public static function otpIsInvalid()
    {
        return new self(__('messages.otp_is_invalid'), 429);
    }

    public static function loginWithPhoneFailed()
    {
        return new self(__('messages.user_phone_not_found'), 429);
    }

    public function render($request): JsonResponse
    {
        $statusCode = is_numeric($this->getCode()) && $this->getCode() >= 400 && $this->getCode() < 600
            ? $this->getCode()
            : 400;

        return $this->errorResponse($this->getMessage(), $statusCode);
    }
}
