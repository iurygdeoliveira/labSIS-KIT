<?php

use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('website.pages.home'))->name('home');
