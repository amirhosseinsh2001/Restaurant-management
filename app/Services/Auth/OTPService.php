<?php

namespace App\Services\Auth;

use App\Exceptions\AuthException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Random\RandomException;

class OTPService
{
    private const expiresInMinutes = 5;
    /**
     * @param array $userData
     * @param string $field
     * @param string $value
     * @return float[]|int[]
     * @throws AuthException
     * @throws RandomException
     */
    public function sendForRegistration(array $userData, string $field, string $value): array
    {
        $otpCacheKey = 'new-user-register-with-' . $value;
        $userDataCacheKey = 'register-data-for-' . $value;
        $cachedData = Cache::get($otpCacheKey);
        if (Cache::has($otpCacheKey)) {
            $cachedData = Cache::get($otpCacheKey);
            $remainingSeconds = isset($cachedData['expires_at'])
                ? max(0, $cachedData['expires_at'] - time())
                : 0;
            throw AuthException::otpAlreadySent($remainingSeconds);
        }
        $code = random_int(100000, 999999);
        Cache::put($otpCacheKey, [
            'field' => $field,
            'value' => $value,
            'code' => $code,
            'expires_at' => time() + (self::expiresInMinutes * 60),
        ], now()->addMinutes(self::expiresInMinutes));
        Cache::put($userDataCacheKey, [
            'name' => $userData['name'],
            'value' => $value,
            $field => $value,
            'password' => $userData['password'],
            'role_id' => $userData['role_id'],
            'otp' => $code,
        ], now()->addMinutes(self::expiresInMinutes));
        Log::info("Registration OTP Code for $value: $code");
        return [
            'expires_in' => self::expiresInMinutes * 60,
        ];
    }

    /**
     * @param string $field
     * @param string $value
     * @return float[]|int[]
     * @throws AuthException
     * @throws RandomException
     */
    public function sendOtpForLogin(string $field, string $value): array
    {
        $loginCacheKey = 'otp-login-for-' . $value;
        if (Cache::has($loginCacheKey)) {
            $cachedData = Cache::get($loginCacheKey);
            $remainingSeconds = isset($cachedData['expires_at'])
                ? max(0, $cachedData['expires_at'] - time())
                : 0;
            throw AuthException::otpAlreadySent($remainingSeconds);
        }
        $code = random_int(100000, 999999);
        Cache::put($loginCacheKey, [
            'field' => $field,
            'value' => $value,
            'otp' => $code,
            'expires_at' => time() + (self::expiresInMinutes * 60),
        ], now()->addMinutes(self::expiresInMinutes));
        Log::info("Login OTP Code for $value: $code");
        return [
            'expires_in' => self::expiresInMinutes * 60,
        ];
    }

    public function infoForRegistration(array $userData, string $field, string $value): array
    {
        $userDataCacheKey = 'register-data-for-' . $value;
        Cache::put($userDataCacheKey, [
            'name' => $userData['name'],
            'value' => $value,
            $field => $value,
            'password' => $userData['password'],
            'role_id' => $userData['role_id'],
//            'otp' => $code,
        ], now()->addMinutes(self::expiresInMinutes));
//        Log::info("Registration OTP Code for $value: $code");
        return [
            'expires_in' => self::expiresInMinutes * 60,
        ];
    }

    /**
     * @param string $action
     * @param string $field
     * @param string $value
     * @param array $userData
     * @return float[]|int[]
     * @throws AuthException
     * @throws RandomException
     */
    public function sendOTP(string $action, string $field, string $value, array $userData = []): array
    {
        $userCacheKey = $this->generateCacheKey($action, $value);
        if (Cache::has($userCacheKey)) {
            $cachedData = Cache::get($userCacheKey);
            $remainingSeconds = isset($cachedData['expires_at'])
                ? max(0, $cachedData['expires_at'] - time())
                : 0;
            throw AuthException::otpAlreadySent($remainingSeconds);
        }
        $code = random_int(100000, 999999);
        Cache::put($userCacheKey, array_merge([
            'field' => $field,
            'value' => $value,
            'otp' => $code,
            'expires_at' => time() + (self::expiresInMinutes * 60),
        ], $userData), now()->addMinutes(self::expiresInMinutes));

        Log::info("OTP code for " . $action . " " . $value . ": " . $code);
        return [
            'expires_in' => self::expiresInMinutes * 60,
        ];
    }

    /**
     * @param string $action
     * @param string $value
     * @return string
     */
    public function generateCacheKey(string $action, string $value)
    {
        return 'otp-' . $action . '-' . $value;
    }
}
