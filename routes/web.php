<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\CandidatController;
use App\Http\Controllers\Admin\ExportController;
use App\Http\Controllers\Admin\ParametreController;
use App\Http\Controllers\Admin\QrCodeController;
use App\Http\Controllers\Admin\TalentController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ResultatController;
use App\Http\Controllers\VoteController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/talent/{talent}/vote', [VoteController::class, 'show'])->name('vote.show');
Route::post('/talent/{talent}/vote', [VoteController::class, 'store'])->name('vote.store');
Route::get('/resultats', [ResultatController::class, 'index'])->name('resultats');
Route::get('/api/resultats', [ResultatController::class, 'api'])->name('resultats.api');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('login', [AdminAuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AdminAuthController::class, 'login'])->name('login.submit');

    Route::middleware(['admin', 'admin.timeout'])->group(function () {
        Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::post('logout', [AdminAuthController::class, 'logout'])->name('logout');

        Route::resource('talents', TalentController::class)->except(['show']);
        Route::resource('candidats', CandidatController::class)->except(['show']);
        Route::post('candidats/preview', [CandidatController::class, 'preview'])->name('candidats.preview');

        Route::get('parametres', [ParametreController::class, 'edit'])->name('parametres.edit');
        Route::put('parametres', [ParametreController::class, 'update'])->name('parametres.update');
        Route::post('votes/toggle', [ParametreController::class, 'toggleVotes'])->name('votes.toggle');
        Route::post('talents/{talent}/reset-votes', [ParametreController::class, 'resetTalentVotes'])->name('talents.reset-votes');

        Route::get('qrcode', [QrCodeController::class, 'show'])->name('qrcode');
        Route::get('export/csv', [ExportController::class, 'csv'])->name('export.csv');
        Route::get('export/pdf', [ExportController::class, 'pdf'])->name('export.pdf');
    });
});
