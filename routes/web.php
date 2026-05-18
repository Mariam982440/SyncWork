<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EmployeeController;

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth'])->group(function () {

    // Accessible à admin et rh uniquement
    Route::middleware(['role:admin,rh'])->group(function () {
        Route::resource('employees', EmployeeController::class);
    });

});

Route::middleware(['auth', 'role:admin'])->group(function () {
    // routes admin uniquement
});

require __DIR__.'/auth.php';