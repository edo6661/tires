# 📧 Tutorial Lengkap Migrasi dari Resend ke Brevo API

## 🎯 Apa yang Sudah Dilakukan

Proyek ini telah dimigrasi dari **Resend** ke **Brevo API** untuk layanan email transaksional. Berikut perubahan yang telah diterapkan:

### ✅ Perubahan File

1. **`composer.json`** - Package `resend/resend-php` dihapus, ditambahkan `getbrevo/brevo-php`
2. **`.env`** - Konfigurasi email diubah menggunakan Brevo API Key
3. **`config/mail.php`** - Default mailer diubah ke `brevo` dengan custom transport
4. **`config/services.php`** - Ditambahkan konfigurasi Brevo API
5. **`app/Mail/Transport/BrevoTransport.php`** - Custom mail transport untuk Brevo API (BARU)
6. **`app/Providers/BrevoMailServiceProvider.php`** - Service provider untuk register Brevo transport (BARU)
7. **`bootstrap/providers.php`** - Register BrevoMailServiceProvider

### 📝 Catatan Penting

- ✅ Semua **email templates** tetap kompatibel (tidak perlu diubah)
- ✅ Semua **Listeners** tetap kompatibel (tidak perlu diubah)
- ✅ Semua **Mailable classes** tetap kompatibel (tidak perlu diubah)
- ✅ Menggunakan **Brevo API** (bukan SMTP) untuk performa lebih baik

---

## 🚀 Panduan Setup Brevo (Langkah demi Langkah)

### 1️⃣ Membuat Akun Brevo

1. **Kunjungi website Brevo**: https://www.brevo.com/
2. **Klik tombol "Sign up free"** di pojok kanan atas
3. **Isi formulir pendaftaran**:
   - Email address (gunakan email perusahaan/bisnis)
   - Password yang kuat
   - Nama perusahaan
   - Negara
4. **Verifikasi email**: Cek inbox email Anda dan klik link verifikasi dari Brevo
5. **Login ke akun Brevo**: https://app.brevo.com/

---

### 2️⃣ Mendapatkan API Key

Setelah login ke dashboard Brevo:

1. **Klik nama profil Anda** di pojok kanan atas
2. **Pilih "SMTP & API"** dari menu dropdown
   - URL langsung: https://app.brevo.com/settings/keys/api

3. **Di tab "API Keys"**, Anda akan melihat daftar API keys atau bisa create baru

4. **Generate API Key baru** (jika belum punya):
   - Klik tombol **"Create a new API key"** atau **"Generate a new API key"**
   - Beri nama key (contoh: "Laravel Production" atau "Tire Xchange App")
   - **Copy dan simpan key ini dengan aman** (hanya ditampilkan sekali!)
   - Format key: `xkeysib-xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx-xxxxxxxxxxxxxxxx`

> **💡 PENTING:**
> - API Key ini berbeda dengan SMTP Key!
> - Gunakan API Key (bukan SMTP Key) untuk implementasi ini
> - API Key memberikan performa lebih baik daripada SMTP

---

### 3️⃣ Konfigurasi Domain Sender (Opsional tapi Direkomendasikan)

Untuk meningkatkan deliverability dan menghindari email masuk spam:

1. **Pergi ke "Senders & IP"**: https://app.brevo.com/settings/senders
2. **Klik "Add a sender"**
3. **Masukkan detail sender**:
   - Sender name: `Tire Xchange` (atau nama bisnis Anda)
   - Sender email: `no-reply@tire.fts.biz.id` (sesuai yang ada di .env)
4. **Verifikasi domain** (sangat penting):
   - Brevo akan memberikan DNS records (SPF, DKIM, DMARC)
   - Tambahkan records ini ke DNS provider Anda (Cloudflare, cPanel, dll)
   - Tunggu propagasi DNS (bisa 24-48 jam)
   - Kembali ke Brevo dan klik "Verify" untuk memastikan setup berhasil

#### Contoh DNS Records yang perlu ditambahkan:

**SPF Record (TXT):**
```
Type: TXT
Name: @ (atau domain Anda)
Value: v=spf1 include:spf.brevo.com ~all
```

**DKIM Record (TXT):**
```
Type: TXT
Name: mail._domainkey (akan diberikan oleh Brevo)
Value: [long string yang diberikan Brevo]
```

**DMARC Record (TXT):**
```
Type: TXT
Name: _dmarc
Value: v=DMARC1; p=none
```

---

### 4️⃣ Update File `.env` di Laravel

Buka file `.env` dan update bagian email:

```env
# Konfigurasi Email - Brevo API
MAIL_MAILER=brevo
BREVO_API_KEY=key_kamu
MAIL_FROM_ADDRESS=email_kamu
MAIL_FROM_NAME="${APP_NAME}"
```

**Penjelasan parameter:**
- `MAIL_MAILER=brevo` - Menggunakan custom Brevo transport
- `BREVO_API_KEY` - API Key dari dashboard Brevo (BUKAN SMTP Key!)
- `MAIL_FROM_ADDRESS` - Alamat email pengirim (harus verified di Brevo)
- `MAIL_FROM_NAME` - Nama pengirim yang muncul di inbox penerima

> **🚀 Keuntungan menggunakan API vs SMTP:**
> - ✅ Lebih cepat - No SMTP handshake overhead
> - ✅ Lebih reliable - Direct HTTP/REST API calls
> - ✅ Better error handling - Detailed error messages
> - ✅ Advanced features - Batch sending, scheduling, templates, etc.

---

### 5️⃣ Clear Cache Laravel

Setelah mengubah `.env`, wajib clear cache:

```bash
php artisan config:clear
php artisan cache:clear
php artisan queue:restart  # Jika pakai queue
```

---

### 6️⃣ Arsitektur Implementasi Brevo API

Proyek ini menggunakan **Custom Mail Transport** untuk integrasi dengan Brevo API:

#### File-file Penting:

1. **`app/Mail/Transport/BrevoTransport.php`**
   - Custom transport yang implement `TransportInterface`
   - Menggunakan Brevo PHP SDK (`getbrevo/brevo-php`)
   - Mengirim email via REST API endpoint `/smtp/email`

2. **`app/Providers/BrevoMailServiceProvider.php`**
   - Register custom transport `brevo` ke Laravel Mail Manager
   - Inject API key dari config

3. **`config/mail.php`**
   - Konfigurasi mailer `brevo` dengan transport custom

4. **`config/services.php`**
   - Menyimpan Brevo API key configuration

#### Cara Kerja:

```
Laravel Mailable
      ↓
Mail Facade
      ↓
BrevoTransport (Custom)
      ↓
Brevo PHP SDK
      ↓
Brevo API (HTTPS REST)
      ↓
Email Delivered ✅
```

#### Contoh Penggunaan di Code:

```php
// Sudah otomatis menggunakan Brevo transport
Mail::to('user@example.com')->send(new BookingConfirmationMail($reservation));

// Atau dengan queue (recommended)
Mail::to('user@example.com')->queue(new BookingConfirmationMail($reservation));
```

Tidak perlu ubah code apapun di Mailable classes atau Listeners yang sudah ada!

---

### 6️⃣ Test Pengiriman Email

#### Option A: Menggunakan Tinker (Recommended)

```bash
php artisan tinker
```

Kemudian jalankan:

```php
Mail::raw('Test email dari Brevo SMTP', function ($message) {
    $message->to('your-email@example.com')
            ->subject('Test Email Brevo');
});
```

#### Option B: Menggunakan Test Route

Buat route sementara di `routes/web.php`:

```php
Route::get('/test-email', function () {
    try {
        Mail::raw('Test email dari Brevo', function ($message) {
            $message->to('your-email@example.com')
                    ->subject('Test Brevo SMTP');
        });
        return 'Email terkirim! Cek inbox Anda.';
    } catch (\Exception $e) {
        return 'Error: ' . $e->getMessage();
    }
});
```

Kemudian akses: `http://localhost:8000/test-email`

#### Option C: Test dengan Actual Booking

Buat booking melalui aplikasi Anda dan lihat apakah email confirmation terkirim.

---

## 🔍 Monitoring & Analytics

### Dashboard Brevo

Login ke https://app.brevo.com/ untuk monitoring:

1. **Statistics** - Lihat statistik email:
   - Email terkirim (sent)
   - Email dibuka (opens)
   - Link diklik (clicks)
   - Email bounce/gagal
   - Spam reports

2. **Transactional** > **Email** - Melihat log email:
   - Semua email yang terkirim
   - Status delivery
   - Detail recipient
   - Email content preview

3. **Real-time alerts** - Setup notifikasi untuk:
   - Bounce rate tinggi
   - Spam complaints
   - Delivery issues

---

## 📊 Limits & Quotas

### Free Plan (Gratis):
- ✅ **300 emails/day**
- ✅ Unlimited contacts
- ✅ Transactional emails
- ✅ Email templates
- ✅ SMTP relay
- ✅ Real-time statistics

### Paid Plans:
Jika perlu lebih dari 300 email/day, upgrade di:
https://www.brevo.com/pricing/

**Lite Plan** (~$25/month):
- 10,000 emails/month
- No daily sending limit
- Remove Brevo logo

**Premium & Enterprise**: Volume lebih tinggi + fitur advanced

---

## 🐛 Troubleshooting

### Problem: Email tidak terkirim / API error

**Solusi:**
1. Cek BREVO_API_KEY di `.env` sudah benar
2. Pastikan menggunakan **API Key**, bukan SMTP Key
3. Verifikasi sender email di dashboard Brevo
4. Clear Laravel config: `php artisan config:clear`
5. Cek log error: `storage/logs/laravel.log`

### Problem: Email masuk spam

**Solusi:**
1. **Verifikasi domain** di Brevo (wajib!)
2. Setup **SPF, DKIM, DMARC** records
3. Gunakan email sender yang sudah verified
4. Hindari kata-kata spammy di subject/content
5. Test email score: https://www.mail-tester.com/

### Problem: Authentication failed / Invalid API key

**Solusi:**
1. Pastikan menggunakan **API Key** (format: `xkeysib-xxxxx-xxxxx`)
2. Bukan SMTP Key atau password akun
3. Generate API Key baru jika lupa
4. Copy paste dengan hati-hati (no extra spaces)
5. Clear Laravel config: `php artisan config:clear`

### Problem: Sender not verified

**Solusi:**
1. Pergi ke **Senders & IP** di dashboard Brevo
2. Tambahkan email sender yang Anda gunakan
3. Verifikasi via email atau DNS records
4. Pastikan `MAIL_FROM_ADDRESS` di `.env` sama dengan sender verified

### Problem: Rate limit exceeded

**Solusi:**
1. Cek quota di dashboard: **Account** > **Plan**
2. Upgrade plan jika perlu
3. Implementasi queue untuk email: `implements ShouldQueue` (sudah ada)
4. Add delay antara email: `->later(now()->addSeconds(5))`

### Problem: Custom Transport not found

**Solusi:**
1. Pastikan `BrevoMailServiceProvider` sudah registered di `bootstrap/providers.php`
2. Run `composer dump-autoload`
3. Clear cache: `php artisan config:clear` dan `php artisan cache:clear`
4. Restart queue workers: `php artisan queue:restart`

---

## 🔐 Best Practices

### 1. Keamanan
- ❌ Jangan commit `.env` ke Git
- ✅ Simpan SMTP Key di environment variables
- ✅ Gunakan `.env.example` sebagai template
- ✅ Rotate SMTP keys secara berkala (6-12 bulan)

### 2. Performance
- ✅ Gunakan Queue untuk email (sudah implemented: `ShouldQueue`)
- ✅ Enable Redis untuk queue jobs
- ✅ Monitor queue workers: `php artisan queue:work`

### 3. Deliverability
- ✅ Verifikasi domain sender (SPF, DKIM, DMARC)
- ✅ Gunakan dedicated IP (untuk high volume)
- ✅ Monitor bounce rate & spam reports
- ✅ Implement unsubscribe link
- ✅ Clean email list secara berkala

### 4. Testing
- ✅ Test di development dengan Mailtrap atau Brevo test mode
- ✅ Test deliverability dengan mail-tester.com
- ✅ Test responsiveness email template
- ✅ Test dengan berbagai email client (Gmail, Outlook, etc)

---

## 📚 Resources & Documentation

### Official Brevo Documentation:
- **Getting Started**: https://developers.brevo.com/docs/getting-started
- **SMTP Integration**: https://developers.brevo.com/docs/smtp-integration
- **API Reference**: https://developers.brevo.com/reference
- **Help Center**: https://help.brevo.com/

### Laravel Mail Documentation:
- **Laravel Mail**: https://laravel.com/docs/11.x/mail
- **Queue**: https://laravel.com/docs/11.x/queues
- **Mailables**: https://laravel.com/docs/11.x/mail#generating-mailables

### Email Testing Tools:
- **Mail Tester**: https://www.mail-tester.com/ (test spam score)
- **Mailtrap**: https://mailtrap.io/ (testing environment)
- **Email on Acid**: https://www.emailonacid.com/ (email preview)

---

## 🎓 Quick Reference

### API Key Format:
```
xkeysib-[64 character string]-[16 character string]
```

### Important URLs:
- **Dashboard**: https://app.brevo.com/
- **API Keys**: https://app.brevo.com/settings/keys/api
- **SMTP Settings** (untuk reference): https://app.brevo.com/settings/keys/smtp
- **Senders**: https://app.brevo.com/settings/senders
- **Statistics**: https://app.brevo.com/statistics/email
- **Transaction Logs**: https://app.brevo.com/sms-campaign/transactional-email
- **API Documentation**: https://developers.brevo.com/
- **API Reference**: https://developers.brevo.com/reference

### Artisan Commands:
```bash
# Clear cache
php artisan config:clear
php artisan cache:clear

# Restart queue
php artisan queue:restart

# Monitor queue
php artisan queue:work --verbose

# Test email via tinker
php artisan tinker
```

### Testing Email via Tinker:
```php
php artisan tinker

Mail::raw('Test email dari Brevo API', function ($message) {
    $message->to('your-email@example.com')
            ->subject('Test Email Brevo API');
});
```

---

## ✅ Checklist Setup

Gunakan checklist ini untuk memastikan setup Brevo sudah lengkap:

- [ ] Akun Brevo sudah dibuat dan verified
- [ ] API Key sudah di-generate (bukan SMTP Key!)
- [ ] File `.env` sudah diupdate dengan `BREVO_API_KEY`
- [ ] Sender email sudah ditambahkan di Brevo
- [ ] Domain sudah diverifikasi (SPF, DKIM, DMARC) - opsional tapi recommended
- [ ] Cache Laravel sudah di-clear (`php artisan config:clear`)
- [ ] Composer dependencies sudah diinstall (`composer install`)
- [ ] Custom BrevoTransport sudah dibuat
- [ ] BrevoMailServiceProvider sudah registered
- [ ] Test email sudah berhasil terkirim
- [ ] Email tidak masuk spam
- [ ] Queue workers sudah running (`php artisan queue:work`)
- [ ] Monitoring dashboard sudah dicek

---

## 🆘 Support

Jika mengalami masalah:

1. **Cek Laravel Logs**: `storage/logs/laravel.log`
2. **Cek Queue Logs**: `php artisan queue:failed`
3. **Contact Brevo Support**: support@brevo.com
4. **Brevo Community**: https://community.brevo.com/

---

**Terakhir diupdate**: 8 Desember 2025
**Dibuat oleh**: GitHub Copilot
**Proyek**: Tire Xchange - Laravel Application
