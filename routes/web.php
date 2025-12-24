<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['static'])->get('/', fn () => view('website.pages.home'))->name('home');

// Rota de compatibilidade para middlewares que usam route('login')
Route::get('/__compat-login', fn () => redirect()->to('/login'))->name('login');
