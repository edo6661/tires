# Reservation API Endpoint Documentation

## Create New Reservation

**Endpoint:** `POST /api/v1/admin/reservations`

**Authentication:** Required (`auth:sanctum` + `admin` middleware)

### Request Body

The API now supports two types of reservations:

#### 1. Existing Customer Reservation (with user_id)
```json
{
  "user_id": 123,
  "menu_id": 1,
  "reservation_datetime": "2024-12-25 14:00:00",
  "number_of_people": 2,
  "amount": 5000,
  "notes": "Special request for window seat"
}
```

#### 2. Guest Customer Reservation (without user_id)
```json
{
  "menu_id": 1,
  "reservation_datetime": "2024-12-25 14:00:00",
  "number_of_people": 2,
  "amount": 5000,
  "full_name": "John Doe",
  "full_name_kana": "ジョン・ドウ",
  "email": "john.doe@example.com",
  "phone_number": "090-1234-5678",
  "notes": "First time customer"
}
```

#### 3. Explicit Customer Type (Optional)
You can also explicitly specify the customer type:
```json
{
  "customer_type": "guest",
  "menu_id": 1,
  "reservation_datetime": "2024-12-25 14:00:00",
  "number_of_people": 2,
  "amount": 5000,
  "full_name": "John Doe",
  "full_name_kana": "ジョン・ドウ",
  "email": "john.doe@example.com",
  "phone_number": "090-1234-5678"
}
```

### Request Fields

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `user_id` | integer | conditional | Required for existing customers. Must exist in users table |
| `menu_id` | integer | required | Must exist in menus table |
| `reservation_datetime` | datetime | required | Must be in future for new reservations |
| `number_of_people` | integer | required | Minimum 1 |
| `amount` | numeric | required | Minimum 0 |
| `status` | string | optional | One of: pending, confirmed, completed, cancelled. Defaults to 'pending' |
| `notes` | string | optional | Additional notes |
| `customer_type` | string | auto-detect | One of: existing, guest. Auto-detected if not provided |
| `full_name` | string | conditional | Required for guest customers |
| `full_name_kana` | string | conditional | Required for guest customers |
| `email` | email | conditional | Required for guest customers |
| `phone_number` | string | conditional | Required for guest customers |

### Validation Rules

1. **Customer Type Detection:**
   - If `user_id` is provided → `customer_type` = "existing"
   - If `user_id` is null/missing → `customer_type` = "guest"

2. **Guest Customer Requirements:**
   - When `customer_type` is "guest" OR `user_id` is null, the following fields are required:
     - `full_name`
     - `full_name_kana`
     - `email`
     - `phone_number`

3. **Availability Check:**
   - The system automatically checks if the requested datetime is available
   - Returns validation error if the slot is already booked

### Success Response

```json
{
  "status": "success",
  "message": "Reservation created successfully",
  "data": {
    "id": 123,
    "reservation_number": "RES-2024-001",
    "reservation_datetime": "2024-12-25T14:00:00.000000Z",
    "number_of_people": 2,
    "amount": {
      "raw": 5000,
      "formatted": "5,000.00"
    },
    "status": {
      "value": "pending",
      "label": "Pending"
    },
    "notes": "Special request for window seat",
    "customer_info": {
      "full_name": "John Doe",
      "full_name_kana": "ジョン・ドウ",
      "email": "john.doe@example.com",
      "phone_number": "090-1234-5678",
      "is_guest": true
    },
    "created_at": "2024-12-20T10:30:00.000000Z",
    "updated_at": "2024-12-20T10:30:00.000000Z"
  }
}
```

### Error Responses

#### Validation Error
```json
{
  "status": "error",
  "message": "Validation failed",
  "errors": {
    "menu_id": ["The selected menu id is invalid."],
    "full_name": ["The full name field is required."]
  }
}
```

#### Availability Error
```json
{
  "status": "error",
  "message": "Validation failed",
  "errors": {
    "reservation_datetime": ["The selected time slot is not available."]
  }
}
```

#### Server Error
```json
{
  "status": "error",
  "message": "Failed to create reservation: Database connection error"
}
```

### Changes Made to Fix the Issue

1. **Made `customer_type` Optional:** The field is now auto-detected based on the presence of `user_id`
2. **Improved Validation Logic:** Guest customer fields are required when `user_id` is null OR `customer_type` is explicitly set to "guest"
3. **Added Data Preparation:** The request automatically sets `customer_type` and default `status` if not provided
4. **Better Error Handling:** Uses ApiResponseTrait for consistent error responses
5. **Cleaned Request Data:** Removes `customer_type` from data sent to the service since it's not stored in the database

### Migration Notes

If you were sending requests with `customer_type` field before, they will continue to work. If you weren't including this field, the API will now automatically detect the appropriate type based on whether `user_id` is provided or not.