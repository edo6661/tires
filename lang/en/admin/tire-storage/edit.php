<?php

return [
    // Page Elements
    'page_title' => 'Edit Blocked Period',
    'page_description' => 'Update the time period when a menu is unavailable for reservation.',
    'back_to_list_button' => 'Back to List',

    // Form Labels & Placeholders
    'form' => [
        'all_menus_label' => 'Block for All Menus?',
        'specific_menu_label' => 'Select Specific Menu',
        'select_menu_placeholder' => '-- Select a menu --',
        'start_time_label' => 'Start Time',
        'end_time_label' => 'End Time',
        'reason_label' => 'Reason',
        'reason_placeholder' => 'Example: Routine maintenance, public holiday, private event, etc.',
    ],

    // Conflict Section
    'conflict' => [
        'title' => 'Schedule Conflict Detected!',
        'description' => 'The period you entered overlaps with the following schedule:',
    ],

    // Button
    'button' => [
        'save_text' => 'Save Changes',
        'checking_text' => 'Checking...',
    ],
    
    // Controller Messages
    'messages' => [
        'not_found' => 'Blocked period not found.',
        'update_success' => 'Blocked period updated successfully.',
        'conflict' => 'A time conflict occurred with an existing blocked period.',
        'update_error' => 'An error occurred: ',
    ],
];