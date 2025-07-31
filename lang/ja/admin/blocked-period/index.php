  <?php
// lang/ja/admin/blocked-period/index.php

return [
    'page' => [
        'title' => '予約不可期間の管理',
        'subtitle' => '予約の受付を停止する期間を管理します',
    ],
    'add_button' => '期間を追加',

    'stats' => [
        'total' => '合計期間数',
        'active' => '現在有効',
        'upcoming' => '今後予定',
        'expired' => '期限切れ',
    ],

    'filters' => [
        'title' => 'フィルターと検索',
        'show' => 'フィルターを表示',
        'hide' => 'フィルターを非表示',
        'menu_label' => 'メニュー',
        'menu_all' => '全てのメニュー',
        'status_label' => 'ステータス',
        'status_all' => '全てのステータス',
        'status_active' => '有効',
        'status_upcoming' => '予定',
        'status_expired' => '期限切れ',
        'start_date_label' => '開始日',
        'end_date_label' => '終了日',
        'all_menus_label' => '全メニューをブロック対象のみ表示',
        'search_label' => '検索',
        'search_placeholder' => '理由やメニュー名で検索...',
        'filter_button' => '絞り込む',
        'reset_button' => 'リセット',
    ],

    'bulk_actions' => [
        'items_selected' => '件選択中',
        'delete_button' => '削除',
    ],

    'list' => [
        'title' => '予約不可期間リスト',
    ],

    'table' => [
        'header' => [
            'menu' => 'メニュー',
            'time' => '時間',
            'duration' => '期間',
            'reason' => '理由',
            'status' => 'ステータス',
            'actions' => '操作',
        ],
        'body' => [
            'all_menus_badge' => '全てのメニュー',
            'menu_not_found' => 'メニューが見つかりません',
            'status_active' => '有効',
            'status_upcoming' => '予定',
            'status_expired' => '期限切れ',
            'action_tooltips' => [
                'detail' => '詳細',
                'edit' => '編集',
                'delete' => '削除',
            ],
        ],
    ],

    'empty' => [
        'title' => '予約不可期間がありません',
        'message' => '作成された予約不可期間がないか、適用されたフィルターに一致するものがありません。',
        'add_button' => '最初の期間を追加',
    ],

    'delete_modal' => [
        'title' => '削除の確認',
        'confirm_button' => '削除',
        'cancel_button' => 'キャンセル',
        'message_single' => 'この予約不可期間を削除してもよろしいですか？',
        'message_multiple' => ':count 件の予約不可期間を削除してもよろしいですか？',
    ],

    'alerts' => [
        'delete_error' => '削除中にエラーが発生しました。',
    ],
];