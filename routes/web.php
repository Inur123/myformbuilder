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

    // --- Rute untuk Form Responses ---

    // Halaman utama untuk melihat semua jawaban (ringkasan)
    // Saya hapus duplikasinya, cukup satu nama route
    Route::get('forms/{form}/responses', [FormController::class, 'responses'])->name('forms.responses');

    // **SOLUSI:** Pindahkan route 'export' ke sini, SEBELUM route dengan '{response}'
    Route::get('forms/{form}/responses/export', [FormController::class, 'exportResponses'])->name('forms.responses.export');

    // Rute untuk melihat detail satu jawaban
    Route::get('/forms/{form}/responses/{response}', [FormController::class, 'showResponseDetail'])->name('forms.responses.show');

    // Rute untuk menghapus satu jawaban
    Route::delete('/forms/{form}/responses/{response}', [FormController::class, 'destroyResponse'])->name('forms.responses.destroy');
});

Route::get('/f/{hash}', [FormController::class, 'publicShow'])->name('forms.public.show');
Route::post('/f/{slug}', [FormResponseController::class, 'store'])->name('forms.public.submit');
Route::get('/f/{slug}/thanks', [FormResponseController::class, 'thanks'])->name('forms.public.thanks');


require __DIR__ . '/auth.php';
