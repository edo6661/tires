# 📧 Tutorial Lengkap Migrasi dari Resend ke Brevo

## 🎯 Apa yang Sudah Dilakukan

Proyek ini telah dimigrasi dari **Resend** ke **Brevo** untuk layanan email transaksional. Berikut perubahan yang telah diterapkan:

### ✅ Perubahan File

1. **`.env`** - Konfigurasi email diubah dari Resend API ke Brevo SMTP
2. **`config/mail.php`** - Default mailer diubah dari `resend` ke `smtp`
3. **`composer.json`** - Package `resend/resend-php` telah dihapus

### 📝 Catatan Penting

- ✅ Semua **email templates** tetap kompatibel (tidak perlu diubah)
- ✅ Semua **Listeners** tetap kompatibel (tidak perlu diubah)
- ✅ Semua **Mailable classes** tetap kompatibel (tidak perlu diubah)

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

### 2️⃣ Mendapatkan SMTP Credentials

Setelah login ke dashboard Brevo:

1. **Klik nama profil Anda** di pojok kanan atas
2. **Pilih "SMTP & API"** dari menu dropdown
   - URL langsung: https://app.brevo.com/settings/keys/smtp

3. **Di tab "SMTP"**, Anda akan melihat:
   ```
   SMTP Server: smtp-relay.brevo.com
   Port: 587 (TLS) atau 465 (SSL)
   Login: email-login-anda@example.com
   SMTP Key: (klik "Create a new SMTP key" jika belum ada)
   ```

4. **Generate SMTP Key baru**:
   - Klik tombol **"Create a new SMTP key"**
   - Beri nama key (contoh: "Laravel Production" atau "Tire Xchange App")
   - **Copy dan simpan key ini dengan aman** (hanya ditampilkan sekali!)

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
# Konfigurasi Email - Brevo SMTP
MAIL_MAILER=smtp
MAIL_HOST=smtp-relay.brevo.com
MAIL_PORT=587
MAIL_USERNAME=your-brevo-login@example.com    # Login yang ada di dashboard SMTP
MAIL_PASSWORD=your-brevo-smtp-key-here        # SMTP Key yang baru di-generate
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=no-reply@tire.fts.biz.id
MAIL_FROM_NAME="${APP_NAME}"
```

**Penjelasan parameter:**
- `MAIL_MAILER=smtp` - Menggunakan SMTP protocol
- `MAIL_HOST=smtp-relay.brevo.com` - Server SMTP Brevo (fixed, jangan diubah)
- `MAIL_PORT=587` - Port untuk TLS (alternatif: 465 untuk SSL, atau 2525)
- `MAIL_USERNAME` - Email login SMTP dari dashboard Brevo
- `MAIL_PASSWORD` - SMTP Key (bukan password akun Brevo!)
- `MAIL_ENCRYPTION=tls` - Gunakan TLS encryption (atau `ssl` jika pakai port 465)
- `MAIL_FROM_ADDRESS` - Alamat email pengirim (harus verified di Brevo)
- `MAIL_FROM_NAME` - Nama pengirim yang muncul di inbox penerima

---

### 5️⃣ Clear Cache Laravel

Setelah mengubah `.env`, wajib clear cache:

```bash
php artisan config:clear
php artisan cache:clear
php artisan queue:restart  # Jika pakai queue
```

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

### Problem: Email tidak terkirim / timeout

**Solusi:**
1. Cek SMTP credentials di `.env` sudah benar
2. Pastikan port 587 tidak diblock oleh firewall/hosting
3. Coba gunakan port alternatif: 2525 atau 465
4. Pastikan `MAIL_ENCRYPTION` sesuai port:
   - Port 587 → `MAIL_ENCRYPTION=tls`
   - Port 465 → `MAIL_ENCRYPTION=ssl`
   - Port 2525 → `MAIL_ENCRYPTION=tls`

### Problem: Email masuk spam

**Solusi:**
1. **Verifikasi domain** di Brevo (wajib!)
2. Setup **SPF, DKIM, DMARC** records
3. Gunakan email sender yang sudah verified
4. Hindari kata-kata spammy di subject/content
5. Test email score: https://www.mail-tester.com/

### Problem: Authentication failed

**Solusi:**
1. Pastikan menggunakan **SMTP Key**, bukan password akun
2. Generate SMTP Key baru jika lupa
3. Cek username = email login di dashboard SMTP
4. Clear Laravel config: `php artisan config:clear`

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
3. Implementasi queue untuk email: `implements ShouldQueue`
4. Add delay antara email: `->later(now()->addSeconds(5))`

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

### Port Options:
| Port | Encryption | Use Case         |
| ---- | ---------- | ---------------- |
| 587  | TLS        | ✅ Recommended    |
| 465  | SSL        | Alternative      |
| 2525 | TLS        | Jika 587 blocked |

### Important URLs:
- **Dashboard**: https://app.brevo.com/
- **SMTP Settings**: https://app.brevo.com/settings/keys/smtp
- **Senders**: https://app.brevo.com/settings/senders
- **Statistics**: https://app.brevo.com/statistics/email
- **Transaction Logs**: https://app.brevo.com/sms-campaign/transactional-email

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

---

## ✅ Checklist Setup

Gunakan checklist ini untuk memastikan setup Brevo sudah lengkap:

- [ ] Akun Brevo sudah dibuat dan verified
- [ ] SMTP Key sudah di-generate
- [ ] Login SMTP sudah dicopy
- [ ] File `.env` sudah diupdate dengan credentials Brevo
- [ ] Sender email sudah ditambahkan di Brevo
- [ ] Domain sudah diverifikasi (SPF, DKIM, DMARC)
- [ ] Cache Laravel sudah di-clear
- [ ] Test email sudah berhasil terkirim
- [ ] Email tidak masuk spam
- [ ] Queue workers sudah running
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
