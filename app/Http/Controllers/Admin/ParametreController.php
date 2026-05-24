<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Parametre;
use App\Services\AdminLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ParametreController extends Controller
{
    public function edit(): View
    {
        return view('admin.parametres', ['parametres' => Parametre::current()]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nom_evenement' => ['required', 'string', 'max:150'],
            'message_accueil' => ['nullable', 'string', 'max:2000'],
            'date_debut_vote' => ['nullable', 'date'],
            'date_fin_vote' => ['nullable', 'date', 'after_or_equal:date_debut_vote'],
            'votes_ouverts' => ['sometimes', 'boolean'],
        ]);

        $data['votes_ouverts'] = $request->boolean('votes_ouverts');

        foreach (['date_debut_vote', 'date_fin_vote'] as $field) {
            if (! empty($data[$field])) {
                $data[$field] = \Carbon\Carbon::parse($data[$field]);
            }
        }

        Parametre::current()->update($data);
        AdminLogger::log('parametres.update', 'Paramètres événement');

        return back()->with('status', 'Paramètres enregistrés.');
    }

    public function toggleVotes(Request $request): RedirectResponse
    {
        $parametres = Parametre::current();
        $parametres->update(['votes_ouverts' => ! $parametres->votes_ouverts]);
        AdminLogger::log('votes.toggle_global', $parametres->votes_ouverts ? 'ouverts' : 'fermés');

        return back()->with('status', 'Votes globaux '.($parametres->votes_ouverts ? 'ouverts' : 'fermés').'.');
    }

    public function resetTalentVotes(\App\Models\Talent $talent): RedirectResponse
    {
        $talent->votes()->delete();
        app(\App\Services\ResultatService::class)->clearCache();
        AdminLogger::log('votes.reset', 'Talent #'.$talent->id);

        return back()->with('status', 'Votes réinitialisés pour '.$talent->nom.'.');
    }
}
