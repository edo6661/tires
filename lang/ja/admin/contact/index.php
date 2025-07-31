<?php
// lang/ja/admin/contact/index.php

return [
    'page_title' => 'お問い合わせ管理',
    'page_subtitle' => '顧客からのお問い合わせメッセージを管理します',

    'add_button' => 'お問い合わせを追加',

    'stats' => [
        'total' => '総お問い合わせ数',
        'pending' => '未対応',
        'replied' => '返信済み',
        'today' => '本日',
    ],

    'filter' => [
        'title' => 'フィルターと検索',
        'show_button' => 'フィルターを表示',
        'hide_button' => 'フィルターを非表示',
        'status_label' => 'ステータス',
        'status_all' => 'すべてのステータス',
        'start_date_label' => '開始日',
        'end_date_label' => '終了日',
        'search_label' => '検索',
        'search_placeholder' => '名前、メール、件名で検索...',
        'filter_button' => 'フィルター',
        'reset_button' => 'リセット',
    ],

    'bulk_actions' => [
        'items_selected' => ':count 件選択中',
        'delete_button' => '削除',
    ],

    'table' => [
        'title' => 'お問い合わせ一覧',
        'header' => [
            'sender' => '送信者',
            'subject' => '件名',
            'message' => 'メッセージ',
            'date' => '日付',
            'status' => 'ステータス',
            'actions' => '操作',
        ],
        'action' => [
            'view_tooltip' => '詳細を表示',
            'reply_tooltip' => 'クイック返信',
            'delete_tooltip' => '削除',
        ],
    ],

    'status' => [
        'pending' => '未対応',
        'replied' => '返信済み',
    ],

    'empty' => [
        'title' => 'お問い合わせがありません',
        'message' => '受信したお問い合わせメッセージがないか、適用されたフィルターに一致するものがありません。',
    ],

    'modal' => [
        'cancel_button' => 'キャンセル',
        'reply' => [
            'title' => 'クイック返信',
            'placeholder' => '返信内容を入力してください...',
            'send_button' => '返信を送信',
        ],
        'delete' => [
            'title' => '削除の確認',
            'confirm_button' => '削除する',
            'confirm_message_single' => 'このお問い合わせを削除してもよろしいですか？',
            'confirm_message_multiple' => ':count件のお問い合わせを削除してもよろしいですか？',
        ],
    ],

    'alert' => [
        'reply_empty' => '返信メッセージは空にできません。',
        'reply_error' => '返信の送信中にエラーが発生しました。',
        'delete_error' => '削除中にエラーが発生しました。',
        'delete_success' => 'お問い合わせを正常に削除しました。',
        'reply_success' => '返信を正常に送信しました。',
    ],
];