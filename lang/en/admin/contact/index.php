<?php
// lang/en/admin/contact/index.php

return [
    'page_title' => 'Contact Management',
    'page_subtitle' => 'Manage contact messages from customers',

    'add_button' => 'Add Contact',

    'stats' => [
        'total' => 'Total Contacts',
        'pending' => 'Pending',
        'replied' => 'Replied',
        'today' => 'Today',
    ],

    'filter' => [
        'title' => 'Filter & Search',
        'show_button' => 'Show Filters',
        'hide_button' => 'Hide Filters',
        'status_label' => 'Status',
        'status_all' => 'All Status',
        'start_date_label' => 'Start Date',
        'end_date_label' => 'End Date',
        'search_label' => 'Search',
        'search_placeholder' => 'Search name, email, subject...',
        'filter_button' => 'Filter',
        'reset_button' => 'Reset',
    ],

    'bulk_actions' => [
        'items_selected' => ':count items selected',
        'delete_button' => 'Delete',
    ],

    'table' => [
        'title' => 'Contact List',
        'header' => [
            'sender' => 'Sender',
            'subject' => 'Subject',
            'message' => 'Message',
            'date' => 'Date',
            'status' => 'Status',
            'actions' => 'Actions',
        ],
        'action' => [
            'view_tooltip' => 'View Details',
            'reply_tooltip' => 'Quick Reply',
            'delete_tooltip' => 'Delete',
        ],
    ],

    'status' => [
        'pending' => 'Pending',
        'replied' => 'Replied',
    ],

    'empty' => [
        'title' => 'No contacts',
        'message' => 'No contact messages have been received or match the applied filters.',
    ],

    'modal' => [
        'cancel_button' => 'Cancel',
        'reply' => [
            'title' => 'Quick Reply',
            'placeholder' => 'Write your reply...',
            'send_button' => 'Send Reply',
        ],
        'delete' => [
            'title' => 'Confirm Delete',
            'confirm_button' => 'Delete',
            'confirm_message_single' => 'Are you sure you want to delete this contact?',
            'confirm_message_multiple' => 'Are you sure you want to delete :count contacts?',
        ],
    ],

    'alert' => [
        'reply_empty' => 'Reply message cannot be empty.',
        'reply_error' => 'An error occurred while sending the reply.',
        'delete_error' => 'An error occurred while deleting.',
        'delete_success' => 'Contact(s) deleted successfully.',
        'reply_success' => 'Reply sent successfully.',
    ],
];