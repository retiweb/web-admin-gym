<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return Auth::check() ? redirect('/admin/dashboard') : redirect('/auth/login');
});

Route::middleware('guest')->group(function () {
    Route::livewire('/auth/login', 'auth.login');
});

Route::middleware('auth')->group(function () {
    Route::livewire('/admin/dashboard', 'admin.dashboard')->name('dashboard');
    Route::livewire('/admin/members', 'admin.member')->name('members');
    Route::livewire('/admin/packages', 'admin.package')->name('packages');
    Route::livewire('/admin/transactions', 'admin.transaction')->name('transactions');
    Route::livewire('/admin/visitors', 'admin.visitor')->name('visitors');
});
