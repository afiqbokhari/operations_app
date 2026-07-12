<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/search', [App\Http\Controllers\SearchController::class, 'api']);
