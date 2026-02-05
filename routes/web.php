<?php

use App\Http\Controllers\TelegramController;
use Illuminate\Support\Facades\Route;

Route::any('/', [TelegramController::class, 'handle'])->withoutMiddleware([Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);;
