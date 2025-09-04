<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\UserRequest;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Services\AuthServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @tags Authentication
 */
class AuthController extends Controller
{
    public function __construct(
        protected AuthServiceInterface $authService
    ) {}

    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->only(['email', 'password']);

        if ($this->authService->login($credentials)) {
            $user = $this->authService->getCurrentUser();

            return response()->json([
                'status' => 'success',
                'message' => 'Login berhasil',
                'data' => [
                    'user' => $user,
                    'token' => $user->createToken('API Token')->plainTextToken
                ]
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Email atau password salah'
        ], 401);
    }

    public function register(UserRequest $request): JsonResponse
    {
        $data = $request->validated();
        $user = $this->authService->register($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Registrasi berhasil',
            'data' => [
                'user' => $user,
                'token' => $user->createToken('API Token')->plainTextToken
            ]
        ], 201);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Logout berhasil'
        ]);
    }

    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        $sent = $this->authService->sendPasswordResetLink($request->email);

        if ($sent) {
            return response()->json(['status' => 'success', 'message' => 'Link reset password dikirim ke email']);
        }

        return response()->json(['status' => 'error', 'message' => 'Email tidak ditemukan'], 404);
    }

    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $reset = $this->authService->resetPassword(
            $request->email,
            $request->token,
            $request->password
        );

        if ($reset) {
            return response()->json(['status' => 'success', 'message' => 'Password berhasil direset']);
        }

        return response()->json(['status' => 'error', 'message' => 'Token tidak valid atau sudah expired'], 400);
    }
}
