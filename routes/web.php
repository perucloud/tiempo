<?php

use App\Http\Controllers\Web\LandingController;
use Illuminate\Support\Facades\Route;

Route::get('/', LandingController::class)->name('home');

require __DIR__.'/admin.php';
require __DIR__.'/app.php';
require __DIR__.'/courier.php';
