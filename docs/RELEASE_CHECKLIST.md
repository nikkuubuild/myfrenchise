# Franchise Dekho — Production Release Checklist & Deployment Runbook

**Document Version:** 1.0.0  
**Target Architecture:** Hostinger Shared Hosting / CloudLinux (PHP 8.2+, MariaDB, Apache)  
**Security Baseline:** DPDPA 2023 & Enterprise Isolation  
**Related Documents:** `docs/ARCHITECTURE.md`, `docs/OPERATIONAL_RECOVERY_RUNBOOK.md`, `docs/SECURITY_PRIVACY.md`

---

## 1. Release Objective

This checklist provides the authoritative, step-by-step procedure for deploying Franchise Dekho to production. It guarantees zero leakage of source code or private customer dossiers, verifies production hardening, and ensures reproducible rollbacks in the event of anomalies.

---

## 2. Directory Architecture Reference

Franchise Dekho strictly utilizes the **sibling directory architecture** on Hostinger shared hosting to ensure the core application files and private storage sit above the public document root:

```text
/home/u123456789/
├── myfranchise/                         <-- Private Core Application (NOT accessible via HTTP)
│   ├── .env                             <-- Production environment file (chmod 600)
│   ├── app/
│   ├── bootstrap/
│   │   └── cache/                       <-- Writable (chmod 755)
│   ├── config/
│   ├── database/
│   ├── storage/
│   │   ├── app/private/                 <-- Private customer docs & audit proofs (isolated)
│   │   ├── framework/                   <-- Writable (sessions, views, cache)
│   │   └── logs/                        <-- Daily audit logs (chmod 755)
│   └── vendor/
└── public_html/                         <-- Public Document Root (Web accessible ONLY)
    ├── index.php                        <-- Modified to require ../myfranchise/bootstrap/app.php
    ├── .htaccess                        <-- Security headers, HTTPS redirects, and caching
    ├── build/                           <-- Compiled production CSS/JS assets (Vite)
    ├── robots.txt                       <-- Search engine & AI crawler access rules
    └── favicon.ico
```

---

## 3. Pre-Flight Verification (Local / CI)

Prior to initiating deployment to the production server, complete and verify every item below:

| # | Check Item | Command / Procedure | Expected Result | Status |
|---|------------|---------------------|-----------------|--------|
| 3.1 | Automated Test Suite | `php artisan test` | 100% tests passing (all feature & unit tests) | [ ] |
| 3.2 | Asset Compilation | `npm run build` | Clean Vite build; `public/build/manifest.json` generated | [ ] |
| 3.3 | Diagnostics Command | `php artisan app:production-check` | All checks PASS | [ ] |
| 3.4 | Git Working Tree | `git status` | Clean working tree on `main` branch | [ ] |
| 3.5 | Git Release Tag | `git tag -a v1.0.0 -m "Release v1.0.0"` | Tagged commit pushed to remote repository | [ ] |
| 3.6 | Secrets Audit | Manual inspection | No credentials or API keys committed in git | [ ] |

---

## 4. Production Environment Setup (Hostinger hPanel / SSH)

Before transferring files:

1. **PHP Version:** Set PHP version to **8.2 or 8.3** in Hostinger hPanel under **Advanced > PHP Configuration**.
   - Required extensions: `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`, `curl`, `fileinfo`.
2. **Database Provisioning:**
   - In hPanel under **Databases > Management**, create database: `u123456789_franchisedekho`.
   - Create database user with a strong, randomly generated 32-character password.
   - Record database name, username, and password in secure credential manager.
3. **SSL Certificate:**
   - Ensure Let's Encrypt SSL certificate is active for the primary domain and `www` subdomain under **Security > SSL**.
4. **SSH Access:**
   - Enable SSH access in hPanel under **Advanced > SSH Access**.

---

## 5. Deployment Execution Runbook

Follow these ten steps sequentially:

### Step 1: Enable Maintenance Mode (If upgrading existing system)
```bash
cd /home/u123456789/myfranchise
php artisan down --secret="fd-ops-deploy-bypass-token" --render="errors::503"
```

### Step 2: Deploy Application Code to Core Directory
Clone or extract the repository code into `/home/u123456789/myfranchise`:
```bash
# Via Git on server:
cd /home/u123456789
git clone https://github.com/your-org/myfranchise.git myfranchise
cd myfranchise
git checkout tags/v1.0.0
```

### Step 3: Configure Production `.env`
Copy `.env.example` to `.env` and configure production parameters:
```bash
cp .env.example .env
chmod 600 .env
```
Ensure the following production settings are configured:
```env
APP_NAME="Franchise Dekho"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=u123456789_franchisedekho
DB_USERNAME=u123456789_dbuser
DB_PASSWORD="your_strong_password"

SESSION_DRIVER=database
SESSION_SECURE_COOKIE=true
SESSION_ENCRYPT=true

FILESYSTEM_DISK=local

RAZORPAY_KEY=rzp_live_xxxxxxxxxxxxxxxx
RAZORPAY_SECRET=xxxxxxxxxxxxxxxxxxxxxxxx
RAZORPAY_WEBHOOK_SECRET=xxxxxxxxxxxxxxxxxxxxxxxx

AI_DISCOVERY_DRIVER=grounded_engine
```
Generate the application key if not previously configured:
```bash
php artisan key:generate --force
```

### Step 4: Install Optimized Dependencies
```bash
composer install --no-dev --optimize-autoloader --no-interaction
```

### Step 5: Run Database Migrations & Automated Baseline Data Sync
```bash
php artisan app:update-db --force
# Or standard migration (migration 2026_10_21_210000 automatically seeds baseline data if fresh):
# php artisan migrate --force
```
Baseline reference categories, verified sample opportunities across sectors (F&B, Logistics, Healthcare), service packages, knowledge hub articles, and staff accounts are automatically populated and synced idempotently.
```

### Step 6: Deploy Public Assets to `public_html`
Move the compiled public assets into `/home/u123456789/public_html`:
```bash
# Copy compiled build directory, robots.txt, and .htaccess
cp -r /home/u123456789/myfranchise/public/build /home/u123456789/public_html/
cp /home/u123456789/myfranchise/public/robots.txt /home/u123456789/public_html/
cp /home/u123456789/myfranchise/public/.htaccess /home/u123456789/public_html/
cp /home/u123456789/myfranchise/public/favicon.ico /home/u123456789/public_html/
```

Configure `/home/u123456789/public_html/index.php` to reference sibling paths:
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

### Step 7: Set Directory Permissions
```bash
chmod -R 755 /home/u123456789/myfranchise/storage
chmod -R 755 /home/u123456789/myfranchise/bootstrap/cache
chmod 600 /home/u123456789/myfranchise/.env
```

### Step 8: Production Optimization & Caching
```bash
cd /home/u123456789/myfranchise
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Step 9: Run Production Diagnostics Check
```bash
php artisan app:production-check --strict
```
Ensure all 8 checks report `PASS`.

### Step 10: Bring Application Live
```bash
php artisan up
```

---

## 6. Post-Deployment Smoke Test Protocol

Perform immediate smoke tests on the live production URL:

| Endpoint | Test Action | Expected Result | Verified |
|----------|-------------|-----------------|----------|
| `/health` | `curl -i https://yourdomain.com/health` | HTTP 200, JSON: `{"status":"healthy","database":"connected"}` | [ ] |
| `/` | Browser visit | Homepage loads with editorial serif typography, no layout breakage | [ ] |
| `/opportunities` | Browser visit & filters | Catalog renders verified opportunities with facet counts | [ ] |
| `/sitemap.xml` | Browser visit | Valid XML response with opportunity and sector URLs | [ ] |
| `/robots.txt` | Browser visit | Rules protect `/admin/`, `/customer/`, and allow discovery pages | [ ] |
| `/non-existent-path` | Browser visit | Custom branded 404 page rendered, no debug stack trace | [ ] |
| `/admin` | Browser visit as guest | Redirects cleanly to `/login` or 403 | [ ] |
| `/assistant` | Query submission | AI Assistant returns grounded matching recommendations | [ ] |
| SSL / HTTPS | HTTP request to `http://...` | 301 Redirect to `https://...` | [ ] |

---

## 7. Fast Rollback Procedure

If critical failures are identified during post-deployment verification:

1. **Immediate Maintenance Mode:**
   ```bash
   cd /home/u123456789/myfranchise
   php artisan down --render="errors::503"
   ```

2. **Rollback Codebase:**
   ```bash
   # Switch to previous stable git release tag:
   git checkout tags/v_previous_stable
   composer install --no-dev --optimize-autoloader
   ```

3. **Rollback Database (If schema migrations caused failure):**
   ```bash
   # Option A: Migrate rollback
   php artisan migrate:rollback --step=1 --force

   # Option B: Restore database backup from pre-deployment snapshot
   mysql -u u123456789_dbuser -p u123456789_franchisedekho < /home/u123456789/backups/pre_deploy_backup.sql
   ```

4. **Re-compile and Re-cache:**
   ```bash
   php artisan optimize:clear
   php artisan optimize
   ```

5. **Bring Application Online:**
   ```bash
   php artisan up
   ```

6. **Log Incident:**
   Record incident details, error traces, and recovery actions in platform operations log.
