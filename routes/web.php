<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\CongeController;

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


    Route::get('/conges', [CongeController::class, 'index'])->name('conges.index');
    Route::get('/conges/create', [CongeController::class, 'create'])->name('conges.create');
    Route::post('/conges', [CongeController::class, 'store'])->name('conges.store');
    Route::delete('/conges/{conge}', [CongeController::class, 'cancel'])->name('conges.cancel');

    // Accessible à admin et rh uniquement
    Route::middleware(['role:admin,rh'])->group(function () {
        Route::resource('employees', EmployeeController::class);
        Route::patch('/conges/{conge}/approve', [CongeController::class, 'approve'])->name('conges.approve');
        Route::patch('/conges/{conge}/reject', [CongeController::class, 'reject'])->name('conges.reject');
    });

});

Route::middleware(['auth', 'role:admin'])->group(function () {
    // routes admin uniquement
});

require __DIR__.'/auth.php';