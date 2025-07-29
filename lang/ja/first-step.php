<?php

return [
    'duration_unit' => '分',

    // Reservation Notes
    'notes_title' => 'ご予約に関する注意事項',
    'notes' => [
        'item1' => '作業時間は目安です',
        'item2' => '作業内容によってはお時間が前後する場合がございます',
        'item3' => '予約期限：1日前の23:59まで',
        'item4' => '予約確定後のキャンセルはできません',
    ],

    // Calendar
    'select_date_title' => '日付を選択',
    'prev_month' => '前の月',
    'next_month' => '次の月',
    'days_of_week' => ['月', '火', '水', '木', '金', '土', '日'],
    'legend' => [
        'available' => '予約可能',
        'fully_booked' => '予約満了',
        'past_date' => '過去の日付',
    ],

    // Time Selection
    'select_time_title' => '時間を選択',
    'no_slots_title' => 'この日付に予約可能な時間枠がありません',
    'no_slots_subtitle' => '別の日付を選択してください',

    // Booking Summary
    'summary_title' => '予約概要',
    'summary_service' => 'サービス:',
    'summary_duration' => '所要時間:',
    'summary_date' => '日付:',
    'summary_time' => '時間:',

    // Actions
    'back_button' => 'サービス一覧に戻る',
    'proceed_button' => '予約に進む',
    
    // JavaScript translations
    'js' => [
        'tooltip_fully_booked' => '予約満了',
        'tooltip_past_date' => '過去の日付 - 選択不可',
        'alert_select_date_time' => '日付と時間を選択してください',
        'date_locale' => 'ja-JP',
    ]
];