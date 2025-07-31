<?php

return [
    // Page Header
    'title' => 'Announcement Management',
    'subtitle' => 'Manage announcements for customers',
    'add_button' => 'Add Announcement',

    // Stats Cards
    'stats' => [
        'total' => 'Total Announcements',
        'active' => 'Active',
        'inactive' => 'Inactive',
        'today' => 'Today',
    ],

    // Filters & Search Section
    'filters' => [
        'title' => 'Filter & Search',
        'status_label' => 'Status',
        'all_statuses' => 'All Statuses',
        'start_date_label' => 'Start Date',
        'end_date_label' => 'End Date',
        'search_label' => 'Search',
        'search_placeholder' => 'Search title or content...',
        'filter_button' => 'Filter',
        'reset_button' => 'Reset',
    ],

    // Bulk Actions Bar
    'bulk_actions' => [
        'activate_button' => 'Activate',
        'deactivate_button' => 'Deactivate',
        'delete_button' => 'Delete',
    ],

    // Announcements List
    'list' => [
        'title' => 'Announcements List',
    ],

    // Table
    'table' => [
        'headers' => [
            'title' => 'Title',
            'content' => 'Content',
            'publish_date' => 'Publish Date',
            'status' => 'Status',
            'actions' => 'Actions',
        ],
        'status_active' => 'Active',
        'status_inactive' => 'Inactive',
        'actions_tooltip' => [
            'view' => 'View Details',
            'edit' => 'Edit',
            'deactivate' => 'Deactivate',
            'activate' => 'Activate',
            'delete' => 'Delete',
        ],
    ],

    // Empty State
    'empty' => [
        'title' => 'No announcements found',
        'description' => 'There are no announcements created yet, or none match the applied filters.',
    ],

    // Delete Modal
    'delete_modal' => [
        'title' => 'Confirm Deletion',
        'cancel_button' => 'Cancel',
        'delete_button' => 'Delete',
    ],

    // JavaScript translations
    'js' => [
        'show_filters' => 'Show Filters',
        'hide_filters' => 'Hide Filters',
        'selected_text' => ':count item(s) selected',
        'delete_single_confirm' => 'Are you sure you want to delete this announcement?',
        'delete_multiple_confirm' => 'Are you sure you want to delete :count announcement(s)?',
        'select_at_least_one' => 'Please select at least one announcement.',
        'error_status' => 'An error occurred while changing the status.',
        'error_delete' => 'An error occurred while deleting.',
        'error_toggle_status' => 'An error occurred while changing the status',
    ],
];