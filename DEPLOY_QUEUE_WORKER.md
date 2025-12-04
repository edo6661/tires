# 🚀 STEP-BY-STEP: Deploy Queue Worker di Railway

## 📋 Prerequisites
- ✅ Railway account sudah ada
- ✅ Web service `tires` sudah running
- ✅ Redis service sudah connected
- ✅ Environment variables sudah ready

---

## 🎯 STEP 1: Buat Service Baru

### Di Railway Dashboard:

1. **Buka Project** `tires`
2. Click **"+ New"** button
3. Pilih **"Empty Service"**
4. Rename service jadi: **`tires-queue-worker`**

---

## 🔗 STEP 2: Connect ke GitHub Repository

1. Click service **`tires-queue-worker`**
2. Go to **Settings** → **Source**
3. Click **"Connect Repo"**
4. Pilih repo yang sama dengan web service: **`edo6661/tires`**
5. Branch: **`main`** (atau branch yang aktif)

---

## ⚙️ STEP 3: Configure Environment Variables

### Option A: Copy Paste Manual (Recommended)

1. Go to **Settings** → **Variables**
2. Click **"+ New Variable"**
3. **Copy semua isi file `.env.queue-worker`** yang sudah dibuat
4. Paste satu-satu atau gunakan "Raw Editor":
   - Click **"Raw Editor"**
   - Paste semua content dari `.env.queue-worker`
   - Click **"Update Variables"**

### Option B: Reference dari Web Service (Faster)

1. Go to **Settings** → **Variables**
2. Click **"Reference"** tab
3. Pilih service **`tires`** (web service)
4. Select **ALL variables**
5. Click **"Add Reference"**

> ⚠️ **IMPORTANT**: Pastikan variable `QUEUE_CONNECTION=redis` dan semua `REDIS_*` variables ada!

---

## 🚀 STEP 4: Set Start Command

1. Go to **Settings** → **Deploy**
2. Scroll ke **"Start Command"**
3. Click **"Custom Start Command"**
4. Input command berikut:

```bash
php artisan queue:work redis --tries=3 --timeout=90 --sleep=3 --max-jobs=1000
```

### Penjelasan Parameters:
- `redis` - Connection yang digunakan
- `--tries=3` - Retry 3x jika gagal
- `--timeout=90` - Timeout per job 90 detik
- `--sleep=3` - Sleep 3 detik jika queue kosong
- `--max-jobs=1000` - Restart worker setelah 1000 jobs (prevent memory leak)

---

## 🎨 STEP 5: Deploy Service

1. Click **"Deploy"** button atau tunggu auto-deploy
2. Service akan mulai build dan deploy
3. Lihat **Logs** untuk verify:

**Expected Output:**
```
[2025-12-04 12:00:00] Processing: App\Mail\BookingConfirmationMail
[2025-12-04 12:00:01] Processed: App\Mail\BookingConfirmationMail
[2025-12-04 12:00:02] Processing: App\Mail\AdminBookingNotificationMail
[2025-12-04 12:00:03] Processed: App\Mail\AdminBookingNotificationMail
```

---

## ✅ STEP 6: Verify Queue Worker Running

### Check via Railway Logs:
```bash
# Railway CLI
railway logs -s tires-queue-worker

# Atau via Dashboard
# Go to service → Deployments → Latest → View Logs
```

### Check via Redis CLI (Optional):
```bash
# Connect to Redis
redis-cli -h shortline.proxy.rlwy.net -p 43828 -a dEVvfZXxWxwtLvnNAyWyRVtNxCAQFRRa

# Check queue length (should be 0 or decreasing)
LLEN queues:default

# List all queues
KEYS queues:*
```

### Test Email Sending:
1. Buat booking baru di website
2. Check logs queue worker
3. Email harus terkirim dalam beberapa detik
4. Website harus **instant response** tanpa lag!

---

## 🔧 STEP 7: Optional - Install Horizon (Advanced Monitoring)

### Jika ingin monitoring lebih baik:

1. **Di local/development:**
```bash
composer require laravel/horizon
php artisan horizon:install
php artisan migrate
```

2. **Commit & Push ke GitHub**

3. **Update Queue Worker Start Command:**
```bash
php artisan horizon
```

4. **Akses Horizon Dashboard:**
```
https://tire.fts.biz.id/horizon
```

### Protect Horizon di Production:

File: `app/Providers/HorizonServiceProvider.php`
```php
protected function gate()
{
    Gate::define('viewHorizon', function ($user) {
        return in_array($user->email, [
            'admin@tire.fts.biz.id',
            'sifactory16@gmail.com'
        ]);
    });
}
```

---

## 🐛 TROUBLESHOOTING

### Issue 1: Worker tidak jalan

**Symptoms:**
- Logs kosong
- Email tidak terkirim
- Jobs bertambah terus di Redis

**Fix:**
```bash
# Check start command
railway service -s tires-queue-worker

# Restart service
railway service restart tires-queue-worker

# Check environment variables
railway variables -s tires-queue-worker
```

---

### Issue 2: Connection refused ke Redis

**Symptoms:**
```
Connection refused [tcp://shortline.proxy.rlwy.net:43828]
```

**Fix:**
1. Verify `REDIS_*` variables sama dengan web service
2. Check Redis service masih running
3. Restart queue worker service

---

### Issue 3: Jobs failed terus

**Symptoms:**
```
[2025-12-04] Failed: App\Mail\BookingConfirmationMail
```

**Fix:**
```bash
# Check failed jobs table
php artisan queue:failed

# Retry all failed jobs
php artisan queue:retry all

# Clear failed jobs
php artisan queue:flush
```

---

### Issue 4: Memory leak / Worker crash

**Symptoms:**
- Worker restart terus
- Out of memory error

**Fix:**
Update start command dengan memory limit:
```bash
php artisan queue:work redis --tries=3 --timeout=90 --sleep=3 --max-jobs=500 --max-time=3600
```

Parameters tambahan:
- `--max-jobs=500` - Restart setelah 500 jobs
- `--max-time=3600` - Restart setelah 1 jam

---

## 📊 MONITORING & MAINTENANCE

### Daily Checks:
```bash
# Check worker health
railway logs -s tires-queue-worker --tail 100

# Check failed jobs
php artisan queue:failed

# Monitor queue length
redis-cli -h ... LLEN queues:default
```

### Weekly Maintenance:
```bash
# Clear old failed jobs (>7 days)
php artisan queue:prune-failed --hours=168

# Check worker uptime
railway service -s tires-queue-worker
```

---

## 🎯 SUCCESS CHECKLIST

Setelah deploy, verify semua ini:

- [ ] Queue worker service created & deployed
- [ ] Start command configured correctly
- [ ] Environment variables semua ada (especially REDIS_*)
- [ ] Worker running tanpa error di logs
- [ ] Test booking → email terkirim instant
- [ ] Website response time < 500ms (tanpa lag!)
- [ ] Redis queue length = 0 atau decreasing
- [ ] No failed jobs (atau minimal)
- [ ] Horizon dashboard accessible (jika pakai)

---

## 💡 TIPS

### 1. Use Supervisor Pattern
Jika worker sering crash, tambahkan restart policy di Railway settings

### 2. Multiple Queues
Untuk prioritas berbeda:
```bash
# High priority queue
php artisan queue:work redis --queue=high,default

# Create job dengan queue spesifik
Mail::to($user)->queue(new WelcomeEmail)->onQueue('high');
```

### 3. Job Batching
Untuk mass email:
```php
use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;

Bus::batch([
    new SendEmail($user1),
    new SendEmail($user2),
])->dispatch();
```

---

## 📞 SUPPORT

Jika masih ada masalah:

1. **Check Railway Logs**: `railway logs -s tires-queue-worker`
2. **Check Laravel Logs**: Di storage/logs/laravel.log
3. **Check Redis**: Pastikan connected
4. **Restart Both Services**: Web + Queue Worker

---

**Last Updated:** December 4, 2025  
**Service:** tires-queue-worker  
**Repository:** edo6661/tires
