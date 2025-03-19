<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/register', function () {
    return view('auth.register');
});

Route::get('/dashboard', function () {
    return view('buku.list');
});

Route::get('/dashboard-admin', function () {
    return view('admin.index');
});

Route::get('/dashboard-admin/users', function () {
    return view('admin.users');
});

Route::get('/dashboard-admin/settings', function () {
    return view('admin.setting');
});

Route::get('/dashboard-admin/users/{id}/edit', function () {
    return view('admin.userEdit');
});