<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\StockReportController;
use App\Http\Controllers\TransactionController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('/login', [AuthController::class, 'login']);

// Protected Routes
Route::group(['middleware' => ['auth:sanctum']], function () {

    // User
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::post('/add_user', [AuthController::class, 'add_user']);
    Route::get('/user', [AuthController::class, 'user']);
    Route::put('/user/{id}', [AuthController::class, 'edit_user']);
    Route::post('/edituser/{id}', [AuthController::class, 'edituser']);
    Route::delete('/user/{id}', [AuthController::class, 'destroy']);

    // Member
    Route::post('/add_member', [MemberController::class, 'store']);
    Route::get('/member', [MemberController::class, 'index']);
    Route::put('/member/{id}', [MemberController::class, 'edit_member']);
    Route::post('/editmember/{id}', [MemberController::class, 'editmember']);
    Route::delete('/member/{id}', [MemberController::class, 'destroy']);

    // Product
    Route::get('/product', [ProductController::class, 'index']);
    Route::post('/product', [ProductController::class, 'store']);
    Route::post('/editproduct/{id}', [ProductController::class, 'editproduct']);
    Route::get('/product/{id}', [ProductController::class, 'show']);
    Route::put('/product/{id}', [ProductController::class, 'update']);
    Route::delete('/product/{id}', [ProductController::class, 'destroy']);

    // Category
    Route::get('/category', [CategoryController::class, 'index']);
    Route::post('/category', [CategoryController::class, 'store']);
    Route::get('/category/{id}', [CategoryController::class, 'show']);
    Route::put('/category/{id}', [CategoryController::class, 'update']);
    Route::delete('/category/{id}', [CategoryController::class, 'destroy']);

    // Transcactions
    Route::get('/transaction', [TransactionController::class, 'index']);
    Route::get('/transaction-by-date', [TransactionController::class, 'getTransactionsByDate']);
    Route::get('/transaction-today', [TransactionController::class, 'today']);
    Route::get('/transaction-week', [TransactionController::class, 'week']);
    Route::get('/transaction-month', [TransactionController::class, 'month']);
    Route::get('/transaction-year', [TransactionController::class, 'month']);
    Route::post('/transaction', [TransactionController::class, 'store']);
    Route::get('/transaction/{id}', [TransactionController::class, 'show']);
    Route::put('/transaction/{id}', [TransactionController::class, 'update']);
    Route::delete('/transaction/{id}', [TransactionController::class, 'destroy']);
    Route::get('/total', [TransactionController::class, 'getTotal']);
    Route::get('/get-latest-invoice-id', [TransactionController::class, 'getLatestInvoiceId']);

    // Stock Report
    Route::get('/stockreport', [StockReportController::class, 'index']);
});
