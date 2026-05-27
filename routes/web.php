<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\IaChatController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\AssetsController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/news/investments', [NewsController::class, 'investments'])->name('news.investments');
    Route::get('/investments', function () {
        return Inertia::render('Investments');
    })->name('investments');
    Route::get('/patrimony', function () {
        return Inertia::render('Patrimony');
    })->name('patrimony');

    Route::get('/patrimony/assets', [AssetsController::class, 'index'])->name('patrimony.assets');

    Route::post('/patrimony/store', [AssetsController::class, 'store'])->name('patrimony.store');

    Route::delete('/patrimony/{id}', [AssetsController::class, 'destroy'])->name('patrimony.destroy');
});

Route::post('/chat/chat_api', [IaChatController::class, 'chat'])->middleware(['auth'])->name('chat.chat_api');

require __DIR__.'/auth.php';
