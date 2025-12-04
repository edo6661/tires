# Struktur Tabel Database - Sistem Manajemen Bengkel Ban

## 📚 Daftar Isi
1. [User Management](#1-user-management)
2. [Menu & Services](#2-menu--services)
3. [Reservations](#3-reservations)
4. [Tire Storage](#4-tire-storage)
5. [Payments](#5-payments)
6. [Communications](#6-communications)
7. [Business Settings](#7-business-settings)
8. [Content Management](#8-content-management)
9. [System Tables](#9-system-tables)

---

## 1. USER MANAGEMENT

### 👤 Table: `users`
Menyimpan data pengguna (customer dan admin).

| Column | Type | Constraint | Description |
|--------|------|------------|-------------|
| `id` | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | ID unik user |
| `email` | VARCHAR(255) | UNIQUE, NOT NULL | Email user (login credential) |
| `email_verified_at` | TIMESTAMP | NULLABLE | Waktu verifikasi email |
| `password` | VARCHAR(255) | NOT NULL | Password ter-hash |
| `full_name` | VARCHAR(255) | NOT NULL | Nama lengkap |
| `full_name_kana` | VARCHAR(255) | NOT NULL | Nama dalam Katakana/Hiragana |
| `phone_number` | VARCHAR(255) | NOT NULL | Nomor telepon |
| `company_name` | VARCHAR(255) | NULLABLE | Nama perusahaan |
| `department` | VARCHAR(255) | NULLABLE | Departemen/divisi |
| `company_address` | TEXT | NULLABLE | Alamat kantor |
| `home_address` | TEXT | NULLABLE | Alamat rumah |
| `date_of_birth` | DATE | NULLABLE | Tanggal lahir |
| `gender` | ENUM | NULLABLE | male, female, other |
| `role` | ENUM | NOT NULL, DEFAULT: customer | customer, admin |
| `remember_token` | VARCHAR(100) | NULLABLE | Token remember me |
| `created_at` | TIMESTAMP | AUTO | Waktu dibuat |
| `updated_at` | TIMESTAMP | AUTO | Waktu update terakhir |

**Indexes:**
- PRIMARY KEY: `id`
- UNIQUE KEY: `email`

**Business Rules:**
- Email harus unik di seluruh sistem
- Password minimal 8 karakter (enforced di aplikasi)
- Role default adalah `customer`
- Admin dibuat manual oleh super admin

---

### 🔑 Table: `password_reset_tokens`
Menyimpan token reset password.

| Column | Type | Constraint | Description |
|--------|------|------------|-------------|
| `email` | VARCHAR(255) | PRIMARY KEY | Email user yang request reset |
| `token` | VARCHAR(255) | NOT NULL | Token reset password |
| `created_at` | TIMESTAMP | NULLABLE | Waktu token dibuat |

**Indexes:**
- PRIMARY KEY: `email`

**Business Rules:**
- Token expired setelah 60 menit
- Satu email hanya bisa punya satu token aktif
- Token dihapus setelah berhasil reset password

---

### 🔐 Table: `personal_access_tokens`
Token autentikasi API (Laravel Sanctum).

| Column | Type | Constraint | Description |
|--------|------|------------|-------------|
| `id` | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | ID token |
| `tokenable_type` | VARCHAR(255) | NOT NULL | Model type (User) |
| `tokenable_id` | BIGINT UNSIGNED | NOT NULL | User ID |
| `name` | VARCHAR(255) | NOT NULL | Nama token |
| `token` | VARCHAR(64) | UNIQUE, NOT NULL | Token string (hashed) |
| `abilities` | TEXT | NULLABLE | Permission abilities |
| `last_used_at` | TIMESTAMP | NULLABLE | Waktu terakhir digunakan |
| `expires_at` | TIMESTAMP | NULLABLE | Waktu expired |
| `created_at` | TIMESTAMP | AUTO | Waktu dibuat |
| `updated_at` | TIMESTAMP | AUTO | Waktu update |

**Indexes:**
- PRIMARY KEY: `id`
- UNIQUE KEY: `token`
- INDEX: `tokenable_type`, `tokenable_id`

---

### 📝 Table: `sessions`
Menyimpan session user.

| Column | Type | Constraint | Description |
|--------|------|------------|-------------|
| `id` | VARCHAR(255) | PRIMARY KEY | Session ID |
| `user_id` | BIGINT UNSIGNED | NULLABLE, FOREIGN KEY | Reference ke users.id |
| `ip_address` | VARCHAR(45) | NULLABLE | IP address user |
| `user_agent` | TEXT | NULLABLE | Browser/device info |
| `payload` | LONGTEXT | NOT NULL | Session data |
| `last_activity` | INTEGER | NOT NULL | Unix timestamp aktivitas terakhir |

**Indexes:**
- PRIMARY KEY: `id`
- INDEX: `user_id`
- INDEX: `last_activity`

**Foreign Keys:**
- `user_id` → `users(id)` ON DELETE SET NULL

---

## 2. MENU & SERVICES

### 🛠️ Table: `menus`
Menyimpan data layanan/menu yang ditawarkan.

| Column | Type | Constraint | Description |
|--------|------|------------|-------------|
| `id` | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | ID menu |
| `required_time` | INTEGER | NOT NULL | Waktu yang dibutuhkan (menit) |
| `price` | DECIMAL(10,2) | NULLABLE | Harga layanan |
| `color` | VARCHAR(7) | DEFAULT: #3B82F6 | Warna untuk calendar (hex) |
| `photo_path` | VARCHAR(255) | NULLABLE | Path foto menu |
| `display_order` | INTEGER | DEFAULT: 0 | Urutan tampilan |
| `is_active` | BOOLEAN | DEFAULT: true | Status aktif/nonaktif |
| `created_at` | TIMESTAMP | AUTO | Waktu dibuat |
| `updated_at` | TIMESTAMP | AUTO | Waktu update |

**Indexes:**
- PRIMARY KEY: `id`
- INDEX: `is_active`
- INDEX: `display_order`

**Business Rules:**
- `required_time` dalam satuan menit
- `color` format hex (#RRGGBB)
- Menu inactive tidak muncul di public API

**Note:** 
- Field `name` dan `description` dipindah ke tabel `menu_translations` (multilingual)

---

### 🌐 Table: `menu_translations`
Terjemahan multilingual untuk menu.

| Column | Type | Constraint | Description |
|--------|------|------------|-------------|
| `id` | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | ID translation |
| `menu_id` | BIGINT UNSIGNED | NOT NULL, FOREIGN KEY | Reference ke menus.id |
| `locale` | VARCHAR(2) | NOT NULL | Kode bahasa (en, ja) |
| `name` | VARCHAR(255) | NOT NULL | Nama menu |
| `description` | TEXT | NULLABLE | Deskripsi menu |
| `created_at` | TIMESTAMP | AUTO | Waktu dibuat |
| `updated_at` | TIMESTAMP | AUTO | Waktu update |

**Indexes:**
- PRIMARY KEY: `id`
- INDEX: `menu_id`, `locale`
- UNIQUE KEY: `menu_id`, `locale` (composite)

**Foreign Keys:**
- `menu_id` → `menus(id)` ON DELETE CASCADE

**Supported Locales:**
- `en`: English
- `ja`: Japanese (日本語)

---

## 3. RESERVATIONS

### 📅 Table: `reservations`
Menyimpan data reservasi/booking customer.

| Column | Type | Constraint | Description |
|--------|------|------------|-------------|
| `id` | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | ID reservasi |
| `reservation_number` | VARCHAR(255) | UNIQUE, NOT NULL | Nomor reservasi (RSV123456) |
| `user_id` | BIGINT UNSIGNED | NULLABLE, FOREIGN KEY | Reference ke users.id |
| `full_name` | VARCHAR(255) | NULLABLE | Nama customer (jika guest) |
| `full_name_kana` | VARCHAR(255) | NULLABLE | Nama kana (jika guest) |
| `email` | VARCHAR(255) | NULLABLE | Email (jika guest) |
| `phone_number` | VARCHAR(255) | NULLABLE | Telepon (jika guest) |
| `menu_id` | BIGINT UNSIGNED | NOT NULL, FOREIGN KEY | Reference ke menus.id |
| `reservation_datetime` | DATETIME | NOT NULL | Waktu reservasi |
| `number_of_people` | INTEGER | DEFAULT: 1 | Jumlah orang |
| `amount` | DECIMAL(10,2) | NOT NULL | Total biaya |
| `status` | ENUM | DEFAULT: pending | pending, confirmed, completed, cancelled |
| `notes` | TEXT | NULLABLE | Catatan tambahan |
| `created_at` | TIMESTAMP | AUTO | Waktu dibuat |
| `updated_at` | TIMESTAMP | AUTO | Waktu update |

**Indexes:**
- PRIMARY KEY: `id`
- UNIQUE KEY: `reservation_number`
- INDEX: `user_id`
- INDEX: `menu_id`
- INDEX: `reservation_datetime`
- INDEX: `status`

**Foreign Keys:**
- `user_id` → `users(id)` ON DELETE SET NULL (preserve reservation history)
- `menu_id` → `menus(id)` ON DELETE CASCADE

**Business Rules:**
- `reservation_number` format: RSV + timestamp + random
- Jika `user_id` NULL → guest booking (data disimpan di full_name, email, dll)
- Jika `user_id` ada → registered user booking
- Status flow: pending → confirmed → completed
- Status bisa langsung cancelled dari pending/confirmed

**Status Explanation:**
- `pending`: Menunggu konfirmasi admin
- `confirmed`: Sudah dikonfirmasi admin
- `completed`: Sudah selesai dilayani
- `cancelled`: Dibatalkan (customer atau admin)

---

### 📋 Table: `questionnaires`
Menyimpan jawaban questionnaire dari customer.

| Column | Type | Constraint | Description |
|--------|------|------------|-------------|
| `id` | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | ID questionnaire |
| `reservation_id` | BIGINT UNSIGNED | NOT NULL, FOREIGN KEY | Reference ke reservations.id |
| `questions_and_answers` | JSON | NOT NULL | Pertanyaan dan jawaban |
| `created_at` | TIMESTAMP | AUTO | Waktu dibuat |
| `updated_at` | TIMESTAMP | AUTO | Waktu update |

**Indexes:**
- PRIMARY KEY: `id`
- INDEX: `reservation_id`

**Foreign Keys:**
- `reservation_id` → `reservations(id)` ON DELETE CASCADE

**JSON Structure Example:**
```json
{
  "questions": [
    {
      "id": 1,
      "question": "Apakah ini kunjungan pertama?",
      "answer": "Ya"
    },
    {
      "id": 2,
      "question": "Dari mana Anda mengetahui layanan kami?",
      "answer": "Google Search"
    }
  ]
}
```

---

### 🚫 Table: `blocked_periods`
Menyimpan periode waktu yang diblokir (tidak bisa booking).

| Column | Type | Constraint | Description |
|--------|------|------------|-------------|
| `id` | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | ID blocked period |
| `menu_id` | BIGINT UNSIGNED | NULLABLE, FOREIGN KEY | Reference ke menus.id |
| `start_datetime` | DATETIME | NOT NULL | Waktu mulai blokir |
| `end_datetime` | DATETIME | NOT NULL | Waktu selesai blokir |
| `reason` | VARCHAR(255) | NOT NULL | Alasan blokir |
| `all_menus` | BOOLEAN | DEFAULT: false | Blokir semua menu? |
| `created_at` | TIMESTAMP | AUTO | Waktu dibuat |
| `updated_at` | TIMESTAMP | AUTO | Waktu update |

**Indexes:**
- PRIMARY KEY: `id`
- INDEX: `menu_id`
- INDEX: `start_datetime`, `end_datetime`

**Foreign Keys:**
- `menu_id` → `menus(id)` ON DELETE CASCADE

**Business Rules:**
- Jika `all_menus = true` → semua menu terblokir di periode tsb
- Jika `all_menus = false` → hanya menu dengan `menu_id` tertentu
- `menu_id` NULL + `all_menus = true` → hari libur nasional

**Use Cases:**
- Hari libur nasional (all_menus = true)
- Maintenance khusus menu tertentu
- Cuti karyawan
- Event khusus

---

## 4. TIRE STORAGE

### 🛞 Table: `tire_storages`
Menyimpan data penyimpanan ban customer.

| Column | Type | Constraint | Description |
|--------|------|------------|-------------|
| `id` | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | ID storage |
| `user_id` | BIGINT UNSIGNED | NOT NULL, FOREIGN KEY | Reference ke users.id |
| `tire_brand` | VARCHAR(255) | NOT NULL | Merek ban |
| `tire_size` | VARCHAR(255) | NOT NULL | Ukuran ban |
| `storage_start_date` | DATE | NOT NULL | Tanggal mulai simpan |
| `planned_end_date` | DATE | NOT NULL | Tanggal rencana ambil |
| `storage_fee` | DECIMAL(10,2) | NULLABLE | Biaya penyimpanan |
| `status` | ENUM | DEFAULT: active | active, ended |
| `notes` | TEXT | NULLABLE | Catatan tambahan |
| `created_at` | TIMESTAMP | AUTO | Waktu dibuat |
| `updated_at` | TIMESTAMP | AUTO | Waktu update |

**Indexes:**
- PRIMARY KEY: `id`
- INDEX: `user_id`
- INDEX: `status`
- INDEX: `storage_start_date`

**Foreign Keys:**
- `user_id` → `users(id)` ON DELETE CASCADE

**Business Rules:**
- `status = active`: Ban masih disimpan
- `status = ended`: Ban sudah diambil customer
- `storage_fee` bisa dihitung otomatis atau manual
- Reminder otomatis 1 minggu sebelum `planned_end_date`

**Tire Size Format Examples:**
- 195/65R15
- 205/55R16
- 225/45R17

---

## 5. PAYMENTS

### 💳 Table: `payments`
Menyimpan data pembayaran.

| Column | Type | Constraint | Description |
|--------|------|------------|-------------|
| `id` | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | ID payment |
| `user_id` | BIGINT UNSIGNED | NOT NULL, FOREIGN KEY | Reference ke users.id |
| `reservation_id` | BIGINT UNSIGNED | NULLABLE, FOREIGN KEY | Reference ke reservations.id |
| `amount` | DECIMAL(10,2) | NOT NULL | Jumlah pembayaran |
| `payment_method` | VARCHAR(255) | DEFAULT: credit_card | Metode pembayaran |
| `status` | ENUM | DEFAULT: pending | pending, completed, failed, refunded |
| `transaction_id` | VARCHAR(255) | NULLABLE | ID transaksi dari payment gateway |
| `payment_details` | JSON | NULLABLE | Detail tambahan |
| `paid_at` | TIMESTAMP | NULLABLE | Waktu pembayaran berhasil |
| `created_at` | TIMESTAMP | AUTO | Waktu dibuat |
| `updated_at` | TIMESTAMP | AUTO | Waktu update |

**Indexes:**
- PRIMARY KEY: `id`
- INDEX: `user_id`
- INDEX: `reservation_id`
- INDEX: `status`
- INDEX: `transaction_id`

**Foreign Keys:**
- `user_id` → `users(id)` ON DELETE CASCADE
- `reservation_id` → `reservations(id)` ON DELETE SET NULL

**Payment Methods:**
- `cash`: Tunai
- `credit_card`: Kartu kredit
- `debit_card`: Kartu debit
- `bank_transfer`: Transfer bank
- `e_wallet`: E-wallet (GoPay, OVO, dll)

**Status Flow:**
- `pending`: Menunggu pembayaran
- `completed`: Pembayaran berhasil
- `failed`: Pembayaran gagal
- `refunded`: Pembayaran di-refund

**JSON payment_details Example:**
```json
{
  "card_type": "Visa",
  "last_4_digits": "1234",
  "payment_gateway": "Stripe",
  "gateway_fee": "3500.00"
}
```

---

## 6. COMMUNICATIONS

### 📧 Table: `contacts`
Menyimpan pesan/inquiry dari customer atau guest.

| Column | Type | Constraint | Description |
|--------|------|------------|-------------|
| `id` | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | ID contact |
| `user_id` | BIGINT UNSIGNED | NULLABLE, FOREIGN KEY | Reference ke users.id |
| `full_name` | VARCHAR(255) | NULLABLE | Nama (jika guest) |
| `email` | VARCHAR(255) | NULLABLE | Email (jika guest) |
| `phone_number` | VARCHAR(255) | NULLABLE | Telepon (jika guest) |
| `subject` | VARCHAR(255) | NOT NULL | Subjek pertanyaan |
| `message` | TEXT | NOT NULL | Isi pesan |
| `status` | ENUM | DEFAULT: pending | pending, replied |
| `admin_reply` | TEXT | NULLABLE | Jawaban dari admin |
| `replied_at` | TIMESTAMP | NULLABLE | Waktu admin reply |
| `created_at` | TIMESTAMP | AUTO | Waktu dibuat |
| `updated_at` | TIMESTAMP | AUTO | Waktu update |

**Indexes:**
- PRIMARY KEY: `id`
- INDEX: `user_id`
- INDEX: `status`

**Foreign Keys:**
- `user_id` → `users(id)` ON DELETE SET NULL (preserve inquiry history)

**Business Rules:**
- Jika `user_id` NULL → guest inquiry (data di full_name, email)
- Jika `user_id` ada → registered user inquiry
- Email notification dikirim ke customer saat admin reply
- Status otomatis jadi `replied` saat admin isi `admin_reply`

---

## 7. BUSINESS SETTINGS

### ⚙️ Table: `business_settings`
Menyimpan konfigurasi bisnis/toko.

| Column | Type | Constraint | Description |
|--------|------|------------|-------------|
| `id` | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | ID setting |
| `phone_number` | VARCHAR(255) | NOT NULL | Nomor telepon toko |
| `business_hours` | JSON | NOT NULL | Jam operasional |
| `website_url` | VARCHAR(255) | NULLABLE | URL website |
| `top_image_path` | VARCHAR(255) | NULLABLE | Path gambar header |
| `site_public` | BOOLEAN | DEFAULT: true | Website publik/maintenance |
| `reply_email` | VARCHAR(255) | NULLABLE | Email untuk auto-reply |
| `google_analytics_id` | VARCHAR(255) | NULLABLE | Google Analytics tracking ID |
| `created_at` | TIMESTAMP | AUTO | Waktu dibuat |
| `updated_at` | TIMESTAMP | AUTO | Waktu update |

**Indexes:**
- PRIMARY KEY: `id`

**JSON business_hours Example:**
```json
{
  "monday": {"open": "09:00", "close": "18:00"},
  "tuesday": {"open": "09:00", "close": "18:00"},
  "wednesday": {"open": "09:00", "close": "18:00"},
  "thursday": {"open": "09:00", "close": "18:00"},
  "friday": {"open": "09:00", "close": "18:00"},
  "saturday": {"open": "09:00", "close": "15:00"},
  "sunday": {"closed": true}
}
```

**Business Rules:**
- Biasanya hanya 1 record di tabel ini (singleton)
- `site_public = false` → website mode maintenance
- `business_hours` digunakan untuk validasi reservasi

**Note:**
- Field multilingual (shop_name, address, dll) dipindah ke `business_setting_translations`

---

### 🌐 Table: `business_setting_translations`
Terjemahan multilingual untuk business settings.

| Column | Type | Constraint | Description |
|--------|------|------------|-------------|
| `id` | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | ID translation |
| `business_setting_id` | BIGINT UNSIGNED | NOT NULL, FOREIGN KEY | Reference ke business_settings.id |
| `locale` | VARCHAR(5) | NOT NULL | Kode bahasa |
| `shop_name` | VARCHAR(255) | NOT NULL | Nama toko |
| `address` | TEXT | NOT NULL | Alamat |
| `access_information` | TEXT | NULLABLE | Info akses/transportasi |
| `site_name` | VARCHAR(255) | NULLABLE | Nama website |
| `shop_description` | TEXT | NULLABLE | Deskripsi toko |
| `terms_of_use` | TEXT | NULLABLE | Syarat & ketentuan |
| `privacy_policy` | TEXT | NULLABLE | Kebijakan privasi |
| `created_at` | TIMESTAMP | AUTO | Waktu dibuat |
| `updated_at` | TIMESTAMP | AUTO | Waktu update |

**Indexes:**
- PRIMARY KEY: `id`
- INDEX: `business_setting_id`, `locale`
- UNIQUE KEY: `business_setting_id`, `locale` (composite)

**Foreign Keys:**
- `business_setting_id` → `business_settings(id)` ON DELETE CASCADE

---

## 8. CONTENT MANAGEMENT

### 📢 Table: `announcements`
Menyimpan pengumuman untuk customer.

| Column | Type | Constraint | Description |
|--------|------|------------|-------------|
| `id` | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | ID announcement |
| `is_active` | BOOLEAN | DEFAULT: true | Status aktif/nonaktif |
| `published_at` | DATETIME | NULLABLE | Waktu publikasi |
| `created_at` | TIMESTAMP | AUTO | Waktu dibuat |
| `updated_at` | TIMESTAMP | AUTO | Waktu update |

**Indexes:**
- PRIMARY KEY: `id`
- INDEX: `is_active`
- INDEX: `published_at`

**Note:**
- Field `title` dan `content` dipindah ke `announcement_translations`

---

### 🌐 Table: `announcement_translations`
Terjemahan multilingual untuk announcements.

| Column | Type | Constraint | Description |
|--------|------|------------|-------------|
| `id` | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | ID translation |
| `announcement_id` | BIGINT UNSIGNED | NOT NULL, FOREIGN KEY | Reference ke announcements.id |
| `locale` | VARCHAR(2) | NOT NULL | Kode bahasa (en, ja) |
| `title` | VARCHAR(255) | NOT NULL | Judul pengumuman |
| `content` | TEXT | NOT NULL | Isi pengumuman |
| `created_at` | TIMESTAMP | AUTO | Waktu dibuat |
| `updated_at` | TIMESTAMP | AUTO | Waktu update |

**Indexes:**
- PRIMARY KEY: `id`
- INDEX: `announcement_id`, `locale`
- UNIQUE KEY: `announcement_id`, `locale` (composite)

**Foreign Keys:**
- `announcement_id` → `announcements(id)` ON DELETE CASCADE

---

### ❓ Table: `faqs`
Menyimpan FAQ (Frequently Asked Questions).

| Column | Type | Constraint | Description |
|--------|------|------------|-------------|
| `id` | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | ID FAQ |
| `question` | VARCHAR(255) | NOT NULL | Pertanyaan |
| `answer` | TEXT | NOT NULL | Jawaban |
| `display_order` | INTEGER | DEFAULT: 0 | Urutan tampilan |
| `is_active` | BOOLEAN | DEFAULT: true | Status aktif |
| `created_at` | TIMESTAMP | AUTO | Waktu dibuat |
| `updated_at` | TIMESTAMP | AUTO | Waktu update |

**Indexes:**
- PRIMARY KEY: `id`
- INDEX: `is_active`
- INDEX: `display_order`

**Business Rules:**
- FAQs ditampilkan berurutan sesuai `display_order`
- FAQ inactive tidak muncul di public API

---

## 9. SYSTEM TABLES

### 🗄️ Table: `cache`
Laravel cache storage.

| Column | Type | Constraint | Description |
|--------|------|------------|-------------|
| `key` | VARCHAR(255) | PRIMARY KEY | Cache key |
| `value` | MEDIUMTEXT | NOT NULL | Cache value |
| `expiration` | INTEGER | NOT NULL | Unix timestamp expiration |

**Indexes:**
- PRIMARY KEY: `key`

---

### 🔒 Table: `cache_locks`
Laravel cache locks.

| Column | Type | Constraint | Description |
|--------|------|------------|-------------|
| `key` | VARCHAR(255) | PRIMARY KEY | Lock key |
| `owner` | VARCHAR(255) | NOT NULL | Lock owner |
| `expiration` | INTEGER | NOT NULL | Unix timestamp expiration |

**Indexes:**
- PRIMARY KEY: `key`

---

### 📋 Table: `jobs`
Laravel queue jobs.

| Column | Type | Constraint | Description |
|--------|------|------------|-------------|
| `id` | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | Job ID |
| `queue` | VARCHAR(255) | NOT NULL | Queue name |
| `payload` | LONGTEXT | NOT NULL | Job payload |
| `attempts` | TINYINT UNSIGNED | NOT NULL | Jumlah percobaan |
| `reserved_at` | INTEGER UNSIGNED | NULLABLE | Reserved timestamp |
| `available_at` | INTEGER UNSIGNED | NOT NULL | Available timestamp |
| `created_at` | INTEGER UNSIGNED | NOT NULL | Created timestamp |

**Indexes:**
- PRIMARY KEY: `id`
- INDEX: `queue`

---

### ❌ Table: `failed_jobs`
Laravel failed queue jobs.

| Column | Type | Constraint | Description |
|--------|------|------------|-------------|
| `id` | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | Failed job ID |
| `uuid` | VARCHAR(255) | UNIQUE | Job UUID |
| `connection` | TEXT | NOT NULL | Connection name |
| `queue` | TEXT | NOT NULL | Queue name |
| `payload` | LONGTEXT | NOT NULL | Job payload |
| `exception` | LONGTEXT | NOT NULL | Exception message |
| `failed_at` | TIMESTAMP | DEFAULT: CURRENT_TIMESTAMP | Failed time |

**Indexes:**
- PRIMARY KEY: `id`
- UNIQUE KEY: `uuid`

---

### 🔄 Table: `job_batches`
Laravel job batches.

| Column | Type | Constraint | Description |
|--------|------|------------|-------------|
| `id` | VARCHAR(255) | PRIMARY KEY | Batch ID |
| `name` | VARCHAR(255) | NOT NULL | Batch name |
| `total_jobs` | INTEGER | NOT NULL | Total jobs |
| `pending_jobs` | INTEGER | NOT NULL | Pending jobs |
| `failed_jobs` | INTEGER | NOT NULL | Failed jobs |
| `failed_job_ids` | LONGTEXT | NOT NULL | Failed job IDs |
| `options` | MEDIUMTEXT | NULLABLE | Batch options |
| `cancelled_at` | INTEGER | NULLABLE | Cancelled timestamp |
| `created_at` | INTEGER | NOT NULL | Created timestamp |
| `finished_at` | INTEGER | NULLABLE | Finished timestamp |

**Indexes:**
- PRIMARY KEY: `id`

---

## 📊 ENTITY RELATIONSHIP DIAGRAM (ERD)

```
┌─────────────────┐
│     USERS       │
├─────────────────┤
│ id (PK)         │◄──┐
│ email (UQ)      │   │
│ password        │   │
│ full_name       │   │
│ role            │   │
│ ...             │   │
└─────────────────┘   │
                      │
         ┌────────────┼────────────┐
         │            │            │
         │            │            │
    ┌────▼────┐  ┌───▼─────┐  ┌──▼──────────┐
    │RESERV-  │  │ TIRE    │  │  PAYMENTS   │
    │ATIONS   │  │STORAGES │  │             │
    ├─────────┤  ├─────────┤  ├─────────────┤
    │id (PK)  │  │id (PK)  │  │id (PK)      │
    │user_id  │  │user_id  │  │user_id      │
    │menu_id  │──┐│tire_*   │  │reservation_id│
    │status   │  ││status   │  │amount       │
    │...      │  │└─────────┘  │status       │
    └─────────┘  │             └─────────────┘
         │       │
         │       │             ┌─────────────┐
    ┌────▼───┐  │             │  CONTACTS   │
    │QUESTION│  │             ├─────────────┤
    │-NAIRES │  │             │id (PK)      │
    ├────────┤  │             │user_id      │
    │id (PK) │  │             │status       │
    │reserv* │  │             │admin_reply  │
    └────────┘  │             └─────────────┘
                │
         ┌──────▼────────┐
         │    MENUS      │
         ├───────────────┤
         │ id (PK)       │◄────┐
         │ price         │     │
         │ required_time │     │
         │ is_active     │     │
         └───────────────┘     │
                │              │
                │              │
         ┌──────▼────────┐    │
         │MENU_          │    │
         │TRANSLATIONS   │    │
         ├───────────────┤    │
         │id (PK)        │    │
         │menu_id (FK)   │    │
         │locale         │    │
         │name           │    │
         └───────────────┘    │
                              │
         ┌────────────────────┘
         │
    ┌────▼──────────┐
    │BLOCKED_       │
    │PERIODS        │
    ├───────────────┤
    │id (PK)        │
    │menu_id (FK)   │
    │start_datetime │
    │end_datetime   │
    │all_menus      │
    └───────────────┘

┌─────────────────────┐
│BUSINESS_SETTINGS    │
├─────────────────────┤
│id (PK)              │◄──┐
│phone_number         │   │
│business_hours (JSON)│   │
│site_public          │   │
└─────────────────────┘   │
                          │
         ┌────────────────┘
         │
    ┌────▼──────────────────┐
    │BUSINESS_SETTING_      │
    │TRANSLATIONS           │
    ├───────────────────────┤
    │id (PK)                │
    │business_setting_id(FK)│
    │locale                 │
    │shop_name              │
    │address                │
    └───────────────────────┘

┌──────────────┐
│ANNOUNCEMENTS │
├──────────────┤
│id (PK)       │◄──┐
│is_active     │   │
│published_at  │   │
└──────────────┘   │
                   │
    ┌──────────────┘
    │
    ▼
┌────────────────────┐
│ANNOUNCEMENT_       │
│TRANSLATIONS        │
├────────────────────┤
│id (PK)             │
│announcement_id (FK)│
│locale              │
│title               │
│content             │
└────────────────────┘

┌──────────┐
│  FAQS    │
├──────────┤
│id (PK)   │
│question  │
│answer    │
│is_active │
└──────────┘
```

---

## 🔑 FOREIGN KEY RELATIONSHIPS

### Users Related
- `reservations.user_id` → `users.id` (ON DELETE SET NULL)
- `tire_storages.user_id` → `users.id` (ON DELETE CASCADE)
- `payments.user_id` → `users.id` (ON DELETE CASCADE)
- `contacts.user_id` → `users.id` (ON DELETE SET NULL)
- `sessions.user_id` → `users.id` (ON DELETE SET NULL)

### Menus Related
- `reservations.menu_id` → `menus.id` (ON DELETE CASCADE)
- `blocked_periods.menu_id` → `menus.id` (ON DELETE CASCADE)
- `menu_translations.menu_id` → `menus.id` (ON DELETE CASCADE)

### Reservations Related
- `questionnaires.reservation_id` → `reservations.id` (ON DELETE CASCADE)
- `payments.reservation_id` → `reservations.id` (ON DELETE SET NULL)

### Translations Related
- `business_setting_translations.business_setting_id` → `business_settings.id` (ON DELETE CASCADE)
- `announcement_translations.announcement_id` → `announcements.id` (ON DELETE CASCADE)

---

## 📈 DATABASE STATISTICS

| Category | Tables | Total Columns |
|----------|--------|---------------|
| User Management | 4 | ~35 |
| Menu & Services | 2 | ~14 |
| Reservations | 3 | ~25 |
| Tire Storage | 1 | ~10 |
| Payments | 1 | ~11 |
| Communications | 1 | ~11 |
| Business Settings | 2 | ~20 |
| Content Management | 3 | ~16 |
| System Tables | 5 | ~30 |
| **TOTAL** | **22** | **~172** |

---

## 🎯 KEY DESIGN DECISIONS

### 1. Multilingual Support
- Menggunakan separate translation tables
- Pattern: `{table_name}_translations`
- Supported locales: `en`, `ja`

### 2. Soft Delete
- Tidak menggunakan soft delete di core tables
- Foreign key `ON DELETE SET NULL` untuk preserve history
- Contoh: user dihapus → reservations tetap ada (user_id = NULL)

### 3. JSON Fields
- `business_hours`: Fleksibel untuk custom schedule
- `payment_details`: Dynamic payment information
- `questionnaire_responses`: Variable questions

### 4. Enum Types
- Ketat untuk field dengan nilai terbatas
- Contoh: `status`, `role`, `gender`
- Lebih efisien daripada VARCHAR + validation

### 5. Indexing Strategy
- Primary keys: Auto-increment BIGINT UNSIGNED
- Foreign keys: Indexed untuk JOIN performance
- Status fields: Indexed untuk filtering
- Datetime fields: Indexed untuk range queries

---

## 🔍 QUERY OPTIMIZATION TIPS

### Frequently Used Queries

**1. Get Active Reservations for Today:**
```sql
SELECT * FROM reservations 
WHERE DATE(reservation_datetime) = CURDATE()
  AND status IN ('pending', 'confirmed')
ORDER BY reservation_datetime;
```
*Uses indexes:* `reservation_datetime`, `status`

**2. Get Customer's Reservation History:**
```sql
SELECT r.*, m.name, mt.name as menu_name
FROM reservations r
JOIN menus m ON r.menu_id = m.id
LEFT JOIN menu_translations mt ON m.id = mt.menu_id AND mt.locale = 'ja'
WHERE r.user_id = ?
ORDER BY r.created_at DESC;
```
*Uses indexes:* `user_id`, `menu_id`

**3. Check Availability:**
```sql
SELECT * FROM blocked_periods
WHERE menu_id = ? OR all_menus = true
  AND ? BETWEEN start_datetime AND end_datetime;

SELECT COUNT(*) FROM reservations
WHERE menu_id = ?
  AND DATE(reservation_datetime) = ?
  AND status != 'cancelled';
```
*Uses indexes:* `menu_id`, `start_datetime`, `end_datetime`, `status`

**4. Active Tire Storages:**
```sql
SELECT * FROM tire_storages
WHERE user_id = ?
  AND status = 'active'
ORDER BY storage_start_date DESC;
```
*Uses indexes:* `user_id`, `status`

---

## 📋 MIGRATION ORDER

Urutan eksekusi migrations (sudah benar):

1. ✅ `0001_01_01_000000_create_users_table`
2. ✅ `0001_01_01_000001_create_cache_table`
3. ✅ `0001_01_01_000002_create_jobs_table`
4. ✅ `2025_07_11_064424_create_business_settings_table`
5. ✅ `2025_07_11_064427_create_menus_table`
6. ✅ `2025_07_11_064428_create_reservations_table`
7. ✅ `2025_07_11_064428_create_tire_storages_table`
8. ✅ `2025_07_11_064429_create_contacts_table`
9. ✅ `2025_07_11_064429_create_payments_table`
10. ✅ `2025_07_11_064430_create_announcements_table`
11. ✅ `2025_07_11_064430_create_blocked_periods_table`
12. ✅ `2025_07_11_064430_create_faqs_table`
13. ✅ `2025_07_11_064431_create_questionnaires_table`
14. ✅ `2025_07_26_085156_create_menu_translations_table`
15. ✅ `2025_07_28_110816_create_business_setting_translations_table`
16. ✅ `2025_07_28_233817_create_announcement_translations_table`
17. ✅ `2025_08_20_140744_create_personal_access_tokens_table`

**Note:** Parent tables (users, menus, business_settings, announcements) harus dibuat sebelum translation tables & foreign key references.

---

## 🛡️ DATA INTEGRITY RULES

### Users
- ✅ Email must be unique
- ✅ Password must be hashed (bcrypt)
- ✅ Role must be 'customer' or 'admin'

### Reservations
- ✅ reservation_datetime must be in the future (on create)
- ✅ reservation_datetime must be within business hours
- ✅ No overlapping reservations for same menu + datetime
- ✅ Check blocked_periods before confirming

### Tire Storages
- ✅ storage_start_date ≤ planned_end_date
- ✅ Cannot delete if status = 'active'

### Payments
- ✅ amount must be > 0
- ✅ Cannot change status from 'completed' to 'pending'
- ✅ paid_at must be set when status = 'completed'

### Business Settings
- ✅ Only 1 active record (singleton pattern)
- ✅ business_hours JSON must be valid
- ✅ Must have translations for all active locales

---

**Generated:** November 2025  
**Database:** MySQL 8.0+  
**Framework:** Laravel 11.x  
**Character Set:** utf8mb4  
**Collation:** utf8mb4_unicode_ci
