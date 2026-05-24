<?php

namespace App\Services;

use App\Models\Talent;
use App\Models\Vote;
use Illuminate\Support\Facades\Cache;

class ResultatService
{
    public function getResults(?int $talentId = null): array
    {
        $cacheKey = 'resultats.'.($talentId ?? 'all');

        return Cache::remember($cacheKey, 15, function () use ($talentId) {
            $query = Talent::query()
                ->with(['candidats' => fn ($q) => $q->withCount(['votes as votes_count' => fn ($v) => $v->where('is_valid', true)])])
                ->withCount(['votes as votes_count' => fn ($v) => $v->where('is_valid', true)])
                ->orderBy('ordre')
                ->orderBy('nom');

            if ($talentId) {
                $query->where('id', $talentId);
            }

            return $query->get()->map(function (Talent $talent) {
                $total = max(1, $talent->votes_count);
                $candidats = $talent->candidats->map(function ($c) use ($total) {
                    $votes = (int) $c->votes_count;

                    return [
                        'id' => $c->id,
                        'nom' => $c->nom_complet,
                        'votes' => $votes,
                        'percent' => round(($votes / $total) * 100, 1),
                        'photo' => $c->thumbUrl(),
                        'initials' => $c->initials(),
                    ];
                })->sortByDesc('votes')->values();

                return [
                    'id' => $talent->id,
                    'nom' => $talent->nom,
                    'total_votes' => $talent->votes_count,
                    'candidats' => $candidats,
                ];
            })->values()->all();
        });
    }

    public function clearCache(): void
    {
        $talents = Talent::query()->pluck('id');

        Cache::forget('resultats.all');

        foreach ($talents as $id) {
            Cache::forget('resultats.'.$id);
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
        return Vote::query()->where('is_valid', true)->count();
    }
}
