<?php

return [
    'header' => [
        'title' => 'Customer Detail',
        'subtitle' => 'View detailed customer information and history.',
        'send_message_button' => 'Send Message',
        'export_button' => 'Export',
    ],

    'stats' => [
        'total_reservations' => 'Total Reservations',
        'total_amount' => 'Total Amount',
        'tire_storage' => 'Tire Storage',
    ],

    'sidebar' => [
        'status_registered' => 'Registered',
        'status_guest' => 'Guest',
        'email' => 'Email',
        'phone' => 'Phone',
        'company' => 'Company',
        'department' => 'Department',
        'dob' => 'Date of Birth',
        'gender' => 'Gender',
        'guest_info' => [
            'title' => 'Guest Customer',
            'body' => 'This customer made reservations as a guest. Limited information available.',
        ],
    ],

    'tabs' => [
        'customer_info' => 'Customer Info',
        'reservation_history' => 'Reservation History',
        'tire_storage' => 'Tire Storage',
    ],

    'main_content' => [
        'customer_info' => [
            'title' => 'Customer Information',
            'full_name' => 'Full Name',
            'full_name_kana' => 'Full Name (Kana)',
            'email' => 'Email',
            'phone_number' => 'Phone Number',
            'company_name' => 'Company Name',
            'department' => 'Department',
            'dob' => 'Date of Birth',
            'gender' => 'Gender',
            'addresses_title' => 'Addresses',
            'company_address' => 'Company Address',
            'home_address' => 'Home Address',
            'guest' => [
                'title' => 'Guest Customer',
                'body' => 'This customer made reservations as a guest. Only basic reservation information is available.',
                'name_label' => 'Name:',
                'name_kana_label' => 'Name (Kana):',
                'email_label' => 'Email:',
                'phone_label' => 'Phone:',
            ],
        ],
        'reservation_history' => [
            'title' => 'Reservation History',
            'count_text' => ':count reservations',
            'date_time' => 'Date & Time:',
            'people' => 'People:',
            'menu' => 'Menu:',
            'amount' => 'Amount:',
            'notes' => 'Notes:',
            'view_details_link' => 'View Details',
            'no_records' => 'No reservation history found.',
        ],
        'tire_storage' => [
            'title' => 'Tire Storage',
            'count_text' => ':count storage records',
            'start_date' => 'Start Date:',
            'planned_end' => 'Planned End:',
            'storage_fee' => 'Storage Fee:',
            'days_remaining' => 'Days Remaining:',
            'days_remaining_text' => ':days days',
            'notes' => 'Notes:',
            'no_records' => 'No tire storage records found.',
        ],
    ],

    'modal' => [
        'title' => 'Send Message to :name',
        'subject_label' => 'Subject',
        'subject_placeholder' => 'Enter subject',
        'message_label' => 'Message',
        'message_placeholder' => 'Enter your message',
        'cancel_button' => 'Cancel',
        'send_button' => 'Send Message',
    ],

    'js_alerts' => [
        'fill_fields' => 'Please fill in both subject and message',
        'send_success' => 'Message sent successfully!',
        'send_failed' => 'Failed to send message',
        'export_placeholder' => 'Export functionality will be implemented',
    ],
];