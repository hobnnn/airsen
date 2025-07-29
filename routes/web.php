<?php

use App\Http\Controllers\FirebaseConnectionController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HighAlertController;

Route::get('/', function () {
    return view('/auth.login');
});

Route::get('/hello-log', function () {
    return response()->json([
        'messages' => Cache::get('hello_world_logs', [])
    ]);
});

//check firebase connection
//Route::get('/', [FirebaseConnectionController::class, 'index']);

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::get('/high-alert', [HighAlertController::class, 'index'])->name('high-alert');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});



require __DIR__.'/auth.php';
