<?php

return [
    'title' => 'Edit Announcement',
    'subtitle' => 'Update multilingual announcement information',
    'back_button' => 'Back',

    'form' => [
        'card_title' => 'Edit Announcement Information',
        'card_subtitle' => 'Update the form below to edit the multilingual announcement',
        'created_at' => 'Created at',

        'tabs' => [
            'english' => 'English',
            'japanese' => 'Japanese (日本語)',
            'translation_filled_tooltip' => 'Translation filled',
        ],

        'english_section' => [
            'title' => 'English Content',
            'subtitle' => 'Edit the title and content in English',
            'label_title' => 'Title (English)',
            'placeholder_title' => 'Enter the announcement title in English...',
            'max_chars' => 'Maximum 255 characters',
            'label_content' => 'Content (English)',
            'placeholder_content' => 'Enter the announcement content in English...',
            'help_content' => 'Write the announcement content in English',
        ],

        'japanese_section' => [
            'title' => 'Japanese Content',
            'subtitle' => 'Edit the title and content in Japanese',
            'label_title' => 'Title (Japanese)',
            'placeholder_title' => 'Enter the announcement title in Japanese...',
            'max_chars' => 'Maximum 255 characters',
            'label_content' => 'Content (Japanese)',
            'placeholder_content' => 'Enter the announcement content in Japanese...',
            'help_content' => 'Write the announcement content in Japanese',
        ],

        'common_settings' => [
            'title' => 'General Settings',
            'publish_date_label' => 'Publication Date & Time',
            'publish_date_help' => 'If empty, the current time will be used',
            'status_label' => 'Status',
            'status_active' => 'Active',
            'status_inactive' => 'Inactive',
            'current_status' => 'Current status:',
        ],

        'translation_info' => [
            'title' => 'Translation Information',
            'language_en' => 'English',
            'language_ja' => 'Japanese',
            'available' => 'Available',
            'not_available' => 'Not Available',
        ],

        'announcement_info' => [
            'title' => 'Announcement Information',
            'created_at' => 'Created at',
            'updated_at' => 'Updated at',
            'published_at' => 'Published at',
            'id' => 'ID',
        ],

        'preview' => [
            'title' => 'Preview Changes',
            'hide_button' => 'Hide Preview',
            'show_button' => 'Show Preview',
            'placeholder_title' => 'Announcement title will appear here',
            'placeholder_content' => 'Announcement content will appear here',
            'date_not_selected' => 'Date not selected',
            'preview_lang_en' => 'English',
            'preview_lang_ja' => 'Japanese',
            'status_active' => 'Active',
            'status_inactive' => 'Inactive',
        ],

        'buttons' => [
            'cancel' => 'Cancel',
            'view_details' => 'View Details',
            'update' => 'Update Announcement',
        ],
    ],
];