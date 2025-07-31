<?php
// lang/ja/admin/reservation/edit.php

return [
    // Page Title and Headers
    'title' => '予約更新 #:id',
    'error_occurred' => 'エラーが発生しました:',

    // Customer Information
    'customer_type_label' => '顧客タイプ',
    'registered_customer' => '登録済み顧客',
    'guest_customer' => 'ゲスト顧客',
    'select_customer_label' => '顧客を選択',
    'select_customer_option' => '顧客を選択してください...',

    // Guest Details
    'full_name_label' => '氏名 *',
    'full_name_kana_label' => '氏名（カナ） *',
    'email_label' => 'メールアドレス *',
    'phone_number_label' => '電話番号 *',

    // Reservation Details
    'menu_label' => 'メニュー *',
    'select_menu_option' => 'メニューを選択してください...',
    'yen' => '円',
    'minutes' => '分',
    'reservation_datetime_label' => '予約日時 *',
    'select_from_calendar_button' => 'カレンダーから選択',
    'number_of_people_label' => '人数 *',
    'total_amount_label' => '合計金額 *',
    'status_label' => 'ステータス',
    'notes_label' => '備考',

    // Status Options
    'status_pending' => '保留中',
    'status_confirmed' => '確定済み',
    'status_completed' => '完了',
    'status_cancelled' => 'キャンセル済み',

    // Action Buttons
    'cancel_button' => 'キャンセル',
    'check_availability_button' => '空き状況を確認',
    'update_reservation_button' => '予約を更新',

    // Calendar Modal
    'modal_title' => 'ご希望の予約日を選択してください',
    'select_date_label' => '日付を選択',
    'select_time_label' => '時間を選択',
    'previous_button' => '前月',
    'next_button' => '次月',
    'date_fully_blocked_message' => 'この日付は完全にブロックされており、予約はできません。',
    'modal_cancel_button' => 'キャンセル',
    'modal_confirm_button' => '確定',
    'months' => [
        '1月', '2月', '3月', '4月', '5月', '6月',
        '7月', '8月', '9月', '10月', '11月', '12月'
    ],
    'days_of_week' => [
        'sun' => '日', 'mon' => '月', 'tue' => '火', 'wed' => '水', 'thu' => '木', 'fri' => '金', 'sat' => '土'
    ],
    
    // Calendar Legend
    'legend_title' => '凡例:',
    'legend_fully_blocked' => '予約不可',
    'legend_blocked_period' => 'ブロック期間',
    'legend_has_reservation' => '予約あり',
    'legend_mixed' => '混合（ブロック＋予約）',
    'legend_available' => '予約可能',
    'legend_past_date' => '過去の日付',

    // Controller Messages
    'success_message' => '予約が正常に更新されました。',
    'reservation_not_found' => '予約が見つかりません。',
    'update_failed_message' => '予約の更新に失敗しました: ',

    // JavaScript Messages
    'js_select_menu_datetime_first' => '最初にメニューと日時を選択してください。',
    'js_timeslot_available' => 'この時間枠は予約可能です。',
    'js_timeslot_unavailable' => 'この時間枠は利用できません。ブロックされた期間または他の予約と競合しています。',
    'js_availability_error' => '空き状況の確認中にエラーが発生しました。',
    'js_select_menu_first_alert' => '最初にメニューを選択してください。',
    'js_select_date_time_alert' => '日付と時間の両方を選択してください。',
];