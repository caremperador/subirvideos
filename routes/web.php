<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VideoController;

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

Route::get('/', function () {
    return view('welcome');
});

// Rutas para los videos
Route::get('/videos/create', [VideoController::class, 'create'])->name('videos.create')->middleware('auth');;
Route::post('/videos', [VideoController::class, 'store'])->name('videos.store')->middleware('auth');;
Route::get('/videos', [VideoController::class, 'index'])->name('videos.index')->middleware('auth');;
Route::delete('/videos/{video}', [VideoController::class, 'destroy'])->name('videos.destroy')->middleware('auth');;
Route::get('/videos/{video}/embed', [VideoController::class, 'embed'])->name('videos.embed');
Route::get('/videos/{video}/play/{token}', [VideoController::class, 'play'])->name('videos.play');


Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});
