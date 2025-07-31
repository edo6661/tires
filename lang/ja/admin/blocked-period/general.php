<?php

return [
    'title' => 'ブロック期間',
    'all_menus' => 'すべてのメニュー',

    'flash_messages' => [
        'create_success'      => 'ブロック期間が正常に作成されました。',
        'create_error'        => 'ブロック期間の作成中にエラーが発生しました: :message',
        'update_success'      => 'ブロック期間が正常に更新されました。',
        'update_error'        => 'ブロック期間の更新中にエラーが発生しました: :message',
        'delete_success'      => 'ブロック期間が正常に削除されました。',
        'delete_error'        => 'ブロック期間の削除中にエラーが発生しました: :message',
        'not_found'           => 'ブロック期間が見つかりません。',
        'conflict'            => '選択したスケジュールは、既存のブロック期間と競合しています。',
        'bulk_delete_success' => ':count 件のブロック期間を正常に削除しました。',
        'bulk_delete_error'   => '一括削除中にエラーが発生しました: :message',
    ],

    'calendar' => [
        'all_menus_label' => 'すべてのメニュー',
    ],

    'confirmation' => [
        'delete_title'   => 'ブロック期間を削除',
        'delete_message' => 'このブロック期間を本当に削除しますか？',
        'bulk_delete_title' => '選択した期間を削除',
        'bulk_delete_message' => '選択したブロック期間を本当に削除しますか？',
    ],
];
