# Operational and Recovery Runbook

**Environment:** Hostinger Shared Hosting / CloudLinux  
**Application:** Franchise Dekho (Laravel 11)  
**Classification:** Operational Security Baseline

---

## 1. Directory Structure on Hostinger

To prevent any possibility of exposing `.env`, SQLite databases, application logs, or source code over HTTP, deploy using the sibling directory architecture:

```
/home/u123456789/
├── myfranchise/                   <-- Application Root (Private, NOT web accessible)
│   ├── .env
│   ├── app/
│   ├── bootstrap/
│   ├── config/
│   ├── database/
│   ├── storage/
│   │   ├── app/private/           <-- Private customer docs & verification proof
│   │   └── logs/
│   └── vendor/
└── public_html/                   <-- Public Document Root (Web accessible ONLY)
    ├── index.php                  <-- Loads bootstrap/app.php from ../myfranchise/
    ├── .htaccess                  <-- Rewrite & file protection rules
    ├── build/                     <-- Compiled CSS / JS assets
    └── favicon.ico
```

### In `public_html/index.php`:
```php
<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../myfranchise/storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../myfranchise/vendor/autoload.php';

// Bootstrap Laravel and handle the request...
(require_once __DIR__.'/../myfranchise/bootstrap/app.php')
    ->handleRequest(Request::capture());
```

---

## 2. Backup Strategy

### 2.1 Database Backups (MySQL / MariaDB)
- **Automated Schedule:** Hostinger provides daily backups in hPanel under **Files > Backups**.
- **Manual CLI Export:**
  ```bash
  mysqldump -u [db_user] -p[db_password] [db_name] | gzip > /home/u123456789/backups/db_$(date +%Y%m%d_%H%M%S).sql.gz
  ```
- **Retention Policy:** Retain daily dumps for 30 days; retain monthly snapshots for 12 months in an encrypted external bucket.

### 2.2 Storage & Uploads Backup
- Customer verification documents and audit trails are stored in `storage/app/private/`.
- Regular archive command:
  ```bash
  tar -czf /home/u123456789/backups/storage_$(date +%Y%m%d).tar.gz /home/u123456789/myfranchise/storage/app/private/
  ```

---

## 3. Disaster Recovery & Rollback Procedure

### 3.1 Database Restore
1. Put application into maintenance mode:
   ```bash
   php artisan down --secret="emergency-bypass-key"
   ```
2. Uncompress and restore database:
   ```bash
   gunzip < /home/u123456789/backups/db_YYYYMMDD_HHMMSS.sql.gz | mysql -u [db_user] -p[db_password] [db_name]
   ```
3. Run pending migrations if rolling back to a known migration point:
   ```bash
   php artisan migrate:status
   ```
4. Bring application back online:
   ```bash
   php artisan up
   ```

### 3.2 Codebase Rollback
1. If deploying via Git:
   ```bash
   git log -n 5 --oneline
   git checkout [previous_stable_commit_hash]
   composer install --no-dev --optimize-autoloader
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

---

## 4. Emergency Incident Procedures

### 4.1 Fraudulent or Disputed Opportunity Emergency Unpublish
1. An admin or verification officer immediately unpublishes the opportunity:
   - Sets `status = 'suspended'` or `status = 'unlisted'`.
   - Clears application caches: `php artisan cache:clear`.
2. The opportunity immediately disappears from search and category listings.
3. An audit log entry is recorded with the moderator's user ID, timestamp, and incident description.

### 4.2 Credential / Secret Compromise
1. **APP_KEY rotation:**
   ```bash
   php artisan key:generate
   ```
   *Note: This will invalidate all active browser sessions, requiring all users to log in again.*
2. **Database password change:**
   - Update password in Hostinger hPanel > Databases.
   - Update `DB_PASSWORD` in `/home/u123456789/myfranchise/.env`.
   - Run `php artisan config:clear`.
3. **API tokens / Gateway secrets:**
   - Rotate webhooks/keys in gateway dashboard (Razorpay, SMS gateway).
   - Update `.env` and restart queue/cache.

---

## 5. Cron & Scheduled Maintenance on Hostinger

Configure in Hostinger hPanel > **Advanced > Cron Jobs**:
- **Frequency:** Every minute (`* * * * *`)
- **Command:**
  ```bash
  /usr/bin/php /home/u123456789/myfranchise/artisan schedule:run >> /dev/null 2>&1
  ```
