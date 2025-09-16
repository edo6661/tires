# Admin Controllers API Documentation

This document provides detailed explanations for all methods and endpoints in the Admin API controllers for the tire storage management system.

## Table of Contents

1. [BlockedPeriodController](#blockedperiodcontroller)
2. [ContactController](#contactcontroller)
3. [AnnouncementController](#announcementcontroller)
4. [BusinessSettingController](#businesssettingcontroller)
5. [QuestionnaireController](#questionnairecontroller)
6. [UserController](#usercontroller)
7. [TireStorageController](#tirestoragecontroller)
8. [ReservationController](#reservationcontroller)
9. [ProfileController](#profilecontroller)
10. [PaymentController](#paymentcontroller)
11. [MenuController](#menucontroller)
12. [FaqController](#faqcontroller)
13. [DashboardController](#dashboardcontroller)
14. [CustomerController](#customercontroller)

---

## BlockedPeriodController

**Tag**: `Admin - Blocked Period Management`  
**Purpose**: Manages time periods when reservations are blocked for specific menus or all menus

### Methods & Endpoints

#### `getStatistics()` - GET `/admin/blocked-periods/statistics`
**Purpose**: Get blocked period statistics overview  
**Description**: Returns counts for total, active, upcoming, expired periods with optional filtering  
**Parameters**: 
- `menu_id` (optional) - Filter by specific menu
- `start_date` (optional) - Filter by start date
- `end_date` (optional) - Filter by end date
- `status` (optional) - Filter by status (active/upcoming/expired)
- `all_menus` (optional) - Filter for all-menu blocks
- `search` (optional) - Search term

#### `search()` - GET `/admin/blocked-periods/search`
**Purpose**: Search blocked periods with enhanced filtering  
**Description**: Advanced search with multiple filter options and pagination  
**Parameters**:
- `search` (optional) - Search term for reason/description
- `menu_id` (optional) - Filter by menu ID
- `status` (optional) - Filter by status
- `start_date/end_date` (optional) - Date range filter
- `all_menus` (optional) - Filter for all-menu blocks
- `per_page` (optional) - Pagination limit (1-100)

#### `index()` - GET `/admin/blocked-periods`
**Purpose**: Display listing of blocked periods  
**Description**: Paginated list with filtering support  

#### `store()` - POST `/admin/blocked-periods`
**Purpose**: Store newly created blocked period  
**Description**: Creates new blocked period with conflict checking  

#### `show()` - GET `/admin/blocked-periods/{id}`
**Purpose**: Display specified blocked period  
**Description**: Gets detailed information for single blocked period  

#### `update()` - PUT `/admin/blocked-periods/{id}`
**Purpose**: Update specified blocked period  
**Description**: Updates blocked period with conflict validation  

#### `destroy()` - DELETE `/admin/blocked-periods/{id}`
**Purpose**: Remove specified blocked period  
**Description**: Deletes blocked period and frees up the time slots  

#### `checkConflict()` - POST `/admin/blocked-periods/check-conflict`
**Purpose**: Check for schedule conflicts  
**Description**: Validates if new blocked period conflicts with existing ones  
**Parameters**:
- `menu_id` (optional) - Menu to check against
- `start_datetime` (required) - Start time
- `end_datetime` (required) - End time
- `all_menus` (optional) - Whether blocking all menus
- `exclude_id` (optional) - ID to exclude from conflict check

#### `calendar()` - GET `/admin/blocked-periods/calendar`
**Purpose**: Get calendar data for visualization  
**Description**: Returns blocked periods formatted for calendar display  
**Parameters**:
- `start` (optional) - Calendar start date
- `end` (optional) - Calendar end date

#### `calendarWithConflicts()` - GET `/admin/blocked-periods/calendar-conflicts`
**Purpose**: Get calendar with conflict indicators  
**Description**: Enhanced calendar view showing potential conflicts  
**Parameters**: Same as calendar() plus menu filtering options

#### `getAvailableSlots()` - GET `/admin/blocked-periods/available-slots`
**Purpose**: Get available time slots for specific date  
**Description**: Returns hourly availability for a specific date and menu  
**Parameters**:
- `date` (required) - Date to check
- `menu_id` (optional) - Menu to check against
- `all_menus` (optional) - Check for all menus

#### `batchCheckConflicts()` - POST `/admin/blocked-periods/batch-check-conflicts`
**Purpose**: Batch check conflicts for multiple date ranges  
**Description**: Efficiently check multiple periods for conflicts  

#### `export()` - GET `/admin/blocked-periods/export`
**Purpose**: Export blocked periods  
**Description**: Export filtered blocked periods to various formats  

#### `bulkDelete()` - DELETE `/admin/blocked-periods/bulk-delete`
**Purpose**: Bulk delete blocked periods  
**Description**: Delete multiple blocked periods at once  

---

## ContactController

**Tag**: `Admin - Contact Management`  
**Purpose**: Manages customer contact messages and inquiries

### Methods & Endpoints

#### `getStatistics()` - GET `/admin/contacts/statistics`
**Purpose**: Get contact statistics overview  
**Description**: Returns counts for total, pending, replied contacts and today's count  

#### `search()` - GET `/admin/contacts/search`
**Purpose**: Search contacts with enhanced filtering  
**Description**: Advanced search through contact messages  
**Parameters**:
- `search` (optional) - Search in name, email, message
- `status` (optional) - Filter by status (pending/replied)
- `start_date/end_date` (optional) - Date range filter
- `per_page` (optional) - Pagination limit

#### `index()` - GET `/admin/contacts`
**Purpose**: Display listing of contacts  
**Description**: Paginated list of all contact messages  

#### `show()` - GET `/admin/contacts/{id}`
**Purpose**: Display specified contact  
**Description**: Get detailed view of single contact message  

#### `update()` - PUT `/admin/contacts/{id}`
**Purpose**: Update specified contact  
**Description**: Update contact status or add admin notes  
**Parameters**:
- `status` (optional) - Update status
- `admin_reply` (optional) - Admin response

#### `destroy()` - DELETE `/admin/contacts/{id}`
**Purpose**: Remove specified contact  
**Description**: Delete contact message permanently  

#### `reply()` - POST `/admin/contacts/{id}/reply`
**Purpose**: Reply to a contact  
**Description**: Send admin reply to customer inquiry  
**Parameters**:
- `admin_reply` (required) - Reply message content

#### `bulkDelete()` - DELETE `/admin/contacts/bulk-delete`
**Purpose**: Bulk delete contacts  
**Description**: Delete multiple contact messages at once  

#### `markAsReplied()` - POST `/admin/contacts/mark-as-replied`
**Purpose**: Mark multiple contacts as replied  
**Description**: Bulk reply to multiple contacts with same message  

---

## AnnouncementController

**Tag**: `Admin - Announcement Management`  
**Purpose**: Manages system announcements and notifications

### Methods & Endpoints

#### `index()` - GET `/admin/announcements`
**Purpose**: List all announcements with filtering and search  
**Description**: Paginated or non-paginated list with advanced filtering  
**Parameters**:
- `per_page` (optional) - Pagination limit
- `paginate` (optional) - Enable/disable pagination
- `cursor` (optional) - Cursor for pagination
- `status` (optional) - Filter by active/inactive
- `published_at` (optional) - Sort by publication date
- `search` (optional) - Search in title/content

#### `store()` - POST `/admin/announcements`
**Purpose**: Create new announcement  
**Description**: Creates announcement with multilingual support  

#### `show()` - GET `/admin/announcements/{id}`
**Purpose**: Get announcement detail  
**Description**: Retrieve specific announcement with translations  

#### `update()` - PUT `/admin/announcements/{id}`
**Purpose**: Update announcement  
**Description**: Update announcement content and translations  

#### `destroy()` - DELETE `/admin/announcements/{id}`
**Purpose**: Delete announcement  
**Description**: Permanently remove announcement  

#### `statistics()` - GET `/admin/announcements/statistics`
**Purpose**: Get announcement statistics  
**Description**: Returns counts and metrics for announcements  

---

## BusinessSettingController

**Tag**: `Admin - Business Setting Management`  
**Purpose**: Manages business configuration and settings

### Methods & Endpoints

#### `index()` - GET `/admin/business-settings`
**Purpose**: Display business settings  
**Description**: Get current business configuration  

#### `edit()` - GET `/admin/business-settings/{id}/edit`
**Purpose**: Get business settings for editing  
**Description**: Retrieve settings in editable format  

#### `update()` - PUT `/admin/business-settings`
**Purpose**: Update business settings  
**Description**: Update business configuration including file uploads  
**Features**:
- Handles top image upload to S3
- Processes business hours configuration
- Updates company information

#### `getBusinessHours()` - GET `/admin/business-settings/business-hours`
**Purpose**: Get business hours  
**Description**: Retrieve current business operating hours  

#### `updateBusinessHours()` - PUT `/admin/business-settings/business-hours`
**Purpose**: Update business hours only  
**Description**: Update just the business hours configuration  

#### `getTopImage()` - GET `/admin/business-settings/top-image`
**Purpose**: Get top image URL  
**Description**: Retrieve current top banner image URL and path  

---

## QuestionnaireController

**Tag**: `Admin - Questionnaire Management`  
**Purpose**: Manages customer feedback questionnaires

### Methods & Endpoints

#### `index()` - GET `/admin/questionnaires`
**Purpose**: Get all questionnaires with cursor pagination  
**Description**: List questionnaires with optional pagination  

#### `store()` - POST `/admin/questionnaires`
**Purpose**: Store newly created questionnaire  
**Description**: Create questionnaire with questions and answers  

#### `show()` - GET `/admin/questionnaires/{id}`
**Purpose**: Display specified questionnaire  
**Description**: Get detailed questionnaire with all Q&A pairs  

#### `update()` - PUT `/admin/questionnaires/{id}`
**Purpose**: Update specified questionnaire  
**Description**: Update questionnaire content and structure  

#### `destroy()` - DELETE `/admin/questionnaires/{id}`
**Purpose**: Remove specified questionnaire  
**Description**: Delete questionnaire permanently  

#### `getByReservation()` - GET `/admin/questionnaires/reservation/{reservationId}`
**Purpose**: Get questionnaire by reservation ID  
**Description**: Retrieve questionnaire associated with specific reservation  

#### `validateAnswers()` - POST `/admin/questionnaires/validate-answers`
**Purpose**: Validate questionnaire answers  
**Description**: Check if provided answers meet validation criteria  

#### `submitAnswers()` - POST `/admin/questionnaires/submit-answers`
**Purpose**: Submit questionnaire answers  
**Description**: Save customer responses to questionnaire  

#### `getCompletionStats()` - GET `/admin/questionnaires/completion-stats`
**Purpose**: Get questionnaire completion statistics  
**Description**: Analytics on questionnaire response rates  

#### `search()` - GET `/admin/questionnaires/search`
**Purpose**: Search questionnaires  
**Description**: Search through questionnaire content  

#### `byCompletionStatus()` - GET `/admin/questionnaires/status/{status}`
**Purpose**: Get questionnaires by completion status  
**Description**: Filter questionnaires by completion status (completed/incomplete/partial)  

---

## UserController

**Tag**: `Admin - User Management`  
**Purpose**: Manages user accounts and authentication

### Methods & Endpoints

#### `index()` - GET `/admin/users`
**Purpose**: Get all users with cursor pagination  
**Description**: List all system users with pagination support  

#### `store()` - POST `/admin/users`
**Purpose**: Store newly created user  
**Description**: Create new user account with validation  

#### `show()` - GET `/admin/users/{id}`
**Purpose**: Display specified user  
**Description**: Get detailed user information  

#### `update()` - PUT `/admin/users/{id}`
**Purpose**: Update specified user  
**Description**: Update user profile and account details  

#### `destroy()` - DELETE `/admin/users/{id}`
**Purpose**: Remove specified user  
**Description**: Delete user account permanently  

#### `search()` - GET `/admin/users/search`
**Purpose**: Search users  
**Description**: Search through user profiles by name, email, phone  
**Parameters**:
- `q` (required) - Search query
- `per_page` (optional) - Results per page
- `cursor` (optional) - Pagination cursor

#### `byRole()` - GET `/admin/users/role/{role}`
**Purpose**: Get users by role  
**Description**: Filter users by their assigned role  

#### `customers()` - GET `/admin/users/customers`
**Purpose**: Get customers only  
**Description**: Retrieve only customer accounts  

#### `admins()` - GET `/admin/users/admins`
**Purpose**: Get admins only  
**Description**: Retrieve only admin accounts  

#### `resetPassword()` - POST `/admin/users/{id}/reset-password`
**Purpose**: Reset user password  
**Description**: Admin-initiated password reset for user  

#### `changePassword()` - POST `/admin/users/{id}/change-password`
**Purpose**: Change user password  
**Description**: Change password with current password verification  

---

## TireStorageController

**Tag**: `Admin - Tire Storage Management`  
**Purpose**: Manages tire storage services and records

### Methods & Endpoints

#### `index()` - GET `/admin/tire-storages`
**Purpose**: List tire storages with filter & pagination  
**Description**: Display all tire storage records with search and filter options  

#### `store()` - POST `/admin/tire-storages`
**Purpose**: Create tire storage record  
**Description**: Register new tire storage service  

#### `show()` - GET `/admin/tire-storages/{id}`
**Purpose**: Get tire storage details  
**Description**: Retrieve detailed information for specific storage record  

#### `update()` - PUT `/admin/tire-storages/{id}`
**Purpose**: Update tire storage record  
**Description**: Modify existing tire storage information  

#### `destroy()` - DELETE `/admin/tire-storages/{id}`
**Purpose**: Delete tire storage record  
**Description**: Remove tire storage record permanently  

#### `end()` - POST `/admin/tire-storages/{id}/end`
**Purpose**: End tire storage service  
**Description**: Mark tire storage service as completed/ended  

#### `bulkDelete()` - DELETE `/admin/tire-storages/bulk-delete`
**Purpose**: Bulk delete tire storage records  
**Description**: Delete multiple storage records at once  

#### `bulkEnd()` - POST `/admin/tire-storages/bulk-end`
**Purpose**: Bulk end tire storage services  
**Description**: End multiple storage services simultaneously  

---

## ReservationController

**Tag**: `Admin - Reservation Management`  
**Purpose**: Comprehensive reservation management for administrators

### Methods & Endpoints

#### `index()` - GET `/admin/reservations`
**Purpose**: Get all reservations with cursor pagination  
**Description**: List reservations with advanced pagination and filtering  

#### `store()` - POST `/admin/reservations`
**Purpose**: Store newly created reservation  
**Description**: Create reservation with validation and conflict checking  

#### `show()` - GET `/admin/reservations/{id}`
**Purpose**: Display specified reservation  
**Description**: Get detailed reservation information with customer data  

#### `update()` - PUT `/admin/reservations/{id}`
**Purpose**: Update specified reservation  
**Description**: Modify reservation details with validation  

#### `destroy()` - DELETE `/admin/reservations/{id}`
**Purpose**: Remove specified reservation  
**Description**: Cancel and delete reservation  

#### `checkAvailability()` - POST `/admin/reservations/check-availability`
**Purpose**: Check availability for specific datetime  
**Description**: Validate if time slot is available for booking  

#### `getAvailability()` - GET `/admin/reservations/availability`
**Purpose**: Get availability data for date range  
**Description**: Comprehensive availability check with blocked periods  

#### `getCalendarData()` - GET `/admin/reservations/calendar-data`
**Purpose**: Get calendar data for booking interface  
**Description**: Optimized calendar view for reservation management  

#### `getAvailableHours()` - GET `/admin/reservations/available-hours`
**Purpose**: Get available hours for specific date  
**Description**: Hourly availability for selected date and menu  

#### `getCalendarReservations()` - GET `/admin/reservations/calendar`
**Purpose**: Get reservations for calendar view  
**Description**: Calendar-formatted reservation data with filtering  
**Parameters**:
- `month` (optional) - Month in Y-m format
- `view` (optional) - View type (month/week/day)
- `menu_id` (optional) - Filter by menu
- `status` (optional) - Filter by status

#### `getListReservations()` - GET `/admin/reservations/list`
**Purpose**: Get reservations for list view with filtering  
**Description**: Paginated list with advanced search and filters  
**Parameters**:
- `search` (optional) - Search term
- `menu_id` (optional) - Filter by menu
- `status` (optional) - Filter by status
- `date_from/date_to` (optional) - Date range
- `sort_by/sort_order` (optional) - Sorting options

#### `getAvailabilityCheck()` - GET `/admin/reservations/availability-check`
**Purpose**: Comprehensive availability check for admin interface  
**Description**: Detailed availability analysis for specific date and menu  

#### `getReservationStatistics()` - GET `/admin/reservations/statistics`
**Purpose**: Get reservation statistics for dashboard  
**Description**: Analytics and metrics for reservation system  
**Parameters**:
- `period` (optional) - Time period (today/week/month/year)
- `date_from/date_to` (optional) - Custom date range

#### `confirm()` - POST `/admin/reservations/{id}/confirm`
**Purpose**: Confirm reservation  
**Description**: Change reservation status to confirmed  

#### `cancel()` - POST `/admin/reservations/{id}/cancel`
**Purpose**: Cancel reservation  
**Description**: Cancel reservation and free up time slot  

---

## ProfileController

**Tag**: `Admin - Profile Settings`  
**Purpose**: Manages current admin user profile

### Methods & Endpoints

#### `show()` - GET `/admin/profile`
**Purpose**: Get current admin profile  
**Description**: Retrieve authenticated admin's profile information  

#### `update()` - PUT `/admin/profile`
**Purpose**: Update current admin profile  
**Description**: Update admin profile with validation  
**Parameters**:
- `full_name` (required) - Full name
- `full_name_kana` (required) - Name in katakana
- `phone_number` (required) - Phone number
- `email` (required) - Email address
- `company_name` (optional) - Company name
- `department` (optional) - Department
- `company_address` (optional) - Company address
- `home_address` (optional) - Home address
- `date_of_birth` (optional) - Birth date
- `gender` (optional) - Gender

#### `updatePassword()` - PUT `/admin/profile/password`
**Purpose**: Update current admin password  
**Description**: Change password with current password verification  

#### `deleteAccount()` - DELETE `/admin/profile`
**Purpose**: Delete current admin account  
**Description**: Permanently delete admin account and revoke tokens  

---

## PaymentController

**Tag**: `Admin - Payment Settings`  
**Purpose**: Manages payment transactions and settings

### Methods & Endpoints

#### `index()` - GET `/admin/payments`
**Purpose**: Display listing of payments  
**Description**: Paginated list of all payment transactions  

#### `store()` - POST `/admin/payments`
**Purpose**: Store newly created payment  
**Description**: Create new payment record  

#### `show()` - GET `/admin/payments/{id}`
**Purpose**: Display specified payment  
**Description**: Get detailed payment information  

#### `update()` - PUT `/admin/payments/{id}`
**Purpose**: Update specified payment  
**Description**: Modify payment details and status  

#### `destroy()` - DELETE `/admin/payments/{id}`
**Purpose**: Remove specified payment  
**Description**: Delete payment record  

#### `getByStatus()` - GET `/admin/payments/status/{status}`
**Purpose**: Get payments by status  
**Description**: Filter payments by their processing status  

#### `getByUser()` - GET `/admin/payments/user/{userId}`
**Purpose**: Get payments by user  
**Description**: Retrieve all payments for specific user  

#### `getByReservation()` - GET `/admin/payments/reservation/{reservationId}`
**Purpose**: Get payments by reservation  
**Description**: Find payments associated with specific reservation  

#### `getTotalRevenue()` - GET `/admin/payments/revenue/total`
**Purpose**: Get total revenue  
**Description**: Calculate total revenue from all payments  

#### `processPayment()` - POST `/admin/payments/{id}/process`
**Purpose**: Process payment  
**Description**: Execute payment processing with transaction data  

#### `getStatistics()` - GET `/admin/payments/statistics`
**Purpose**: Get payment statistics  
**Description**: Analytics including success rates and revenue metrics  

#### `bulkUpdateStatus()` - PUT `/admin/payments/bulk-update-status`
**Purpose**: Bulk update payment status  
**Description**: Update status for multiple payments simultaneously  

---

## MenuController

**Tag**: `Admin - Menu Management`  
**Purpose**: Manages service menus and offerings

### Methods & Endpoints

#### `getStatistics()` - GET `/admin/menus/statistics`
**Purpose**: Get menu statistics overview  
**Description**: Returns counts for total, active, inactive menus and pricing metrics  

#### `search()` - GET `/admin/menus/search`
**Purpose**: Search menus with enhanced filtering  
**Description**: Advanced search with price range and status filters  
**Parameters**:
- `search` (optional) - Search in name/description
- `status` (optional) - Filter by active/inactive
- `min_price/max_price` (optional) - Price range filter

#### `index()` - GET `/admin/menus`
**Purpose**: List all menus with cursor pagination  
**Description**: Paginated or simple list with multilingual support  

#### `show()` - GET `/admin/menus/{id}`
**Purpose**: Display specified menu  
**Description**: Get detailed menu information with translations  

#### `store()` - POST `/admin/menus`
**Purpose**: Store newly created menu  
**Description**: Create menu with multilingual support  

#### `update()` - PUT `/admin/menus/{id}`
**Purpose**: Update specified menu  
**Description**: Update menu with translation support  

#### `destroy()` - DELETE `/admin/menus/{id}`
**Purpose**: Remove specified menu  
**Description**: Delete menu and associated data  

#### `toggleStatus()` - POST `/admin/menus/{id}/toggle-status`
**Purpose**: Toggle menu active/inactive status  
**Description**: Switch menu availability status  

#### `reorder()` - POST `/admin/menus/reorder`
**Purpose**: Reorder menus  
**Description**: Update display order for multiple menus  

#### `getMenuDetails()` - GET `/admin/menus/{id}/details`
**Purpose**: Get menu details for booking  
**Description**: Retrieve menu information for reservation system  

#### `calculateEndTime()` - POST `/admin/menus/calculate-end-time`
**Purpose**: Calculate service end time  
**Description**: Calculate when service will end based on menu duration  

#### `getAvailableSlots()` - GET `/admin/menus/available-slots`
**Purpose**: Get available time slots for menu  
**Description**: Find available booking slots for specific menu and date  

#### `bulkDelete()` - DELETE `/admin/menus/bulk-delete`
**Purpose**: Bulk delete menus  
**Description**: Delete multiple menus simultaneously  

#### `bulkUpdateStatus()` - PUT `/admin/menus/bulk-update-status`
**Purpose**: Bulk update menu status  
**Description**: Update active/inactive status for multiple menus  

---

## FaqController

**Tag**: `Admin - Faq Management`  
**Purpose**: Manages frequently asked questions

### Methods & Endpoints

#### `index()` - GET `/admin/faqs`
**Purpose**: Display listing of FAQs  
**Description**: Paginated list of all FAQ entries  

#### `store()` - POST `/admin/faqs`
**Purpose**: Store newly created FAQ  
**Description**: Create new FAQ entry with validation  

#### `show()` - GET `/admin/faqs/{id}`
**Purpose**: Display specified FAQ  
**Description**: Get detailed FAQ information  

#### `update()` - PUT `/admin/faqs/{id}`
**Purpose**: Update specified FAQ  
**Description**: Modify FAQ content and settings  

#### `destroy()` - DELETE `/admin/faqs/{id}`
**Purpose**: Remove specified FAQ  
**Description**: Delete FAQ entry permanently  

#### `toggleStatus()` - POST `/admin/faqs/{id}/toggle-status`
**Purpose**: Toggle FAQ status  
**Description**: Switch FAQ active/inactive status  

#### `reorder()` - POST `/admin/faqs/reorder`
**Purpose**: Reorder FAQs  
**Description**: Update display order for FAQ entries  

#### `getActiveFaqs()` - GET `/admin/faqs/active`
**Purpose**: Get active FAQs only  
**Description**: Retrieve only published/active FAQ entries  

---

## DashboardController

**Tag**: `Admin - Dashboard`  
**Purpose**: Provides dashboard data and overview metrics

### Methods & Endpoints

#### `index()` - GET `/admin/dashboard`
**Purpose**: Get admin dashboard data with locale-filtered translations  
**Description**: Comprehensive dashboard with announcements, reservations, contacts, and statistics  
**Returns**:
- Recent announcements (top 5)
- Today's reservations
- Pending contacts (top 5) with count
- Monthly reservations
- Customer statistics
- Last login information

#### `getStatistics()` - GET `/admin/dashboard/statistics`
**Purpose**: Get dashboard statistics only  
**Description**: Dashboard metrics without detailed data for quick overview  
**Returns**:
- New customers this month
- Online reservations this month
- Total customers and limit tracking
- Pending contacts count
- Today's reservations count

---

## CustomerController

**Tag**: `Admin - Customer Management`  
**Purpose**: Manages customer accounts and analytics

### Methods & Endpoints

#### `index()` - GET `/admin/customers`
**Purpose**: Display listing of customers with search and filter support  
**Description**: Paginated customer list with advanced filtering  
**Parameters**:
- `search` (optional) - Search by name, email, or phone
- `customer_type` (optional) - Filter by customer type (first_time/repeat/dormant/all)
- `per_page` (optional) - Pagination limit (1-100)
- `page` (optional) - Current page

#### `show()` - GET `/admin/customers/{id}`
**Purpose**: Display specified customer  
**Description**: Get detailed customer profile with reservation history  

#### `getFirstTimeCustomers()` - GET `/admin/customers/first-time`
**Purpose**: Get first time customers  
**Description**: Retrieve customers who have made only one reservation  

#### `getRepeatCustomers()` - GET `/admin/customers/repeat`
**Purpose**: Get repeat customers  
**Description**: Retrieve customers with multiple reservations  

#### `getDormantCustomers()` - GET `/admin/customers/dormant`
**Purpose**: Get dormant customers  
**Description**: Retrieve customers who haven't made recent reservations  

#### `search()` - GET `/admin/customers/search`
**Purpose**: Search customers by name, email, or phone number  
**Description**: Advanced customer search with type filtering  

#### `getStatistics()` - GET `/admin/customers/statistics`
**Purpose**: Get customer statistics overview  
**Description**: Analytics matching the web interface customer metrics  

#### `getCustomerTypeCounts()` - GET `/admin/customers/type-counts`
**Purpose**: Get customer type counts  
**Description**: Breakdown of customers by their activity level  

---

## Common Features Across Controllers

### Authentication & Authorization
- All endpoints require admin authentication via Sanctum
- Rate limiting applied to prevent abuse
- Role-based access control ensures admin-only access

### Response Format
- Consistent JSON response structure using `ApiResponseTrait`
- Standardized error handling with detailed error information
- Pagination support with cursor or offset pagination
- Multilingual support where applicable

### Validation & Error Handling
- Comprehensive input validation using Form Requests
- Detailed validation error responses
- Exception handling with appropriate HTTP status codes
- Database transaction support for data integrity

### Search & Filtering
- Advanced search capabilities across relevant fields
- Multiple filter options for refined results
- Sorting and ordering support
- Export functionality for data analysis

### Bulk Operations
- Bulk delete operations for efficiency
- Bulk status updates
- Batch processing for multiple records
- Transaction safety for bulk operations

This documentation provides a comprehensive overview of all admin controller methods, their purposes, parameters, and usage patterns. Each endpoint is designed to support the admin interface with robust functionality for managing the tire storage reservation system.