<?php

return [
    'page_title' => '空き状況の確認',
    'page_subtitle' => '予約の時間的な空き状況を確認します',

    'form' => [
        'date_label' => '予約日',
        'menu_label' => 'メニューを選択',
        'menu_placeholder' => '-- メニューを選択してください --',
        'menu_minutes' => '分',
    ],

    'buttons' => [
        'previous' => '前日',
        'next' => '翌日',
    ],

    'summary' => [
        'date' => '日付',
        'year' => '年',
        'month' => '月',
        'current_time' => '現在時刻',
    ],

    'loading_text' => '空き状況データを読み込み中...',

    'availability' => [
        'title' => '時間の空き状況',
        'available_slots' => '件の空きスロット',
        'reserved_slots' => '件の予約済みスロット',
        'blocked_slots' => '件のブロック済みスロット',
    ],
    
    'status' => [
        'available' => '予約可能',
        'reserved' => '予約済み',
        'blocked' => 'ブロック済み',
    ],

    'legend' => [
        'available' => [
            'title' => '予約可能',
            'description' => '予約が可能です',
        ],
        'reserved' => [
            'title' => '予約済み',
            'description' => '既に予約が入っています',
        ],
        'blocked' => [
            'title' => 'ブロック済み',
            'description' => 'ブロック期間',
        ],
    ],

    'empty' => [
        'title' => '空き状況データがありません',
        'description' => '選択されたメニューと日付に空きがありません',
    ],
    
    'script_texts' => [
        'months' => [
            '1月', '2月', '3月', '4月', '5月', '6月',
            '7月', '8月', '9月', '10月', '11月', '12月'
        ],
        'days' => ['日曜日', '月曜日', '火曜日', '水曜日', '木曜日', '金曜日', '土曜日'],
        'alerts' => [
            'load_fail' => '空き状況データの読み込みに失敗しました',
            'load_error' => 'データの読み込み中にエラーが発生しました',
            'slot_unavailable' => 'この時間帯は予約できません',
            'select_slot_first' => '最初に時間帯を選択してください',
        ],
        'selected_time_at' => ' ',
    ],
];