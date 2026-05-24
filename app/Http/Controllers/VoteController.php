<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVoteRequest;
use App\Models\Candidat;
use App\Models\Parametre;
use App\Models\Talent;
use App\Models\Vote;
use App\Services\FraudeDetector;
use App\Services\ResultatService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\View\View;

class VoteController extends Controller
{
    public function __construct(
        private ResultatService $resultats,
        private FraudeDetector $fraude,
    ) {}

    public function show(Talent $talent): View|RedirectResponse
    {
        $parametres = Parametre::current();

        if (! $parametres->votesAreOpen() || ! $talent->votes_actifs) {
            return view('public.votes-fermes', compact('parametres'));
        }

        if ($this->hasVoted($talent->id)) {
            return redirect()->route('resultats', ['talent' => $talent->id])
                ->with('status', 'Vous avez déjà voté pour ce talent.');
        }

        $talent->load('candidatsActifs');

        return view('public.vote', compact('talent', 'parametres'));
    }

    public function store(StoreVoteRequest $request, Talent $talent): RedirectResponse
    {
        $parametres = Parametre::current();

        if (! $parametres->votesAreOpen() || ! $talent->votes_actifs) {
            return redirect()->route('home');
        }

        $candidat = Candidat::query()->where('is_active', true)->findOrFail($request->validated('candidat_id'));

        if ($candidat->talent_id !== $talent->id) {
            abort(422);
        }

        if ($this->hasVoted($talent->id)) {
            return redirect()->route('resultats', ['talent' => $talent->id]);
        }

        $key = 'vote:'.$request->ip().':'.$talent->id;

        if (RateLimiter::tooManyAttempts($key, $talent->max_votes_par_ip ?? 1)) {
            return back()->withErrors(['vote' => 'Trop de tentatives. Réessayez dans quelques minutes.']);
        }

        RateLimiter::hit($key, 600);

        // Détection fraude
        $score = $this->fraude->calculerScore($request, $talent->id);
        $isFlagged = $this->fraude->doitFlaguer($score);
        $flagReason = $isFlagged ? 'score_confiance_bas' : null;

        Vote::query()->create([
            'talent_id'          => $talent->id,
            'candidat_id'        => $candidat->id,
            'session_id'         => session()->getId(),
            'ip_address'         => $request->ip(),
            'user_agent'         => $request->userAgent(),
            'device_fingerprint' => $request->input('_fp'),
            'is_valid'           => true,
            'is_flagged'         => $isFlagged,
            'flag_reason'        => $flagReason,
            'score_confiance'    => $score,
            'created_at'         => now(),
        ]);

        $voted = json_decode($request->cookie('akwa_voted', '[]'), true) ?: [];
        $voted[] = $talent->id;
        $voted = array_values(array_unique($voted));

        session()->push('voted_talents', $talent->id);
        session()->put('vote_success', true);

        $this->resultats->clearCache();

        Cookie::queue('akwa_voted', json_encode($voted), 60 * 24 * 30);

        return redirect()->route('resultats', ['talent' => $talent->id, 'voted' => 1]);
    }

    private function hasVoted(int $talentId): bool
    {
        if (in_array($talentId, session('voted_talents', []), true)) {
            return true;
        }
        $cookie = json_decode(request()->cookie('akwa_voted', '[]'), true) ?: [];
        return in_array($talentId, $cookie, true);
    }
}
