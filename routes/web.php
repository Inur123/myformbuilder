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
});

Route::get('/{hash}', [FormController::class, 'publicShow'])->name('forms.public.show');

Route::post('/{slug}', [FormResponseController::class, 'store'])->name('forms.public.submit');
Route::get('/{slug}/thanks', [FormResponseController::class, 'thanks'])->name('forms.public.thanks');

require __DIR__.'/auth.php';
