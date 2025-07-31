<?php
// lang/ja/admin/tire-storage/edit.php

return [
    'title' => 'タイヤ保管情報の編集',
    'description' => 'このタイヤ保管記録の詳細を更新します。',
    'back_button' => '戻る',
    'form_title' => 'タイヤ保管更新フォーム',

    'customer_section' => [
        'label' => '顧客',
        'select_placeholder' => '顧客を選択',
    ],

    'tire_info_section' => [
        'title' => 'タイヤ情報',
        'brand_label' => 'タイヤメーカー',
        'brand_placeholder' => '例：ブリヂストン、ミシュラン、グッドイヤー',
        'size_label' => 'タイヤサイズ',
        'size_placeholder' => '例：225/60R16、185/65R15',
    ],

    'schedule_section' => [
        'title' => '保管スケジュール',
        'start_date_label' => '保管開始日',
        'end_date_label' => '保管終了予定日',
    ],

    'fee_status_section' => [
        'title' => '料金とステータス',
        'fee_label' => '保管料金 (:currency)',
        'fee_auto_calc_note' => '空欄の場合は自動計算されます（:currency :rate/月）',
        'calculated_fee_note' => '計算料金: :currency',
        'status_label' => 'ステータス',
        'status_active' => '保管中',
        'status_ended' => '終了',
    ],

    'notes_section' => [
        'label' => '備考',
        'placeholder' => 'このタイヤ保管に関する追加の備考...',
    ],

    'buttons' => [
        'cancel' => 'キャンセル',
        'update' => '保管情報を更新',
    ],
];