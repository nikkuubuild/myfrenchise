<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Franchise Dekho Baseline
|--------------------------------------------------------------------------
*/

// Public Editorial Homepage
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Health Check / System Status Endpoint
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

// Public Placeholders for Phase 1 MVP routes
Route::get('/opportunities', function () {
    return response()->view('welcome'); // Will be replaced by Opportunity Catalog in T07
})->name('opportunities.index');

Route::get('/how-it-works', function () {
    return response()->view('welcome'); // Detailed in T07
})->name('pages.how-it-works');

Route::get('/verification-policy', function () {
    return response()->view('welcome'); // Detailed in T06/T07
})->name('pages.verification-policy');

Route::get('/enquiry', function () {
    return response()->view('welcome'); // Detailed in T07/T08
})->name('enquiry.create');

Route::get('/grievance-and-fraud', function () {
    return response()->view('welcome'); // Detailed in T12
})->name('pages.grievance');

Route::get('/privacy-policy', function () {
    return response()->view('welcome'); // Detailed in T12
})->name('pages.privacy');

Route::get('/terms-of-service', function () {
    return response()->view('welcome'); // Detailed in T12
})->name('pages.terms');
