<?php

namespace App\Http\Controllers;

use App\Models\Candidat;
use App\Models\Talent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class CandidatureController extends Controller
{
    public function store(Request $request, Talent $talent): RedirectResponse
    {
        // Vérifier que la fonctionnalité est activée pour ce talent
        if (! $talent->allow_candidature_spontanee) {
            abort(403, 'Les propositions de candidats ne sont pas activées pour ce talent.');
        }

        // Validation
        $data = $request->validate([
            'nom_complet' => ['required', 'string', 'min:3', 'max:100'],
            'slogan'      => ['nullable', 'string', 'max:200'],
        ], [
            'nom_complet.required' => 'Le nom du candidat est obligatoire.',
            'nom_complet.min'      => 'Le nom doit contenir au moins 3 caractères.',
            'nom_complet.max'      => 'Le nom ne peut pas dépasser 100 caractères.',
        ]);

        // Limite : 1 proposition par IP toutes les 30 minutes par talent
        $key = 'candidature:'.$request->ip().':'.$talent->id;
        if (RateLimiter::tooManyAttempts($key, 1)) {
            $seconds = RateLimiter::availableIn($key);
            return back()->withErrors([
                'nom_complet' => "Vous avez déjà proposé un candidat récemment. Réessayez dans ".ceil($seconds / 60)." minute(s).",
            ])->withInput();
        }

        // Vérifier si un candidat avec ce nom existe déjà (validé ou en attente) pour ce talent
        $existe = Candidat::where('talent_id', $talent->id)
            ->whereRaw('LOWER(nom_complet) = ?', [strtolower(trim($data['nom_complet']))])
            ->whereIn('statut', ['valide', 'en_attente'])
            ->exists();

        if ($existe) {
            return back()->withErrors([
                'nom_complet' => 'Ce candidat existe déjà ou est déjà en attente de validation.',
            ])->withInput();
        }

        RateLimiter::hit($key, 1800); // 30 minutes

        Candidat::query()->create([
            'talent_id'             => $talent->id,
            'nom_complet'           => trim($data['nom_complet']),
            'slogan'                => $data['slogan'] ? trim($data['slogan']) : null,
            'statut'                => 'en_attente',
            'is_active'             => false,
            'propose_par_ip'        => $request->ip(),
            'propose_par_session'   => session()->getId(),
            'propose_le'            => now(),
        ]);

        return back()->with('candidature_succes', 'Votre proposition a été envoyée ! Elle sera visible après validation par l\'équipe organisatrice.');
    }
}
