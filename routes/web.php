<?php

use App\Http\Controllers\EnquiryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OpportunityController;
use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Franchise Dekho Application
|--------------------------------------------------------------------------
*/

// Homepage
Route::get('/', [HomeController::class, 'index'])->name('home');

// Health Check / System Status
Route::get('/health', function () {
    try {
        DB::connection()->getPdo();
        $dbStatus = 'connected';
        $status = 'healthy';
        $code = 200;
    } catch (\Throwable $e) {
        $dbStatus = 'disconnected';
        $status = 'unhealthy';
        $code = 503;
    }

    return response()->json([
        'status' => $status,
        'app' => config('app.name'),
        'environment' => config('app.env'),
        'database' => $dbStatus,
        'timestamp' => now()->toIso8601String(),
    ], $code);
})->name('health');

// Opportunity Discovery Catalog & Profiles
Route::get('/opportunities', [OpportunityController::class, 'index'])->name('opportunities.index');
Route::get('/opportunities/{slug}', [OpportunityController::class, 'show'])->name('opportunities.show');

// Sector-Specific Landing Pages & Due Diligence Guides
Route::get('/sectors/{slug}', [\App\Http\Controllers\SectorController::class, 'show'])->name('sectors.show');

// Editorial Due Diligence & Legal Knowledge Hub
Route::get('/guides', [\App\Http\Controllers\ArticleController::class, 'index'])->name('guides.index');
Route::get('/guides/{slug}', [\App\Http\Controllers\ArticleController::class, 'show'])->name('guides.show');

// Technical SEO: Dynamic XML Sitemap & Crawl Controls
Route::get('/sitemap.xml', [\App\Http\Controllers\SitemapController::class, 'index'])->name('sitemap');
Route::get('/robots.txt', function () {
    return response(file_get_contents(public_path('robots.txt')), 200)
        ->header('Content-Type', 'text/plain; charset=UTF-8');
})->name('robots');

// Needs Assessment & Consented Enquiry (with rate limiting)
Route::get('/enquiry', [EnquiryController::class, 'create'])->name('enquiry.create');
Route::post('/enquiry', [EnquiryController::class, 'store'])->middleware('throttle:enquiry')->name('enquiry.store');
Route::get('/enquiry/confirmed', [EnquiryController::class, 'confirmed'])->name('enquiry.confirmed');

// Interactive Opportunity Matching Engine
Route::get('/assessment', [\App\Http\Controllers\AssessmentController::class, 'index'])->name('assessment.index');
Route::post('/assessment/match', [\App\Http\Controllers\AssessmentController::class, 'match'])->name('assessment.match');

// Grounded AI Discovery Assistant (P2 - T15)
Route::get('/assistant', [\App\Http\Controllers\AiDiscoveryController::class, 'index'])->name('assistant.index');
Route::post('/assistant/query', [\App\Http\Controllers\AiDiscoveryController::class, 'query'])->middleware('throttle:ai_discovery')->name('assistant.query');

use App\Http\Controllers\GrievanceController;

// Informational, Governance & Policy Pages
Route::get('/how-it-works', [PageController::class, 'howItWorks'])->name('pages.how-it-works');
Route::get('/verification-policy', [PageController::class, 'verificationPolicy'])->name('pages.verification-policy');
Route::get('/grievance-and-fraud', [GrievanceController::class, 'index'])->name('pages.grievance');
Route::post('/grievance-and-fraud', [GrievanceController::class, 'store'])->middleware('throttle:enquiry')->name('grievance.store');
Route::get('/grievance-and-fraud/confirmed', [GrievanceController::class, 'confirmed'])->name('grievance.confirmed');
Route::get('/grievance-and-fraud/track', [GrievanceController::class, 'track'])->name('grievance.track');
Route::get('/privacy-policy', [PageController::class, 'privacy'])->name('pages.privacy');
Route::get('/terms-of-service', [PageController::class, 'terms'])->name('pages.terms');
Route::get('/cookie-policy', [PageController::class, 'cookiePolicy'])->name('pages.cookie-policy');

// Authentication & Account Recovery Routes
Route::middleware('guest')->group(function () {
    Route::get('/register', [\App\Http\Controllers\Auth\RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [\App\Http\Controllers\Auth\RegisteredUserController::class, 'store'])->middleware('throttle:auth');

    Route::get('/login', [\App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [\App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'store'])->middleware('throttle:auth');

    Route::get('/forgot-password', [\App\Http\Controllers\Auth\PasswordResetController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [\App\Http\Controllers\Auth\PasswordResetController::class, 'store'])->middleware('throttle:auth')->name('password.email');
    Route::get('/reset-password/{token}', [\App\Http\Controllers\Auth\PasswordResetController::class, 'edit'])->name('password.reset');
    Route::post('/reset-password', [\App\Http\Controllers\Auth\PasswordResetController::class, 'update'])->middleware('throttle:auth')->name('password.store');
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [\App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'destroy'])->name('logout');

    // Customer Portal Dashboard
    Route::get('/dashboard', [\App\Http\Controllers\CustomerDashboardController::class, 'index'])->name('dashboard');
    Route::get('/customer/dashboard', [\App\Http\Controllers\CustomerDashboardController::class, 'index'])->name('customer.dashboard');

    // Customer Case Document Vault & Sharing Consents
    Route::prefix('customer')->name('customer.cases.')->group(function () {
        Route::get('/cases/{enquiry}/documents', [\App\Http\Controllers\Customer\CaseDocumentController::class, 'show'])->name('documents.show');
        Route::post('/cases/{enquiry}/documents', [\App\Http\Controllers\Customer\CaseDocumentController::class, 'store'])->name('documents.store');
        Route::get('/documents/{document}/download', [\App\Http\Controllers\Customer\CaseDocumentController::class, 'download'])->name('documents.download');
        Route::post('/consents/{consent}/grant', [\App\Http\Controllers\Customer\CaseDocumentController::class, 'grantConsent'])->name('consents.grant');
        Route::post('/consents/{consent}/revoke', [\App\Http\Controllers\Customer\CaseDocumentController::class, 'revokeConsent'])->name('consents.revoke');
    });

    // Staff Case Management Pipeline & Workspace (Case Manager & Admin only)
    Route::middleware('role:case_manager,admin')->prefix('admin/cases')->name('admin.cases.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\CaseController::class, 'index'])->name('index');
        Route::get('/{enquiry}', [\App\Http\Controllers\Admin\CaseController::class, 'show'])->name('show');
        Route::post('/{enquiry}/assign', [\App\Http\Controllers\Admin\CaseController::class, 'assign'])->name('assign');
        Route::post('/{enquiry}/status', [\App\Http\Controllers\Admin\CaseController::class, 'updateStage'])->name('status');
        Route::post('/{enquiry}/communications', [\App\Http\Controllers\Admin\CaseController::class, 'storeCommunication'])->name('communications.store');
        Route::post('/{enquiry}/tasks', [\App\Http\Controllers\Admin\CaseController::class, 'storeTask'])->name('tasks.store');
        Route::patch('/tasks/{task}/toggle', [\App\Http\Controllers\Admin\CaseController::class, 'toggleTask'])->name('tasks.toggle');
        Route::post('/{enquiry}/followup', [\App\Http\Controllers\Admin\CaseController::class, 'updateFollowUp'])->name('followup');

        // Staff Document & Consent Actions
        Route::post('/{enquiry}/documents', [\App\Http\Controllers\Admin\CaseDocumentController::class, 'store'])->name('documents.store');
        Route::get('/documents/{document}/download', [\App\Http\Controllers\Admin\CaseDocumentController::class, 'download'])->name('documents.download');
        Route::post('/documents/{document}/verify', [\App\Http\Controllers\Admin\CaseDocumentController::class, 'verify'])->name('documents.verify');
        Route::post('/{enquiry}/consents/request', [\App\Http\Controllers\Admin\CaseDocumentController::class, 'requestConsent'])->name('consents.request');
    });

    // Customer Data Subject Rights & Privacy (DPDPA 2023)
    Route::get('/customer/data-export', [\App\Http\Controllers\Customer\DataSubjectController::class, 'export'])->name('customer.data-export');
    Route::get('/customer/data-deletion', [\App\Http\Controllers\Customer\DataSubjectController::class, 'showDeletion'])->name('customer.data-deletion.show');
    Route::post('/customer/data-deletion', [\App\Http\Controllers\Customer\DataSubjectController::class, 'destroy'])->name('customer.data-deletion.destroy');

    // Customer Checkout, Orders & Statutory Tax Invoices
    Route::post('/checkout/{servicePackage:slug}', [\App\Http\Controllers\CheckoutController::class, 'process'])->name('checkout.process');
    Route::get('/checkout/{order}/pay', [\App\Http\Controllers\CheckoutController::class, 'pay'])->name('checkout.pay');
    Route::post('/checkout/{order}/callback', [\App\Http\Controllers\CheckoutController::class, 'callback'])->name('checkout.callback');
    Route::get('/customer/billing', [\App\Http\Controllers\Customer\BillingController::class, 'index'])->name('customer.billing.index');
    Route::get('/customer/orders/{order}', [\App\Http\Controllers\Customer\BillingController::class, 'showOrder'])->name('customer.orders.show');
    Route::get('/customer/invoices/{invoice}', [\App\Http\Controllers\Customer\BillingController::class, 'showInvoice'])->name('customer.invoices.show');

    // Staff Grievance Redressal & Fraud Investigation Queue
    Route::middleware('role:case_manager,admin')->prefix('admin/grievances')->name('admin.grievances.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\GrievanceController::class, 'index'])->name('index');
        Route::get('/{grievance}', [\App\Http\Controllers\Admin\GrievanceController::class, 'show'])->name('show');
        Route::post('/{grievance}/assign', [\App\Http\Controllers\Admin\GrievanceController::class, 'assign'])->name('assign');
        Route::post('/{grievance}/status', [\App\Http\Controllers\Admin\GrievanceController::class, 'updateStatus'])->name('status');
        Route::post('/{grievance}/resolve', [\App\Http\Controllers\Admin\GrievanceController::class, 'resolve'])->name('resolve');
    });

    // Staff Commerce, Invoicing & Revenue Management
    Route::middleware('role:case_manager,admin')->prefix('admin/billing')->name('admin.billing.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\BillingController::class, 'index'])->name('index');
        Route::get('/orders/{order}', [\App\Http\Controllers\Admin\BillingController::class, 'show'])->name('show');
        Route::post('/orders/{order}/refund', [\App\Http\Controllers\Admin\BillingController::class, 'refund'])->name('refund');
    });

    // Staff Editorial & Content Freshness Queue
    Route::middleware('role:case_manager,admin')->prefix('admin/articles')->name('admin.articles.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\ArticleController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\ArticleController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\ArticleController::class, 'store'])->name('store');
        Route::get('/{article}/edit', [\App\Http\Controllers\Admin\ArticleController::class, 'edit'])->name('edit');
        Route::put('/{article}', [\App\Http\Controllers\Admin\ArticleController::class, 'update'])->name('update');
        Route::post('/{article}/verify', [\App\Http\Controllers\Admin\ArticleController::class, 'markVerified'])->name('verify');
    });
});

// Public Transparent Checkout Overview
Route::get('/checkout/{servicePackage:slug}', [\App\Http\Controllers\CheckoutController::class, 'show'])->name('checkout.show');

// Gateway Webhooks (Idempotent & CSRF Exempted)
Route::post('/webhooks/razorpay', [\App\Http\Controllers\WebhookController::class, 'handleRazorpay'])->name('webhooks.razorpay');


