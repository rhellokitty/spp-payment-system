<?php

use App\Http\Controllers\StudentController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::apiResource('user', UserController::class);
Route::get('user/all/paginated', [UserController::class, 'getAllPaginated']);

Route::apiResource('student', StudentController::class);
Route::get('student/all/paginated', [StudentController::class, 'getAllPaginated']);
