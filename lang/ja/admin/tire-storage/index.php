<?php

return [
    'title' => 'タイヤ保管管理',
    'description' => '顧客のタイヤ保管データを管理する',
    'add_storage' => '保管追加',
    
    'stats' => [
        'total_storages' => '総保管数',
        'active' => 'アクティブ',
        'ended' => '終了',
        'average_fee' => '平均料金',
    ],
    
    'filters' => [
        'title' => 'フィルター＆検索',
        'show_filters' => 'フィルターを表示',
        'hide_filters' => 'フィルターを非表示',
        'status' => 'ステータス',
        'all_statuses' => '全てのステータス',
        'tire_brand' => 'タイヤブランド',
        'tire_brand_placeholder' => 'タイヤブランドを検索...',
        'tire_size' => 'タイヤサイズ',
        'tire_size_placeholder' => 'タイヤサイズを検索...',
        'customer_name' => '顧客名',
        'customer_name_placeholder' => '顧客名を検索...',
        'filter' => 'フィルター',
        'reset' => 'リセット',
    ],
    
    'bulk_actions' => [
        'selected_items' => '件選択中',
        'end_storage' => '保管終了',
        'delete' => '削除',
    ],
    
    'table' => [
        'title' => 'タイヤ保管リスト',
        'customer' => '顧客',
        'tire_info' => 'タイヤ情報',
        'dates' => '日付',
        'fee' => '料金',
        'status' => 'ステータス',
        'actions' => 'アクション',
        'size' => 'サイズ',
        'start' => '開始',
        'end' => '終了',
    ],
    
    'actions' => [
        'view_details' => '詳細を見る',
        'edit' => '編集',
        'end_storage' => '保管終了',
        'delete' => '削除',
    ],
    
    'empty_state' => [
        'title' => '保管データがありません',
        'description' => 'タイヤ保管がまだ作成されていないか、適用されたフィルターに一致するものがありません。',
        'add_storage' => '保管追加',
    ],
    
    'modals' => [
        'delete' => [
            'title' => '削除の確認',
            'single_message' => 'このタイヤ保管を削除してもよろしいですか？',
            'multiple_message' => ':count件の保管アイテムを削除してもよろしいですか？',
            'cancel' => 'キャンセル',
            'delete' => '削除',
        ],
        'end_storage' => [
            'single_message' => 'このタイヤ保管を終了してもよろしいですか？',
            'multiple_message' => ':count件の保管アイテムを終了してもよろしいですか？',
        ],
    ],
    
    'alerts' => [
        'error_occurred' => 'エラーが発生しました。',
        'deletion_error' => '削除中にエラーが発生しました。',
    ],
];