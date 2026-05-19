<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\CongeController;
use App\Http\Controllers\DashboardController;

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::middleware(['auth'])->group(function () {


    Route::get('/conges', [CongeController::class, 'index'])->name('conges.index');
    Route::get('/conges/create', [CongeController::class, 'create'])->name('conges.create');
    Route::post('/conges', [CongeController::class, 'store'])->name('conges.store');
    Route::delete('/conges/{conge}', [CongeController::class, 'cancel'])->name('conges.cancel');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');


    // accessible à admin et rh uniquement
    Route::middleware(['role:admin,rh'])->group(function () {
        Route::resource('employees', EmployeeController::class);
        Route::patch('/conges/{conge}/approve', [CongeController::class, 'approve'])->name('conges.approve');
        Route::patch('/conges/{conge}/reject', [CongeController::class, 'reject'])->name('conges.reject');
    });

});



require __DIR__.'/auth.php';
