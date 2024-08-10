<?php
use App\Http\Controllers\WaterLevelController;
use App\Http\Middleware\IsAdmin;
use Illuminate\Support\Facades\Route;

// Route untuk halaman dashboard
Route::get('/dashboard', [WaterLevelController::class, 'index'])->name('dashboard');

// Route untuk halaman statis lainnya
Route::view('/', 'home')->name('home');
Route::view('/about', 'about')->name('about');
Route::view('/contact', 'contact')->name('contact');

// Route untuk halaman riwayat, hanya dapat dilihat oleh admin
Route::middleware(['auth', IsAdmin::class])->group(function () {
    Route::get('/history', [WaterLevelController::class, 'history'])->name('history');
});

// Route untuk API dan lainnya
Route::get('/api/water-level-data', [WaterLevelController::class, 'getWaterLevelData']);
Route::get('/download-report', [WaterLevelController::class, 'downloadReport'])->name('downloadReport');
Route::get('/get-chart-data', [WaterLevelController::class, 'getChartData'])->name('getChartData');

// Require authentication routes
require __DIR__.'/auth.php';
