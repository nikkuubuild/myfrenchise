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
