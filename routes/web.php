<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('index'); // Ini merujuk ke resources/views/index.blade.php
});