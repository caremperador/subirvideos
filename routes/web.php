<?php

use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VideoController;
use App\Http\Controllers\UsuarioProhibidoController;

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
    return redirect()->route('login');
});
Route::get('/prohibido', [UsuarioProhibidoController::class, 'index'])->name('prohibido');

// Rutas para los videos
Route::get('/videos/create', [VideoController::class, 'create'])
    ->name('videos.create')
    ->middleware(['auth', 'ensure_role:admin,uploader']); // Pasa 'admin' y 'uploader' como roles permitidos


Route::post('/videos', [VideoController::class, 'store'])->name('videos.store')->middleware('auth');
Route::get('/videos', [VideoController::class, 'index'])->name('videos.index')->middleware(['auth', 'ensure_role:admin,uploader']);
Route::delete('/videos/{video}', [VideoController::class, 'destroy'])->name('videos.destroy')->middleware('auth');
Route::get('/videos/{video}/embed', [VideoController::class, 'embed'])->name('videos.embed');
Route::get('/videos/{video}/play', [VideoController::class, 'play'])->name('videos.play');


// En tu archivo de rutas web.php o routes/web.php

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified', 'ensure_role:admin,uploader'])
    ->group(function () {
        Route::get('/dashboard', function () {
            return view('dashboard');
        })->name('dashboard');
    });

// Suponiendo que deseas deshabilitar el registro público y solo permitirlo a los usuarios con ciertos roles
Route::middleware(['auth', 'ensure_role:admin,uploader'])->group(function () {
    Route::get('/register', function () {
        // Redirige o muestra una vista que diga que el registro está deshabilitado.
        return redirect()->route('prohibido');
    })->name('register');
});

