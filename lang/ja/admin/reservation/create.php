<?php

return [
    // Page Title
    'page_title' => '新規予約作成',

    // Form Fields & Labels
    'form' => [
        'customer_type' => '顧客タイプ',
        'registered_customer' => '登録済み顧客',
        'guest_customer' => 'ゲスト顧客',
        'select_customer' => '顧客を選択',
        'select_customer_placeholder' => '顧客を選択してください...',
        'full_name' => '氏名 *',
        'full_name_kana' => '氏名（カナ） *',
        'email' => 'メールアドレス *',
        'phone_number' => '電話番号 *',
        'menu' => 'メニュー *',
        'select_menu_placeholder' => 'メニューを選択してください...',
        'yen' => '円',
        'minutes' => '分',
        'reservation_datetime' => '予約日時 *',
        'number_of_people' => '人数 *',
        'total_amount' => '合計金額 *',
        'status' => 'ステータス',
        'notes' => '備考',
    ],

    // Status Options
    'status_options' => [
        'pending' => '保留中',
        'confirmed' => '確定済み',
        'completed' => '完了',
        'cancelled' => 'キャンセル済み',
    ],

    // Buttons
    'buttons' => [
        'select_from_calendar' => 'カレンダーから選択',
        'cancel' => 'キャンセル',
        'check_availability' => '空き状況を確認',
        'save_reservation' => '予約を保存',
        'confirm' => '確定',
    ],

    // Availability Messages
    'availability' => [
        'select_menu_and_date' => '最初にメニューと日時を選択してください。',
        'available' => 'この時間枠は予約可能です。',
        'unavailable' => 'この時間枠は利用できません。予約不可期間または他の予約と重複しています。',
        'error' => '空き状況の確認中にエラーが発生しました。',
    ],

    // Calendar Modal
    'calendar_modal' => [
        'title' => '予約したい日付を選択してください',
        'select_date' => '日付を選択',
        'select_time' => '時間を選択',
        'previous_month' => '前の月',
        'next_month' => '次の月',
        'fully_blocked_message' => 'この日付は完全にブロックされており、予約はできません。',
        'legend_title' => '凡例:',
        'legends' => [
            'fully_blocked' => '予約不可日',
            'blocked_period' => '予約不可期間',
            'has_reservation' => '予約あり',
            'mixed' => '混合 (予約不可 + 予約あり)',
            'available' => '予約可能',
            'past_date' => '過去の日付',
        ],
        'days_short' => ['日', '月', '火', '水', '木', '金', '土'],
        'months' => ['1月', '2月', '3月', '4月', '5月', '6月', '7月', '8月', '9月', '10月', '11月', '12月'],
        'alert_select_menu' => '最初にメニューを選択してください。',
        'alert_date_fully_blocked' => '選択した日付は完全にブロックされており、予約できません。',
        'alert_select_date_time' => '日付と時間の両方を選択してください。',
    ],

    // Notifications and Errors
    'notifications' => [
        'error_occurred' => 'エラーが発生しました:',
        'creation_success' => '予約が正常に作成されました。',
        'creation_failed' => '予約の作成に失敗しました: :error',
    ],

    // Validation Messages from ReservationRequest
    'validation' => [
        'user_id_required' => 'ユーザーIDは必須です。',
        'user_id_exists' => '選択されたユーザーは存在しません。',
        'menu_id_required' => 'メニューは必須です。',
        'menu_id_exists' => '選択されたメニューは存在しません。',
        'reservation_datetime_required' => '予約日時は必須です。',
        'reservation_datetime_date' => '予約日時は有効な日付である必要があります。',
        'reservation_datetime_after' => '予約日時は現在時刻より後である必要があります。',
        'number_of_people_required' => '人数は必須です。',
        'number_of_people_integer' => '人数は整数である必要があります。',
        'number_of_people_min' => '人数は最低1人である必要があります。',
        'amount_required' => '金額は必須です。',
        'amount_numeric' => '金額は数値である必要があります。',
        'amount_min' => '金額は0以上である必要があります。',
        'status_in' => '選択されたステータスは無効です。',
        'notes_string' => '備考は文字列である必要があります。',
        'full_name_required' => 'ゲスト顧客の場合、氏名は必須です。',
        'full_name_string' => '氏名は文字列である必要があります。',
        'full_name_max' => '氏名は255文字以内で入力してください。',
        'full_name_kana_required' => 'ゲスト顧客の場合、氏名（カナ）は必須です。',
        'full_name_kana_string' => '氏名（カナ）は文字列である必要があります。',
        'full_name_kana_max' => '氏名（カナ）は255文字以内で入力してください。',
        'email_required' => 'ゲスト顧客の場合、メールアドレスは必須です。',
        'email_email' => '有効なメールアドレスを入力してください。',
        'email_max' => 'メールアドレスは255文字以内で入力してください。',
        'phone_number_required' => 'ゲスト顧客の場合、電話番号は必須です。',
        'phone_number_string' => '電話番号は文字列である必要があります。',
        'phone_number_max' => '電話番号は20文字以内で入力してください。',
        'reservation_datetime_unavailable' => '選択された予約日時は利用できません。他の時間を選択してください。',
    ],
];