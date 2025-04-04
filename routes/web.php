<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');

Route::get('/info/server', [InfoController::class, 'server']);
Route::get('/info/client', [InfoController::class, 'client']);
Route::get('/info/database', [InfoController::class, 'database']);
});
