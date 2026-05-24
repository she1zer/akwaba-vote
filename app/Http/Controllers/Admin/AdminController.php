<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Candidat;
use App\Models\Parametre;
use App\Models\Talent;
use App\Models\Vote;
use App\Services\ResultatService;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function __construct(private ResultatService $resultats) {}

    public function dashboard(): View
    {
        $parametres = Parametre::current();
        $stats = [
            'talents'       => Talent::count(),
            'candidats'     => Candidat::count(),
            'votes'         => Vote::where('is_valid', true)->where('is_flagged', false)->count(),
            'votes_flagges' => Vote::where('is_flagged', true)->count(),
        ];
        $live = $this->resultats->hasRecentVotes();
        $recentVotes = Vote::with(['candidat', 'talent'])
            ->where('is_valid', true)
            ->latest('created_at')
            ->limit(20)
            ->get();

        return view('admin.dashboard', compact('parametres', 'stats', 'live', 'recentVotes'));
    }
}
