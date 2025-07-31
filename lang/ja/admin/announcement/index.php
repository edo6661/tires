<?php

return [
    // Page Header
    'title' => 'お知らせ管理',
    'subtitle' => '顧客向けのお知らせを管理します',
    'add_button' => 'お知らせを追加',

    // Stats Cards
    'stats' => [
        'total' => '総お知らせ数',
        'active' => '有効',
        'inactive' => '無効',
        'today' => '本日',
    ],

    // Filters & Search Section
    'filters' => [
        'title' => '絞り込みと検索',
        'status_label' => 'ステータス',
        'all_statuses' => 'すべてのステータス',
        'start_date_label' => '開始日',
        'end_date_label' => '終了日',
        'search_label' => '検索',
        'search_placeholder' => 'タイトルや内容で検索...',
        'filter_button' => '絞り込む',
        'reset_button' => 'リセット',
    ],

    // Bulk Actions Bar
    'bulk_actions' => [
        'activate_button' => '有効化',
        'deactivate_button' => '無効化',
        'delete_button' => '削除',
    ],

    // Announcements List
    'list' => [
        'title' => 'お知らせ一覧',
    ],

    // Table
    'table' => [
        'headers' => [
            'title' => 'タイトル',
            'content' => '内容',
            'publish_date' => '公開日',
            'status' => 'ステータス',
            'actions' => '操作',
        ],
        'status_active' => '有効',
        'status_inactive' => '無効',
        'actions_tooltip' => [
            'view' => '詳細を表示',
            'edit' => '編集',
            'deactivate' => '無効化',
            'activate' => '有効化',
            'delete' => '削除',
        ],
    ],

    // Empty State
    'empty' => [
        'title' => 'お知らせが見つかりません',
        'description' => 'まだお知らせが作成されていないか、適用されたフィルターに一致するものがありません。',
    ],

    // Delete Modal
    'delete_modal' => [
        'title' => '削除の確認',
        'cancel_button' => 'キャンセル',
        'delete_button' => '削除',
    ],

    // JavaScript translations
    'js' => [
        'show_filters' => 'フィルターを表示',
        'hide_filters' => 'フィルターを非表示',
        'selected_text' => ':count 件の項目が選択されています',
        'delete_single_confirm' => 'このお知らせを削除してもよろしいですか？',
        'delete_multiple_confirm' => ':count件のお知らせを削除してもよろしいですか？',
        'select_at_least_one' => '少なくとも1つのお知らせを選択してください。',
        'error_status' => 'ステータスの変更中にエラーが発生しました。',
        'error_delete' => '削除中にエラーが発生しました。',
        'error_toggle_status' => 'ステータスの変更中にエラーが発生しました',
    ],
];