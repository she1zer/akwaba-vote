<?php

namespace App\Services;

use App\Models\Vote;
use Illuminate\Http\Request;

class FraudeDetector
{
    /**
     * Calcule un score de confiance entre 0 et 100.
     * Plus c'est élevé, plus le vote est considéré légitime.
     */
    public function calculerScore(Request $request, int $talentId): int
    {
        $score = 100;
        $ip = $request->ip();

        // Pénalité si trop de votes depuis cette IP (global, toutes catégories)
        $totalVotesIp = Vote::where('ip_address', $ip)
            ->where('created_at', '>=', now()->subHour())
            ->count();
        if ($totalVotesIp > 10) {
            $score -= 40;
        } elseif ($totalVotesIp > 5) {
            $score -= 20;
        }

        // Pénalité si le user-agent est vide ou générique
        $ua = $request->userAgent() ?? '';
        if (empty($ua) || str_contains(strtolower($ua), 'bot') || str_contains(strtolower($ua), 'curl')) {
            $score -= 30;
        }

        // Pénalité si vote trop rapide (< 5s après le précédent depuis même IP)
        $dernierVote = Vote::where('ip_address', $ip)
            ->orderByDesc('created_at')
            ->value('created_at');
        if ($dernierVote && now()->diffInSeconds($dernierVote) < 5) {
            $score -= 25;
        }

        return max(0, $score);
    }

    public function doitFlaguer(int $score): bool
    {
        return $score < 40;
    }
}
