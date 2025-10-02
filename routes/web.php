<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\FormController;
use App\Http\Controllers\User\FormResponseController;

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
    Route::resource('forms', FormController::class);
    // tambah route untuk export/lihat responses jika mau
    Route::get('forms/{form}/responses', [FormController::class, 'responses'])->name('forms.responses');
    Route::get('/forms/{form}/responses', [FormController::class, 'responses'])->name('forms.responses.index');

    // Rute untuk melihat detail jawaban individual
    Route::get('/forms/{form}/responses/{response}', [FormController::class, 'showResponseDetail'])->name('forms.responses.show');
    Route::delete('/forms/{form}/responses/{response}', [FormController::class, 'destroyResponse'])->name('forms.responses.destroy');
});

Route::get('/f/{hash}', [FormController::class, 'publicShow'])->name('forms.public.show');
Route::post('/f/{slug}', [FormResponseController::class, 'store'])->name('forms.public.submit');
Route::get('/f/{slug}/thanks', [FormResponseController::class, 'thanks'])->name('forms.public.thanks');


require __DIR__ . '/auth.php';
