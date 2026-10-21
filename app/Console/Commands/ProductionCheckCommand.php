<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ProductionCheckCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:production-check {--strict : Treat warnings as failures}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verify production environment readiness, security configuration, storage isolation, and asset compilation';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting Franchise Dekho Production Readiness Pre-flight Diagnostics...');
        $this->newLine();

        $checks = [];
        $hasFailure = false;
        $hasWarning = false;

        // 1. App Environment & Debug Mode
        $env = config('app.env');
        $debug = config('app.debug');

        if ($env === 'production') {
            if ($debug) {
                $checks[] = ['App Debug Mode', 'FAIL', 'APP_DEBUG is true in production! Must be false to prevent stack trace leakage.'];
                $hasFailure = true;
            } else {
                $checks[] = ['App Debug Mode', 'PASS', 'APP_DEBUG is false in production. Zero trace leakage.'];
            }
        } else {
            if ($debug) {
                $checks[] = ['App Debug Mode', 'WARN', "Environment is '{$env}' with APP_DEBUG=true. Must be set to false prior to live deployment."];
                $hasWarning = true;
            } else {
                $checks[] = ['App Debug Mode', 'PASS', "APP_DEBUG is false in '{$env}' environment."];
            }
        }

        // 2. Encryption Key
        $key = config('app.key');
        if (empty($key)) {
            $checks[] = ['Application Key', 'FAIL', 'APP_KEY is missing! Run php artisan key:generate.'];
            $hasFailure = true;
        } elseif (! str_starts_with($key, 'base64:')) {
            $checks[] = ['Application Key', 'WARN', 'APP_KEY exists but is not a base64 string.'];
            $hasWarning = true;
        } else {
            $checks[] = ['Application Key', 'PASS', 'APP_KEY is configured with secure base64 encryption key.'];
        }

        // 3. Database Connectivity
        try {
            DB::connection()->getPdo();
            $connectionName = config('database.default');
            $checks[] = ['Database Connectivity', 'PASS', "Connection [{$connectionName}] established successfully."];
        } catch (\Throwable $e) {
            $checks[] = ['Database Connectivity', 'FAIL', 'Database connection failed: ' . $e->getMessage()];
            $hasFailure = true;
        }

        // 4. Storage & Cache Permissions
        $writablePaths = [
            'storage/framework/views' => storage_path('framework/views'),
            'storage/framework/sessions' => storage_path('framework/sessions'),
            'storage/framework/cache' => storage_path('framework/cache'),
            'storage/logs' => storage_path('logs'),
            'bootstrap/cache' => base_path('bootstrap/cache'),
        ];

        $storageIssues = [];
        foreach ($writablePaths as $label => $path) {
            if (! File::exists($path)) {
                File::makeDirectory($path, 0755, true, true);
            }
            if (! is_writable($path)) {
                $storageIssues[] = "{$label} is not writable";
            }
        }

        if (empty($storageIssues)) {
            $checks[] = ['Directory Permissions', 'PASS', 'All runtime storage and bootstrap cache directories are writable.'];
        } else {
            $checks[] = ['Directory Permissions', 'FAIL', implode(', ', $storageIssues)];
            $hasFailure = true;
        }

        // 5. Private Disk Isolation
        $localRoot = config('filesystems.disks.local.root');
        $publicPath = public_path();

        if (str_starts_with($localRoot, $publicPath)) {
            $checks[] = ['Private Storage Isolation', 'FAIL', "Local disk root [{$localRoot}] resides inside public web root! Sensitive documents exposed."];
            $hasFailure = true;
        } elseif (str_contains($localRoot, 'storage' . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'private') || str_ends_with($localRoot, 'private')) {
            $checks[] = ['Private Storage Isolation', 'PASS', 'Local disk correctly isolated in storage/app/private (non-public).'];
        } else {
            $checks[] = ['Private Storage Isolation', 'WARN', "Local disk root is [{$localRoot}], ensure it is not directly reachable over HTTP."];
            $hasWarning = true;
        }

        // 6. Vite Manifest & Compiled Assets
        $manifestPath = public_path('build/manifest.json');
        if (File::exists($manifestPath)) {
            $checks[] = ['Vite Asset Compilation', 'PASS', 'Production asset manifest found at public/build/manifest.json.'];
        } else {
            $checks[] = ['Vite Asset Compilation', 'WARN', 'public/build/manifest.json not found. Run npm run build before deployment.'];
            $hasWarning = true;
        }

        // 7. Robots.txt Crawl Controls
        $robotsPath = public_path('robots.txt');
        if (File::exists($robotsPath)) {
            $robotsContent = File::get($robotsPath);
            if (str_contains($robotsContent, 'Disallow: /admin/') && str_contains($robotsContent, 'Disallow: /customer/')) {
                $checks[] = ['Robots.txt Crawl Rules', 'PASS', 'robots.txt exists and protects admin/customer endpoints.'];
            } else {
                $checks[] = ['Robots.txt Crawl Rules', 'WARN', 'robots.txt exists but lacks strict admin/customer disallow rules.'];
                $hasWarning = true;
            }
        } else {
            $checks[] = ['Robots.txt Crawl Rules', 'FAIL', 'public/robots.txt is missing.'];
            $hasFailure = true;
        }

        // 8. .htaccess Security Rules
        $htaccessPath = public_path('.htaccess');
        if (File::exists($htaccessPath)) {
            $htaccessContent = File::get($htaccessPath);
            if (str_contains($htaccessContent, 'mod_headers.c') && str_contains($htaccessContent, 'X-Content-Type-Options')) {
                $checks[] = ['Web Server Hardening (.htaccess)', 'PASS', 'Apache .htaccess has defense-in-depth headers and rewrite rules.'];
            } else {
                $checks[] = ['Web Server Hardening (.htaccess)', 'WARN', '.htaccess exists but lacks security headers module.'];
                $hasWarning = true;
            }
        } else {
            $checks[] = ['Web Server Hardening (.htaccess)', 'WARN', 'public/.htaccess missing (ensure Nginx or reverse proxy handles security headers).'];
            $hasWarning = true;
        }

        // Output results table
        $this->table(['Check Name', 'Status', 'Diagnostic Details'], $checks);

        $this->newLine();
        if ($hasFailure) {
            $this->error('Pre-flight check FAILED. Resolve the issues marked [FAIL] before launching.');
            return Command::FAILURE;
        }

        if ($hasWarning && $this->option('strict')) {
            $this->error('Pre-flight check failed due to warnings under --strict mode.');
            return Command::FAILURE;
        }

        if ($hasWarning) {
            $this->warn('Pre-flight check passed with non-critical warnings. Review warnings prior to production traffic.');
        } else {
            $this->info('All pre-flight production checks PASSED. Platform is ready for deployment.');
        }

        return Command::SUCCESS;
    }
}
