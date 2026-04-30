<?php

use App\Http\Controllers\BillController;
use App\Http\Controllers\ClassRoomController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PaymentTypeController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('dashboard/get-dashboard-data', [DashboardController::class, 'getDashboardData']);

Route::apiResource('user', UserController::class);
Route::get('user/all/paginated', [UserController::class, 'getAllPaginated']);

Route::apiResource('student', StudentController::class);
Route::get('student/all/paginated', [StudentController::class, 'getAllPaginated']);

Route::apiResource('classRoom', ClassRoomController::class);
Route::get('classRoom/all/paginated', [ClassRoomController::class, 'getAllPaginated']);

Route::apiResource('teacher', TeacherController::class);
Route::get('teacher/all/paginated', [TeacherController::class, 'getAllPaginated']);

Route::apiResource('paymentType', PaymentTypeController::class);
Route::get('paymentType/all/paginated', [PaymentTypeController::class, 'getAllPaginated']);

Route::post('bill/generate/classRoom', [BillController::class, 'generateByClassRoom']);
Route::apiResource('bill', BillController::class);
Route::get('bill/all/paginated', [BillController::class, 'getAllPaginated']);

Route::post('transaction/retry', [TransactionController::class, 'retry']);
Route::post('transaction/initiate-payment', [TransactionController::class, 'initiatePayment']);
Route::post('transaction/webhook', [TransactionController::class, 'webhook']);
Route::get('transaction/all/paginated', [TransactionController::class, 'getAllPaginated']);

Route::apiResource('transaction', TransactionController::class)->except(['destroy', 'update']);
