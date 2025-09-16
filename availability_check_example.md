# Availability Check API - Admin Interface

This document shows how to use the new availability check functionality for the admin interface.

## API Endpoints

### 1. Get Availability Check
**Endpoint:** `GET /api/v1/admin/reservations/availability-check`

**Parameters:**
- `date` (required): Date in Y-m-d format (e.g., 2025-09-16)
- `menu_id` (optional): Menu ID to check availability for

**Example Usage:**

#### Step 1: Get Menu Selection (when no menu_id provided)
```bash
curl -X GET "http://localhost/tires/api/v1/admin/reservations/availability-check?date=2025-09-16" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json"
```

**Response:**
```json
{
  "status": "success",
  "message": "Please select a menu to check availability",
  "data": {
    "date": "2025-09-16",
    "date_formatted": "September 16, 2025",
    "day_name": "Tuesday",
    "current_time": "11:46",
    "menu_required": true,
    "available_menus": [
      {
        "id": 1,
        "name": "Installation of tires purchased at our store",
        "required_time": 50,
        "is_active": true
      }
    ]
  }
}
```

#### Step 2: Check Availability for Specific Menu
```bash
curl -X GET "http://localhost/tires/api/v1/admin/reservations/availability-check?date=2025-09-16&menu_id=1" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json"
```

**Response:**
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
      },
      {
        "time": "09:00",
        "datetime": "2025-09-16 09:00:00",
        "status": "available",
        "available": true,
        "reason": null,
        "service_end_time": "09:50"
      },
      {
        "time": "10:00",
        "datetime": "2025-09-16 10:00:00",
        "status": "available",
        "available": true,
        "reason": null,
        "service_end_time": "10:50"
      },
      {
        "time": "11:00",
        "datetime": "2025-09-16 11:00:00",
        "status": "past",
        "available": false,
        "reason": "Past time",
        "service_end_time": "11:50"
      },
      {
        "time": "12:00",
        "datetime": "2025-09-16 12:00:00",
        "status": "available",
        "available": true,
        "reason": null,
        "service_end_time": "12:50"
      },
      {
        "time": "13:00",
        "datetime": "2025-09-16 13:00:00",
        "status": "reserved",
        "available": false,
        "reason": "Already has reservation",
        "service_end_time": "13:50"
      },
      {
        "time": "14:00",
        "datetime": "2025-09-16 14:00:00",
        "status": "blocked",
        "available": false,
        "reason": "Time blocked by administrator",
        "service_end_time": "14:50"
      },
      {
        "time": "15:00",
        "datetime": "2025-09-16 15:00:00",
        "status": "available",
        "available": true,
        "reason": null,
        "service_end_time": "15:50"
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

## Status Types

The API returns different status types for each time slot:

1. **available** - Time slot can be booked
2. **reserved** - Already has a reservation
3. **blocked** - Blocked by administrator (blocked period)
4. **past** - Time has already passed

## Frontend Interface Implementation

Based on the interface you provided, here's how to implement the frontend:

### HTML Structure Example
```html
<div class="availability-check">
  <div class="date-picker">
    <input type="date" id="reservation-date" value="2025-09-16">
    <button class="nav-btn" id="prev-day">&lt; Previous</button>
    <button class="nav-btn" id="next-day">Next &gt;</button>
  </div>
  
  <div class="date-display">
    <h2>September 16, 2025</h2>
    <h3>Tuesday</h3>
  </div>
  
  <div class="menu-selection">
    <label>Select Menu</label>
    <select id="menu-select">
      <option value="1">Installation of tires purchased at our store (50 minutes)</option>
    </select>
  </div>
  
  <div class="availability-grid">
    <h3>Time Availability</h3>
    <p class="available-count">13 available slots</p>
    
    <div class="time-slots">
      <!-- Time slots will be populated here -->
    </div>
  </div>
  
  <div class="legend">
    <span class="available">Available - Can make reservation</span>
    <span class="reserved">Reserved - Already has reservation</span>
    <span class="blocked">Blocked - Time blocked by administrator</span>
  </div>
</div>
```

### JavaScript Implementation Example
```javascript
class AvailabilityChecker {
    constructor() {
        this.selectedDate = '2025-09-16';
        this.selectedMenu = null;
        this.init();
    }
    
    init() {
        this.bindEvents();
        this.loadAvailability();
    }
    
    bindEvents() {
        document.getElementById('reservation-date').addEventListener('change', (e) => {
            this.selectedDate = e.target.value;
            this.loadAvailability();
        });
        
        document.getElementById('menu-select').addEventListener('change', (e) => {
            this.selectedMenu = e.target.value;
            this.loadAvailability();
        });
        
        document.getElementById('prev-day').addEventListener('click', () => {
            this.navigateDate(-1);
        });
        
        document.getElementById('next-day').addEventListener('click', () => {
            this.navigateDate(1);
        });
    }
    
    navigateDate(days) {
        const date = new Date(this.selectedDate);
        date.setDate(date.getDate() + days);
        this.selectedDate = date.toISOString().split('T')[0];
        document.getElementById('reservation-date').value = this.selectedDate;
        this.loadAvailability();
    }
    
    async loadAvailability() {
        try {
            const params = new URLSearchParams({
                date: this.selectedDate
            });
            
            if (this.selectedMenu) {
                params.append('menu_id', this.selectedMenu);
            }
            
            const response = await fetch(`/api/v1/admin/reservations/availability-check?${params}`, {
                headers: {
                    'Authorization': `Bearer ${this.getToken()}`,
                    'Accept': 'application/json'
                }
            });
            
            const data = await response.json();
            
            if (data.status === 'success') {
                this.renderAvailability(data.data);
            }
        } catch (error) {
            console.error('Failed to load availability:', error);
        }
    }
    
    renderAvailability(data) {
        // Update date display
        document.querySelector('.date-display h2').textContent = data.date_formatted;
        document.querySelector('.date-display h3').textContent = data.day_name;
        
        // If menus need to be loaded
        if (data.menu_required) {
            this.renderMenuOptions(data.available_menus);
            return;
        }
        
        // Render time slots
        this.renderTimeSlots(data.time_slots, data.statistics);
    }
    
    renderMenuOptions(menus) {
        const select = document.getElementById('menu-select');
        select.innerHTML = '<option value="">Select a menu...</option>';
        
        menus.forEach(menu => {
            const option = document.createElement('option');
            option.value = menu.id;
            option.textContent = `${menu.name} (${menu.required_time} minutes)`;
            select.appendChild(option);
        });
    }
    
    renderTimeSlots(slots, statistics) {
        document.querySelector('.available-count').textContent = 
            `${statistics.available_slots} available slots`;
        
        const container = document.querySelector('.time-slots');
        container.innerHTML = '';
        
        slots.forEach(slot => {
            const slotEl = document.createElement('div');
            slotEl.className = `time-slot ${slot.status}`;
            slotEl.innerHTML = `
                <span class="time">${slot.time}</span>
                <span class="status">${this.getStatusLabel(slot.status)}</span>
            `;
            
            if (slot.available) {
                slotEl.addEventListener('click', () => {
                    this.selectTimeSlot(slot);
                });
            }
            
            container.appendChild(slotEl);
        });
    }
    
    getStatusLabel(status) {
        const labels = {
            'available': 'Available',
            'reserved': 'Reserved',
            'blocked': 'Blocked',
            'past': 'Past'
        };
        return labels[status] || status;
    }
    
    selectTimeSlot(slot) {
        // Handle time slot selection for booking
        console.log('Selected slot:', slot);
    }
    
    getToken() {
        // Get authentication token from local storage or meta tag
        return document.querySelector('meta[name="api-token"]')?.content || localStorage.getItem('api_token');
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    new AvailabilityChecker();
});
```

### CSS Styling Example
```css
.availability-check {
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
}

.date-picker {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 20px;
}

.date-display {
    text-align: center;
    margin-bottom: 20px;
}

.menu-selection {
    margin-bottom: 20px;
}

.menu-selection select {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.time-slots {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
    gap: 10px;
    margin-bottom: 20px;
}

.time-slot {
    padding: 15px;
    border: 2px solid #ddd;
    border-radius: 8px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s;
}

.time-slot.available {
    background-color: #f0f9ff;
    border-color: #3b82f6;
    color: #1e40af;
}

.time-slot.available:hover {
    background-color: #dbeafe;
}

.time-slot.reserved {
    background-color: #fef2f2;
    border-color: #ef4444;
    color: #dc2626;
    cursor: not-allowed;
}

.time-slot.blocked {
    background-color: #f9fafb;
    border-color: #6b7280;
    color: #6b7280;
    cursor: not-allowed;
}

.time-slot.past {
    background-color: #f3f4f6;
    border-color: #d1d5db;
    color: #9ca3af;
    cursor: not-allowed;
}

.legend {
    display: flex;
    gap: 20px;
    flex-wrap: wrap;
    font-size: 14px;
}

.legend span {
    padding: 5px 10px;
    border-radius: 4px;
}

.legend .available {
    background-color: #f0f9ff;
    color: #1e40af;
}

.legend .reserved {
    background-color: #fef2f2;
    color: #dc2626;
}

.legend .blocked {
    background-color: #f9fafb;
    color: #6b7280;
}
```

## Additional API Endpoints

The implementation also includes these existing endpoints that work together:

1. **Check Single Availability**: `POST /api/v1/admin/reservations/check-availability`
2. **Get Calendar Data**: `GET /api/v1/admin/reservations/calendar-data`
3. **Get Available Hours**: `GET /api/v1/admin/reservations/available-hours`
4. **Get General Availability**: `GET /api/v1/admin/reservations/availability`

This comprehensive availability check system provides all the functionality shown in your interface design!