<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmed - {{ config('app.name') }}</title>
    <style>
        body { font-family: sans-serif; line-height: 1.6; margin: 0; padding: 20px; background-color: #f4f7f6; }
        .email-wrapper { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .header { background: #4a90e2; color: white; padding: 40px 30px; text-align: center; }
        .header h1 { margin: 0; font-size: 28px; }
        .content { padding: 40px; color: #333; }
        .content h2 { color: #4a90e2; }
        .content p { margin-bottom: 20px; }
        .booking-summary { background: #f7fafc; border-radius: 8px; padding: 25px; margin: 30px 0; border-left: 4px solid #4a90e2; }
        .detail-row { display: flex; margin-bottom: 12px; }
        .detail-label { font-weight: 600; min-width: 150px; color: #555; }
        .detail-value { color: #333; }
        .footer { text-align: center; padding: 30px; font-size: 14px; color: #888; }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="header">
            <h1>Booking Confirmed!</h1>
        </div>
        
        <div class="content">
            <h2>Hello {{ $reservation->getFullName() }},</h2>
            
            <p>Thank you for booking with us! Your reservation has been successfully confirmed. We are looking forward to welcoming you.</p>
            
            <div class="booking-summary">
                <h3 style="margin-top: 0; margin-bottom: 20px; color: #333;">📋 Your Reservation Details</h3>
                
                <div class="detail-row">
                    <div class="detail-label">Reservation Number:</div>
                    <div class="detail-value"><strong>{{ $reservation->reservation_number }}</strong></div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">Service:</div>
                    <div class="detail-value">{{ $reservation->menu->name }}</div>
                </div>

                <div class="detail-row">
                    <div class="detail-label">Date:</div>
                    <div class="detail-value">{{ $reservation->reservation_datetime->format('F j, Y') }}</div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">Time:</div>
                    <div class="detail-value">{{ $reservation->reservation_datetime->format('g:i A') }} ({{ $reservation->menu->required_time }} minutes)</div>
                </div>

                <div class="detail-row">
                    <div class="detail-label">Name:</div>
                    <div class="detail-value">{{ $reservation->getFullName() }}</div>
                </div>

                <div class="detail-row">
                    <div class="detail-label">Email:</div>
                    <div class="detail-value">{{ $reservation->getEmail() }}</div>
                </div>

                @php
                    $phoneNumber = $reservation->getPhoneNumber();
                @endphp
                @if($phoneNumber && $phoneNumber !== 'N/A')
                <div class="detail-row">
                    <div class="detail-label">Phone:</div>
                    <div class="detail-value">{{ $phoneNumber }}</div>
                </div>
                @endif
            </div>

            <p><strong>Important Notes:</strong></p>
            <ul style="padding-left: 20px; margin-bottom: 20px;">
                <li>Please arrive 5 minutes before your scheduled appointment.</li>
                <li>If you need to reschedule, please contact us at least 24 hours in advance.</li>
            </ul>
            
            <p>If you have any questions, feel free to reply to this email or contact us at our support line.</p>
            
            <p>Best regards,<br>The {{ config('app.name') }} Team</p>
        </div>
        <p>
            <strong>🗓️ Add to Calendar:</strong> We've attached a calendar invite (.ics file) to this email for your convenience. Simply click on it to add this booking to your personal calendar.
        </p>

        <p><strong>Important Notes:</strong></p>

        
        <div class="footer">
            &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
        </div>
    </div>
</body>
</html>