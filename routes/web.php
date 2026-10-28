<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\HabitController;
use App\Http\Controllers\TagController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/habits');

Route::resource('habits', HabitController::class)->except('show');
Route::resource('categories', CategoryController::class)->except('show');
Route::resource('tags', TagController::class)->except('show');
