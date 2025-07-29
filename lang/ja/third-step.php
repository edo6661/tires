<?php

return [
    // Page Titles
    'title_auth' => '予約内容の確認',
    'title_guest' => '連絡先入力と予約内容の確認',
    'subtitle_auth' => '予約内容をご確認の上、予約を確定してください',
    'subtitle_guest' => '連絡先情報を入力し、予約内容をご確認ください',

    // Guest Form
    'form_title' => '連絡先情報',
    'labels' => [
        'full_name' => '氏名',
        'full_name_kana' => '氏名（カナ）',
        'email' => 'メールアドレス',
        'phone_number' => '電話番号',
    ],
    'placeholders' => [
        'full_name' => '氏名を入力してください',
        'full_name_kana' => '氏名（カナ）を入力してください',
        'email' => 'メールアドレスを入力してください',
        'phone_number' => '電話番号を入力してください',
    ],
    'form_button_back' => '戻る',
    'form_button_continue' => '確認画面へ進む',

    // Confirmation View
    'summary_title' => '予約概要',
    'service_details_title' => 'サービス詳細',
    'customer_info_title' => 'お客様情報',
    'details' => [
        'service' => 'サービス:',
        'duration' => '所要時間:',
        'date' => '日付:',
        'time' => '時間:',
        'name' => '氏名:',
        'name_kana' => '氏名（カナ）:',
        'email' => 'メール:',
        'phone' => '電話番号:',
        'status' => '会員ステータス:',
    ],
    'member_status' => [
        'member' => 'RESERVA会員',
        'guest' => 'ゲスト',
    ],
    'important_notes_title' => '重要事項',
    'notes' => [
        'item1' => '予約時間の5分前にお越しください',
        'item2' => '予約確定後のキャンセルはできません',
        'item3' => '予約の変更は、少なくとも24時間前までにお願いします',
        'item4' => '本人確認のため、有効な身分証明書をご持参ください',
    ],
    'terms_agree' => 'に同意します',
    'terms_and_conditions' => '利用規約',
    'terms_and' => 'と',
    'terms_privacy_policy' => 'プライバシーポリシー',
    
    // Actions
    'action_back_guest_edit' => '情報を編集',
    'action_complete_booking' => '予約を完了する',
    'duration_unit' => '分',

    // JavaScript Translations
    'js' => [
        'booking_info_not_found' => '予約情報が見つかりません。最初からやり直してください。',
        'error_loading_service' => 'サービスの読み込みエラー',
        'error_service_not_found' => 'エラー：サービスが見つかりません',
        'not_applicable' => '適用外',
        'date_locale' => 'ja-JP',
        'validation' => [
            'full_name_required' => '氏名は必須です',
            'full_name_kana_required' => '氏名（カナ）は必須です',
            'email_required' => 'メールアドレスは必須です',
            'email_invalid' => '有効なメールアドレスを入力してください',
            'phone_required' => '電話番号は必須です',
            'terms_required' => '利用規約とプライバシーポリシーに同意する必要があります',
        ],
    ],
];