# Daftar Endpoint API
# Sistem Manajemen Bengkel Ban

## Ringkasan
Dokumentasi lengkap semua endpoint API dalam bentuk tabel untuk sistem manajemen bengkel ban dengan support multi-language (EN/JA).

---

## 📊 Statistik API

| Kategori | Jumlah Endpoint | Auth Required | Public |
|----------|----------------|---------------|---------|
| **Public API** | 8 | ❌ | ✅ |
| **Auth API** | 6 | ✅ | ❌ |
| **Customer API** | 28 | ✅ | ❌ |
| **Admin API** | 100+ | ✅ (Admin) | ❌ |
| **Total** | **140+** | - | - |

---

# 1. PUBLIC API (Guest Users)

## 1.1 Menu/Layanan

| No | Method | Endpoint | Deskripsi | Auth | Params/Body |
|---|--------|----------|-----------|------|-------------|
| 1 | GET | `/api/v1/menus` | Lihat daftar menu aktif | ❌ | `?locale=en\|ja` |
| 2 | GET | `/api/v1/menus/{id}` | Detail menu | ❌ | `?locale=en\|ja` |

## 1.2 Business Info

| No | Method | Endpoint | Deskripsi | Auth | Params/Body |
|---|--------|----------|-----------|------|-------------|
| 3 | GET | `/api/v1/business-info` | Info bengkel | ❌ | `?locale=en\|ja` |
| 4 | GET | `/api/v1/business-hours` | Jam operasional | ❌ | - |

## 1.3 Konten

| No | Method | Endpoint | Deskripsi | Auth | Params/Body |
|---|--------|----------|-----------|------|-------------|
| 5 | GET | `/api/v1/faqs` | Daftar FAQ | ❌ | `?locale=en\|ja&category=string` |
| 6 | GET | `/api/v1/announcements` | Pengumuman aktif | ❌ | `?locale=en\|ja` |

## 1.4 Inquiry/Contact

| No | Method | Endpoint | Deskripsi | Auth | Params/Body |
|---|--------|----------|-----------|------|-------------|
| 7 | POST | `/api/v1/contacts` | Submit inquiry | ❌ | `full_name, email, phone, subject, message` |
| 8 | POST | `/api/v1/reservations/check-availability` | Cek ketersediaan | ❌ | `menu_id, date, time` |

---

# 2. AUTHENTICATION API

## 2.1 Auth Endpoints

| No | Method | Endpoint | Deskripsi | Auth | Body |
|---|--------|----------|-----------|------|------|
| 1 | POST | `/api/v1/auth/register` | Register customer | ❌ | `full_name, email, password, phone_number, etc` |
| 2 | POST | `/api/v1/auth/login` | Login | ❌ | `email, password` |
| 3 | POST | `/api/v1/auth/logout` | Logout | ✅ | - |
| 4 | POST | `/api/v1/auth/forgot-password` | Request reset password | ❌ | `email` |
| 5 | POST | `/api/v1/auth/reset-password` | Reset password | ❌ | `token, email, password` |
| 6 | GET | `/api/v1/auth/verify-email/{id}/{hash}` | Verifikasi email | ❌ | - |

---

# 3. CUSTOMER API

## 3.1 Profile Management

| No | Method | Endpoint | Deskripsi | Auth | Body |
|---|--------|----------|-----------|------|------|
| 1 | GET | `/api/v1/customer/profile` | Lihat profil | ✅ Customer | - |
| 2 | PUT | `/api/v1/customer/profile` | Update profil | ✅ Customer | `full_name, phone_number, company_name, etc` |
| 3 | POST | `/api/v1/customer/change-password` | Ubah password | ✅ Customer | `current_password, new_password` |
| 4 | DELETE | `/api/v1/customer/account` | Hapus akun | ✅ Customer | `password` |
| 5 | POST | `/api/v1/customer/resend-verification` | Kirim ulang verifikasi email | ✅ Customer | - |

## 3.2 Reservations

| No | Method | Endpoint | Deskripsi | Auth | Params/Body |
|---|--------|----------|-----------|------|-------------|
| 6 | GET | `/api/v1/customer/reservations` | Daftar reservasi | ✅ Customer | `?status=pending\|confirmed\|cancelled\|completed&per_page=15` |
| 7 | GET | `/api/v1/customer/reservations/{id}` | Detail reservasi | ✅ Customer | - |
| 8 | POST | `/api/v1/customer/reservations` | Buat reservasi | ✅ Customer | `menu_id, reservation_datetime, number_of_people` |
| 9 | PUT | `/api/v1/customer/reservations/{id}` | Update reservasi | ✅ Customer | `reservation_datetime, number_of_people, notes` |
| 10 | DELETE | `/api/v1/customer/reservations/{id}` | Batalkan reservasi | ✅ Customer | `cancellation_reason` |
| 11 | GET | `/api/v1/customer/reservations/history` | Riwayat reservasi | ✅ Customer | `?start_date=date&end_date=date` |
| 12 | GET | `/api/v1/customer/reservations/upcoming` | Reservasi mendatang | ✅ Customer | - |

## 3.3 Questionnaires

| No | Method | Endpoint | Deskripsi | Auth | Params/Body |
|---|--------|----------|-----------|------|-------------|
| 13 | GET | `/api/v1/customer/questionnaires` | Daftar kuesioner aktif | ✅ Customer | `?locale=en\|ja` |
| 14 | POST | `/api/v1/customer/reservations/{id}/questionnaire-responses` | Submit jawaban kuesioner | ✅ Customer | `responses: [{questionnaire_id, response}]` |
| 15 | GET | `/api/v1/customer/reservations/{id}/questionnaire-responses` | Lihat jawaban kuesioner | ✅ Customer | - |

## 3.4 Tire Storage

| No | Method | Endpoint | Deskripsi | Auth | Params/Body |
|---|--------|----------|-----------|------|-------------|
| 16 | GET | `/api/v1/customer/tire-storages` | Daftar penyimpanan ban | ✅ Customer | `?status=active\|completed\|cancelled` |
| 17 | GET | `/api/v1/customer/tire-storages/{id}` | Detail penyimpanan ban | ✅ Customer | - |
| 18 | POST | `/api/v1/customer/tire-storages` | Tambah penyimpanan ban | ✅ Customer | `tire_type, tire_size, quantity, storage_location, start_date` |
| 19 | PUT | `/api/v1/customer/tire-storages/{id}` | Update penyimpanan ban | ✅ Customer | `tire_type, tire_size, quantity, notes` |
| 20 | DELETE | `/api/v1/customer/tire-storages/{id}` | Hapus penyimpanan ban | ✅ Customer | - |
| 21 | PATCH | `/api/v1/customer/tire-storages/{id}/complete` | Selesaikan penyimpanan | ✅ Customer | `end_date` |

## 3.5 Inquiry/Contact

| No | Method | Endpoint | Deskripsi | Auth | Params/Body |
|---|--------|----------|-----------|------|-------------|
| 22 | GET | `/api/v1/customer/contacts` | Daftar inquiry saya | ✅ Customer | `?status=new\|in_progress\|resolved` |
| 23 | GET | `/api/v1/customer/contacts/{id}` | Detail inquiry | ✅ Customer | - |
| 24 | POST | `/api/v1/customer/contacts` | Submit inquiry baru | ✅ Customer | `subject, message` |

## 3.6 Dashboard & Stats

| No | Method | Endpoint | Deskripsi | Auth | Params |
|---|--------|----------|-----------|------|--------|
| 25 | GET | `/api/v1/customer/dashboard` | Dashboard customer | ✅ Customer | - |
| 26 | GET | `/api/v1/customer/statistics` | Statistik customer | ✅ Customer | - |

## 3.7 Content (Customer View)

| No | Method | Endpoint | Deskripsi | Auth | Params |
|---|--------|----------|-----------|------|--------|
| 27 | GET | `/api/v1/customer/announcements` | Pengumuman untuk customer | ✅ Customer | `?locale=en\|ja` |
| 28 | GET | `/api/v1/customer/faqs` | FAQ | ✅ Customer | `?locale=en\|ja&category=string` |

---

# 4. ADMIN API

## 4.1 User Management

| No | Method | Endpoint | Deskripsi | Auth | Params/Body |
|---|--------|----------|-----------|------|-------------|
| 1 | GET | `/api/v1/admin/users` | Daftar user | ✅ Admin | `?role=admin\|customer&search=keyword&per_page=15` |
| 2 | GET | `/api/v1/admin/users/{id}` | Detail user | ✅ Admin | - |
| 3 | POST | `/api/v1/admin/users` | Tambah user | ✅ Admin | `full_name, email, password, role, phone_number, etc` |
| 4 | PUT | `/api/v1/admin/users/{id}` | Update user | ✅ Admin | `full_name, email, role, phone_number, etc` |
| 5 | DELETE | `/api/v1/admin/users/{id}` | Hapus user | ✅ Admin | - |
| 6 | GET | `/api/v1/admin/users/customers` | Daftar customer | ✅ Admin | `?search=keyword` |
| 7 | GET | `/api/v1/admin/users/admins` | Daftar admin | ✅ Admin | `?search=keyword` |
| 8 | PATCH | `/api/v1/admin/users/{id}/reset-password` | Reset password user | ✅ Admin | `new_password, send_email` |
| 9 | PATCH | `/api/v1/admin/users/{id}/toggle-status` | Aktifkan/nonaktifkan user | ✅ Admin | - |

## 4.2 Menu Management

| No | Method | Endpoint | Deskripsi | Auth | Params/Body |
|---|--------|----------|-----------|------|-------------|
| 10 | GET | `/api/v1/admin/menus` | Daftar menu | ✅ Admin | `?is_active=true\|false&search=keyword&locale=en\|ja` |
| 11 | GET | `/api/v1/admin/menus/{id}` | Detail menu | ✅ Admin | `?locale=en\|ja` |
| 12 | POST | `/api/v1/admin/menus` | Tambah menu | ✅ Admin | `name, description, required_time, price, photo, translations` |
| 13 | PUT | `/api/v1/admin/menus/{id}` | Update menu | ✅ Admin | `name, description, required_time, price, is_active, translations` |
| 14 | DELETE | `/api/v1/admin/menus/{id}` | Hapus menu | ✅ Admin | - |
| 15 | PATCH | `/api/v1/admin/menus/{id}/toggle-status` | Toggle status menu | ✅ Admin | - |
| 16 | POST | `/api/v1/admin/menus/reorder` | Reorder menu | ✅ Admin | `orders: [{id, display_order}]` |
| 17 | PATCH | `/api/v1/admin/menus/bulk-update-status` | Bulk update status | ✅ Admin | `menu_ids: [], is_active: boolean` |
| 18 | GET | `/api/v1/admin/menus/statistics` | Statistik menu | ✅ Admin | - |
| 19 | POST | `/api/v1/admin/menus/calculate-end-time` | Hitung waktu selesai | ✅ Admin | `menu_id, start_time` |

## 4.3 Reservation Management

| No | Method | Endpoint | Deskripsi | Auth | Params/Body |
|---|--------|----------|-----------|------|-------------|
| 20 | GET | `/api/v1/admin/reservations/list` | Daftar reservasi | ✅ Admin | `?status=string&start_date=date&end_date=date&menu_id=int&customer_id=int` |
| 21 | GET | `/api/v1/admin/reservations/calendar` | Kalender reservasi | ✅ Admin | `?start=date&end=date&view=month\|week\|day` |
| 22 | GET | `/api/v1/admin/reservations/{id}` | Detail reservasi | ✅ Admin | - |
| 23 | POST | `/api/v1/admin/reservations` | Buat reservasi (admin) | ✅ Admin | `user_id/guest_info, menu_id, reservation_datetime, amount, status` |
| 24 | PUT | `/api/v1/admin/reservations/{id}` | Update reservasi | ✅ Admin | `menu_id, reservation_datetime, status, notes` |
| 25 | DELETE | `/api/v1/admin/reservations/{id}` | Hapus reservasi | ✅ Admin | - |
| 26 | PATCH | `/api/v1/admin/reservations/{id}/confirm` | Konfirmasi reservasi | ✅ Admin | - |
| 27 | PATCH | `/api/v1/admin/reservations/{id}/cancel` | Batalkan reservasi | ✅ Admin | `cancellation_reason, send_notification` |
| 28 | PATCH | `/api/v1/admin/reservations/{id}/complete` | Selesaikan reservasi | ✅ Admin | `actual_end_time, service_notes, payment_status` |
| 29 | PATCH | `/api/v1/admin/reservations/bulk/status` | Bulk update status | ✅ Admin | `reservation_ids: [], status, send_notification` |
| 30 | POST | `/api/v1/admin/reservations/check-availability` | Cek ketersediaan | ✅ Admin | `menu_id, date, time` |
| 31 | GET | `/api/v1/admin/reservations/statistics` | Statistik reservasi | ✅ Admin | - |
| 32 | GET | `/api/v1/admin/reservations/export` | Export data reservasi | ✅ Admin | `?format=csv\|excel\|pdf&start_date=date&end_date=date` |

## 4.4 Customer Management

| No | Method | Endpoint | Deskripsi | Auth | Params/Body |
|---|--------|----------|-----------|------|-------------|
| 33 | GET | `/api/v1/admin/customers` | Daftar customer | ✅ Admin | `?customer_type=first_time\|repeat\|dormant&search=keyword` |
| 34 | GET | `/api/v1/admin/customers/{id}` | Detail customer | ✅ Admin | - |
| 35 | GET | `/api/v1/admin/customers/search` | Cari customer | ✅ Admin | `?search=keyword` |
| 36 | GET | `/api/v1/admin/customers/statistics` | Statistik customer | ✅ Admin | - |
| 37 | GET | `/api/v1/admin/customers/export` | Export data customer | ✅ Admin | `?format=csv\|excel\|pdf` |

## 4.5 Tire Storage Management

| No | Method | Endpoint | Deskripsi | Auth | Params/Body |
|---|--------|----------|-----------|------|-------------|
| 38 | GET | `/api/v1/admin/storages` | Daftar storage | ✅ Admin | `?status=active\|completed&user_id=int` |
| 39 | GET | `/api/v1/admin/storages/{id}` | Detail storage | ✅ Admin | - |
| 40 | POST | `/api/v1/admin/storages` | Tambah storage | ✅ Admin | `user_id, tire_type, tire_size, quantity, storage_location` |
| 41 | PUT | `/api/v1/admin/storages/{id}` | Update storage | ✅ Admin | `tire_type, tire_size, quantity, notes` |
| 42 | DELETE | `/api/v1/admin/storages/{id}` | Hapus storage | ✅ Admin | - |
| 43 | PATCH | `/api/v1/admin/storages/{id}/end` | Akhiri storage | ✅ Admin | `end_date` |
| 44 | PATCH | `/api/v1/admin/storages/bulk-end` | Bulk end storage | ✅ Admin | `storage_ids: [], end_date` |

## 4.6 Contact/Inquiry Management

| No | Method | Endpoint | Deskripsi | Auth | Params/Body |
|---|--------|----------|-----------|------|-------------|
| 45 | GET | `/api/v1/admin/contacts` | Daftar kontak | ✅ Admin | `?status=new\|in_progress\|resolved&search=keyword` |
| 46 | GET | `/api/v1/admin/contacts/{id}` | Detail kontak | ✅ Admin | - |
| 47 | PATCH | `/api/v1/admin/contacts/{id}/reply` | Balas kontak | ✅ Admin | `admin_reply` |
| 48 | PATCH | `/api/v1/admin/contacts/{id}/status` | Update status kontak | ✅ Admin | `status` |
| 49 | DELETE | `/api/v1/admin/contacts/{id}` | Hapus kontak | ✅ Admin | - |

## 4.7 Questionnaire Management

| No | Method | Endpoint | Deskripsi | Auth | Params/Body |
|---|--------|----------|-----------|------|-------------|
| 50 | GET | `/api/v1/admin/questionnaires` | Daftar kuesioner | ✅ Admin | `?is_active=true\|false&locale=en\|ja` |
| 51 | GET | `/api/v1/admin/questionnaires/{id}` | Detail kuesioner | ✅ Admin | `?locale=en\|ja` |
| 52 | POST | `/api/v1/admin/questionnaires` | Tambah kuesioner | ✅ Admin | `question_type, is_required, translations: {locale: {question_text, options}}` |
| 53 | PUT | `/api/v1/admin/questionnaires/{id}` | Update kuesioner | ✅ Admin | `question_type, is_required, is_active, translations` |
| 54 | DELETE | `/api/v1/admin/questionnaires/{id}` | Hapus kuesioner | ✅ Admin | - |
| 55 | POST | `/api/v1/admin/questionnaires/reorder` | Reorder kuesioner | ✅ Admin | `orders: [{id, display_order}]` |
| 56 | PATCH | `/api/v1/admin/questionnaires/{id}/toggle-status` | Toggle status | ✅ Admin | - |

## 4.8 FAQ Management

| No | Method | Endpoint | Deskripsi | Auth | Params/Body |
|---|--------|----------|-----------|------|-------------|
| 57 | GET | `/api/v1/admin/faqs` | Daftar FAQ | ✅ Admin | `?category=string&is_active=boolean&locale=en\|ja` |
| 58 | GET | `/api/v1/admin/faqs/{id}` | Detail FAQ | ✅ Admin | `?locale=en\|ja` |
| 59 | POST | `/api/v1/admin/faqs` | Tambah FAQ | ✅ Admin | `category, translations: {locale: {question, answer}}` |
| 60 | PUT | `/api/v1/admin/faqs/{id}` | Update FAQ | ✅ Admin | `category, is_active, translations` |
| 61 | DELETE | `/api/v1/admin/faqs/{id}` | Hapus FAQ | ✅ Admin | - |
| 62 | POST | `/api/v1/admin/faqs/reorder` | Reorder FAQ | ✅ Admin | `orders: [{id, display_order}]` |
| 63 | PATCH | `/api/v1/admin/faqs/{id}/toggle-status` | Toggle status | ✅ Admin | - |

## 4.9 Announcement Management

| No | Method | Endpoint | Deskripsi | Auth | Params/Body |
|---|--------|----------|-----------|------|-------------|
| 64 | GET | `/api/v1/admin/announcements` | Daftar pengumuman | ✅ Admin | `?priority=string&is_active=boolean&locale=en\|ja` |
| 65 | GET | `/api/v1/admin/announcements/{id}` | Detail pengumuman | ✅ Admin | `?locale=en\|ja` |
| 66 | POST | `/api/v1/admin/announcements` | Tambah pengumuman | ✅ Admin | `priority, published_at, expires_at, translations: {locale: {title, content}}` |
| 67 | PUT | `/api/v1/admin/announcements/{id}` | Update pengumuman | ✅ Admin | `priority, is_active, published_at, expires_at, translations` |
| 68 | DELETE | `/api/v1/admin/announcements/{id}` | Hapus pengumuman | ✅ Admin | - |
| 69 | PATCH | `/api/v1/admin/announcements/{id}/toggle-status` | Toggle status | ✅ Admin | - |

## 4.10 Blocked Period Management

| No | Method | Endpoint | Deskripsi | Auth | Params/Body |
|---|--------|----------|-----------|------|-------------|
| 70 | GET | `/api/v1/admin/blocked-periods` | Daftar blocked periods | ✅ Admin | `?start_date=date&end_date=date&menu_id=int&locale=en\|ja` |
| 71 | GET | `/api/v1/admin/blocked-periods/{id}` | Detail blocked period | ✅ Admin | `?locale=en\|ja` |
| 72 | POST | `/api/v1/admin/blocked-periods` | Tambah blocked period | ✅ Admin | `start_datetime, end_datetime, menu_id, is_all_day, translations` |
| 73 | PUT | `/api/v1/admin/blocked-periods/{id}` | Update blocked period | ✅ Admin | `start_datetime, end_datetime, menu_id, is_all_day, translations` |
| 74 | DELETE | `/api/v1/admin/blocked-periods/{id}` | Hapus blocked period | ✅ Admin | - |
| 75 | GET | `/api/v1/admin/blocked-periods/calendar` | Kalender blocked periods | ✅ Admin | `?start=date&end=date` |
| 76 | GET | `/api/v1/admin/blocked-periods/statistics` | Statistik blocked periods | ✅ Admin | - |

## 4.11 Business Settings Management

| No | Method | Endpoint | Deskripsi | Auth | Params/Body |
|---|--------|----------|-----------|------|-------------|
| 77 | GET | `/api/v1/admin/business-settings` | Lihat pengaturan bisnis | ✅ Admin | `?locale=en\|ja` |
| 78 | PUT | `/api/v1/admin/business-settings` | Update pengaturan bisnis | ✅ Admin | `phone_number, business_hours, website_url, site_public, locale, shop_name, address, etc` |
| 79 | PATCH | `/api/v1/admin/business-settings/business-hours` | Update jam operasional | ✅ Admin | `business_hours: {day: {open, close}}` |
| 80 | POST | `/api/v1/admin/business-settings/top-image` | Upload gambar utama | ✅ Admin | `top_image: file` |
| 81 | DELETE | `/api/v1/admin/business-settings/top-image` | Hapus gambar utama | ✅ Admin | - |

## 4.12 Dashboard & Reports

| No | Method | Endpoint | Deskripsi | Auth | Params |
|---|--------|----------|-----------|------|--------|
| 82 | GET | `/api/v1/admin/dashboard` | Dashboard admin | ✅ Admin | - |
| 83 | GET | `/api/v1/admin/dashboard/today` | Statistik hari ini | ✅ Admin | - |
| 84 | GET | `/api/v1/admin/dashboard/week` | Statistik minggu ini | ✅ Admin | - |
| 85 | GET | `/api/v1/admin/dashboard/month` | Statistik bulan ini | ✅ Admin | - |
| 86 | GET | `/api/v1/admin/reports/revenue` | Laporan revenue | ✅ Admin | `?start_date=date&end_date=date` |
| 87 | GET | `/api/v1/admin/reports/reservations` | Laporan reservasi | ✅ Admin | `?start_date=date&end_date=date&format=json\|csv\|excel` |
| 88 | GET | `/api/v1/admin/reports/customers` | Laporan customer | ✅ Admin | `?customer_type=string&format=json\|csv\|excel` |
| 89 | GET | `/api/v1/admin/analytics/trends` | Analitik trend | ✅ Admin | `?period=week\|month\|year` |

## 4.13 Payment Management

| No | Method | Endpoint | Deskripsi | Auth | Params/Body |
|---|--------|----------|-----------|------|-------------|
| 90 | GET | `/api/v1/admin/payments` | Daftar pembayaran | ✅ Admin | `?status=unpaid\|paid\|refunded&start_date=date&end_date=date` |
| 91 | GET | `/api/v1/admin/payments/{id}` | Detail pembayaran | ✅ Admin | - |
| 92 | PATCH | `/api/v1/admin/payments/{id}/status` | Update status pembayaran | ✅ Admin | `payment_status` |
| 93 | POST | `/api/v1/admin/payments/reconciliation` | Rekonsiliasi pembayaran | ✅ Admin | `date, transactions: []` |
| 94 | GET | `/api/v1/admin/payments/report` | Laporan keuangan | ✅ Admin | `?start_date=date&end_date=date&format=csv\|excel\|pdf` |

---

# 📋 Konvensi Endpoint

## Base URL
```
Production: https://api.tirepro.co.id
Development: http://localhost:8000
```

## Versioning
Semua endpoint menggunakan versioning: `/api/v1/`

## Authentication
- **Public**: Tidak perlu auth token
- **Customer**: Memerlukan Sanctum token dengan role `customer`
- **Admin**: Memerlukan Sanctum token dengan role `admin`

### Headers Required
```
Authorization: Bearer {token}
Accept: application/json
Content-Type: application/json
Accept-Language: en|ja (optional, default: en)
```

## Query Parameters Common

| Parameter | Tipe | Deskripsi | Default |
|-----------|------|-----------|---------|
| `locale` | string | Bahasa konten (en/ja) | `en` |
| `per_page` | integer | Jumlah item per halaman | `15` |
| `cursor` | string | Cursor untuk pagination | - |
| `search` | string | Keyword pencarian | - |
| `sort_by` | string | Field untuk sorting | `created_at` |
| `sort_order` | string | Arah sorting (asc/desc) | `desc` |

## Response Format

### Success Response
```json
{
  "data": [...],
  "message": "Success message",
  "meta": {
    "current_page": 1,
    "per_page": 15,
    "total": 100
  }
}
```

### Error Response
```json
{
  "message": "Error message",
  "errors": {
    "field_name": ["Validation error message"]
  },
  "status_code": 422
}
```

### Pagination (Cursor-based)
```json
{
  "data": [...],
  "cursor": {
    "next": "eyJpZCI6MTB9",
    "prev": "eyJpZCI6MjB9",
    "has_more": true
  }
}
```

## HTTP Status Codes

| Code | Meaning |
|------|---------|
| 200 | OK - Request berhasil |
| 201 | Created - Resource berhasil dibuat |
| 204 | No Content - Delete berhasil |
| 400 | Bad Request - Request tidak valid |
| 401 | Unauthorized - Token tidak valid/expired |
| 403 | Forbidden - Tidak punya akses |
| 404 | Not Found - Resource tidak ditemukan |
| 422 | Unprocessable Entity - Validasi gagal |
| 500 | Internal Server Error - Error server |

## Rate Limiting

| Role | Limit |
|------|-------|
| Guest | 60 requests/minute |
| Customer | 120 requests/minute |
| Admin | 300 requests/minute |

## Multi-language Support

Semua endpoint konten mendukung parameter `locale`:
- `en`: English
- `ja`: Japanese (日本語)

**Cara Penggunaan**:
1. Query parameter: `?locale=ja`
2. Header: `Accept-Language: ja`
3. Default: `en`

## File Upload Endpoints

| Endpoint | Max Size | Allowed Types |
|----------|----------|---------------|
| `/admin/menus` (photo) | 2MB | jpg, jpeg, png, webp |
| `/admin/business-settings/top-image` | 2MB | jpg, jpeg, png, webp |

## Export Formats

Endpoint dengan fitur export support:
- CSV (`.csv`)
- Excel (`.xlsx`)
- PDF (`.pdf`)

**Usage**: `?format=csv`

---

# 🔐 Authentication Flow

## 1. Register
```
POST /api/v1/auth/register
→ User created
→ Email verification sent
```

## 2. Login
```
POST /api/v1/auth/login
→ Returns access token
→ Store token in client
```

## 3. Authenticated Request
```
GET /api/v1/customer/profile
Header: Authorization: Bearer {token}
→ Returns user data
```

## 4. Logout
```
POST /api/v1/auth/logout
Header: Authorization: Bearer {token}
→ Token invalidated
```

---

# 📊 Endpoint Statistics by Category

## Public Endpoints: 8
- Menu: 2
- Business Info: 2
- Content: 2
- Contact: 2

## Auth Endpoints: 6
- Register/Login: 2
- Password: 2
- Email: 2

## Customer Endpoints: 28
- Profile: 5
- Reservations: 7
- Questionnaires: 3
- Tire Storage: 6
- Contact: 3
- Dashboard: 2
- Content: 2

## Admin Endpoints: 94
- Users: 9
- Menus: 10
- Reservations: 13
- Customers: 5
- Tire Storage: 7
- Contacts: 5
- Questionnaires: 7
- FAQs: 7
- Announcements: 6
- Blocked Periods: 7
- Business Settings: 5
- Dashboard/Reports: 8
- Payments: 5

**Total API Endpoints: 136**

---

# 🎯 Key Features

## ✅ Multi-language (EN/JA)
Support bahasa Inggris dan Jepang untuk semua konten

## ✅ Guest & Registered Users
Support reservasi untuk guest dan registered users

## ✅ Cursor Pagination
Efficient pagination untuk list yang besar

## ✅ Advanced Filtering
Multi-criteria filtering di hampir semua list endpoints

## ✅ Bulk Operations
Batch updates untuk efficiency (menus, reservations, storage)

## ✅ Export Data
Export ke CSV/Excel/PDF untuk reporting

## ✅ Real-time Availability
Check availability sebelum booking

## ✅ Calendar Integration
Calendar view untuk reservasi dan blocked periods

## ✅ Dynamic Questionnaires
Flexible questionnaire system dengan berbagai question types

## ✅ Email Notifications
Auto email untuk booking confirmation, cancellation, replies

---

## Kesimpulan

API ini menyediakan **136 endpoints** yang mencakup:
- ✅ Complete CRUD operations
- ✅ Multi-language support (EN/JA)
- ✅ Role-based access control (Guest/Customer/Admin)
- ✅ Advanced filtering & search
- ✅ Bulk operations
- ✅ Export functionality
- ✅ Real-time data
- ✅ Comprehensive reporting

Semua endpoint mengikuti **RESTful conventions** dan **Laravel best practices**.

