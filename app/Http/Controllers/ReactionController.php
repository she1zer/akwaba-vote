<?php

namespace App\Http\Controllers;

use App\Models\Candidat;
use App\Models\Reaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReactionController extends Controller
{
    public function store(Request $request, Candidat $candidat): JsonResponse
    {
        $request->validate([
            'type' => ['required', 'in:coeur,feu,star,clap'],
        ]);

        // Limiter à 1 réaction par type par IP par candidat par heure
        $exists = Reaction::where('candidat_id', $candidat->id)
            ->where('ip_address', $request->ip())
            ->where('type', $request->type)
            ->where('created_at', '>=', now()->subHour())
            ->exists();

        if (! $exists) {
            Reaction::create([
                'candidat_id' => $candidat->id,
                'session_id'  => session()->getId(),
                'ip_address'  => $request->ip(),
                'type'        => $request->type,
                'created_at'  => now(),
            ]);
        }

        $counts = Reaction::where('candidat_id', $candidat->id)
            ->selectRaw('type, count(*) as total')
            ->groupBy('type')
            ->pluck('total', 'type');

        return response()->json(['counts' => $counts, 'success' => ! $exists]);
    }
}
