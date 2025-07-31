<?php

return [
    // Page details
    'title' => '新規ブロック期間の作成',
    'description' => '特定または全てのメニューの予約を不可にする期間を設定します。',

    // Buttons
    'back_to_list_button' => '一覧に戻る',
    'save_button' => 'ブロック期間を保存',
    'saving_button' => '保存中...',

    // Form fields
    'form' => [
        'all_menus_label' => '全てのメニューをブロックしますか？',
        'select_menu_label' => '特定のメニューを選択',
        'select_menu_placeholder' => '-- メニューを選択してください --',
        'start_time_label' => '開始日時',
        'end_time_label' => '終了日時',
        'reason_label' => '理由',
        'reason_placeholder' => '例：定期メンテナンス、祝日、貸切イベントなど',
    ],

    // Conflict alert (Alpine.js)
    'conflict_alert' => [
        'title' => 'スケジュールの競合が検出されました！',
        'message' => '入力された期間は、以下のスケジュールと重複しています：',
    ],

    // Flash messages from controller
    'flash_messages' => [
        'create_success' => 'ブロック期間が正常に作成されました。',
        'create_error' => 'エラーが発生しました: :message',
        'conflict_error' => '既存のブロック期間と時間の競合が発生しました。',
    ],

    // Validation messages from BlockedPeriodRequest
    'validation' => [
        'menu_required_if_not_all' => '全てのメニューをブロックしない場合は、メニューを選択する必要があります。',
        'start_before_end' => '開始日時は終了日時より前の時刻である必要があります。',
        'min_duration' => '最短期間は15分です。',
        'max_duration' => '最長期間は30日です。',
        'all_menus_boolean' => '「全てのメニュー」フィールドは true または false である必要があります。',
        'conflict_message' => "以下のブロック期間と時間が競合しています：\n:details",

        'menu_id' => [
            'exists' => '選択されたメニューは無効です。',
        ],
        'start_datetime' => [
            'required' => '開始日時は必須です。',
            'date' => '開始日時の形式が無効です。',
            'after_or_equal' => '開始日時は現在時刻以降である必要があります。',
        ],
        'end_datetime' => [
            'required' => '終了日時は必須です。',
            'date' => '終了日時の形式が無効です。',
            'after' => '終了日時は開始日時より後の時刻である必要があります。',
        ],
        'reason' => [
            'required' => '理由は必須です。',
            'string' => '理由は文字列である必要があります。',
            'max' => '理由は500文字以内で入力してください。',
            'min' => '理由は3文字以上で入力してください。',
        ],
    ],

    // Attributes for validation
    'attributes' => [
        'menu_id' => 'メニュー',
        'start_datetime' => '開始日時',
        'end_datetime' => '終了日時',
        'reason' => '理由',
        'all_menus' => '全てのメニュー',
    ],
];