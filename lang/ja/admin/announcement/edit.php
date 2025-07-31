<?php

return [
    'title' => 'お知らせを編集',
    'subtitle' => '多言語のお知らせ情報を更新',
    'back_button' => '戻る',

    'form' => [
        'card_title' => 'お知らせ情報の編集',
        'card_subtitle' => '以下のフォームを更新して、多言語のお知らせを編集してください',
        'created_at' => '作成日時',

        'tabs' => [
            'english' => '英語',
            'japanese' => '日本語',
            'translation_filled_tooltip' => '翻訳済み',
        ],

        'english_section' => [
            'title' => '英語のコンテンツ',
            'subtitle' => '英語でタイトルとコンテンツを編集',
            'label_title' => 'タイトル (英語)',
            'placeholder_title' => '英語でお知らせのタイトルを入力してください...',
            'max_chars' => '最大255文字',
            'label_content' => 'コンテンツ (英語)',
            'placeholder_content' => '英語でお知らせの内容を入力してください...',
            'help_content' => '英語でお知らせの内容を記入してください',
        ],

        'japanese_section' => [
            'title' => '日本語のコンテンツ',
            'subtitle' => '日本語でタイトルとコンテンツを編集',
            'label_title' => 'タイトル (日本語)',
            'placeholder_title' => '日本語でお知らせのタイトルを入力してください...',
            'max_chars' => '最大255文字',
            'label_content' => 'コンテンツ (日本語)',
            'placeholder_content' => '日本語でお知らせの内容を入力してください...',
            'help_content' => '日本語でお知らせの内容を記入してください',
        ],

        'common_settings' => [
            'title' => '一般設定',
            'publish_date_label' => '公開日時',
            'publish_date_help' => '空の場合、現在時刻が使用されます',
            'status_label' => 'ステータス',
            'status_active' => '有効',
            'status_inactive' => '無効',
            'current_status' => '現在のステータス:',
        ],

        'translation_info' => [
            'title' => '翻訳情報',
            'language_en' => '英語',
            'language_ja' => '日本語',
            'available' => '利用可能',
            'not_available' => '利用不可',
        ],

        'announcement_info' => [
            'title' => 'お知らせ情報',
            'created_at' => '作成日時',
            'updated_at' => '更新日時',
            'published_at' => '公開日時',
            'id' => 'ID',
        ],

        'preview' => [
            'title' => '変更のプレビュー',
            'hide_button' => 'プレビューを非表示',
            'show_button' => 'プレビューを表示',
            'placeholder_title' => 'お知らせのタイトルはここに表示されます',
            'placeholder_content' => 'お知らせの内容はここに表示されます',
            'date_not_selected' => '日付が選択されていません',
            'preview_lang_en' => 'English',
            'preview_lang_ja' => '日本語',
            'status_active' => '有効',
            'status_inactive' => '無効',
        ],

        'buttons' => [
            'cancel' => 'キャンセル',
            'view_details' => '詳細を見る',
            'update' => 'お知らせを更新',
        ],
    ],
];