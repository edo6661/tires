# RAILWAY DEPLOYMENT GUIDE - QUEUE WORKER

## 🚨 MASALAH: Email Lag karena Queue Worker Tidak Jalan

Website lag karena email masuk ke Redis queue tapi **tidak ada worker yang memproses**.

---

## ✅ SOLUSI 1: Deploy Queue Worker sebagai Service Terpisah (RECOMMENDED)

### Step 1: Di Railway Dashboard

1. **New Service** → **GitHub Repo** → Pilih repo `tires`
2. Rename service jadi **"tires-queue-worker"**

### Step 2: Configure Queue Worker Service

**Settings** → **General**:
- ✅ **Start Command**: 
  ```bash
  php artisan queue:work redis --tries=3 --timeout=90 --sleep=3 --max-jobs=1000
  ```

**Settings** → **Environment**:
- Copy **semua environment variables** dari web service
- Atau **Reference** environment dari web service

### Step 3: Deploy
- Service akan auto-deploy
- Worker akan mulai memproses jobs di Redis

---

## ✅ SOLUSI 2: Install Laravel Horizon (BETTER)

### Step 1: Install Horizon
```bash
composer require laravel/horizon
php artisan horizon:install
php artisan migrate
```

### Step 2: Configure Horizon
File: `config/horizon.php`
```php
'environments' => [
    'production' => [
        'supervisor-1' => [
            'connection' => 'redis',
            'queue' => ['default'],
            'balance' => 'auto',
            'autoScalingStrategy' => 'time',
            'maxProcesses' => 10,
            'minProcesses' => 1,
            'balanceMaxShift' => 1,
            'balanceCooldown' => 3,
            'tries' => 3,
            'timeout' => 90,
        ],
    ],
],
```

### Step 3: Queue Worker Service Start Command
```bash
php artisan horizon
```

### Step 4: Access Horizon Dashboard
- URL: `https://tire.fts.biz.id/horizon`
- Monitor jobs, failures, throughput

---

## ✅ SOLUSI 3: Supervisor (Traditional)

Jika Railway support supervisor, buat config:

File: `supervisor-queue-worker.conf`
```ini
[program:laravel-queue-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /app/artisan queue:work redis --sleep=3 --tries=3 --timeout=90
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=3
redirect_stderr=true
stdout_logfile=/app/storage/logs/queue-worker.log
stopwaitsecs=3600
```

---

## 🔍 VERIFY Queue Worker Berjalan

### Check dari Code
```php
// Di tinker atau controller
use Illuminate\Support\Facades\Queue;

// Dispatch test job
Queue::push(function() {
    \Log::info('Queue test job executed!');
});

// Check pending jobs
$jobs = DB::table('jobs')->count();
echo "Pending jobs: $jobs";
```

### Check Redis
```bash
# Di Railway CLI atau terminal
redis-cli -h shortline.proxy.rlwy.net -p 43828 -a dEVvfZXxWxwtLvnNAyWyRVtNxCAQFRRa

# Lihat queue
LLEN queues:default
KEYS *queue*
```

### Check Logs
```bash
# Railway CLI
railway logs --service tires-queue-worker
```

---

## 📊 MONITORING

### Horizon Dashboard
- Jobs processed per minute
- Failed jobs
- Recent jobs
- Job throughput

### Laravel Telescope (Optional)
```bash
composer require laravel/telescope --dev
php artisan telescope:install
php artisan migrate
```

---

## 🐛 TROUBLESHOOTING

### Issue: Jobs tidak diproses
**Check:**
1. ✅ Queue worker service running?
2. ✅ Redis connection working?
3. ✅ Environment variables correct?

**Fix:**
```bash
# Restart queue worker
railway service restart tires-queue-worker

# Clear failed jobs
php artisan queue:flush

# Retry failed jobs
php artisan queue:retry all
```

### Issue: Jobs failed terus
**Check:**
```bash
# Lihat failed jobs
php artisan queue:failed

# Lihat detail
php artisan queue:failed-table
php artisan migrate
```

### Issue: Memory leak
**Add to queue worker command:**
```bash
php artisan queue:work redis --max-jobs=1000 --max-time=3600
```

---

## 📝 BEST PRACTICES

### 1. Always Use ShouldQueue
```php
class MyMail extends Mailable implements ShouldQueue
{
    use Queueable;
}
```

### 2. Set Timeouts
```php
class MyJob implements ShouldQueue
{
    public $timeout = 90;
    public $tries = 3;
    public $backoff = [10, 30, 60]; // Retry delays
}
```

### 3. Monitor Failed Jobs
```php
// EventServiceProvider.php
Queue::failing(function (JobFailed $event) {
    Log::error('Job failed', [
        'job' => $event->job->getName(),
        'exception' => $event->exception->getMessage(),
    ]);
});
```

### 4. Use Job Batching
```php
use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;

Bus::batch([
    new SendEmail($user1),
    new SendEmail($user2),
])->dispatch();
```

---

## 🚀 DEPLOYMENT CHECKLIST

- [ ] Queue worker service created di Railway
- [ ] Start command configured
- [ ] Environment variables copied
- [ ] Service deployed & running
- [ ] Test email sending
- [ ] Check jobs processed di Horizon/logs
- [ ] Monitor failed jobs
- [ ] Set up alerts for failures

---

## 📌 CURRENT SETUP

**Production (Railway):**
- ✅ `QUEUE_CONNECTION=redis`
- ✅ Redis host: `shortline.proxy.rlwy.net:43828`
- ✅ All Mailable classes implement `ShouldQueue`
- ❌ Queue worker NOT running → **NEED TO FIX THIS**

**Local (Laragon):**
- ✅ `QUEUE_CONNECTION=database`
- ❌ Queue worker not running
- 💡 Run: `php artisan queue:work --tries=3`

---

## 🎯 RECOMMENDED SOLUTION

**Deploy 2 Railway Services:**

1. **tires-web** (existing)
   - Start: `php artisan octane:start --server=frankenphp`
   - Handle HTTP requests

2. **tires-queue-worker** (new)
   - Start: `php artisan horizon` or `php artisan queue:work redis`
   - Process background jobs (emails, etc)

**Result:**
- ⚡ Instant response (no blocking)
- 📧 Emails sent in background
- 📊 Easy monitoring via Horizon
- 🔄 Auto-restart on failure

---

Generated: December 4, 2025
