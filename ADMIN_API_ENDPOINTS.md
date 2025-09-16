# Admin API Endpoints Documentation

## Base URL
All admin endpoints are prefixed with `/api/v1/admin/` and require:
- `auth:sanctum` middleware (user must be authenticated)
- `admin` middleware (user must have admin role)

## Authentication
Include the Bearer token in the Authorization header:
```
Authorization: Bearer {your-sanctum-token}
```

## Admin Questionnaire Management
**Base Path:** `/api/v1/admin/admin-questionnaires`

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/` | List all questionnaires |
| POST | `/` | Create new questionnaire |
| GET | `/{id}` | Show specific questionnaire |
| PUT | `/{id}` | Update questionnaire |
| DELETE | `/{id}` | Delete questionnaire |
| GET | `/reservation/{reservationId}` | Get questionnaire by reservation |
| POST | `/validate-answers` | Validate questionnaire answers |

## Contact Management
**Base Path:** `/api/v1/admin/contacts`

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/` | List all contacts |
| GET | `/{id}` | Show specific contact |
| PUT | `/{id}` | Update contact |
| DELETE | `/{id}` | Delete contact |
| POST | `/{id}/reply` | Reply to contact |
| DELETE | `/bulk-delete` | Bulk delete contacts |
| POST | `/mark-as-replied` | Mark multiple contacts as replied |

## Customer Management
**Base Path:** `/api/v1/admin/customers`

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/` | List all customers with search and filter support |
| GET | `/statistics` | Get customer statistics (first-time, repeat, dormant counts) |
| GET | `/search` | Search customers by name, email, or phone |
| GET | `/type-counts` | Get customer type counts |
| GET | `/first-time` | Get first-time customers |
| GET | `/repeat-customers` | Get repeat customers |
| GET | `/dormant-customers` | Get dormant customers |
| GET | `/{id}` | Show specific customer details |

### List Customers with Filtering
**GET** `/api/v1/admin/customers`

**Query Parameters:**
- `search` (optional): Search by name, email, or phone number
- `customer_type` (optional): Filter by type (`first_time`, `repeat`, `dormant`, `all`)
- `per_page` (optional): Number of items per page (default: 15, max: 100)
- `page` (optional): Current page number (default: 1)

**Response includes:**
- Paginated customer list
- Customer type counts
- Applied filters
- Pagination information

### Customer Statistics
**GET** `/api/v1/admin/customers/statistics`

Returns overview statistics matching the web interface:

**Response Example:**
```json
{
    "success": true,
    "message": "Customer statistics retrieved successfully",
    "data": {
        "statistics": {
            "first_time": 24,
            "repeat": 4,
            "dormant": 0
        },
        "total_customers": 28
    }
}
```

### Search Customers
**GET** `/api/v1/admin/customers/search`

**Query Parameters:**
- `search` (required): Search term for name, email, or phone
- `customer_type` (optional): Filter by customer type
- `per_page` (optional): Pagination limit

**Response includes:**
- Filtered customer results
- Search term used
- Results count

## Dashboard
**Base Path:** `/api/v1/admin`

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/dashboard` | Get admin dashboard data |

## Business Settings
**Base Path:** `/api/v1/admin/business-settings`

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/` | Get business settings |
| GET | `/business-hours` | Get business hours only |
| GET | `/top-image` | Get top image URL |
| PUT | `/business-hours` | Update business hours only |
| GET | `/{id}/edit` | Get settings for editing |
| PUT | `/update` | Update business settings |

## FAQ Management
**Base Path:** `/api/v1/admin/faqs`

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/` | List all FAQs |
| POST | `/` | Create new FAQ |
| GET | `/active` | Get active FAQs only |
| POST | `/reorder` | Reorder FAQs |
| GET | `/{id}` | Show specific FAQ |
| PUT | `/{id}` | Update FAQ |
| DELETE | `/{id}` | Delete FAQ |
| PATCH | `/{id}/toggle-status` | Toggle FAQ status |

## Payment Management
**Base Path:** `/api/v1/admin/payments`

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/` | List all payments |
| POST | `/` | Create new payment |
| GET | `/revenue/total` | Get total revenue |
| GET | `/statistics` | Get payment statistics |
| POST | `/bulk-update-status` | Bulk update payment status |
| GET | `/status/{status}` | Get payments by status |
| GET | `/user/{user_id}` | Get payments by user |
| GET | `/reservation/{reservation_id}` | Get payments by reservation |
| GET | `/{id}` | Show specific payment |
| PUT | `/{id}` | Update payment |
| DELETE | `/{id}` | Delete payment |
| POST | `/{id}/process` | Process payment |

## Blocked Period Management
**Base Path:** `/api/v1/admin/blocked-periods`

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/` | List all blocked periods |
| POST | `/` | Create new blocked period |
| POST | `/check-conflict` | Check for conflicts |
| GET | `/calendar` | Get calendar data |
| GET | `/calendar-with-conflicts` | Get calendar with conflict indicators |
| GET | `/available-slots` | Get available time slots |
| POST | `/batch-check-conflicts` | Batch check conflicts |
| POST | `/export` | Export blocked periods |
| DELETE | `/bulk-delete` | Bulk delete periods |
| GET | `/{id}` | Show specific blocked period |
| PUT | `/{id}` | Update blocked period |
| DELETE | `/{id}` | Delete blocked period |

## Existing Admin Endpoints (Already Available)

### User Management
**Base Path:** `/api/v1/admin/users`
- Full CRUD operations
- Search, filter by role
- Password management

### Menu Management  
**Base Path:** `/api/v1/admin/menus`
- Full CRUD operations
- Status management, bulk operations
- Availability and scheduling

### Tire Storage Management
**Base Path:** `/api/v1/admin/storages`
- Full CRUD operations
- Status management, bulk operations

### Reservation Management
**Base Path:** `/api/v1/admin/reservations`
- Full CRUD operations
- Status management (confirm, cancel, complete)
- Bulk status updates

### Announcement Management
**Base Path:** `/api/v1/admin/announcements`
- Full CRUD operations
- Status management, bulk operations

### Questionnaire Management (API)
**Base Path:** `/api/v1/admin/questionnaires`
- Full CRUD operations
- Answer submission and validation
- Completion statistics

## Request/Response Format

### Standard Response Format
```json
{
    "success": true|false,
    "message": "Success/Error message",
    "data": {...},
    "errors": {...} // Only on validation errors
}
```

### Pagination
Most list endpoints support pagination:
```json
{
    "data": [...],
    "links": {
        "first": "...",
        "last": "...",
        "prev": null,
        "next": "..."
    },
    "meta": {
        "current_page": 1,
        "from": 1,
        "last_page": 5,
        "per_page": 15,
        "to": 15,
        "total": 67
    }
}
```

## Error Handling

### Common HTTP Status Codes
- `200` - Success
- `201` - Created
- `400` - Bad Request
- `401` - Unauthorized
- `403` - Forbidden (not admin)
- `404` - Not Found
- `422` - Validation Error
- `500` - Server Error

### Validation Errors
```json
{
    "success": false,
    "message": "Validation failed",
    "errors": {
        "field_name": ["Error message 1", "Error message 2"]
    }
}
```

## Notes

1. **Route Ordering**: Specific routes (like `/search`, `/calendar`) are placed before parameterized routes (like `/{id}`) to prevent conflicts.

2. **Middleware**: All admin endpoints require both authentication and admin role verification.

3. **Localization**: The system supports multiple locales. Some endpoints may return localized messages.

4. **File Uploads**: Business settings endpoint supports file uploads for images (stored in S3).

5. **Bulk Operations**: Many endpoints support bulk operations for efficiency in admin interfaces.

6. **Filtering**: List endpoints often support filtering via query parameters.

7. **Real-time Features**: Calendar and conflict checking endpoints provide real-time availability information.
