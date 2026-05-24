<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Candidat;
use App\Models\Talent;
use App\Models\Vote;
use App\Services\ResultatService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StatistiqueController extends Controller
{
    public function __construct(private ResultatService $resultats) {}

    public function index(): View
    {
        $fraudeStats = $this->resultats->getFraudeStats();
        $totalVotes = Vote::where('is_valid', true)->count();
        $votesParHeure = Vote::where('is_valid', true)
            ->where('created_at', '>=', now()->subHours(24))
            ->selectRaw('HOUR(created_at) as heure, COUNT(*) as total')
            ->groupBy('heure')
            ->orderBy('heure')
            ->pluck('total', 'heure')
            ->toArray();

        $topCandidats = Candidat::withCount(['votes as votes_count' => fn ($q) => $q->where('is_valid', true)->where('is_flagged', false)])
            ->orderByDesc('votes_count')
            ->limit(10)
            ->with('talent')
            ->get();

        $votesFlagges = Vote::with(['candidat', 'talent'])
            ->where('is_flagged', true)
            ->latest('created_at')
            ->limit(20)
            ->get();

        return view('admin.statistiques', compact(
            'fraudeStats', 'totalVotes', 'votesParHeure',
            'topCandidats', 'votesFlagges'
        ));
    }

    public function api(Request $request): JsonResponse
    {
        $talentId = $request->integer('talent') ?: null;

        return response()->json([
            'stats_horaires' => $this->resultats->getStatsParHeure($talentId),
            'results'        => $this->resultats->getResults($talentId),
            'fraude'         => $this->resultats->getFraudeStats(),
            'updated_at'     => now()->toIso8601String(),
        ]);
    }

    public function flagVote(Vote $vote): JsonResponse
    {
        $vote->update(['is_flagged' => ! $vote->is_flagged]);
        $this->resultats->clearCache();

        return response()->json(['flagged' => $vote->is_flagged]);
    }
}
