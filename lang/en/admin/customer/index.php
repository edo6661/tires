<?php

return [
    'title' => 'Customer Management',
    'description' => 'Manage customer data and their reservation history.',

    'stats' => [
        'first_time' => 'First Time',
        'repeat' => 'Repeat',
        'dormant' => 'Dormant',
    ],

    'filters' => [
        'search_placeholder' => 'Search by name, email, or phone number...',
        'all_types' => 'All Customer Types',
        'first_time' => 'First Time',
        'repeat' => 'Repeat Customer',
        'dormant' => 'Dormant',
        'reset' => 'Reset',
    ],

    'table' => [
        'header' => [
            'customer' => 'Customer',
            'contact_info' => 'Contact Info',
            'status' => 'Status',
            'reservations' => 'Reservations',
            'total_amount' => 'Total Amount',
            'last_reservation' => 'Last Reservation',
            'actions' => 'Actions',
        ],
        'status_badge' => [
            'registered' => 'Registered',
            'guest' => 'Guest',
        ],
        'type_badge' => [
            'first_time' => 'First Time',
            'repeat' => 'Repeat',
            'dormant' => 'Dormant',
        ],
        'reservations_count' => ':count times',
        'actions_tooltip' => [
            'view_details' => 'View Details',
            'send_message' => 'Send Message',
        ],
        'empty' => [
            'title' => 'No customers found',
            'description' => 'There are no customers registered or matching the selected filters.',
        ],
    ],

    'pagination' => [
        'previous' => 'Previous',
        'next' => 'Next',
    ],

    'modal' => [
        'title' => 'Send Message',
        'subject' => 'Subject',
        'subject_placeholder' => 'Enter subject',
        'message' => 'Message',
        'message_placeholder' => 'Enter your message',
        'cancel' => 'Cancel',
        'send' => 'Send Message',
    ],

    'alerts' => [
        'validation_error' => 'Please fill in both subject and message',
        'send_success' => 'Message sent successfully!',
        'send_error' => 'Failed to send message',
    ],
];