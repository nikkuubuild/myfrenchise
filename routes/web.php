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
    } catch (\Throwable $e) {
        $dbStatus = 'disconnected';
    }

    return response()->json([
        'status' => 'healthy',
        'app' => config('app.name'),
        'environment' => config('app.env'),
        'database' => $dbStatus,
        'timestamp' => now()->toIso8601String(),
    ]);
})->name('health');

// Opportunity Discovery Catalog & Profiles
Route::get('/opportunities', [OpportunityController::class, 'index'])->name('opportunities.index');
Route::get('/opportunities/{slug}', [OpportunityController::class, 'show'])->name('opportunities.show');

// Needs Assessment & Consented Enquiry (with rate limiting)
Route::get('/enquiry', [EnquiryController::class, 'create'])->name('enquiry.create');
Route::post('/enquiry', [EnquiryController::class, 'store'])->middleware('throttle:enquiry')->name('enquiry.store');
Route::get('/enquiry/confirmed', [EnquiryController::class, 'confirmed'])->name('enquiry.confirmed');

// Interactive Opportunity Matching Engine
Route::get('/assessment', [\App\Http\Controllers\AssessmentController::class, 'index'])->name('assessment.index');
Route::post('/assessment/match', [\App\Http\Controllers\AssessmentController::class, 'match'])->name('assessment.match');

// Informational, Governance & Policy Pages
Route::get('/how-it-works', [PageController::class, 'howItWorks'])->name('pages.how-it-works');
Route::get('/verification-policy', [PageController::class, 'verificationPolicy'])->name('pages.verification-policy');
Route::get('/grievance-and-fraud', [PageController::class, 'grievance'])->name('pages.grievance');
Route::get('/privacy-policy', [PageController::class, 'privacy'])->name('pages.privacy');
Route::get('/terms-of-service', [PageController::class, 'terms'])->name('pages.terms');

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
});
