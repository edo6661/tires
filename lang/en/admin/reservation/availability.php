<?php

return [
    'page_title' => 'Availability Check',
    'page_subtitle' => 'Check time availability for reservations',

    'form' => [
        'date_label' => 'Reservation Date',
        'menu_label' => 'Select Menu',
        'menu_placeholder' => '-- Select Menu --',
        'menu_minutes' => 'minutes',
    ],

    'buttons' => [
        'previous' => 'Previous',
        'next' => 'Next',
    ],

    'summary' => [
        'date' => 'Date',
        'year' => 'Year',
        'month' => 'Month',
        'current_time' => 'Current Time',
    ],

    'loading_text' => 'Loading availability data...',

    'availability' => [
        'title' => 'Time Availability',
        'available_slots' => 'available slots',
        'reserved_slots' => 'reserved slots',
        'blocked_slots' => 'blocked slots',
    ],

    'status' => [
        'available' => 'Available',
        'reserved' => 'Reserved',
        'blocked' => 'Blocked',
    ],

    'legend' => [
        'available' => [
            'title' => 'Available',
            'description' => 'Can make reservation',
        ],
        'reserved' => [
            'title' => 'Reserved',
            'description' => 'Already has reservation',
        ],
        'blocked' => [
            'title' => 'Blocked',
            'description' => 'Blocked Period',
        ],
    ],

    'empty' => [
        'title' => 'No availability data',
        'description' => 'for the selected menu on this date',
    ],
    
    'script_texts' => [
        'months' => [
            'January', 'February', 'March', 'April', 'May', 'June',
            'July', 'August', 'September', 'October', 'November', 'December'
        ],
        'days' => ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
        'alerts' => [
            'load_fail' => 'Failed to load availability data',
            'load_error' => 'An error occurred while loading data',
            'slot_unavailable' => 'This time slot is not available for reservation',
            'select_slot_first' => 'Please select a time slot first',
        ],
        'selected_time_at' => 'at',
    ],
];