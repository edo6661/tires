<?php

namespace App\Http\Controllers\Api\Customer;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Events\InquirySubmitted;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Traits\ApiResponseTrait;
use App\Services\AuthServiceInterface;
use App\Services\ContactServiceInterface;
use Illuminate\Support\Facades\Validator;

/**
 * @tags Customer - Contact
 */
class ContactController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        protected AuthServiceInterface $authService,
        protected ContactServiceInterface $contactService,
    ) {}

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
     * INQUIRY AND CONTACT FUNCTIONALITY
     */

    /**
     * Submit customer inquiry
     */
    // public function submitInquiry(Request $request): JsonResponse
    // {
    //     try {
    //         $user = $this->authService->getCurrentUser();

    //         // Ensure only customers can access this
    //         if (!$user->isCustomer()) {
    //             return $this->errorResponse(
    //                 'Access denied',
    //                 403,
    //                 [
    //                     [
    //                         'field' => 'role',
    //                         'tag' => 'access_denied',
    //                         'value' => $user->role,
    //                         'message' => 'Only customers can access this endpoint'
    //                     ]
    //                 ]
    //             );
    //         }

    //         $validator = Validator::make($request->all(), [
    //             'subject' => 'required|string|max:255',
    //             'message' => 'required|string|max:2000',
    //             'phone' => 'nullable|string|max:20',
    //         ]);

    //         if ($validator->fails()) {
    //             return $this->errorResponse(
    //                 'Validation failed',
    //                 422,
    //                 collect($validator->errors())->map(function ($messages, $field) {
    //                     return [
    //                         'field' => $field,
    //                         'tag' => 'validation_error',
    //                         'value' => request($field),
    //                         'message' => $messages[0]
    //                     ];
    //                 })->values()->toArray()
    //             );
    //         }

    //         $contactData = [
    //             'user_id' => $user->id,
    //             'full_name' => $user->full_name,
    //             'email' => $user->email,
    //             'phone_number' => $request->phone ?? $user->phone_number,
    //             'subject' => $request->subject,
    //             'message' => $request->message,
    //         ];

    //         $contact = $this->contactService->createContact($contactData);
    //         event(new InquirySubmitted($contact));

    //         return $this->successResponse(
    //             [
    //                 'inquiry_id' => $contact->id,
    //                 'reference_number' => $contact->created_at->format('YmdHis') . $contact->id,
    //                 'submitted_by' => $user->full_name,
    //                 'email' => $user->email
    //             ],
    //             'Your inquiry has been submitted successfully! We will get back to you soon.'
    //         );
    //     } catch (\Exception $e) {
    //         return $this->errorResponse(
    //             'Failed to submit inquiry',
    //             500,
    //             [
    //                 [
    //                     'field' => 'general',
    //                     'tag' => 'submission_failed',
    //                     'value' => $e->getMessage(),
    //                     'message' => 'Inquiry submission failed'
    //                 ]
    //             ]
    //         );
    //     }
    // }

    /**
     * Submit customer contact message
     */
    public function submitContact(Request $request): JsonResponse
    {
        try {
            $user = $this->authService->getCurrentUser();

            // Ensure only customers can access this
            if (!$user->isCustomer()) {
                return $this->errorResponse(
                    'Access denied',
                    403,
                    [
                        [
                            'field' => 'role',
                            'tag' => 'access_denied',
                            'value' => $user->role,
                            'message' => 'Only customers can access this endpoint'
                        ]
                    ]
                );
            }

            $validator = Validator::make($request->all(), [
                'subject' => 'required|string|max:255',
                'message' => 'required|string|max:2000',
            ]);

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
                'user_id' => $user->id,
                'full_name' => $user->full_name,
                'email' => $user->email,
                'phone_number' => $user->phone_number,
                'subject' => $request->subject,
                'message' => $request->message,
            ];

            $contact = $this->contactService->createContact($contactData);
            event(new InquirySubmitted($contact));

            return $this->successResponse(
                [
                    'contact_id' => $contact->id,
                    'reference_number' => $contact->created_at->format('YmdHis') . $contact->id,
                    'submitted_by' => $user->full_name,
                    'email' => $user->email
                ],
                'Your message has been sent successfully! We will get back to you soon.'
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to submit contact message',
                500,
                [
                    [
                        'field' => 'general',
                        'tag' => 'submission_failed',
                        'value' => $e->getMessage(),
                        'message' => 'Contact submission failed'
                    ]
                ]
            );
        }
    }

    /**
     * Get customer inquiry history
     */
    public function getInquiryHistory(Request $request): JsonResponse
    {
        try {
            $user = $this->authService->getCurrentUser();

            // Ensure only customers can access this
            if (!$user->isCustomer()) {
                return $this->errorResponse(
                    'Access denied',
                    403,
                    [
                        [
                            'field' => 'role',
                            'tag' => 'access_denied',
                            'value' => $user->role,
                            'message' => 'Only customers can access this endpoint'
                        ]
                    ]
                );
            }

            // Get customer's contact history
            $contacts = $this->contactService->getContactsByUser($user->id);

            return $this->successResponse(
                $contacts->map(function ($contact) {
                    return [
                        'id' => $contact->id,
                        'subject' => $contact->subject,
                        'message' => $contact->message,
                        'phone_number' => $contact->phone_number,
                        'status' => $contact->status ?? 'pending',
                        'reference_number' => $contact->created_at->format('YmdHis') . $contact->id,
                        'submitted_at' => $contact->created_at->format('Y-m-d H:i:s'),
                        'replied_at' => $contact->replied_at ? $contact->replied_at->format('Y-m-d H:i:s') : null,
                    ];
                }),
                'Customer inquiry history retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to retrieve inquiry history',
                500,
                [
                    [
                        'field' => 'general',
                        'tag' => 'retrieval_failed',
                        'value' => $e->getMessage(),
                        'message' => 'Inquiry history retrieval failed'
                    ]
                ]
            );
        }
    }
}
