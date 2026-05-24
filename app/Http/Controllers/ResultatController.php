<?php

namespace App\Http\Controllers;

use App\Models\Parametre;
use App\Models\Talent;
use App\Services\ResultatService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ResultatController extends Controller
{
    public function __construct(private ResultatService $resultats) {}

    public function index(Request $request): View
    {
        $parametres = Parametre::current();
        $talents = Talent::query()->orderBy('ordre')->orderBy('nom')->get();
        $selectedTalent = $request->integer('talent') ?: ($talents->first()?->id);
        $voteSuccess = $request->boolean('voted') || session()->pull('vote_success', false);

        if ($this->resultats->totalVotes() === 0) {
            return view('public.resultats-vides', compact('parametres', 'talents'));
        }

        $results = $this->resultats->getResults($selectedTalent);

        return view('public.resultats', [
            'parametres' => $parametres,
            'talents' => $talents,
            'selectedTalent' => $selectedTalent,
            'results' => $results,
            'voteSuccess' => $voteSuccess,
        ]);
    }

    public function api(Request $request): JsonResponse
    {
        $talentId = $request->integer('talent') ?: null;

        if ($this->resultats->totalVotes() === 0) {
            return response()->json(['results' => [], 'empty' => true]);
        }

        return response()->json([
            'results' => $this->resultats->getResults($talentId),
            'updated_at' => now()->toIso8601String(),
        ]);
    }
}
