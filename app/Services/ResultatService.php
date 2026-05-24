<?php

namespace App\Services;

use App\Models\Talent;
use App\Models\Vote;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ResultatService
{
    public function getResults(?int $talentId = null): array
    {
        $cacheKey = 'resultats.'.($talentId ?? 'all');

        return Cache::remember($cacheKey, 15, function () use ($talentId) {
            $query = Talent::query()
                ->with(['candidatsActifs' => fn ($q) => $q->withCount([
                    'votes as votes_count' => fn ($v) => $v->where('is_valid', true)->where('is_flagged', false),
                ])])
                ->withCount(['votes as votes_count' => fn ($v) => $v->where('is_valid', true)->where('is_flagged', false)])
                ->orderBy('ordre')
                ->orderBy('nom');

            if ($talentId) {
                $query->where('id', $talentId);
            }

            return $query->get()->map(function (Talent $talent) {
                $total = max(1, $talent->votes_count);
                $candidats = $talent->candidatsActifs->map(function ($c) use ($total) {
                    $votes = (int) $c->votes_count;
                    return [
                        'id'        => $c->id,
                        'nom'       => $c->nom_complet,
                        'slug'      => $c->slug,
                        'slogan'    => $c->slogan,
                        'votes'     => $votes,
                        'percent'   => round(($votes / $total) * 100, 1),
                        'photo'     => $c->thumbUrl(),
                        'initials'  => $c->initials(),
                        'reactions' => $c->reactionsCount(),
                    ];
                })->sortByDesc('votes')->values();

                return [
                    'id'          => $talent->id,
                    'nom'         => $talent->nom,
                    'couleur'     => $talent->couleur_hex ?? '#cc0000',
                    'total_votes' => $talent->votes_count,
                    'candidats'   => $candidats,
                ];
            })->values()->all();
        });
    }

    public function getStatsParHeure(?int $talentId = null): array
    {
        $key = 'stats_horaires.'.($talentId ?? 'all');

        return Cache::remember($key, 30, function () use ($talentId) {
            $query = Vote::query()
                ->where('is_valid', true)
                ->where('is_flagged', false)
                ->where('created_at', '>=', now()->subHours(24))
                ->selectRaw('HOUR(created_at) as heure, COUNT(*) as total');

            if ($talentId) {
                $query->where('talent_id', $talentId);
            }

            return $query->groupBy('heure')
                ->orderBy('heure')
                ->pluck('total', 'heure')
                ->toArray();
        });
    }

    public function getVotesFlaggues(): int
    {
        return Vote::where('is_flagged', true)->count();
    }

    public function getFraudeStats(): array
    {
        return Cache::remember('fraude.stats', 60, function () {
            // IPs qui ont voté plus de 3 fois en 10 minutes
            $suspiciousIps = Vote::query()
                ->where('is_valid', true)
                ->where('created_at', '>=', now()->subMinutes(10))
                ->selectRaw('ip_address, COUNT(*) as cnt')
                ->groupBy('ip_address')
                ->havingRaw('cnt > 3')
                ->pluck('cnt', 'ip_address')
                ->toArray();

            return [
                'ips_suspectes' => count($suspiciousIps),
                'votes_flagges' => $this->getVotesFlaggues(),
                'total_invalides' => Vote::where('is_valid', false)->count(),
            ];
        });
    }

    public function clearCache(): void
    {
        $talents = Talent::query()->pluck('id');
        Cache::forget('resultats.all');
        Cache::forget('stats_horaires.all');
        Cache::forget('fraude.stats');
        foreach ($talents as $id) {
            Cache::forget('resultats.'.$id);
            Cache::forget('stats_horaires.'.$id);
        }
    }

    public function hasRecentVotes(): bool
    {
        return Vote::query()
            ->where('is_valid', true)
            ->where('created_at', '>=', now()->subMinutes(5))
            ->exists();
    }

    public function totalVotes(): int
    {
        return Vote::query()->where('is_valid', true)->where('is_flagged', false)->count();
    }
}
