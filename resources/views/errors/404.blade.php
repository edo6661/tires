<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --color-brand: #004080; 
            --color-sub: #E6F0FA; 
            --color-main-button: #FF9900;
            --color-secondary-button: #CCCCCC; 
            --color-main-text: #333333; 
            --color-link: #004080; 
            --color-link-hover: #002244; 
            --color-disabled: #E0E0E0;
            --color-footer-bg: #004080;
            --color-footer-text: #FFFFFF;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: var(--color-main-text);
            background-color: #ffffff;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .error-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 60vh;
            text-align: center;
            gap: 24px;
            padding: 40px 0;
        }

        .error-icon {
            color: var(--color-brand);
            margin-bottom: 16px;
        }

        .error-icon i {
            font-size: 128px;
            opacity: 0.5;
        }

        .error-code {
            font-size: 96px;
            font-weight: bold;
            color: var(--color-brand);
            margin-bottom: 16px;
            line-height: 1;
        }

        .error-title {
            font-size: 32px;
            font-weight: 600;
            color: var(--color-main-text);
            margin-bottom: 16px;
        }

        .error-description {
            font-size: 18px;
            color: rgba(51, 51, 51, 0.8);
            max-width: 448px;
            margin-bottom: 32px;
        }

        .button-group {
            display: flex;
            flex-direction: column;
            gap: 16px;
            align-items: center;
        }

        @media (min-width: 640px) {
            .button-group {
                flex-direction: row;
            }
        }

        .btn {
            display: inline-block;
            border-radius: 50px;
            font-size: 18px;
            font-weight: 600;
            padding: 12px 32px;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background-color: var(--color-main-button);
            color: var(--color-footer-text);
        }

        .btn-primary:hover {
            background-color: #e68a00;
        }

        .btn-secondary {
            background-color: var(--color-secondary-button);
            color: var(--color-main-text);
        }

        .btn-secondary:hover {
            background-color: var(--color-disabled);
        }

        .help-section {
            margin-top: 48px;
            padding: 24px;
            background-color: var(--color-sub);
            border-radius: 8px;
            max-width: 512px;
        }

        .help-title {
            font-size: 24px;
            font-weight: 600;
            color: var(--color-brand);
            margin-bottom: 12px;
        }

        .help-list {
            font-size: 16px;
            color: rgba(51, 51, 51, 0.8);
            text-align: left;
            list-style: none;
        }

        .help-list li {
            margin-bottom: 8px;
        }

        .contact-section {
            margin-top: 16px;
            padding-top: 16px;
            border-top: 1px solid rgba(224, 224, 224, 0.3);
        }

        .contact-text {
            font-size: 16px;
            color: var(--color-main-text);
        }

        .contact-link {
            color: var(--color-link);
            text-decoration: none;
            font-weight: 500;
        }

        .contact-link:hover {
            color: var(--color-link-hover);
        }

        @media (max-width: 768px) {
            .error-code {
                font-size: 72px;
            }

            .error-title {
                font-size: 24px;
            }

            .error-description {
                font-size: 16px;
            }

            .error-icon i {
                font-size: 96px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="error-container">
            <div class="error-icon">
                <i class="fas fa-exclamation-triangle" aria-hidden="true"></i>
            </div>
            
            <h1 class="error-code">404</h1>
            
            <h2 class="error-title">
                {{ __('404.title') }}
            </h2>
            
            <p class="error-description">
                {{ __('404.description') }}
            </p>
            
            <div class="button-group">
                <a href="{{ route('home',[
                    'locale' => app()->getLocale()
                ]) }}" class="btn btn-primary">
                    {{ __('404.back_to_home') }}
                </a>
                
                <button onclick="history.back()" class="btn btn-secondary">
                    {{ __('404.go_back') }}
                </button>
            </div>
            
            <div class="help-section">
                <h3 class="help-title">
                    {{ __('404.help_title') }}
                </h3>
                <ul class="help-list">
                    <li>• {{ __('404.help_check_url') }}</li>
                    <li>• {{ __('404.help_use_navigation') }}</li>
                    <li>• {{ __('404.help_contact_support') }}</li>
                </ul>
                
                @if(isset($businessSettings) && $businessSettings->phone_number)
                    <div class="contact-section">
                        <p class="contact-text">
                            {{ __('404.contact_us') }}: 
                            <a href="tel:{{ $businessSettings->phone_number }}" class="contact-link">
                                {{ $businessSettings->phone_number }}
                            </a>
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</body>
</html>