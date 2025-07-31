<?php
// lang/en/admin/tire-storage/edit.php

return [
    'title' => 'Edit Tire Storage',
    'description' => 'Update the details for this tire storage record.',
    'back_button' => 'Back',
    'form_title' => 'Update Tire Storage Form',

    'customer_section' => [
        'label' => 'Customer',
        'select_placeholder' => 'Select Customer',
    ],

    'tire_info_section' => [
        'title' => 'Tire Information',
        'brand_label' => 'Tire Brand',
        'brand_placeholder' => 'e.g., Bridgestone, Michelin, Goodyear',
        'size_label' => 'Tire Size',
        'size_placeholder' => 'e.g., 225/60R16, 185/65R15',
    ],

    'schedule_section' => [
        'title' => 'Storage Schedule',
        'start_date_label' => 'Storage Start Date',
        'end_date_label' => 'Planned End Date',
    ],

    'fee_status_section' => [
        'title' => 'Fee & Status',
        'fee_label' => 'Storage Fee (:currency)',
        'fee_auto_calc_note' => 'Leave blank for auto-calculation (:currency :rate/month)',
        'calculated_fee_note' => 'Calculated fee: :currency',
        'status_label' => 'Status',
        'status_active' => 'Active',
        'status_ended' => 'Ended',
    ],

    'notes_section' => [
        'label' => 'Notes',
        'placeholder' => 'Additional notes about this tire storage...',
    ],

    'buttons' => [
        'cancel' => 'Cancel',
        'update' => 'Update Storage',
    ],
];