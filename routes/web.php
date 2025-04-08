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

Route::get('/dashboard-admin/pinjam', function () {
    return view('admin.pinjam');
});

Route::get('/dashboard-admin/kembali', function () {
    return view('admin.kembali');
});

Route::get('/dashboard-admin/denda', function () {
    return view('admin.setting');
});

Route::get('/dashboard-admin/buku', function () {
    return view('admin.buku');
});