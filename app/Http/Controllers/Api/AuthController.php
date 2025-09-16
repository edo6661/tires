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
 * API Authentication Controller
 *
 * Handles all API-based authentication operations including:
 * - User login with Sanctum token generation
 * - User registration with automatic token creation
 * - Password reset functionality via email
 * - Token-based logout for API clients
 *
 * This controller manages token-based authentication for mobile apps and API clients
 * using Laravel Sanctum for secure token management.
 *
 * All endpoints return JSON responses with consistent structure:
 * - Success: {status: 'success', message: '...', data: {...}}
 * - Error: {status: 'error', message: '...', code?: int}
 *
 * @tags Authentication
 */
class AuthController extends Controller
{
    /**
     * Dependency injection for authentication service
     *
     * @param AuthServiceInterface $authService Service layer handling auth business logic
     */
    public function __construct(
        protected AuthServiceInterface $authService
    ) {}

    /**
     * Login and generate API token
     *
     * Validates user credentials and returns a Sanctum API token for authenticated requests.
     * Token should be included in subsequent requests as Bearer token in Authorization header.
     *
     * @param LoginRequest $request Validated login credentials (email, password)
     * @return JsonResponse Success with user data and token, or error with 401 status
     *
     * @response 200 {
     *   "status": "success",
     *   "message": "Login success",
     *   "data": {
     *     "user": {...},
     *     "token": "1|plainTextToken..."
     *   }
     * }
     *
     * @response 401 {
     *   "status": "error",
     *   "message": "Email or password incorrect"
     * }
     */

    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->only(['email', 'password']);

        if ($this->authService->login($credentials)) {
            $user = $this->authService->getCurrentUser();

            return response()->json([
                'status' => 'success',
                'message' => 'Login success',
                'data' => [
                    'user' => $user,
                    'token' => $user->createToken('API Token')->plainTextToken
                ]
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Email or password incorrect'
        ], 401);
    }

    /**
     * Register new user account with API token
     *
     * Creates a new user account with validated data and automatically generates an API token.
     * User is immediately authenticated after successful registration.
     *
     * @param UserRequest $request Validated registration data (name, email, password, etc.)
     * @return JsonResponse Success with user data and token, or validation errors
     *
     * @response 201 {
     *   "status": "success",
     *   "message": "Registrasi success",
     *   "data": {
     *     "user": {...},
     *     "token": "2|plainTextToken..."
     *   }
     * }
     */

    public function register(UserRequest $request): JsonResponse
    {
        $data = $request->validated();
        $user = $this->authService->register($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Registrasi success',
            'data' => [
                'user' => $user,
                'token' => $user->createToken('API Token')->plainTextToken
            ]
        ], 201);
    }

    /**
     * Logout user and revoke all API tokens
     *
     * Revokes all API tokens associated with the authenticated user, effectively logging them out
     * from all devices and API clients. Requires valid Bearer token in Authorization header.
     *
     * @param Request $request HTTP request with authenticated user
     * @return JsonResponse Success confirmation message
     *
     * @authenticated
     *
     * @response 200 {
     *   "status": "success",
     *   "message": "Logout success"
     * }
     */

    public function logout(Request $request): JsonResponse
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Logout success'
        ]);
    }

    /**
     * Send password reset link via email
     *
     * Sends a password reset link to the provided email address if the email exists in the system.
     * The link contains a secure token for password reset verification.
     *
     * @param ForgotPasswordRequest $request Validated request containing email address
     * @return JsonResponse Success or error message with appropriate status code
     *
     * @response 200 {
     *   "status": "success",
     *   "message": "Link reset password dikirim ke email"
     * }
     *
     * @response 404 {
     *   "status": "error",
     *   "message": "Email tidak ditemukan"
     * }
     */

    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        $sent = $this->authService->sendPasswordResetLink($request->email);

        if ($sent) {
            return response()->json(['status' => 'success', 'message' => 'Link reset password dikirim ke email']);
        }

        return response()->json(['status' => 'error', 'message' => 'Email tidak ditemukan'], 404);
    }

    /**
     * Reset user password with token validation
     *
     * Validates the password reset token and updates the user's password.
     * Token must be valid and not expired. User receives this token via email link.
     *
     * @param ResetPasswordRequest $request Validated request with email, token, and new password
     * @return JsonResponse Success or error message with appropriate status code
     *
     * @response 200 {
     *   "status": "success",
     *   "message": "Password success direset"
     * }
     *
     * @response 400 {
     *   "status": "error",
     *   "message": "Token tidak valid or sudah expired"
     * }
     */

    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $reset = $this->authService->resetPassword(
            $request->email,
            $request->token,
            $request->password
        );

        if ($reset) {
            return response()->json(['status' => 'success', 'message' => 'Password success direset']);
        }

        return response()->json(['status' => 'error', 'message' => 'Token tidak valid or sudah expired'], 400);
    }
}
