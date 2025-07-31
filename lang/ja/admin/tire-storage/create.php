<?php

return [
    'page_title' => 'タイヤ保管の追加',
    'page_subtitle' => '顧客の新しいタイヤ保管記録を作成します',
    'back_button' => '戻る',

    'form_title' => '新規タイヤ保管フォーム',

    'form' => [
        'customer' => [
            'label' => '顧客',
            'select_placeholder' => '顧客を選択',
        ],
        'tire_info' => [
            'title' => 'タイヤ情報',
            'brand_label' => 'タイヤブランド',
            'brand_placeholder' => '例：ブリヂストン、ミシュラン、グッドイヤー',
            'size_label' => 'タイヤサイズ',
            'size_placeholder' => '例：225/60R16、185/65R15',
        ],
        'schedule' => [
            'title' => '保管スケジュール',
            'start_date_label' => '保管開始日',
            'end_date_label' => '予定終了日',
        ],
        'fee_status' => [
            'title' => '料金とステータス',
            'fee_label' => '保管料金 (IDR)',
            'fee_placeholder' => '0',
            'fee_helper' => '空欄の場合は自動計算されます (月額50,000ルピア)',
            'calculated_fee_text' => '計算料金：IDR',
            'status_label' => 'ステータス',
            'status_active' => '有効',
            'status_ended' => '終了',
        ],
        'notes' => [
            'label' => '備考',
            'placeholder' => 'このタイヤ保管に関する追加の備考...',
        ],
    ],

    'cancel_button' => 'キャンセル',
    'save_button' => '保管を保存',

    'info_box' => [
        'title' => '重要事項',
        'point1' => '予定終了日は保管開始日より後でなければなりません。',
        'point2' => '保管料金は空欄の場合、自動計算されます（月額50,000ルピア）。',
        'point3' => '「有効」ステータスは保管が進行中であることを意味します。',
        'point4' => '「終了」ステータスは保管が完了したことを意味します。',
    ],
];