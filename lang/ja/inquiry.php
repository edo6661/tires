<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Inquiry Page Language Lines (Japanese)
    |--------------------------------------------------------------------------
    */

    // Sidebar
    'location' => '所在地',
    'opening_hours' => '営業時間',
    'closed' => '休業日',
    'about_us' => '会社概要',
    'terms_of_use' => '利用規約',

    // Days of the week (used in opening hours)
    'days' => [
        'monday'    => '月曜',
        'tuesday'   => '火曜',
        'wednesday' => '水曜',
        'thursday'  => '木曜',
        'friday'    => '金曜',
        'saturday'  => '土曜',
        'sunday'    => '日曜',
    ],

    // Main Content - Form
    'title' => 'お名前（必須）',
    'name' => 'お名前 *',
    'email' => 'メールアドレス（必須）',
    'phone' => '電話番号',
    'subject' => '件名（必須）',
    'inquiry_content' => 'お問い合わせ内容（必須）',
    'submit_button' => 'お問い合わせを送信する',

    // Placeholders for form inputs
    'placeholders' => [
        'name' => '東京 太郎',
        'email' => 'your@email.com',
        'phone' => '00-0000-0000',
        'message' => 'お問い合わせ内容をご入力ください',
    ],
    
    // Session Messages (optional, but good practice)
    'success_message' => 'お問い合わせが正常に送信されました！',
    'error_message' => 'お問い合わせの送信中にエラーが発生しました。後でもう一度お試しください。',
];