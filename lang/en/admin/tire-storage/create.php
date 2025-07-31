<?php

return [
    // Page details
    'title' => 'Create New Blocked Period',
    'description' => 'Set a time period during which a specific menu or all menus are unavailable for reservation.',

    // Buttons
    'back_to_list_button' => 'Back to List',
    'save_button' => 'Save Blocked Period',
    'saving_button' => 'Saving...',

    // Form fields
    'form' => [
        'all_menus_label' => 'Block All Menus?',
        'select_menu_label' => 'Select Specific Menu',
        'select_menu_placeholder' => '-- Select a menu --',
        'start_time_label' => 'Start Time',
        'end_time_label' => 'End Time',
        'reason_label' => 'Reason',
        'reason_placeholder' => 'e.g., Regular maintenance, holiday, private event, etc.',
    ],

    // Conflict alert (Alpine.js)
    'conflict_alert' => [
        'title' => 'Schedule Conflict Detected!',
        'message' => 'The entered period overlaps with the following schedule(s):',
    ],

    // Flash messages from controller
    'flash_messages' => [
        'create_success' => 'Blocked period created successfully.',
        'create_error' => 'An error occurred: :message',
        'conflict_error' => 'A time conflict occurred with an existing blocked period.',
    ],

    // Validation messages from BlockedPeriodRequest
    'validation' => [
        'menu_required_if_not_all' => 'The menu field is required when not blocking all menus.',
        'start_before_end' => 'The start time must be a date before the end time.',
        'min_duration' => 'The minimum duration is 15 minutes.',
        'max_duration' => 'The maximum duration is 30 days.',
        'all_menus_boolean' => 'The all menus field must be true or false.',
        'conflict_message' => "Time conflict with the following blocked period(s):\n:details",

        'menu_id' => [
            'exists' => 'The selected menu is invalid.',
        ],
        'start_datetime' => [
            'required' => 'The start time is required.',
            'date' => 'The start time format is invalid.',
            'after_or_equal' => 'The start time must be a date after or equal to now.',
        ],
        'end_datetime' => [
            'required' => 'The end time is required.',
            'date' => 'The end time format is invalid.',
            'after' => 'The end time must be a date after the start time.',
        ],
        'reason' => [
            'required' => 'The reason is required.',
            'string' => 'The reason must be a string.',
            'max' => 'The reason may not be greater than 500 characters.',
            'min' => 'The reason must be at least 3 characters.',
        ],
    ],

    // Attributes for validation
    'attributes' => [
        'menu_id' => 'menu',
        'start_datetime' => 'start time',
        'end_datetime' => 'end time',
        'reason' => 'reason',
        'all_menus' => 'all menus',
    ],
];