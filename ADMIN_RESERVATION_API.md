# Admin Reservation Management API

This document provides comprehensive information about the admin reservation management API endpoints that support both Calendar View and List View interfaces.

## Authentication

All admin reservation endpoints require:
- `Authorization: Bearer {token}` header
- Admin role middleware validation

## API Endpoints

### 1. Calendar View - Get Calendar Reservations

**Endpoint:** `GET /api/v1/admin/reservations/calendar`

**Description:** Retrieve reservations formatted for calendar display with monthly navigation.

**Parameters:**
- `month` (optional): Month in Y-m format (e.g., "2025-09"). Default: current month
- `view` (optional): View type - "month", "week", "day". Default: "month"
- `menu_id` (optional): Filter by specific menu ID
- `status` (optional): Filter by status - "pending", "confirmed", "completed", "cancelled"

**Example Request:**
```bash
curl -X GET "http://localhost/tires/api/v1/admin/reservations/calendar?month=2025-09&view=month&menu_id=1" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json"
```

**Example Response:**
```json
{
  "status": "success",
  "message": "Calendar reservations retrieved successfully",
  "data": {
    "view": "month",
    "current_period": {
      "month": "September 2025",
      "start_date": "2025-09-01",
      "end_date": "2025-09-30"
    },
    "navigation": {
      "previous_month": "2025-08",
      "next_month": "2025-10",
      "current_month": "2025-09"
    },
    "calendar_data": [
      {
        "date": "2025-09-01",
        "day": 1,
        "is_current_month": true,
        "is_today": false,
        "day_name": "Monday",
        "reservations": [
          {
            "id": 1,
            "reservation_number": "RES-001",
            "customer_name": "Emanuel Yundt",
            "time": "18:00",
            "end_time": "18:50",
            "menu_name": "Tire Installation Service",
            "menu_color": "#10B981",
            "status": "pending",
            "people_count": 1,
            "amount": 5000
          }
        ],
        "total_reservations": 1
      }
    ],
    "statistics": {
      "total_reservations": 74,
      "pending": 45,
      "confirmed": 15,
      "completed": 10,
      "cancelled": 4
    }
  }
}
```

### 2. List View - Get List Reservations

**Endpoint:** `GET /api/v1/admin/reservations/list`

**Description:** Retrieve reservations in a filterable, paginated list format.

**Parameters:**
- `search` (optional): Search by customer name, phone, email, or reservation number
- `menu_id` (optional): Filter by menu ID
- `status` (optional): Filter by status
- `date_from` (optional): Start date filter (Y-m-d format)
- `date_to` (optional): End date filter (Y-m-d format)
- `per_page` (optional): Items per page (5-100). Default: 15
- `page` (optional): Page number. Default: 1
- `sort_by` (optional): Sort field - "reservation_datetime", "created_at", "customer_name", "status"
- `sort_order` (optional): Sort order - "asc", "desc". Default: "desc"

**Example Request:**
```bash
curl -X GET "http://localhost/tires/api/v1/admin/reservations/list?search=Amba&status=pending&per_page=15&page=1" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json"
```

**Example Response:**
```json
{
  "status": "success",
  "message": "List reservations retrieved successfully",
  "data": {
    "reservations": [
      {
        "id": 1,
        "reservation_number": "RES-001",
        "customer": {
          "name": "Amba Tukam-sama",
          "email": "amba@example.com",
          "phone": "081234567898",
          "type": "guest"
        },
        "date_time": {
          "date": "Sep 15, 2025",
          "time": "13:00",
          "datetime": "2025-09-15 13:00:00",
          "day_name": "Monday"
        },
        "menu": {
          "id": 1,
          "name": "Sushi Set",
          "required_time": 60,
          "color": "#3B82F6"
        },
        "people_count": 1,
        "amount": 7500,
        "status": "pending",
        "notes": "Special dietary requirements",
        "created_at": "2025-09-10 10:30:00",
        "updated_at": "2025-09-10 10:30:00"
      }
    ],
    "pagination": {
      "current_page": 1,
      "per_page": 15,
      "total": 74,
      "last_page": 5,
      "from": 1,
      "to": 15,
      "has_more_pages": true
    },
    "filters": {
      "current": {
        "search": "Amba",
        "menu_id": null,
        "status": "pending",
        "date_from": null,
        "date_to": null,
        "sort_by": "reservation_datetime",
        "sort_order": "desc"
      },
      "options": {
        "menus": [
          {
            "id": 1,
            "name": "Tire Installation Service"
          }
        ],
        "statuses": [
          {"value": "pending", "label": "Pending"},
          {"value": "confirmed", "label": "Confirmed"},
          {"value": "completed", "label": "Completed"},
          {"value": "cancelled", "label": "Cancelled"}
        ]
      }
    },
    "statistics": {
      "total_results": 74,
      "showing": 15,
      "from": 1,
      "to": 15
    }
  }
}
```

### 3. Reservation Statistics

**Endpoint:** `GET /api/v1/admin/reservations/statistics`

**Description:** Get statistical data for reservations dashboard.

**Parameters:**
- `period` (optional): "today", "week", "month", "year". Default: "month"
- `date_from` (optional): Custom start date (Y-m-d format)
- `date_to` (optional): Custom end date (Y-m-d format)

**Example Request:**
```bash
curl -X GET "http://localhost/tires/api/v1/admin/reservations/statistics?period=month" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json"
```

**Example Response:**
```json
{
  "status": "success",
  "message": "Reservation statistics retrieved successfully",
  "data": {
    "total_reservations": 74,
    "by_status": {
      "pending": 45,
      "confirmed": 15,
      "completed": 10,
      "cancelled": 4
    },
    "total_revenue": 555000,
    "average_amount": 7500,
    "total_customers": 68,
    "period": {
      "start_date": "2025-09-01",
      "end_date": "2025-09-30",
      "period_type": "month"
    }
  }
}
```

### 4. Availability Check

**Endpoint:** `GET /api/v1/admin/reservations/availability-check`

**Description:** Check time availability for a specific date and menu.

**Parameters:**
- `date` (required): Date in Y-m-d format
- `menu_id` (optional): Menu ID to check availability for

**Example Request:**
```bash
curl -X GET "http://localhost/tires/api/v1/admin/reservations/availability-check?date=2025-09-16&menu_id=1" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json"
```

**Example Response:**
```json
{
  "status": "success", 
  "message": "Availability check completed successfully",
  "data": {
    "date": "2025-09-16",
    "date_formatted": "September 16, 2025",
    "day_name": "Tuesday",
    "current_time": "11:46",
    "menu": {
      "id": 1,
      "name": "Installation of tires purchased at our store",
      "required_time": 50,
      "description": "Professional tire installation service"
    },
    "available_slots": 13,
    "time_slots": [
      {
        "time": "08:00",
        "datetime": "2025-09-16 08:00:00",
        "status": "available",
        "available": true,
        "reason": null,
        "service_end_time": "08:50"
      }
    ],
    "statistics": {
      "total_slots": 13,
      "available_slots": 10,
      "reserved_slots": 1,
      "blocked_slots": 2
    }
  }
}
```

## Status Management Endpoints

### 5. Confirm Reservation
**Endpoint:** `PATCH /api/v1/admin/reservations/{id}/confirm`

### 6. Cancel Reservation  
**Endpoint:** `PATCH /api/v1/admin/reservations/{id}/cancel`

### 7. Complete Reservation
**Endpoint:** `PATCH /api/v1/admin/reservations/{id}/complete`

### 8. Bulk Status Update
**Endpoint:** `PATCH /api/v1/admin/reservations/bulk/status`

**Request Body:**
```json
{
  "ids": [1, 2, 3],
  "status": "confirmed"
}
```

## CRUD Operations

### 9. Get All Reservations
**Endpoint:** `GET /api/v1/admin/reservations`

### 10. Get Single Reservation
**Endpoint:** `GET /api/v1/admin/reservations/{id}`

### 11. Create Reservation
**Endpoint:** `POST /api/v1/admin/reservations`

### 12. Update Reservation
**Endpoint:** `PUT/PATCH /api/v1/admin/reservations/{id}`

### 13. Delete Reservation
**Endpoint:** `DELETE /api/v1/admin/reservations/{id}`

## Frontend Implementation Examples

### Calendar View Implementation

```javascript
class ReservationCalendar {
    constructor() {
        this.currentMonth = '2025-09';
        this.currentView = 'month';
        this.selectedFilters = {};
        this.init();
    }
    
    async loadCalendarData() {
        try {
            const params = new URLSearchParams({
                month: this.currentMonth,
                view: this.currentView,
                ...this.selectedFilters
            });
            
            const response = await fetch(`/api/v1/admin/reservations/calendar?${params}`, {
                headers: {
                    'Authorization': `Bearer ${this.getToken()}`,
                    'Accept': 'application/json'
                }
            });
            
            const data = await response.json();
            if (data.status === 'success') {
                this.renderCalendar(data.data);
            }
        } catch (error) {
            console.error('Failed to load calendar:', error);
        }
    }
    
    renderCalendar(calendarData) {
        const calendarContainer = document.querySelector('.calendar-container');
        calendarContainer.innerHTML = '';
        
        calendarData.calendar_data.forEach(day => {
            const dayElement = this.createDayElement(day);
            calendarContainer.appendChild(dayElement);
        });
        
        this.updateNavigationInfo(calendarData.current_period);
        this.updateStatistics(calendarData.statistics);
    }
    
    createDayElement(dayData) {
        const dayDiv = document.createElement('div');
        dayDiv.className = `calendar-day ${!dayData.is_current_month ? 'other-month' : ''} ${dayData.is_today ? 'today' : ''}`;
        
        dayDiv.innerHTML = `
            <div class="day-number">${dayData.day}</div>
            <div class="reservations">
                ${dayData.reservations.map(reservation => `
                    <div class="reservation-item" style="border-left-color: ${reservation.menu_color}">
                        <div class="customer-name">${reservation.customer_name}</div>
                        <div class="time">${reservation.time} - ${reservation.end_time}</div>
                        <div class="status ${reservation.status}">${reservation.status}</div>
                    </div>
                `).join('')}
            </div>
        `;
        
        return dayDiv;
    }
}
```

### List View Implementation

```javascript
class ReservationList {
    constructor() {
        this.currentPage = 1;
        this.perPage = 15;
        this.filters = {};
        this.sortBy = 'reservation_datetime';
        this.sortOrder = 'desc';
        this.init();
    }
    
    async loadListData() {
        try {
            const params = new URLSearchParams({
                page: this.currentPage,
                per_page: this.perPage,
                sort_by: this.sortBy,
                sort_order: this.sortOrder,
                ...this.filters
            });
            
            const response = await fetch(`/api/v1/admin/reservations/list?${params}`, {
                headers: {
                    'Authorization': `Bearer ${this.getToken()}`,
                    'Accept': 'application/json'
                }
            });
            
            const data = await response.json();
            if (data.status === 'success') {
                this.renderList(data.data);
            }
        } catch (error) {
            console.error('Failed to load list:', error);
        }
    }
    
    renderList(listData) {
        this.renderTable(listData.reservations);
        this.renderPagination(listData.pagination);
        this.renderFilters(listData.filters);
        this.updateStatistics(listData.statistics);
    }
    
    renderTable(reservations) {
        const tbody = document.querySelector('.reservations-table tbody');
        tbody.innerHTML = '';
        
        reservations.forEach(reservation => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>
                    <div class="customer-info">
                        <div class="name">${reservation.customer.name}</div>
                        <div class="contact">${reservation.customer.phone}</div>
                    </div>
                </td>
                <td>
                    <div class="datetime-info">
                        <div class="date">${reservation.date_time.date}</div>
                        <div class="time">${reservation.date_time.time}</div>
                    </div>
                </td>
                <td>
                    <div class="menu-info">
                        <div class="name">${reservation.menu.name}</div>
                        <div class="duration">${reservation.menu.required_time} minutes</div>
                    </div>
                </td>
                <td class="people-count">${reservation.people_count}</td>
                <td class="status">
                    <span class="status-badge ${reservation.status}">${reservation.status}</span>
                </td>
            `;
            tbody.appendChild(row);
        });
    }
}
```

## Error Handling

All endpoints return consistent error responses:

```json
{
  "status": "error",
  "message": "Error description",
  "errors": [
    {
      "field": "field_name",
      "tag": "error_tag",
      "value": "field_value",
      "message": "Field-specific error message"
    }
  ]
}
```

## Common HTTP Status Codes

- `200`: Success
- `400`: Bad Request (validation errors)
- `401`: Unauthorized
- `403`: Forbidden (not admin)
- `404`: Not Found
- `422`: Unprocessable Entity (validation failed)
- `500`: Internal Server Error

This comprehensive API supports both the calendar and list views shown in your admin reservation management interface, providing all necessary data for a complete admin experience.
