<?php

namespace App\Services\Auth;

use App\Exceptions\AuthException;
use App\Models\User;
use App\Repositories\Auth\AuthRepository;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Mockery\Exception;
use Random\RandomException;

class AuthService
{
    public function __construct(private AuthRepository $repository, private OTPService $otpService){}

    /**
     * @return User|Authenticatable
     * @throws AuthException
     */
    public function profile()
    {
        $user = Auth::user();
        if (!$user) {
            throw AuthException::failLoadUserInfo();
        }
        return $user;
    }

    /**
     * @param array $data
     * @return array
     * @throws AuthException
     */
    public function register(array $data): array
    {
        $field = isset($data['email']) ? 'email' : 'phone';
        $value = $data[$field];
        $value = trim($value);
        $exists = $this->repository->findByField($field, $value);
        if ($exists) {
            throw AuthException::userAlreadyExists($field);
        }
        $userData = [
            'name' => $data['name'],
            $field => $value,
            'password' => Hash::make($data['password']),
            'role_id' => $data['role_id'],
        ];
        return $this->otpService->sendOTP('register', $field, $value, $userData);
    }

    /**
     * @param array $data
     * @param string $field
     * @return void
     * @throws AuthException
     */
    public function verify(array $data, string $field)
    {
        $userDataCacheKey = $this->otpService->generateCacheKey('register', $data[$field]);
        $userData = Cache::get($userDataCacheKey);
        if (!Cache::has($userDataCacheKey)) {
            throw AuthException::otpExpired();
        }
        if ($userData['otp'] !== $data['otp'] and $userData['value'] !== $data[$field]) {
            throw AuthException::otpIsInvalid();
        }
        $verifiedField = $field . '_verified_at';
        $userData[$verifiedField] = now()->toDateTimeString();
        $user = $this->repository->verify($userData);
        Cache::forget($userDataCacheKey);
        return $user;
    }

    /**
     * @param string $field
     * @param string $value
     * @param string $password
     * @return string[]
     * @throws AuthException
     */
    public function loginWithEmail(string $field, string $value, string $password): array
    {
        $user = $this->repository->findByField($field, $value);
        if (!$user || !Hash::check($password, $user->password)) {
            throw AuthException::loginWithEmailFailed();
        }
        $token = $user->createToken('Access Token')->accessToken;
        return ['token' => 'Bearer ' . $token,];
    }

    /**
     * @param string $field
     * @param string $value
     * @return void
     * @throws AuthException
     * @throws RandomException
     */
    public function loginWithPhone(string $field, string $value)
    {
        $value = trim($value);
        $this->otpService->sendOTP('login', $field, $value);
    }

    /**
     * @param array $data
     * @param string $field
     * @param string $value
     * @return string[]
     * @throws AuthException
     */
    public function verifyLoginWithPhone(array $data, string $field, string $value): array
    {
//        $loginCacheKey = 'otp-login-for-'. $data[$field];
        $userDataCacheKey = $this->otpService->generateCacheKey('login', $data[$field]);
        $userDataCache = Cache::get($userDataCacheKey);
        if (!Cache::has($userDataCacheKey)) {
            throw AuthException::otpExpired();
        }
        $user = $this->repository->findByField($field, $value);
        if (!$user) {
            throw AuthException::loginWithPhoneFailed();
        }
        if ($userDataCache['otp'] !== $data['otp'] and $userDataCache['value'] !== $data[$field] ) {
            throw AuthException::otpIsInvalid();
        }
        $token = $user->createToken('Access Token')->accessToken;
        Cache::forget($userDataCacheKey);
        return ['token' => 'Bearer ' . $token,];
    }

    /** @noinspection PhpPossiblePolymorphicInvocationInspection */
    public function logout(User|Authenticatable|null $user)
    {
//        $user->token()->revoke();
        $user?->token()?->revoke();
    }

    public function updateInfo(User $userId, array $data): array
    {
        $user = $this->repository->findByField('id', $userId);

        if (!$user) {
            throw new AuthException(__('messages.user_not_found'), 404);
        }

        $mustReVerify = false;


        if (isset($data['email']) && $data['email'] !== $user->email) {
            $data['email'] = trim($data['email']);
            $data['email_verified_at'] = null;
            $mustReVerify = true;
        }


        if (isset($data['phone']) && $data['phone'] !== $user->phone) {
            $data['phone'] = trim($data['phone']);
            $data['phone_verified_at'] = null;
            $mustReVerify = true;
        }


        $updatedUser = $this->repository->update($data, $user);


        if ($mustReVerify) {
            $this->logout($user);
        }

        return $updatedUser;
    }
}
