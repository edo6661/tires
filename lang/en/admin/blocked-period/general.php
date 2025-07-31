<?php

return [
    'title' => 'Blocked Periods',
    'all_menus' => 'All Menus',

    'flash_messages' => [
        'create_success'      => 'Blocked period has been created successfully.',
        'create_error'        => 'An error occurred while creating the blocked period: :message',
        'update_success'      => 'Blocked period has been updated successfully.',
        'update_error'        => 'An error occurred while updating the blocked period: :message',
        'delete_success'      => 'Blocked period has been deleted successfully.',
        'delete_error'        => 'An error occurred while deleting the blocked period: :message',
        'not_found'           => 'Blocked period not found.',
        'conflict'            => 'The selected schedule conflicts with an existing blocked period.',
        'bulk_delete_success' => 'Successfully deleted :count blocked period(s).',
        'bulk_delete_error'   => 'An error occurred during bulk deletion: :message',
    ],

    'calendar' => [
        'all_menus_label' => 'All Menus',
    ],

    'confirmation' => [
        'delete_title'   => 'Delete Blocked Period',
        'delete_message' => 'Are you sure you want to delete this blocked period?',
        'bulk_delete_title' => 'Delete Selected Periods',
        'bulk_delete_message' => 'Are you sure you want to delete the selected blocked periods?',
    ],
];
