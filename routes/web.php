<?php

use App\Http\Controllers\BookCategoryController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\BorrowedLogController;

Route::get('/', [HomeController::class, 'index'])->name('home'); // Home page showing history, latest arrivals, categories, and search functionality

Route::get('/book/{id}', [BookController::class, 'show'])->name('book.show'); // Show book details

Route::get('/register', [RegisterController::class, 'index'])->name('register.index'); // Go to registration form
Route::post('/register', [RegisterController::class, 'store'])->name('register.store'); // Handle registration form submission


Route::get('/login', [LoginController::class, 'index'])->name('login.index'); // Show login form
Route::post('/login', [LoginController::class, 'authenticate'])->name('login.authenticate'); // Handle login form submission
Route::post('/logout', [LoginController::class, 'logout'])->name('logout'); // Handle logout

Route::middleware(['auth', 'can:is-member'])->group(function () {
    Route::get('/setting', [LoginController::class, 'setting'])->name('login.setting'); // Show account settings form (e.g., change password)
    Route::post('/setting/password', [LoginController::class, 'updatePassword'])->name('login.update-password'); // Handle password update form submission
});

Route::prefix('books')
    ->controller(BookController::class)
    ->middleware('can:is-admin')
    ->group(function () {
        Route::get('/create', 'create')->name('book.create'); // Show form to create a new book metadata + first copy
        Route::post('/store', 'store')->name('book.store'); // Handle form submission to create a new book metadata + first copy

        Route::get('/update-metadata/{id}', 'editMetadata')->name('book.update-metadata'); // Show form to update book metadata
        Route::post('/update-metadata/{id}', 'updateMetadata')->name('book.update-metadata.save'); // Handle update book metadata submission

        Route::post('/copy/create/{bookId}', 'createCopy')->name('book.copy.store'); // Create a new book copy
        Route::post('/copy/update/{id}', 'updateCopy')->name('book.copy.update'); // Update book copy status (e.g., mark as lost)
    });

Route::prefix('admin/members')
    ->controller(MemberController::class)
    ->middleware('can:is-admin')
    ->group(function () {
        Route::get('/', 'index')->name('admin.members.index'); // List members with search functionality
        Route::get('/{id}', 'show')->name('admin.members.show'); // Show member details and borrowing history
        Route::post('/{id}/status', 'updateStatus')->name('admin.members.update-status'); // Update member status (active/inactive)
    });

Route::prefix('admin/borrowed-logs')
    ->controller(BorrowedLogController::class)
    ->middleware('can:is-admin')
    ->group(function () {
        
        Route::get('/create', 'create')->name('admin.borrowed-logs.create'); // Show form to issue a book + borrow history
        Route::post('/store', 'store')->name('admin.borrowed-logs.store'); // Issue Book

        Route::get('/return', 'returnForm')->name('admin.borrowed-logs.return'); // Show form to return a book + return history
        Route::post('/return/store', 'returnBook')->name('admin.borrowed-logs.return-store'); // Handle book return
    });

Route::prefix('categories')
    ->controller(BookCategoryController::class)
    ->middleware(['auth', 'can:is-admin'])
    ->group(function () {
        Route::get('/', 'index')->name('categories.list'); // List all categories
        Route::post('/store', 'store')->name('categories.store'); // Handle form submission to create a new category
        Route::post('/update/{id}', 'update')->name('categories.update'); // Handle form submission to update an existing category
        Route::post('/destroy/{id}', 'destroy')->name('categories.destroy'); // Handle category deletion
    });