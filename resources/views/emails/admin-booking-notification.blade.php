<!-- File: resources/views/emails/admin-booking-notification.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Reservation - {{ config('app.name') }}</title>
    <style>
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            line-height: 1.6; 
            margin: 0; 
            padding: 20px; 
            background-color: #f8fafc; 
        }
        .email-wrapper { 
            max-width: 650px; 
            margin: 0 auto; 
            background: #ffffff; 
            border-radius: 12px; 
            overflow: hidden; 
            box-shadow: 0 8px 25px rgba(0,0,0,0.1); 
            border: 1px solid #e2e8f0;
        }
        .header { 
            background: linear-gradient(135deg, #e53e3e 0%, #fc8181 100%); 
            color: white; 
            padding: 30px; 
            text-align: center; 
        }
        .header h1 { 
            margin: 0; 
            font-size: 26px; 
            font-weight: 600;
        }
        .alert-badge {
            background: rgba(255,255,255,0.2);
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            margin-bottom: 10px;
            display: inline-block;
        }
        .content { 
            padding: 35px; 
            color: #2d3748; 
        }
        .content h2 { 
            color: #e53e3e; 
            margin-bottom: 20px;
            font-size: 20px;
        }
        .content p { 
            margin-bottom: 15px; 
            font-size: 16px;
        }
        .booking-summary { 
            background: #f7fafc; 
            border-radius: 10px; 
            padding: 25px; 
            margin: 25px 0; 
            border-left: 5px solid #e53e3e;
            border: 1px solid #e2e8f0;
        }
        .detail-row { 
            display: flex; 
            margin-bottom: 15px; 
            align-items: flex-start;
        }
        .detail-label { 
            font-weight: 600; 
            min-width: 160px; 
            color: #4a5568; 
            font-size: 14px;
        }
        .detail-value { 
            color: #2d3748; 
            font-size: 14px;
            flex: 1;
        }
        .status-badge {
            background: #fed7d7;
            color: #c53030;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        .action-section {
            background: #edf2f7;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border: 1px solid #e2e8f0;
        }
        .action-button {
            background: #e53e3e;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            display: inline-block;
            margin: 5px 10px 5px 0;
        }
        .footer { 
            text-align: center; 
            padding: 25px; 
            font-size: 13px; 
            color: #718096; 
            background: #f7fafc;
            border-top: 1px solid #e2e8f0;
        }
        .urgent-note {
            background: #fef5e7;
            border: 1px solid #f6e05e;
            border-radius: 6px;
            padding: 15px;
            margin: 20px 0;
        }
        .urgent-note h4 {
            color: #d69e2e;
            margin: 0 0 8px 0;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="header">
            <div class="alert-badge">🔔 ADMIN NOTIFICATION</div>
            <h1>New Reservation Received!</h1>
        </div>
        
        <div class="content">
            <h2>Hello {{ config('app.name') }} Admin,</h2>
            
            <p>A new reservation has just been created by a customer. Please review and process the following reservation:</p>
            
            
            <div class="booking-summary">
                <h3 style="margin-top: 0; margin-bottom: 20px; color: #2d3748;">📋 Reservation Details</h3>
                
                <div class="detail-row">
                    <div class="detail-label">Reservation Number:</div>
                    <div class="detail-value"><strong>{{ $reservation->reservation_number }}</strong></div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Service:</div>
                    <div class="detail-value"><strong>{{ $reservation->menu->name }}</strong></div>
                </div>

                <div class="detail-row">
                    <div class="detail-label">Date:</div>
                    <div class="detail-value">{{ $reservation->reservation_datetime->format('l, F j, Y') }}</div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">Time:</div>
                    <div class="detail-value">{{ $reservation->reservation_datetime->format('g:i A') }} ({{ $reservation->menu->required_time }} minutes)</div>
                </div>

                <div class="detail-row">
                    <div class="detail-label">Customer Name:</div>
                    <div class="detail-value"><strong>{{ $reservation->getFullName() }}</strong></div>
                </div>

                <div class="detail-row">
                    <div class="detail-label">Customer Email:</div>
                    <div class="detail-value">{{ $reservation->getEmail() }}</div>
                </div>

                @php
                    $phoneNumber = $reservation->getPhoneNumber();
                @endphp
                @if($phoneNumber && $phoneNumber !== 'N/A')
                <div class="detail-row">
                    <div class="detail-label">Phone Number:</div>
                    <div class="detail-value">{{ $phoneNumber }}</div>
                </div>
                @endif

                <div class="detail-row">
                    <div class="detail-label">Created At:</div>
                    <div class="detail-value">{{ $reservation->created_at->format('F j, Y g:i A') }}</div>
                </div>
            </div>

            <div class="urgent-note">
                <h4>⚠️ Action Required:</h4>
                <p style="margin: 0; font-size: 14px; color: #744210;">
                    This reservation is pending confirmation. Please login to the admin panel to approve or reject this reservation.
                </p>
            </div>

            <div class="action-section">
                <h4 style="margin-top: 0; color: #4a5568;">Admin Actions:</h4>
                <p style="margin-bottom: 15px; font-size: 14px;">Click one of the buttons below to manage this reservation:</p>
                
                <a href="{{ config('app.url') }}/admin/reservations/{{ $reservation->id }}" class="action-button">
                    📝 View Details
                </a>
                <a href="{{ config('app.url') }}/admin/reservations" class="action-button" style="background: #38a169;">
                    📋 All Reservations
                </a>
            </div>
            
            <p><strong>Important Notes:</strong></p>
            <ul style="padding-left: 20px; margin-bottom: 20px; font-size: 14px;">
                <li>Customer has received an automatic confirmation email</li>
                <li>Reservation will expire automatically if not confirmed within 24 hours</li>
                <li>Make sure to contact the customer if there are any changes</li>
            </ul>
            
            <p style="font-size: 14px;">This email was sent automatically by the system. Please do not reply to this email.</p>
            
        </div>
        
        <div class="footer">
            <p><strong>{{ config('app.name') }} - Admin Notification System</strong></p>
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
            <p style="margin-top: 10px; font-size: 12px;">
                Email sent to: {{ env('MAIL_FROM_ADDRESS') }} on {{ now()->format('F j, Y g:i A') }}
            </p>
        </div>
    </div>
</body>
</html>