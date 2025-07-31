<?php

return [
    'page_title' => 'Add Tire Storage',
    'page_subtitle' => 'Create a new tire storage record for a customer',
    'back_button' => 'Back',

    'form_title' => 'New Tire Storage Form',

    'form' => [
        'customer' => [
            'label' => 'Customer',
            'select_placeholder' => 'Select Customer',
        ],
        'tire_info' => [
            'title' => 'Tire Information',
            'brand_label' => 'Tire Brand',
            'brand_placeholder' => 'e.g., Bridgestone, Michelin, Goodyear',
            'size_label' => 'Tire Size',
            'size_placeholder' => 'e.g., 225/60R16, 185/65R15',
        ],
        'schedule' => [
            'title' => 'Storage Schedule',
            'start_date_label' => 'Storage Start Date',
            'end_date_label' => 'Planned End Date',
        ],
        'fee_status' => [
            'title' => 'Fee & Status',
            'fee_label' => 'Storage Fee (IDR)',
            'fee_placeholder' => '0',
            'fee_helper' => 'Leave blank for auto-calculation (IDR 50,000/month)',
            'calculated_fee_text' => 'Calculated fee: IDR',
            'status_label' => 'Status',
            'status_active' => 'Active',
            'status_ended' => 'Ended',
        ],
        'notes' => [
            'label' => 'Notes',
            'placeholder' => 'Additional notes about this tire storage...',
        ],
    ],

    'cancel_button' => 'Cancel',
    'save_button' => 'Save Storage',

    'info_box' => [
        'title' => 'Important Information',
        'point1' => 'The planned end date must be after the storage start date.',
        'point2' => 'The storage fee will be auto-calculated if left blank (IDR 50,000 per month).',
        'point3' => '"Active" status means the storage is ongoing.',
        'point4' => '"Ended" status means the storage has concluded.',
    ],
];