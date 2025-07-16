<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - {{ config('app.name') }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 0;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .header {
            background-color: #007bff;
            color: white;
            padding: 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 30px;
        }
        .content h2 {
            color: #333;
            margin-top: 0;
        }
        .content p {
            color: #666;
            margin-bottom: 15px;
        }
        .button {
            display: inline-block;
            background-color: #007bff;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }
        .button:hover {
            background-color: #0056b3;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            border-top: 1px solid #e9ecef;
        }
        .footer p {
            color: #666;
            margin: 5px 0;
            font-size: 14px;
        }
        .url-box {
            word-break: break-all;
            background: #e9ecef;
            padding: 10px;
            border-radius: 5px;
            color: #495057;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ config('app.name') }}</h1>
        </div>
        <div class="content">
            <h2>Hai {{ $user->full_name }},</h2>
            <p>Kami menerima permintaan untuk mereset password akun Anda.</p>
            <p>Klik tombol di bawah ini untuk membuat password baru:</p>
            <p style="text-align: center; margin: 30px 0;">
                <a href="{{ $resetUrl }}" class="button">Reset Password</a>
            </p>
            <p>Jika tombol di atas tidak berfungsi, Anda dapat menyalin dan menempelkan URL berikut ke browser Anda:</p>
            <div class="url-box">
                {{ $resetUrl }}
            </div>
            <p><strong>Catatan:</strong> Link reset password ini akan kedaluwarsa dalam 60 minute.</p>
            <p>Jika Anda tidak meminta reset password, abaikan email ini. Password akun Anda tidak akan berubah.</p>
        </div>
        <div class="footer">
            <p>Untuk keamanan akun Anda, jangan bagikan link ini kepada siapa pun.</p>
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. Semua hak dilindungi.</p>
        </div>
    </div>
</body>
</html>