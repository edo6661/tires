<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('reset-password.email.subject', ['app_name' => config('app.name')]) }}</title>
    <style>
        /* CSS Anda tetap sama, tidak perlu diubah */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; margin: 0; padding: 20px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; }
        .email-wrapper { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 20px; overflow: hidden; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1); position: relative; }
        .email-wrapper::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 4px; background: linear-gradient(90deg, #667eea, #764ba2, #f093fb, #667eea); background-size: 300% 100%; animation: gradient 3s ease infinite; }
        @keyframes gradient { 0% { background-position: 0% 50%; } 50% { background-position: 100% 50%; } 100% { background-position: 0% 50%; } }
        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 40px 30px; text-align: center; position: relative; overflow: hidden; }
        .header::before { content: ''; position: absolute; top: -50%; left: -50%; width: 200%; height: 200%; background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="50" cy="50" r="2" fill="rgba(255,255,255,0.1)"/></svg>') repeat; animation: float 20s linear infinite; }
        @keyframes float { 0% { transform: translate(-50%, -50%) rotate(0deg); } 100% { transform: translate(-50%, -50%) rotate(360deg); } }
        .header-content { position: relative; z-index: 2; }
        .header h1 { font-size: 28px; font-weight: 700; margin-bottom: 8px; text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); }
        .header-subtitle { font-size: 16px; opacity: 0.9; font-weight: 300; }
        .content { padding: 50px 40px; background: #ffffff; }
        .icon-container { text-align: center; margin-bottom: 30px; }
        .security-icon { width: 80px; height: 80px; background: linear-gradient(135deg, #667eea, #764ba2); border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 20px; box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3); }
        .security-icon::before { content: '🔐'; font-size: 36px; }
        .greeting { font-size: 24px; font-weight: 600; color: #2d3748; margin-bottom: 10px; }
        .content p { color: #4a5568; margin-bottom: 20px; font-size: 16px; line-height: 1.7; }
        .button-container { text-align: center; margin: 40px 0; }
        .reset-button { display: inline-block; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #ffffff !important; padding: 16px 40px; text-decoration: none; border-radius: 50px; font-weight: 600; font-size: 16px; transition: all 0.3s ease; box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4); position: relative; overflow: hidden; }
        .reset-button::before { content: ''; position: absolute; top: 0; left: -100%; width: 100%; height: 100%; background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent); transition: left 0.5s; }
        .reset-button:hover::before { left: 100%; }
        .reset-button:hover { transform: translateY(-2px); box-shadow: 0 12px 35px rgba(102, 126, 234, 0.5); color: #ffffff !important; }
        .url-section { background: #f7fafc; border-radius: 12px; padding: 25px; margin: 30px 0; border-left: 4px solid #667eea; }
        .url-section h4 { color: #2d3748; margin-bottom: 15px; font-size: 16px; font-weight: 600; }
        .url-box { word-break: break-all; background: #edf2f7; padding: 15px; border-radius: 8px; color: #4a5568; font-size: 14px; font-family: 'Courier New', monospace; border: 1px solid #e2e8f0; }
        .warning-box { background: linear-gradient(135deg, #fed7d7, #feb2b2); border-radius: 12px; padding: 20px; margin: 25px 0; border-left: 4px solid #f56565; }
        .warning-box p { color: #742a2a; margin: 0; font-weight: 500; }
        .info-box { background: linear-gradient(135deg, #bee3f8, #90cdf4); border-radius: 12px; padding: 20px; margin: 25px 0; border-left: 4px solid #4299e1; }
        .info-box p { color: #2a4365; margin: 0; font-weight: 500; }
        .footer { background: linear-gradient(135deg, #f7fafc 0%, #edf2f7 100%); padding: 40px 30px; text-align: center; border-top: 1px solid #e2e8f0; }
        .footer-content { max-width: 400px; margin: 0 auto; }
        .security-notice { background: #4a5568; color: white; padding: 15px; border-radius: 10px; margin-bottom: 20px; font-size: 14px; font-weight: 500; }
        .security-notice::before { content: '🛡️ '; margin-right: 8px; }
        .copyright { color: #718096; font-size: 14px; margin: 0; }
        .divider { height: 1px; background: linear-gradient(90deg, transparent, #e2e8f0, transparent); margin: 30px 0; }
        @media (max-width: 600px) { body { padding: 10px; } .content { padding: 30px 25px; } .header { padding: 30px 20px; } .header h1 { font-size: 24px; } .reset-button { padding: 14px 30px; font-size: 15px; } .greeting { font-size: 22px; } }
        @media (prefers-color-scheme: dark) { .url-box { background: #2d3748; color: #e2e8f0; border-color: #4a5568; } }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="header">
            <div class="header-content">
                <h1>{{ config('app.name') }}</h1>
                <div class="header-subtitle">{{ __('reset-password.email.header_subtitle') }}</div>
            </div>
        </div>
        
        <div class="content">
            <div class="icon-container">
                <div class="security-icon"></div>
            </div>
            
            <div class="greeting">{{ __('reset-password.email.greeting', ['user_name' => $user->full_name]) }}</div>
            
            <p>{{ __('reset-password.email.intro') }}</p>
            
            <div class="button-container">
                <a href="{{ $resetUrl }}" class="reset-button">{{ __('reset-password.email.button_text') }}</a>
            </div>
            
            <div class="url-section">
                <h4>{{ __('reset-password.email.alt_link_header') }}</h4>
                <p style="margin-bottom: 10px; font-size: 14px;">{{ __('reset-password.email.alt_link_instruction') }}</p>
                <div class="url-box">{{ $resetUrl }}</div>
            </div>
            
            <div class="divider"></div>
            
            <div class="warning-box">
                <p><strong>{{ __('reset-password.email.warning_header') }}</strong> {!! __('reset-password.email.warning_body', ['count' => config('auth.passwords.users.expire', 60)]) !!}</p>
            </div>
            
            <div class="info-box">
                <p><strong>{{ __('reset-password.email.info_header') }}</strong> {{ __('reset-password.email.info_body') }}</p>
            </div>
        </div>
        
        <div class="footer">
            <div class="footer-content">
                <div class="security-notice">
                    {{ __('reset-password.email.footer_security_notice') }}
                </div>
                <p class="copyright">{{ __('reset-password.email.copyright', ['year' => date('Y'), 'app_name' => config('app.name')]) }}</p>
            </div>
        </div>
    </div>
</body>
</html>