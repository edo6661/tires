<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank You for Your Inquiry - {{ config('app.name') }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }

        .email-wrapper {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
            position: relative;
        }

        .email-wrapper::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #667eea, #764ba2, #f093fb, #667eea);
            background-size: 300% 100%;
            animation: gradient 3s ease infinite;
        }

        @keyframes gradient {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="50" cy="50" r="2" fill="rgba(255,255,255,0.1)"/></svg>') repeat;
            animation: float 20s linear infinite;
        }

        @keyframes float {
            0% { transform: translate(-50%, -50%) rotate(0deg); }
            100% { transform: translate(-50%, -50%) rotate(360deg); }
        }

        .header-content {
            position: relative;
            z-index: 2;
        }

        .header h1 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .header-subtitle {
            font-size: 16px;
            opacity: 0.9;
            font-weight: 300;
        }

        .content {
            padding: 50px 40px;
            background: #ffffff;
        }

        .icon-container {
            text-align: center;
            margin-bottom: 30px;
        }

        .thank-you-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #48bb78, #38a169);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            box-shadow: 0 10px 30px rgba(72, 187, 120, 0.3);
        }

        .thank-you-icon::before {
            content: '✅';
            font-size: 36px;
        }

        .greeting {
            font-size: 24px;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 10px;
        }

        .content p {
            color: #4a5568;
            margin-bottom: 20px;
            font-size: 16px;
            line-height: 1.7;
        }

        .inquiry-summary {
            background: #f7fafc;
            border-radius: 12px;
            padding: 25px;
            margin: 30px 0;
            border-left: 4px solid #48bb78;
        }

        .summary-title {
            font-size: 18px;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 15px;
        }

        .detail-row {
            display: flex;
            margin-bottom: 12px;
        }

        .detail-row:last-child {
            margin-bottom: 0;
        }

        .detail-label {
            font-weight: 600;
            color: #2d3748;
            min-width: 100px;
            margin-right: 15px;
        }

        .detail-value {
            color: #4a5568;
            flex: 1;
        }

        .message-preview {
            background: #edf2f7;
            border-radius: 8px;
            padding: 15px;
            margin-top: 10px;
            font-size: 14px;
            line-height: 1.6;
            color: #2d3748;
            border: 1px solid #e2e8f0;
            max-height: 150px;
            overflow: hidden;
            position: relative;
        }

        .message-preview::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 30px;
            background: linear-gradient(transparent, #edf2f7);
        }

        .next-steps-box {
            background: linear-gradient(135deg, #bee3f8, #90cdf4);
            border-radius: 12px;
            padding: 20px;
            margin: 25px 0;
            border-left: 4px solid #4299e1;
        }

        .next-steps-box p {
            color: #2a4365;
            margin: 0;
            font-weight: 500;
        }

        .contact-info-box {
            background: linear-gradient(135deg, #fbb6ce, #f687b3);
            border-radius: 12px;
            padding: 20px;
            margin: 25px 0;
            border-left: 4px solid #d53f8c;
        }

        .contact-info-box p {
            color: #702459;
            margin: 0;
            font-weight: 500;
        }

        .footer {
            background: linear-gradient(135deg, #f7fafc 0%, #edf2f7 100%);
            padding: 40px 30px;
            text-align: center;
            border-top: 1px solid #e2e8f0;
        }

        .footer-content {
            max-width: 400px;
            margin: 0 auto;
        }

        .appreciation-notice {
            background: #4a5568;
            color: white;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 14px;
            font-weight: 500;
        }

        .appreciation-notice::before {
            content: '🙏 ';
            margin-right: 8px;
        }

        .copyright {
            color: #718096;
            font-size: 14px;
            margin: 0;
        }

        .divider {
            height: 1px;
            background: linear-gradient(90deg, transparent, #e2e8f0, transparent);
            margin: 30px 0;
        }

        .highlight {
            background: linear-gradient(135deg, #fef5e7, #fed7aa);
            padding: 2px 8px;
            border-radius: 4px;
            font-weight: 600;
            color: #7c2d12;
        }

        /* Responsive Design */
        @media (max-width: 600px) {
            body {
                padding: 10px;
            }
            
            .content {
                padding: 30px 25px;
            }
            
            .header {
                padding: 30px 20px;
            }
            
            .header h1 {
                font-size: 24px;
            }
            
            .greeting {
                font-size: 22px;
            }

            .detail-row {
                flex-direction: column;
            }

            .detail-label {
                min-width: auto;
                margin-right: 0;
                margin-bottom: 3px;
            }
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="header">
            <div class="header-content">
                <h1>{{ config('app.name') }}</h1>
                <div class="header-subtitle">Thank You for Your Inquiry!</div>
            </div>
        </div>
        
        <div class="content">
            <div class="icon-container">
                <div class="thank-you-icon"></div>
            </div>
            
            <div class="greeting">Hello {{ $customerName }},</div>
            
            <p>Thank you for reaching out to us! We have successfully received your inquiry and truly appreciate you taking the time to contact us.</p>
            
            <p>Our team will review your message carefully and get back to you as soon as possible. We typically respond to inquiries within <span class="highlight">24-48 hours</span> during business days.</p>
            
            <div class="inquiry-summary">
                <div class="summary-title">📋 Your Inquiry Summary</div>
                
                <div class="detail-row">
                    <div class="detail-label">Subject:</div>
                    <div class="detail-value"><strong>{{ $inquirySubject }}</strong></div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">Submitted:</div>
                    <div class="detail-value">{{ date('F j, Y \a\t g:i A') }}</div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">Contact Email:</div>
                    <div class="detail-value">{{ $customerEmail }}</div>
                </div>
                
                @if($customerPhone)
                <div class="detail-row">
                    <div class="detail-label">Phone:</div>
                    <div class="detail-value">{{ $customerPhone }}</div>
                </div>
                @endif
                
                <div class="detail-row">
                    <div class="detail-label">Message:</div>
                    <div class="detail-value">
                        <div class="message-preview">{{ $inquiryMessage }}</div>
                    </div>
                </div>
            </div>
            
            <div class="divider"></div>
            
            <div class="next-steps-box">
                <p><strong>🔄 What happens next?</strong> Our team is reviewing your inquiry and will respond directly to your email address. Please keep an eye on your inbox (and spam folder, just in case!).</p>
            </div>
            
            <div class="contact-info-box">
                <p><strong>📞 Need urgent assistance?</strong> If your inquiry is time-sensitive, feel free to contact us directly through our other channels listed on our website.</p>
            </div>
        </div>
        
        <div class="footer">
            <div class="footer-content">
                <div class="appreciation-notice">
                    We value your interest in our services and look forward to assisting you
                </div>
                <p class="copyright">&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>