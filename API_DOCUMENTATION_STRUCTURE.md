# API Documentation Structure - Tire Storage Management System

This document provides a comprehensive explanation of the API controller organization and documentation structure for the Tire Storage Management System.

## Overview

The API is organized into three main categories with clear separation of concerns and proper authentication requirements:

1. **Public APIs** - Accessible without authentication
2. **Customer APIs** - Require customer authentication (`auth:sanctum`)
3. **Admin APIs** - Require admin authentication (`auth:sanctum` + `admin` middleware)

## Documentation Tag Structure

Following Laravel Scramble best practices, the API documentation uses hierarchical tags with main groups and subgroups for better organization.

### Main Documentation Groups

#### 1. Public
- **Description**: Endpoints accessible to all users without authentication
- **Controllers**: Located in `App\Http\Controllers\Api\`
- **Authentication**: None required

#### 2. Authentication  
- **Description**: User authentication and account management endpoints
- **Controllers**: Located in `App\Http\Controllers\Api\`
- **Authentication**: Mixed (login/register don't require auth, logout requires auth)

#### 3. Customer
- **Description**: Customer-specific functionality with role-based subgroups
- **Controllers**: Located in `App\Http\Controllers\Api\Customer\`
- **Authentication**: `auth:sanctum` middleware with customer role validation

#### 4. Admin
- **Description**: Administrative functionality with management subgroups
- **Controllers**: Located in `App\Http\Controllers\Api\Admin\`
- **Authentication**: `auth:sanctum` + `admin` middleware

## Detailed Controller Documentation

### Public Controllers (`App\Http\Controllers\Api\`)

#### MenuController.php
- **Tag**: `@tags Public`
- **Purpose**: Public menu information access
- **Key Endpoints**:
  - `GET /api/v1/public/menus` - List all active menus
  - `GET /api/v1/public/menus/{id}` - Get specific menu details
- **Features**: Locale-aware menu content, cursor pagination support

#### BusinessSettingController.php
- **Tag**: `@tags Public`
- **Purpose**: Public business information and settings
- **Key Endpoints**:
  - `GET /api/v1/public/business-settings` - Complete business information
  - `GET /api/v1/public/business-settings/business-hours` - Operating hours only
  - `GET /api/v1/public/business-settings/company-info` - Company details
  - `GET /api/v1/public/business-settings/terms-and-policies` - Legal information
- **Features**: Locale-filtered content, structured business data

#### ContactController.php
- **Tag**: `@tags Public`
- **Purpose**: Public contact and inquiry submission
- **Key Endpoints**:
  - `POST /api/v1/public/inquiry` - Submit public inquiry
  - `POST /api/v1/public/contact` - Submit contact form (legacy)
  - `GET /api/v1/public/user-data` - Get current user data for auto-fill
- **Features**: Auto-fill for authenticated users, event-driven notifications

### Authentication Controller (`App\Http\Controllers\Api\`)

#### AuthController.php
- **Tag**: `@tags Authentication`
- **Purpose**: User authentication and account management
- **Key Endpoints**:
  - `POST /api/v1/auth/login` - User login
  - `POST /api/v1/auth/register` - User registration
  - `POST /api/v1/auth/logout` - User logout (requires auth)
  - `POST /api/v1/auth/forgot-password` - Password reset request
  - `POST /api/v1/auth/reset-password` - Password reset completion
- **Features**: Sanctum token management, role-based authentication

### Customer Controllers (`App\Http\Controllers\Api\Customer\`)

#### BookingController.php
- **Tag**: `@tags Customer - Booking`
- **Purpose**: Customer reservation creation and booking process
- **Key Endpoints**:
  - `GET /api/v1/customer/booking/first-step/{menuId}` - Booking initialization
  - `GET /api/v1/customer/booking/calendar-data` - Calendar availability
  - `GET /api/v1/customer/booking/available-hours` - Time slot availability
  - `GET /api/v1/customer/booking/menu-details/{menuId}` - Menu information
  - `POST /api/v1/customer/booking/create-reservation` - Create reservation
- **Features**: Real-time availability checking, blocked period integration, calendar view

#### ProfileController.php
- **Tag**: `@tags Customer - Profile`
- **Purpose**: Customer profile and account management
- **Key Endpoints**:
  - `GET /api/v1/customer/profile` - Get profile information
  - `PATCH /api/v1/customer/profile` - Update profile
  - `PATCH /api/v1/customer/change-password` - Change password
  - `DELETE /api/v1/customer/account` - Delete account
- **Features**: Comprehensive profile management, secure password changes

#### CustomerController.php
- **Tag**: `@tags Customer - Dashboard`
- **Purpose**: Customer dashboard and summary information
- **Key Endpoints**:
  - `GET /api/v1/customer/dashboard` - Dashboard summary data
- **Features**: Aggregated statistics, recent activity summaries

#### ReservationController.php
- **Tag**: `@tags Customer - Reservation`
- **Purpose**: Customer reservation management and history
- **Key Endpoints**:
  - `GET /api/v1/customer/reservations` - List customer reservations
  - `GET /api/v1/customer/reservations/{id}` - Get specific reservation
  - `GET /api/v1/customer/reservations/status/{status}` - Filter by status
  - `GET /api/v1/customer/reservations/summary` - Reservation statistics
  - `GET /api/v1/customer/reservations/pending` - Pending reservations
  - `GET /api/v1/customer/reservations/completed` - Completed reservations
- **Features**: Status-based filtering, cursor pagination, detailed reservation data

#### TireStorageController.php
- **Tag**: `@tags Customer - TireStorage`
- **Purpose**: Customer tire storage management
- **Key Endpoints**:
  - `GET /api/v1/customer/tire-storage` - List tire storage entries
  - `POST /api/v1/customer/tire-storage` - Create storage entry
  - `GET /api/v1/customer/tire-storage/{id}` - Get specific entry
  - `PATCH /api/v1/customer/tire-storage/{id}` - Update entry
  - `POST /api/v1/customer/tire-storage/{id}/pickup` - Request pickup
  - `GET /api/v1/customer/tire-storage/summary` - Storage statistics
- **Features**: Complete tire storage lifecycle management, pickup scheduling

#### ContactController.php (Customer)
- **Tag**: `@tags Customer - Contact`
- **Purpose**: Customer inquiry and contact management
- **Key Endpoints**:
  - `POST /api/v1/customer/inquiry` - Submit authenticated inquiry
  - `GET /api/v1/customer/inquiry-history` - Get inquiry history
- **Features**: Auto-filled customer information, inquiry tracking

### Admin Controllers (`App\Http\Controllers\Api\Admin\`)

#### ReservationController.php
- **Tag**: `@tags Admin - Reservation Management`
- **Purpose**: Administrative reservation oversight and management
- **Key Endpoints**:
  - `GET /api/v1/admin/reservations/calendar` - Calendar view data
  - `GET /api/v1/admin/reservations/list` - List view with filtering
  - `GET /api/v1/admin/reservations/statistics` - Reservation statistics
  - `GET /api/v1/admin/reservations/availability-check` - Time availability
  - `PATCH /api/v1/admin/reservations/{id}/confirm` - Confirm reservation
  - `PATCH /api/v1/admin/reservations/{id}/cancel` - Cancel reservation
  - `PATCH /api/v1/admin/reservations/{id}/complete` - Complete reservation
  - `PATCH /api/v1/admin/reservations/bulk/status` - Bulk status update
- **Features**: Calendar/list views, comprehensive filtering, bulk operations, real-time statistics

#### MenuController.php (Admin)
- **Tag**: `@tags Admin - Menu Management`
- **Purpose**: Menu administration and configuration
- **Features**: Menu CRUD operations, status management, ordering, bulk operations

#### CustomerController.php (Admin)
- **Tag**: `@tags Admin - Customer Management`
- **Purpose**: Customer administration and analytics
- **Features**: Customer overview, statistics, search, type-based filtering

#### TireStorageController.php (Admin)
- **Tag**: `@tags Admin - TireStorage Management`
- **Purpose**: Administrative tire storage oversight
- **Features**: Storage management, bulk operations, status tracking

#### ContactController.php (Admin)
- **Tag**: `@tags Admin - Contact Management`
- **Purpose**: Contact and inquiry administration
- **Features**: Inquiry management, response tracking, bulk operations

#### BusinessSettingController.php (Admin)
- **Tag**: `@tags Admin - Business Settings`
- **Purpose**: Business configuration management
- **Features**: Settings administration, business hours, company information

#### BlockedPeriodController.php
- **Tag**: `@tags Admin - System Management`
- **Purpose**: Blocked period management for scheduling
- **Features**: Period blocking, conflict detection, calendar integration

#### AnnouncementController.php
- **Tag**: `@tags Admin - Content Management`
- **Purpose**: Announcement management
- **Features**: Announcement CRUD, status management, bulk operations

#### DashboardController.php
- **Tag**: `@tags Admin - Dashboard`
- **Purpose**: Administrative dashboard and analytics
- **Features**: System overview, key metrics, administrative summaries

#### ProfileController.php (Admin)
- **Tag**: `@tags Admin - Profile`
- **Purpose**: Admin profile management
- **Features**: Admin account management, profile updates

#### QuestionnaireController.php
- **Tag**: `@tags Admin - Content Management`
- **Purpose**: Questionnaire and survey management
- **Features**: Survey administration, response tracking, analytics

#### FaqController.php
- **Tag**: `@tags Admin - Content Management`
- **Purpose**: FAQ management and organization
- **Features**: FAQ CRUD, ordering, status management

## API Design Principles

### 1. **Consistent Response Structure**
All endpoints use the `ApiResponseTrait` for standardized responses:
```json
{
  "status": "success|error",
  "message": "Descriptive message",
  "data": {},
  "errors": []
}
```

### 2. **Authentication & Authorization**
- **Public**: No authentication required
- **Customer**: `auth:sanctum` middleware with customer role validation
- **Admin**: `auth:sanctum` + `admin` middleware

### 3. **Pagination Support**
- Cursor-based pagination for performance
- Configurable page sizes (max 100 items)
- Optional pagination toggle

### 4. **Error Handling**
- Structured error responses with field-specific details
- HTTP status code compliance
- Validation error mapping

### 5. **Filtering & Search**
- Advanced filtering capabilities
- Search across multiple fields
- Status-based filtering
- Date range filtering

### 6. **Internationalization**
- Locale-aware content delivery
- Translation support for multi-language data
- Automatic locale detection

## API Documentation Access

The API documentation is generated using Laravel Scramble and can be accessed at:
- **Local Development**: `http://localhost:8000/docs/api`
- **Production**: `https://yourdomain.com/docs/api`

### Documentation Features
- Interactive API testing with "Try It" functionality
- Comprehensive endpoint documentation with examples
- Request/response schema definitions
- Authentication requirements clearly marked
- Grouped endpoints for easy navigation

## Security Considerations

### 1. **Authentication**
- Sanctum token-based authentication
- Role-based access control
- Token revocation on logout/account deletion

### 2. **Input Validation**
- Comprehensive request validation
- CSRF protection for web routes
- Rate limiting on authentication endpoints

### 3. **Data Protection**
- User data isolation (customers can only access their own data)
- Admin privilege validation
- Secure password handling

### 4. **API Rate Limiting**
- Configurable rate limits per endpoint group
- Different limits for authenticated vs. public endpoints
- Protection against abuse

This documentation structure ensures clear separation of concerns, proper authentication flows, and comprehensive functionality for both customer and administrative use cases in the tire storage management system.
