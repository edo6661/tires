<?php
// lang/en/admin/blocked-period/index.php

return [
    'page' => [
        'title' => 'Blocked Period Management',
        'subtitle' => 'Manage blocked time periods for reservations',
    ],
    'add_button' => 'Add Period',

    'stats' => [
        'total' => 'Total Periods',
        'active' => 'Currently Active',
        'upcoming' => 'Upcoming',
        'expired' => 'Expired',
    ],

    'filters' => [
        'title' => 'Filters & Search',
        'show' => 'Show Filters',
        'hide' => 'Hide Filters',
        'menu_label' => 'Menu',
        'menu_all' => 'All Menus',
        'status_label' => 'Status',
        'status_all' => 'All Status',
        'status_active' => 'Active',
        'status_upcoming' => 'Upcoming',
        'status_expired' => 'Expired',
        'start_date_label' => 'Start Date',
        'end_date_label' => 'End Date',
        'all_menus_label' => 'Block All Menus Only',
        'search_label' => 'Search',
        'search_placeholder' => 'Search by reason or menu name...',
        'filter_button' => 'Filter',
        'reset_button' => 'Reset',
    ],

    'bulk_actions' => [
        'items_selected' => 'items selected',
        'delete_button' => 'Delete',
    ],

    'list' => [
        'title' => 'Blocked Periods List',
    ],

    'table' => [
        'header' => [
            'menu' => 'Menu',
            'time' => 'Time',
            'duration' => 'Duration',
            'reason' => 'Reason',
            'status' => 'Status',
            'actions' => 'Actions',
        ],
        'body' => [
            'all_menus_badge' => 'All Menus',
            'menu_not_found' => 'Menu not found',
            'status_active' => 'Active',
            'status_upcoming' => 'Upcoming',
            'status_expired' => 'Expired',
            'action_tooltips' => [
                'detail' => 'Detail',
                'edit' => 'Edit',
                'delete' => 'Delete',
            ],
        ],
    ],

    'empty' => [
        'title' => 'No blocked periods',
        'message' => 'No blocked periods have been created or match the applied filters.',
        'add_button' => 'Add First Period',
    ],

    'delete_modal' => [
        'title' => 'Delete Confirmation',
        'confirm_button' => 'Delete',
        'cancel_button' => 'Cancel',
        'message_single' => 'Are you sure you want to delete this blocked period?',
        'message_multiple' => 'Are you sure you want to delete :count blocked periods?',
    ],

    'alerts' => [
        'delete_error' => 'An error occurred while deleting.',
    ],
];