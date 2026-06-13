<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\InventarisController;
use App\Http\Controllers\Admin\NotificationController;
use Illuminate\Support\Facades\Route;

// ─── Root Redirect ────────────────────────────────────────────────────────────
Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('admin.dashboard')
        : redirect()->route('login');
});

// ─── Admin Routes ─────────────────────────────────────────────────────────────
Route::prefix('admin')
    ->middleware(['auth'])
    ->name('admin.')
    ->group(function () {

        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // ── Inventaris ──────────────────────────────────────────────────
        Route::prefix('inventaris')->name('inventaris.')->group(function () {

            // Export (harus sebelum {item} agar tidak konflik)
            Route::get('/export',           [InventarisController::class, 'export'])->name('export');
            Route::get('/export-transaksi', [InventarisController::class, 'exportTransaksi'])->name('export-transaksi');

            // CRUD + List
            Route::get('/',         [InventarisController::class, 'index'])->name('index');
            Route::get('/create',   [InventarisController::class, 'create'])->name('create');
            Route::post('/',        [InventarisController::class, 'store'])->name('store');

            Route::get('/{item}',   [InventarisController::class, 'show'])->name('show');
            Route::get('/{item}/edit', [InventarisController::class, 'edit'])->name('edit');
            Route::put('/{item}',   [InventarisController::class, 'update'])->name('update');
            Route::delete('/{item}', [InventarisController::class, 'destroy'])->name('destroy');

            // Actions
            Route::post('/{item}/nonaktifkan',    [InventarisController::class, 'nonaktifkan'])->name('nonaktifkan');
            Route::post('/{item}/stok-masuk',     [InventarisController::class, 'stokMasuk'])->name('stok-masuk');
            Route::post('/{item}/stok-keluar',    [InventarisController::class, 'stokKeluar'])->name('stok-keluar');
            Route::get('/{item}/info-kebutuhan',  [InventarisController::class, 'infoKebutuhan'])->name('info-kebutuhan');
        });

        // ── Notifikasi ──────────────────────────────────────────────────
        Route::prefix('notifikasi')->name('notifikasi.')->group(function () {
            Route::get('/',                          [NotificationController::class, 'index'])->name('index');
            Route::post('/{notification}/read',      [NotificationController::class, 'markRead'])->name('read');
            Route::post('/mark-all-read',            [NotificationController::class, 'markAllRead'])->name('mark-all-read');
        });
    });

require __DIR__ . '/auth.php';
