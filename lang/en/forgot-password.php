<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Reset Password Language Lines
    |--------------------------------------------------------------------------
    */

    // Bagian untuk halaman web (sudah ada)
    'title' => 'Set a New Password',
    'password_label' => 'New Password*',
    'password_confirm_label' => 'Confirm New Password*',
    'password_placeholder' => '••••••••',
    'submit_button' => 'Reset Password',
    'back_to_login_link' => 'Back to Login',

    'email' => [
        'subject' => 'Reset Your Password - :app_name',
        'header_subtitle' => 'Account Security System',
        'greeting' => 'Hi :user_name,',
        'intro' => 'We received a request to reset the password for your account. To proceed, please click the button below.',
        'button_text' => 'Reset My Password',
        'alt_link_header' => 'Or use the following link:',
        'alt_link_instruction' => 'If the button above does not work, copy and paste the following URL into your browser:',
        'warning_header' => '⏰ Important:',
        'warning_body' => 'This password reset link will expire in <strong>:count minutes</strong> for your account\'s security.',
        'info_header' => '💡 Didn\'t request a reset?',
        'info_body' => 'If you did not request a password reset, please ignore this email. Your account password will remain secure and unchanged.',
        'footer_security_notice' => 'For your account\'s security, do not share this link with anyone.',
        'copyright' => '© :year :app_name. All rights reserved.',
    ],
];