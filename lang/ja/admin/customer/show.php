<?php

return [
    'header' => [
        'title' => '顧客詳細',
        'subtitle' => '顧客の詳細情報と履歴を表示します。',
        'send_message_button' => 'メッセージを送信',
        'export_button' => 'エクスポート',
    ],

    'stats' => [
        'total_reservations' => '総予約数',
        'total_amount' => '合計金額',
        'tire_storage' => 'タイヤ保管',
    ],

    'sidebar' => [
        'status_registered' => '登録済み',
        'status_guest' => 'ゲスト',
        'email' => 'メールアドレス',
        'phone' => '電話番号',
        'company' => '会社名',
        'department' => '部署',
        'dob' => '生年月日',
        'gender' => '性別',
        'guest_info' => [
            'title' => 'ゲスト顧客',
            'body' => 'この顧客はゲストとして予約を行いました。利用可能な情報は限られています。',
        ],
    ],

    'tabs' => [
        'customer_info' => '顧客情報',
        'reservation_history' => '予約履歴',
        'tire_storage' => 'タイヤ保管',
    ],

    'main_content' => [
        'customer_info' => [
            'title' => '顧客情報',
            'full_name' => '氏名',
            'full_name_kana' => '氏名（カナ）',
            'email' => 'メールアドレス',
            'phone_number' => '電話番号',
            'company_name' => '会社名',
            'department' => '部署',
            'dob' => '生年月日',
            'gender' => '性別',
            'addresses_title' => '住所',
            'company_address' => '会社住所',
            'home_address' => '自宅住所',
            'guest' => [
                'title' => 'ゲスト顧客',
                'body' => 'この顧客はゲストとして予約を行いました。基本的な予約情報のみが利用可能です。',
                'name_label' => '名前：',
                'name_kana_label' => '名前（カナ）：',
                'email_label' => 'メール：',
                'phone_label' => '電話：',
            ],
        ],
        'reservation_history' => [
            'title' => '予約履歴',
            'count_text' => '予約 :count件',
            'date_time' => '日時：',
            'people' => '人数：',
            'menu' => 'メニュー：',
            'amount' => '金額：',
            'notes' => '備考：',
            'view_details_link' => '詳細を見る',
            'no_records' => '予約履歴が見つかりません。',
        ],
        'tire_storage' => [
            'title' => 'タイヤ保管',
            'count_text' => '保管記録 :count件',
            'start_date' => '開始日：',
            'planned_end' => '終了予定日：',
            'storage_fee' => '保管料：',
            'days_remaining' => '残り日数：',
            'days_remaining_text' => ':days日',
            'notes' => '備考：',
            'no_records' => 'タイヤ保管記録が見つかりません。',
        ],
    ],

    'modal' => [
        'title' => ':name にメッセージを送信',
        'subject_label' => '件名',
        'subject_placeholder' => '件名を入力してください',
        'message_label' => 'メッセージ',
        'message_placeholder' => 'メッセージを入力してください',
        'cancel_button' => 'キャンセル',
        'send_button' => 'メッセージを送信',
    ],

    'js_alerts' => [
        'fill_fields' => '件名とメッセージの両方を入力してください',
        'send_success' => 'メッセージが正常に送信されました！',
        'send_failed' => 'メッセージの送信に失敗しました',
        'export_placeholder' => 'エクスポート機能は後ほど実装されます',
    ],
];