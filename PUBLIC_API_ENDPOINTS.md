# Public API Endpoints Documentation

## Base URL
All public endpoints are prefixed with `/api/v1/public/` and do **not** require authentication.

## Localization
All endpoints support localization via the `X-Locale` header:
```
X-Locale: en    # English (default)
X-Locale: ja    # Japanese
```

## Public Business Settings
**Base Path:** `/api/v1/public/business-settings`

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/` | Get complete business settings |
| GET | `/business-hours` | Get business hours only |
| GET | `/company-info` | Get company information |
| GET | `/terms-and-policies` | Get terms and policies |

### Get Complete Business Settings
**GET** `/api/v1/public/business-settings`

Returns all business settings including company details, opening hours, terms, and policies.

**Response Example:**
```json
{
    "success": true,
    "message": "Business settings retrieved successfully",
    "data": {
        "id": 1,
        "shop_name": "Tire Pro Service",
        "site_name": "Tire Pro Service",
        "shop_description": "We are a professional tire service provider with over 10 years of experience.",
        "access_information": "Near Iruma Miyadera, about 5 minutes by car from Iruma IC.",
        "terms_of_use": "By using our services, you agree to comply with the applicable terms and conditions.",
        "privacy_policy": "We respect your privacy and are committed to protecting the personal information you provide to us.",
        "address": "2095-8 Miyadera, Iruma-shi, Saitama 358-0014, Japan",
        "phone_number": "04-2937-5296",
        "business_hours": {
            "monday": {"open": "09:00", "close": "18:00"},
            "tuesday": {"open": "09:00", "close": "18:00"},
            "wednesday": {"open": "09:00", "close": "18:00"},
            "thursday": {"open": "09:00", "close": "18:00"},
            "friday": {"open": "09:00", "close": "18:00"},
            "saturday": {"open": "09:00", "close": "17:00"},
            "sunday": {"closed": true}
        },
        "website_url": null,
        "top_image_path": null,
        "top_image_url": null,
        "meta": {
            "locale": "en",
            "fallback_used": false
        }
    }
}
```

### Get Business Hours Only
**GET** `/api/v1/public/business-settings/business-hours`

Returns only the opening hours information.

**Response Example:**
```json
{
    "success": true,
    "message": "Business hours retrieved successfully",
    "data": {
        "business_hours": {
            "monday": {"open": "09:00", "close": "18:00"},
            "tuesday": {"open": "09:00", "close": "18:00"},
            "wednesday": {"open": "09:00", "close": "18:00"},
            "thursday": {"open": "09:00", "close": "18:00"},
            "friday": {"open": "09:00", "close": "18:00"},
            "saturday": {"open": "09:00", "close": "17:00"},
            "sunday": {"closed": true}
        }
    }
}
```

### Get Company Information
**GET** `/api/v1/public/business-settings/company-info`

Returns company details like name, address, phone, and description.

**Response Example:**
```json
{
    "success": true,
    "message": "Company information retrieved successfully",
    "data": {
        "shop_name": "Tire Pro Service",
        "address": "2095-8 Miyadera, Iruma-shi, Saitama 358-0014, Japan",
        "phone_number": "04-2937-5296",
        "shop_description": "We are a professional tire service provider with over 10 years of experience.",
        "access_information": "Near Iruma Miyadera, about 5 minutes by car from Iruma IC.",
        "website_url": null,
        "meta": {
            "locale": "en",
            "fallback_used": false
        }
    }
}
```

### Get Terms and Policies
**GET** `/api/v1/public/business-settings/terms-and-policies`

Returns terms of service and privacy policy.

**Response Example:**
```json
{
    "success": true,
    "message": "Terms and policies retrieved successfully",
    "data": {
        "terms_of_use": "By using our services, you agree to comply with the applicable terms and conditions.",
        "privacy_policy": "We respect your privacy and are committed to protecting the personal information you provide to us.",
        "meta": {
            "locale": "en",
            "fallback_used": false
        }
    }
}
```

## Public Menu Access
**Base Path:** `/api/v1/public/menus`

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/` | List all active menus |
| GET | `/{id}` | Get specific menu details |

### List All Active Menus
**GET** `/api/v1/public/menus`

Returns all active menu items with pricing and descriptions.

### Get Specific Menu
**GET** `/api/v1/public/menus/{id}`

Returns detailed information about a specific menu item.

## Contact and Inquiry
**Base Path:** `/api/v1/public`

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/inquiry` | Submit inquiry form |

### Submit Inquiry
**POST** `/api/v1/public/inquiry`

Submit an inquiry or contact form.

**Request Body:**
```json
{
    "name": "Customer Name",
    "email": "customer@example.com",
    "phone_number": "123-456-7890",
    "subject": "Inquiry Subject",
    "message": "Inquiry message content"
}
```

## Error Handling

### Common HTTP Status Codes
- `200` - Success
- `404` - Not Found
- `422` - Validation Error
- `500` - Server Error

### Standard Response Format
```json
{
    "success": true|false,
    "message": "Success/Error message",
    "data": {...},
    "errors": {...} // Only on validation errors
}
```

## Usage Examples

### Get Business Settings with Japanese Locale
```bash
curl -X GET "http://your-domain.com/api/v1/public/business-settings" \
  -H "X-Locale: ja" \
  -H "Accept: application/json"
```

### Get Business Hours
```bash
curl -X GET "http://your-domain.com/api/v1/public/business-settings/business-hours" \
  -H "Accept: application/json"
```

### Get Company Information
```bash
curl -X GET "http://your-domain.com/api/v1/public/business-settings/company-info" \
  -H "Accept: application/json"
```

## Notes

1. **Localization**: All text content is automatically filtered based on the `X-Locale` header. If no header is provided, English is used as the default.

2. **No Authentication**: These endpoints are publicly accessible and do not require any authentication tokens.

3. **Rate Limiting**: Standard rate limiting applies to prevent abuse.

4. **Caching**: Responses may be cached for performance. Business settings typically don't change frequently.

5. **CORS**: These endpoints support CORS for frontend applications.
