<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('index'); // Ini merujuk ke resources/views/index.blade.php
});
use App\Http\Controllers\UserController;

// Route untuk memproses update profil
Route::put('/profile/update', [UserController::class, 'update'])->name('profile.update');