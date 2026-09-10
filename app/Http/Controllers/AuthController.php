<?php

namespace App\Http\Controllers;

use App\Exceptions\AuthException;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\LoginWithPhoneRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\VerifyRequest;
use App\Services\Auth\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Random\RandomException;

class AuthController extends Controller
{
    public function __construct(protected AuthService $authService){}

    /**
     * @param RegisterRequest $request
     * @return JsonResponse
     * @throws AuthException
     * @throws RandomException
     */
    public function register(RegisterRequest $request)
    {
        $data = $request->validated();
        $result = $this->authService->register($data);
        return $this->successResponse($result, __('messages.otp_sent_successfully'), 200);
    }

    /**
     * @param VerifyRequest $request
     * @return JsonResponse
     * @throws AuthException
     */
    public function verify_register(VerifyRequest $request)
    {
        $field = $request->has('email') ? 'email' : 'phone';
        $data = $request->validated();
        $result = $this->authService->verify($data, $field);
        return $this->successResponse($result, __('messages.user_created_successfully'), 200);
    }

    /**
     * @param LoginRequest $request
     * @return JsonResponse
     * @throws AuthException
     * @throws RandomException
     */
    public function login(LoginRequest $request)
    {
        $field = $request->has('email') ? 'email' : 'phone';
        $value = $request->input($field);
        $password = $request->input('password');
            $result = match ($field) {
                'email' => $this->authService->loginWithEmail($field, $value, $password),
                'phone' => $this->authService->loginWithPhone($field, $value),
            };
        $message = $field === 'email'
            ? __('messages.user_login_successfully')
            : __('messages.otp_sent_successfully');
        return $this->successResponse($result, $message, 200);
    }

    /**
     * @param LoginWithPhoneRequest $request
     * @return JsonResponse
     * @throws AuthException
     */
    public function verifyLogin(LoginWithPhoneRequest $request)
    {
        $field = $request->has('phone') ? 'phone' : null;
        $value = $request->input($field);
        $data = $request->validated();
        $result = $this->authService->verifyLoginWithPhone($data, $field, $value);
        return $this->successResponse($result, __('messages.user_login_successfully'), 200);
    }

    /**
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        //TODO: Customize message for unauthenticated users
        $user = Auth::user();
        $result = $this->authService->logout($user);
        return $this->successResponse($result, __('messages.user_logged_out_successfully'), 200);
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $result = $this->authService->updateInfo((array)$user);
        return $this->successResponse($result, __('messages.user_updated_successfully'), 200);
    }
}

