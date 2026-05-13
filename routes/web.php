<?php

use App\Http\Controllers\WebsiteLandingController;
use Illuminate\Support\Facades\Route;

Route::middleware(['static'])->get('/', WebsiteLandingController::class)->name('home');

// Rota de compatibilidade para middlewares que usam route('login')
Route::get('/__compat-login', fn () => redirect()->to('/login'))->name('login');
