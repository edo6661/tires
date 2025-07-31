<?php

return [
    // Page Titles
    'title_auth' => 'Booking Confirmation',
    'title_guest' => 'Contact Information & Booking Confirmation',
    'subtitle_auth' => 'Please review your reservation details before confirmation',
    'subtitle_guest' => 'Please provide your contact information and review your reservation details',

    // Guest Form
    'form_title' => 'Contact Information',
    'labels' => [
        'full_name' => 'Full Name',
        'full_name_kana' => 'Full Name (Kana)',
        'email' => 'Email Address',
        'phone_number' => 'Phone Number',
    ],
    'placeholders' => [
        'full_name' => 'Enter your full name',
        'full_name_kana' => 'Enter your full name in Kana',
        'email' => 'Enter your email address',
        'phone_number' => 'Enter your phone number',
    ],
    'form_button_back' => 'Back',
    'form_button_continue' => 'Continue to Confirmation',

    // Confirmation View
    'summary_title' => 'Reservation Summary',
    'service_details_title' => 'Service Details',
    'customer_info_title' => 'Customer Information',
    'details' => [
        'service' => 'Service:',
        'duration' => 'Duration:',
        'date' => 'Date:',
        'time' => 'Time:',
        'name' => 'Name:',
        'name_kana' => 'Name (Kana):',
        'email' => 'Email:',
        'phone' => 'Phone:',
        'status' => 'Member Status:',
    ],
    'member_status' => [
        'member' => 'RESERVA Member',
        'guest' => 'Guest',
    ],
    'important_notes_title' => 'Important Notes',
    'notes' => [
        'item1' => 'Please arrive 5 minutes before your scheduled time',
        'item2' => 'Cancellation is not allowed after confirmation',
        'item3' => 'Changes to reservation must be made at least 24 hours in advance',
        'item4' => 'Please bring a valid ID for verification',
    ],
    'terms_agree' => 'I agree to the',
    'terms_and_conditions' => 'Terms and Conditions',
    'terms_and' => 'and',
    'terms_privacy_policy' => 'Privacy Policy',

    // Actions
    'action_back_guest_edit' => 'Edit Information',
    'action_complete_booking' => 'Complete Booking',
    'duration_unit' => 'minutes',

    // JavaScript Translations
    'js' => [
        'booking_info_not_found' => 'Booking information not found. Please start over.',
        'error_loading_service' => 'Error Loading Service',
        'error_service_not_found' => 'Error: Service not found',
        'not_applicable' => 'N/A',
        'date_locale' => 'en-US',
        'validation' => [
            'full_name_required' => 'Full name is required',
            'full_name_kana_required' => 'Full name (Kana) is required',
            'email_required' => 'Email is required',
            'email_invalid' => 'Please enter a valid email address',
            'phone_required' => 'Phone number is required',
            'phone_invalid' => 'Phone number is invalid',
            'terms_required' => 'You must agree to the terms and conditions',
        ],
    ],
];