<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('web.landing');
})->name('home');

require __DIR__.'/admin.php';
require __DIR__.'/app.php';
