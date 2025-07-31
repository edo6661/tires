<?php

return [
    'header' => [
        'title' => 'タイヤ保管詳細',
        'description' => '顧客のタイヤ保管の詳細情報。',
    ],
    'buttons' => [
        'end_storage' => '保管を終了',
        'edit' => '編集',
        'delete' => '削除',
    ],
    'customer_info' => [
        'title' => '顧客情報',
        'joined_on' => '登録日: :date',
    ],
    'storage_details' => [
        'title' => '保管詳細',
        'tire_info_title' => 'タイヤ情報',
        'tire_brand_label' => 'タイヤブランド',
        'tire_size_label' => 'タイヤサイズ',
        'tire_type_label' => 'タイヤタイプ',
        'storage_info_title' => '保管情報',
        'start_date_label' => '開始日',
        'planned_end_date_label' => '終了予定日',
        'storage_fee_label' => '保管料',
        'notes_title' => '備考',
        'duration_days' => '保管期間（日）',
        'days_passed' => '経過日数',
        'days_remaining' => '残り日数',
    ],
    'timeline' => [
        'title' => '保管タイムライン',
        'created_title' => '保管作成',
        'created_desc' => ':name 様のタイヤ保管が作成されました。',
        'started_title' => '保管開始',
        'started_desc' => 'タイヤ :brand サイズ :size の保管が開始されました。',
        'ended_title' => '保管終了',
        'ended_desc' => 'タイヤの保管期間が終了しました。',
        'planned_end_title' => '終了予定日',
        'planned_end_desc' => '保管期間の終了予定日です。',
    ],
    'modals' => [
        'end_confirm' => 'このタイヤ保管を終了しますか？',
        'end_error' => '保管の終了中にエラーが発生しました。',
        'delete_confirm' => 'このタイヤ保管を削除してもよろしいですか？この操作は元に戻せません。',
        'delete_error' => '保管の削除中にエラーが発生しました。',
    ],
    'status' => [
        'active' => '保管中',
        'ended' => '終了',
    ]
];