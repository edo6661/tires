<?php

return [
    'title' => 'Tire Storage Management',
    'description' => 'Manage customer tire storage data',
    'add_storage' => 'Add Storage',
    
    'stats' => [
        'total_storages' => 'Total Storages',
        'active' => 'Active',
        'ended' => 'Ended',
        'average_fee' => 'Average Fee',
    ],
    
    'filters' => [
        'title' => 'Filters & Search',
        'show_filters' => 'Show Filters',
        'hide_filters' => 'Hide Filters',
        'status' => 'Status',
        'all_statuses' => 'All Statuses',
        'tire_brand' => 'Tire Brand',
        'tire_brand_placeholder' => 'Search tire brand...',
        'tire_size' => 'Tire Size',
        'tire_size_placeholder' => 'Search tire size...',
        'customer_name' => 'Customer Name',
        'customer_name_placeholder' => 'Search customer name...',
        'filter' => 'Filter',
        'reset' => 'Reset',
    ],
    
    'bulk_actions' => [
        'selected_items' => 'item(s) selected',
        'end_storage' => 'End Storage',
        'delete' => 'Delete',
    ],
    
    'table' => [
        'title' => 'Tire Storage List',
        'customer' => 'Customer',
        'tire_info' => 'Tire Info',
        'dates' => 'Dates',
        'fee' => 'Fee',
        'status' => 'Status',
        'actions' => 'Actions',
        'size' => 'Size',
        'start' => 'Start',
        'end' => 'End',
    ],
    
    'actions' => [
        'view_details' => 'View Details',
        'edit' => 'Edit',
        'end_storage' => 'End Storage',
        'delete' => 'Delete',
    ],
    
    'empty_state' => [
        'title' => 'No storage data yet',
        'description' => 'No tire storages have been created yet, or none match the applied filters.',
        'add_storage' => 'Add Storage',
    ],
    
    'modals' => [
        'delete' => [
            'title' => 'Confirm Deletion',
            'single_message' => 'Are you sure you want to delete this tire storage?',
            'multiple_message' => 'Are you sure you want to delete :count storage item(s)?',
            'cancel' => 'Cancel',
            'delete' => 'Delete',
        ],
        'end_storage' => [
            'single_message' => 'Are you sure you want to end this tire storage?',
            'multiple_message' => 'Are you sure you want to end :count storage item(s)?',
        ],
    ],
    
    'alerts' => [
        'error_occurred' => 'An error occurred.',
        'deletion_error' => 'An error occurred during deletion.',
    ],
];