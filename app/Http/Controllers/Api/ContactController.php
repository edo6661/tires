<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTrait;
use App\Services\ContactServiceInterface;
use App\Events\InquirySubmitted;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

/**
 * @tags Public
 */
class ContactController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        protected ContactServiceInterface $contactService
    ) {}

    /**
     * Submit contact form (Public route with auto-fill for authenticated users)
     */
    public function submitContact(Request $request): JsonResponse
    {
        try {
            $user = Auth::user(); // Get authenticated user (if any)

            // Validation rules - make name, email optional if user is authenticated
            $rules = [
                'subject' => 'required|string|max:255',
                'message' => 'required|string|max:2000',
            ];

            // If user is not authenticated, require name and email
            if (!$user) {
                $rules['name'] = 'required|string|max:255';
                $rules['email'] = 'required|email|max:255';
            }

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return $this->errorResponse(
                    'Validation failed',
                    422,
                    collect($validator->errors())->map(function ($messages, $field) {
                        return [
                            'field' => $field,
                            'tag' => 'validation_error',
                            'value' => request($field),
                            'message' => $messages[0]
                        ];
                    })->values()->toArray()
                );
            }

            $contactData = [
                'user_id' => $user ? $user->id : null,
                'full_name' => $user ? $user->full_name : $request->name,
                'email' => $user ? $user->email : $request->email,
                'phone_number' => $user ? $user->phone_number : null,
                'subject' => $request->subject,
                'message' => $request->message,
            ];

            $contact = $this->contactService->createContact($contactData);
            event(new InquirySubmitted($contact));

            $responseData = [
                'contact_id' => $contact->id,
                'reference_number' => $contact->created_at->format('YmdHis') . $contact->id,
            ];

            // Add user info to response if authenticated
            if ($user) {
                $responseData['submitted_by'] = $user->full_name;
                $responseData['email'] = $user->email;
                $responseData['auto_filled'] = true;
            } else {
                $responseData['submitted_by'] = $request->name;
                $responseData['email'] = $request->email;
                $responseData['auto_filled'] = false;
            }

            return $this->successResponse(
                $responseData,
                'Thank you for your message! We will get back to you soon.'
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to submit contact form',
                500,
                [
                    [
                        'field' => 'general',
                        'tag' => 'submission_failed',
                        'value' => $e->getMessage(),
                        'message' => 'Contact form submission failed'
                    ]
                ]
            );
        }
    }

    /**
     * Submit inquiry form (Public route with auto-fill for authenticated users)
     */
    public function submitInquiry(Request $request): JsonResponse
    {
        try {
            $user = Auth::user(); // Get authenticated user (if any)

            // Validation rules - make name, email optional if user is authenticated
            $rules = [
                'subject' => 'required|string|max:255',
                'message' => 'required|string|max:2000',
            ];

            // If user is not authenticated, require name and email
            if (!$user) {
                $rules['name'] = 'required|string|max:255';
                $rules['email'] = 'required|email|max:255';
            }

            // Phone is always optional
            $rules['phone'] = 'nullable|string|max:20';

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return $this->errorResponse(
                    'Validation failed',
                    422,
                    collect($validator->errors())->map(function ($messages, $field) {
                        return [
                            'field' => $field,
                            'tag' => 'validation_error',
                            'value' => request($field),
                            'message' => $messages[0]
                        ];
                    })->values()->toArray()
                );
            }

            // Prepare contact data with auto-fill for authenticated users
            $contactData = [
                'user_id' => $user ? $user->id : null,
                'full_name' => $user ? $user->full_name : $request->name,
                'email' => $user ? $user->email : $request->email,
                'phone_number' => $request->phone ?? ($user ? $user->phone_number : null),
                'subject' => $request->subject,
                'message' => $request->message,
            ];

            $contact = $this->contactService->createContact($contactData);
            event(new InquirySubmitted($contact));

            $responseData = [
                'inquiry_id' => $contact->id,
                'reference_number' => $contact->created_at->format('YmdHis') . $contact->id,
            ];

            // Add user info to response if authenticated
            if ($user) {
                $responseData['submitted_by'] = $user->full_name;
                $responseData['email'] = $user->email;
                $responseData['auto_filled'] = true;
            } else {
                $responseData['submitted_by'] = $request->name;
                $responseData['email'] = $request->email;
                $responseData['auto_filled'] = false;
            }

            return $this->successResponse(
                $responseData,
                'Thank you for your inquiry! We will get back to you soon.'
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to submit inquiry',
                500,
                [
                    [
                        'field' => 'general',
                        'tag' => 'submission_failed',
                        'value' => $e->getMessage(),
                        'message' => 'Inquiry submission failed'
                    ]
                ]
            );
        }
    }

    /**
     * Get current user data for auto-filling forms (if authenticated)
     */
    public function getCurrentUserData(): JsonResponse
    {
        try {
            $user = Auth::user();

            if ($user) {
                return $this->successResponse(
                    [
                        'authenticated' => true,
                        'user_data' => [
                            'name' => $user->full_name,
                            'email' => $user->email,
                            'phone' => $user->phone_number
                        ]
                    ],
                    'User data retrieved successfully'
                );
            } else {
                return $this->successResponse(
                    [
                        'authenticated' => false,
                        'user_data' => null
                    ],
                    'No authenticated user'
                );
            }
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to retrieve user data',
                500,
                [
                    [
                        'field' => 'general',
                        'tag' => 'retrieval_failed',
                        'value' => $e->getMessage(),
                        'message' => 'User data retrieval failed'
                    ]
                ]
            );
        }
    }
}
