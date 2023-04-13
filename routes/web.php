<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoomController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/



Route::post('/room/{id}/add-user/{badgeId}', [RoomController::class, 'setUserPresent'])->name('room.set.present');
Route::middleware('auth')->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('index');

    Route::post('/room/{id}/book', [RoomController::class, 'book'])->name('room.book');
    Route::post('/room/{id}/unbook', [RoomController::class, 'unBook'])->name('room.unbook');
    Route::get('/room/{id}/not-available', [RoomController::class, 'notAvailable'])->name('room.not.available');


    Route::get('/user/{badgeId}/add-to-room', [ProfileController::class, 'addToRoom'])->name('profile.add.to.room');







    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
