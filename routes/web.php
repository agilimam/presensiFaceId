<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\DashboardController as AdminController;
use App\Http\Controllers\User\DashboardController as UserController;
use App\Http\Controllers\Admin\RegisterController;
use App\Http\Controllers\Admin\KeluargaController;
use App\Http\Controllers\User\PresensiController as UserPresensi;
use App\Http\Controllers\Admin\LogPresensiController as AdminPresensi;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth'])->group(function () {

    // 1. Redirect Dashboard Otomatis
    Route::get('/dashboard', function () {
        if (Auth::user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('user.dashboard'); 
    })->name('dashboard');

    // GRUP ROLE: ADMIN
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
        Route::get('/admin/presensi', [AdminPresensi::class, 'index'])->name('admin.presensi.index');
        Route::post('/admin/clean-storage', [AdminController::class, 'cleanStorage'])->name('admin.clean.storage');
        Route::get('admin/log-presensi/export', [AdminPresensi::class, 'exportPdf'])->name('admin.presensi.export');
        
        // --- ROUTE MANAJEMEN KELUARGA (ADMIN) ---
        Route::get('/admin/keluarga', [KeluargaController::class, 'index'])->name('admin.keluarga.index');
        Route::put('/admin/keluarga/{id}', [KeluargaController::class, 'update'])->name('admin.keluarga.update');
        Route::delete('/admin/keluarga/{id}', [KeluargaController::class, 'destroy'])->name('admin.keluarga.destroy');
        // ----------------------------------------
        
        // Rute Update & Hapus Register Wajah
        Route::post('/admin/register/update/{id}', [RegisterController::class, 'updateFace'])->name('admin.register.update');
        Route::delete('/admin/register/destroy/{id}', [RegisterController::class, 'destroyFace'])->name('admin.register.destroyFace');
        Route::put('/admin/anggota/{id}', [RegisterController::class, 'updateAnggota'])->name('admin.anggota.update');
        Route::delete('/admin/presensi/{id}', [AdminPresensi::class, 'destroy'])->name('admin.presensi.destroy');
    });

    // GRUP ROLE: KELUARGA / USER
    Route::middleware(['role:keluarga'])->group(function () {
        Route::get('/user/dashboard', [UserController::class, 'index'])->name('user.dashboard');
        
        // --- ROUTE ANGGOTA KELUARGA (USER) ---
        Route::post('/user/anggota/store', [UserController::class, 'storeAnggota'])->name('user.anggota.store');
        Route::put('/user/anggota/{id}', [UserController::class, 'update'])->name('user.anggota.update');
        Route::delete('/user/anggota/{id}', [UserController::class, 'destroy'])->name('user.anggota.destroy');
        // -------------------------------------

        Route::get('/user/presensi', [UserPresensi::class, 'index'])->name('user.presensi.index');
        Route::post('/user/presensi/scan', [UserPresensi::class, 'scan'])->name('user.presensi.scan');
    });
    
});

require __DIR__.'/auth.php';