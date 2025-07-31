<?php

return [
    // Page Elements
    'page_title' => 'ブロック期間の編集',
    'page_description' => 'メニューが予約不可となる期間を更新します。',
    'back_to_list_button' => '一覧に戻る',

    // Form Labels & Placeholders
    'form' => [
        'all_menus_label' => 'すべてのメニューをブロックしますか？',
        'specific_menu_label' => '特定のメニューを選択',
        'select_menu_placeholder' => '-- メニューを選択してください --',
        'start_time_label' => '開始日時',
        'end_time_label' => '終了日時',
        'reason_label' => '理由',
        'reason_placeholder' => '例：定期メンテナンス、祝日、プライベートイベントなど',
    ],
    
    // Conflict Section
    'conflict' => [
        'title' => 'スケジュールの競合が検出されました！',
        'description' => '入力した期間は、以下のスケジュールと重複しています：',
    ],

    // Button
    'button' => [
        'save_text' => '変更を保存',
        'checking_text' => '確認中...',
    ],

    // Controller Messages
    'messages' => [
        'not_found' => 'ブロック期間が見つかりません。',
        'update_success' => 'ブロック期間が正常に更新されました。',
        'conflict' => '既存のブロック期間と時間の競合が発生しました。',
        'update_error' => 'エラーが発生しました： ',
    ],
];