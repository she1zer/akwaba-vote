<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\CandidatController;
use App\Http\Controllers\Admin\ExportController;
use App\Http\Controllers\Admin\ModerationController;
use App\Http\Controllers\Admin\ParametreController;
use App\Http\Controllers\Admin\QrCodeController;
use App\Http\Controllers\Admin\StatistiqueController;
use App\Http\Controllers\Admin\TalentController;
use App\Http\Controllers\CandidatureController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ReactionController;
use App\Http\Controllers\ResultatController;
use App\Http\Controllers\VoteController;
use Illuminate\Support\Facades\Route;

// ── Public ───────────────────────────────────────────────────────────────────
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/talent/{talent}/vote', [VoteController::class, 'show'])->name('vote.show');
Route::post('/talent/{talent}/vote', [VoteController::class, 'store'])->name('vote.store');
Route::get('/resultats', [ResultatController::class, 'index'])->name('resultats');
Route::get('/api/resultats', [ResultatController::class, 'api'])->name('resultats.api');
Route::post('/candidat/{candidat}/reaction', [ReactionController::class, 'store'])->name('reaction.store');

// Proposition de candidat par un voteur
Route::post('/talent/{talent}/proposer-candidat', [CandidatureController::class, 'store'])->name('candidature.store');

// ── Admin ─────────────────────────────────────────────────────────────────────
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('login', [AdminAuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AdminAuthController::class, 'login'])->name('login.submit');

    Route::middleware(['admin', 'admin.timeout'])->group(function () {
        Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::post('logout', [AdminAuthController::class, 'logout'])->name('logout');

        // Talents
        Route::resource('talents', TalentController::class)->except(['show']);
        Route::post('talents/{talent}/reorder/{direction}', [TalentController::class, 'reorder'])
            ->name('talents.reorder')
            ->where('direction', 'up|down');
        Route::post('talents/{talent}/reset-votes', [ParametreController::class, 'resetTalentVotes'])
            ->name('talents.reset-votes');

        // Candidats — routes spécifiques AVANT la resource
        Route::post('candidats/preview', [CandidatController::class, 'preview'])->name('candidats.preview');
        Route::post('candidats/{candidat}/toggle', [CandidatController::class, 'toggleActive'])->name('candidats.toggle');
        Route::resource('candidats', CandidatController::class)->except(['show']);

        // Modération des candidatures spontanées
        Route::get('moderation', [ModerationController::class, 'index'])->name('moderation');
        Route::post('moderation/{candidat}/valider', [ModerationController::class, 'valider'])->name('moderation.valider');
        Route::post('moderation/{candidat}/rejeter', [ModerationController::class, 'rejeter'])->name('moderation.rejeter');
        Route::post('moderation/{candidat}/modifier', [ModerationController::class, 'modifier'])->name('moderation.modifier');
        Route::delete('moderation/{candidat}', [ModerationController::class, 'supprimer'])->name('moderation.supprimer');

        // Paramètres
        Route::get('parametres', [ParametreController::class, 'edit'])->name('parametres.edit');
        Route::put('parametres', [ParametreController::class, 'update'])->name('parametres.update');
        Route::post('votes/toggle', [ParametreController::class, 'toggleVotes'])->name('votes.toggle');

        // QR Code
        Route::get('qrcode', [QrCodeController::class, 'show'])->name('qrcode');

        // Exports
        Route::get('export/csv', [ExportController::class, 'csv'])->name('export.csv');
        Route::get('export/csv/bruts', [ExportController::class, 'csvVotesBruts'])->name('export.csv.bruts');
        Route::get('export/pdf', [ExportController::class, 'pdf'])->name('export.pdf');

        // Statistiques & anti-fraude
        Route::get('statistiques', [StatistiqueController::class, 'index'])->name('statistiques');
        Route::get('api/statistiques', [StatistiqueController::class, 'api'])->name('statistiques.api');
        Route::post('votes/{vote}/flag', [StatistiqueController::class, 'flagVote'])->name('votes.flag');
    });
});
