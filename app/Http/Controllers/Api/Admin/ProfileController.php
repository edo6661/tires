<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Http\Resources\ReservationResource;
use App\Http\Traits\ApiResponseTrait;
use App\Services\AuthServiceInterface;
use App\Services\UserServiceInterface;
use App\Services\ReservationServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

/**
 * @tags Admin - Profile Settings
 */
class ProfileController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        protected UserServiceInterface $userService,
        protected AuthServiceInterface $authService,
        protected ReservationServiceInterface $reservationService,
    ) {}

    /**
     * Get current admin profile
     */
    public function show(): JsonResponse
    {
        try {
            $user = $this->authService->getCurrentUser();

            return $this->successResponse(
                new UserResource($user),
                'Profile retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to retrieve profile',
                500,
                [
                    [
                        'field' => 'general',
                        'tag' => 'retrieval_failed',
                        'value' => $e->getMessage(),
                        'message' => 'Profile retrieval failed'
                    ]
                ]
            );
        }
    }

    /**
     * Update current admin profile
     */
    public function update(Request $request): JsonResponse
    {
        try {
            $user = $this->authService->getCurrentUser();

            $validated = $request->validate([
                'full_name' => 'required|string|max:255',
                'full_name_kana' => 'required|string|max:255',
                'phone_number' => 'required|string|max:20',
                'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
                'company_name' => 'nullable|string|max:255',
                'department' => 'nullable|string|max:255',
                'company_address' => 'nullable|string|max:500',
                'home_address' => 'nullable|string|max:500',
                'date_of_birth' => 'nullable|date',
                'gender' => 'nullable|in:male,female,other',
            ]);

            $updatedUser = $this->userService->updateUser($user->id, $validated);

            return $this->successResponse(
                new UserResource($updatedUser),
                'Profile updated successfully'
            );

        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->errorResponse(
                'Validation failed',
                422,
                collect($e->errors())->map(function ($messages, $field) {
                    return [
                        'field' => $field,
                        'tag' => 'validation_error',
                        'value' => request($field),
                        'message' => $messages[0]
                    ];
                })->values()->toArray()
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to update profile',
                500,
                [
                    [
                        'field' => 'general',
                        'tag' => 'update_failed',
                        'value' => $e->getMessage(),
                        'message' => 'Profile update failed'
                    ]
                ]
            );
        }
    }

    /**
     * Update current admin password
     */
    public function updatePassword(Request $request): JsonResponse
    {
        try {
            $user = $this->authService->getCurrentUser();

            $validated = $request->validate([
                'current_password' => ['required', 'current_password'],
                'new_password' => ['required', Password::defaults(), 'confirmed'],
            ]);

            $updatedUser = $this->userService->updateUser($user->id, [
                'password' => Hash::make($validated['new_password']),
            ]);

            return $this->successResponse(
                null,
                'Password updated successfully'
            );

        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->errorResponse(
                'Validation failed',
                422,
                collect($e->errors())->map(function ($messages, $field) {
                    return [
                        'field' => $field,
                        'tag' => 'validation_error',
                        'value' => request($field),
                        'message' => $messages[0]
                    ];
                })->values()->toArray()
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to update password',
                500,
                [
                    [
                        'field' => 'general',
                        'tag' => 'password_update_failed',
                        'value' => $e->getMessage(),
                        'message' => 'Password update failed'
                    ]
                ]
            );
        }
    }


    /**
     * Delete current admin account
     */
    public function deleteAccount(): JsonResponse
    {
        try {
            $user = $this->authService->getCurrentUser();

            // Revoke all tokens
            $user->tokens()->delete();

            // Delete user account
            $success = $this->userService->deleteUser($user->id);

            if (!$success) {
                return $this->errorResponse(
                    'Failed to delete account',
                    500,
                    [
                        [
                            'field' => 'general',
                            'tag' => 'deletion_failed',
                            'value' => null,
                            'message' => 'Account deletion failed'
                        ]
                    ]
                );
            }

            return $this->successResponse(
                null,
                'Account deleted successfully'
            );

        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to delete account',
                500,
                [
                    [
                        'field' => 'general',
                        'tag' => 'deletion_failed',
                        'value' => $e->getMessage(),
                        'message' => 'Account deletion failed'
                    ]
                ]
            );
        }
    }
}
