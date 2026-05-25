<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Candidat;
use App\Models\Talent;
use App\Services\AdminLogger;
use App\Services\ResultatService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ModerationController extends Controller
{
    public function __construct(private ResultatService $resultats) {}

    public function index(): View
    {
        $enAttente = Candidat::with('talent')
            ->where('statut', 'en_attente')
            ->orderBy('propose_le')
            ->get();

        $rejetes = Candidat::with('talent')
            ->where('statut', 'rejete')
            ->orderByDesc('updated_at')
            ->limit(30)
            ->get();

        return view('admin.moderation', compact('enAttente', 'rejetes'));
    }

    public function valider(Candidat $candidat): RedirectResponse
    {
        $candidat->update([
            'statut'    => 'valide',
            'is_active' => true,
        ]);

        AdminLogger::log('moderation.valider', $candidat->nom_complet.' (talent: '.$candidat->talent?->nom.')');
        $this->resultats->clearCache();

        return back()->with('status', "✅ {$candidat->nom_complet} a été validé et est maintenant visible dans le vote.");
    }

    public function rejeter(Request $request, Candidat $candidat): RedirectResponse
    {
        $request->validate([
            'note_admin' => ['nullable', 'string', 'max:300'],
        ]);

        $candidat->update([
            'statut'     => 'rejete',
            'is_active'  => false,
            'note_admin' => $request->input('note_admin'),
        ]);

        AdminLogger::log('moderation.rejeter', $candidat->nom_complet.' (talent: '.$candidat->talent?->nom.')');
        $this->resultats->clearCache();

        return back()->with('status', "❌ {$candidat->nom_complet} a été rejeté.");
    }

    public function modifier(Request $request, Candidat $candidat): RedirectResponse
    {
        $data = $request->validate([
            'nom_complet' => ['required', 'string', 'max:100'],
            'slogan'      => ['nullable', 'string', 'max:200'],
        ]);

        $candidat->update([
            'nom_complet' => trim($data['nom_complet']),
            'slogan'      => $data['slogan'] ? trim($data['slogan']) : null,
            'statut'      => 'valide',
            'is_active'   => true,
        ]);

        AdminLogger::log('moderation.modifier_valider', $candidat->nom_complet);
        $this->resultats->clearCache();

        return back()->with('status', "✅ {$candidat->nom_complet} a été modifié et validé.");
    }

    public function supprimer(Candidat $candidat): RedirectResponse
    {
        $nom = $candidat->nom_complet;
        AdminLogger::log('moderation.supprimer', $nom);
        $candidat->delete();

        return back()->with('status', "🗑 {$nom} a été supprimé définitivement.");
    }
}
