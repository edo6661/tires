<?php

return [
    'title' => '顧客管理',
    'description' => '顧客データと予約履歴を管理します。',

    'stats' => [
        'first_time' => '新規顧客',
        'repeat' => 'リピーター',
        'dormant' => '休眠顧客',
    ],

    'filters' => [
        'search_placeholder' => '名前、メールアドレス、電話番号で検索...',
        'all_types' => 'すべての顧客タイプ',
        'first_time' => '新規顧客',
        'repeat' => 'リピート顧客',
        'dormant' => '休眠顧客',
        'reset' => 'リセット',
    ],

    'table' => [
        'header' => [
            'customer' => '顧客',
            'contact_info' => '連絡先情報',
            'status' => 'ステータス',
            'reservations' => '予約',
            'total_amount' => '合計金額',
            'last_reservation' => '最終予約',
            'actions' => '操作',
        ],
        'status_badge' => [
            'registered' => '登録済み',
            'guest' => 'ゲスト',
        ],
        'type_badge' => [
            'first_time' => '新規',
            'repeat' => 'リピート',
            'dormant' => '休眠',
        ],
        'reservations_count' => ':count 回',
        'actions_tooltip' => [
            'view_details' => '詳細を表示',
            'send_message' => 'メッセージを送信',
        ],
        'empty' => [
            'title' => '顧客が見つかりません',
            'description' => '登録されている、または選択されたフィルターに一致する顧客がいません。',
        ],
    ],

    'pagination' => [
        'previous' => '前へ',
        'next' => '次へ',
    ],

    'modal' => [
        'title' => 'メッセージを送信',
        'subject' => '件名',
        'subject_placeholder' => '件名を入力してください',
        'message' => 'メッセージ',
        'message_placeholder' => 'メッセージ本文を入力してください',
        'cancel' => 'キャンセル',
        'send' => '送信',
    ],

    'alerts' => [
        'validation_error' => '件名とメッセージの両方を入力してください',
        'send_success' => 'メッセージが正常に送信されました！',
        'send_error' => 'メッセージの送信に失敗しました',
    ],
];