<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/api/tes', function () {
    return response()->json([
        'status' => true,
        'pesan' => 'Laravel berhasil terhubung!'
    ]);
});