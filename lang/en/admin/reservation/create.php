<?php

return [
    // Page Title
    'page_title' => 'Create New Reservation',

    // Form Fields & Labels
    'form' => [
        'customer_type' => 'Customer Type',
        'registered_customer' => 'Registered Customer',
        'guest_customer' => 'Guest Customer',
        'select_customer' => 'Select Customer',
        'select_customer_placeholder' => 'Select Customer...',
        'full_name' => 'Full Name *',
        'full_name_kana' => 'Full Name (Kana) *',
        'email' => 'Email *',
        'phone_number' => 'Phone Number *',
        'menu' => 'Menu *',
        'select_menu_placeholder' => 'Select Menu...',
        'yen' => 'yen',
        'minutes' => 'minutes',
        'reservation_datetime' => 'Reservation Date & Time *',
        'number_of_people' => 'Number of People *',
        'total_amount' => 'Total Amount *',
        'status' => 'Status',
        'notes' => 'Notes',
    ],

    // Status Options
    'status_options' => [
        'pending' => 'Pending',
        'confirmed' => 'Confirmed',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled',
    ],

    // Buttons
    'buttons' => [
        'select_from_calendar' => 'Select from Calendar',
        'cancel' => 'Cancel',
        'check_availability' => 'Check Availability',
        'save_reservation' => 'Save Reservation',
        'confirm' => 'Confirm',
    ],

    // Availability Messages
    'availability' => [
        'select_menu_and_date' => 'Please select menu and date/time first.',
        'available' => 'Time slot is available for reservation.',
        'unavailable' => 'Time slot is not available - conflicts with blocked periods or other reservations.',
        'error' => 'An error occurred while checking availability.',
    ],

    // Calendar Modal
    'calendar_modal' => [
        'title' => 'Select the date you want to book',
        'select_date' => 'Select Date',
        'select_time' => 'Select Time',
        'previous_month' => 'Previous',
        'next_month' => 'Next',
        'fully_blocked_message' => 'This date is fully blocked and not available for reservations.',
        'legend_title' => 'Legend:',
        'legends' => [
            'fully_blocked' => 'Fully Blocked',
            'blocked_period' => 'Blocked Period',
            'has_reservation' => 'Has Reservation',
            'mixed' => 'Mixed (Blocked + Reservation)',
            'available' => 'Available',
            'past_date' => 'Past Date',
        ],
        'days_short' => ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
        'months' => ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
        'alert_select_menu' => 'Please select a menu first.',
        'alert_date_fully_blocked' => 'Selected date is fully blocked and not available for reservations.',
        'alert_select_date_time' => 'Please select both date and time.',
    ],
    
    // Notifications and Errors
    'notifications' => [
        'error_occurred' => 'An error occurred:',
        'creation_success' => 'Reservation created successfully.',
        'creation_failed' => 'Failed to create reservation: :error',
    ],

    // Validation Messages from ReservationRequest
    'validation' => [
        'user_id_required' => 'The user ID field is required.',
        'user_id_exists' => 'The selected user does not exist.',
        'menu_id_required' => 'The menu field is required.',
        'menu_id_exists' => 'The selected menu does not exist.',
        'reservation_datetime_required' => 'The reservation datetime field is required.',
        'reservation_datetime_date' => 'The reservation datetime must be a valid date.',
        'reservation_datetime_after' => 'The reservation datetime must be after now.',
        'number_of_people_required' => 'The number of people field is required.',
        'number_of_people_integer' => 'The number of people must be an integer.',
        'number_of_people_min' => 'The number of people must be at least 1.',
        'amount_required' => 'The amount field is required.',
        'amount_numeric' => 'The amount must be a number.',
        'amount_min' => 'The amount must be at least 0.',
        'status_in' => 'The selected status is invalid.',
        'notes_string' => 'The notes must be a string.',
        'full_name_required' => 'The full name field is required for a guest customer.',
        'full_name_string' => 'The full name must be a string.',
        'full_name_max' => 'The full name may not be greater than 255 characters.',
        'full_name_kana_required' => 'The full name (kana) field is required for a guest customer.',
        'full_name_kana_string' => 'The full name (kana) must be a string.',
        'full_name_kana_max' => 'The full name (kana) may not be greater than 255 characters.',
        'email_required' => 'The email field is required for a guest customer.',
        'email_email' => 'The email must be a valid email address.',
        'email_max' => 'The email may not be greater than 255 characters.',
        'phone_number_required' => 'The phone number field is required for a guest customer.',
        'phone_number_string' => 'The phone number must be a string.',
        'phone_number_max' => 'The phone number may not be greater than 20 characters.',
    ],
];