<?php
// lang/en/admin/reservation/edit.php

return [
    // Page Title and Headers
    'title' => 'Update Reservation #:id',
    'error_occurred' => 'An error occurred:',

    // Customer Information
    'customer_type_label' => 'Customer Type',
    'registered_customer' => 'Registered Customer',
    'guest_customer' => 'Guest Customer',
    'select_customer_label' => 'Select Customer',
    'select_customer_option' => 'Select Customer...',

    // Guest Details
    'full_name_label' => 'Full Name *',
    'full_name_kana_label' => 'Full Name (Kana) *',
    'email_label' => 'Email *',
    'phone_number_label' => 'Phone Number *',

    // Reservation Details
    'menu_label' => 'Menu *',
    'select_menu_option' => 'Select Menu...',
    'yen' => 'yen',
    'minutes' => 'minutes',
    'reservation_datetime_label' => 'Reservation Date & Time *',
    'select_from_calendar_button' => 'Select from Calendar',
    'number_of_people_label' => 'Number of People *',
    'total_amount_label' => 'Total Amount *',
    'status_label' => 'Status',
    'notes_label' => 'Notes',

    // Status Options
    'status_pending' => 'Pending',
    'status_confirmed' => 'Confirmed',
    'status_completed' => 'Completed',
    'status_cancelled' => 'Cancelled',

    // Action Buttons
    'cancel_button' => 'Cancel',
    'check_availability_button' => 'Check Availability',
    'update_reservation_button' => 'Update Reservation',

    // Calendar Modal
    'modal_title' => 'Select the date you want to book',
    'select_date_label' => 'Select Date',
    'select_time_label' => 'Select Time',
    'previous_button' => 'Previous',
    'next_button' => 'Next',
    'date_fully_blocked_message' => 'This date is fully blocked and not available for reservations.',
    'modal_cancel_button' => 'Cancel',
    'modal_confirm_button' => 'Confirm',
    'months' => [
        'January', 'February', 'March', 'April', 'May', 'June',
        'July', 'August', 'September', 'October', 'November', 'December'
    ],
    'days_of_week' => [
        'sun' => 'Sun', 'mon' => 'Mon', 'tue' => 'Tue', 'wed' => 'Wed', 'thu' => 'Thu', 'fri' => 'Fri', 'sat' => 'Sat'
    ],
    
    // Calendar Legend
    'legend_title' => 'Legend:',
    'legend_fully_blocked' => 'Fully Blocked',
    'legend_blocked_period' => 'Blocked Period',
    'legend_has_reservation' => 'Has Reservation',
    'legend_mixed' => 'Mixed (Blocked + Reservation)',
    'legend_available' => 'Available',
    'legend_past_date' => 'Past Date',

    // Controller Messages
    'success_message' => 'Reservation updated successfully.',
    'reservation_not_found' => 'Reservation not found.',
    'update_failed_message' => 'Failed to update reservation: ',

    // JavaScript Messages
    'js_select_menu_datetime_first' => 'Please select menu and date/time first.',
    'js_timeslot_available' => 'Time slot is available for reservation.',
    'js_timeslot_unavailable' => 'Time slot is not available - conflicts with blocked periods or other reservations.',
    'js_availability_error' => 'An error occurred while checking availability.',
    'js_select_menu_first_alert' => 'Please select a menu first.',
    'js_select_date_time_alert' => 'Please select both date and time.',
];