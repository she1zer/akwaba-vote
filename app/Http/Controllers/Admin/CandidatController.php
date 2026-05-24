<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CandidatRequest;
use App\Models\Candidat;
use App\Models\Talent;
use App\Services\AdminLogger;
use App\Services\CandidatPhotoService;
use App\Services\ResultatService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CandidatController extends Controller
{
    public function __construct(
        private CandidatPhotoService $photos,
        private ResultatService $resultats,
    ) {}

    public function index(): View
    {
        $candidats = Candidat::with('talent')
            ->withCount(['votes as votes_count' => fn ($q) => $q->where('is_valid', true)->where('is_flagged', false)])
            ->orderBy('talent_id')->orderBy('ordre')->orderBy('nom_complet')
            ->get();

        return view('admin.candidats.index', compact('candidats'));
    }

    public function create(): View
    {
        $talents = Talent::orderBy('nom')->get();
        return view('admin.candidats.form', ['candidat' => new Candidat, 'talents' => $talents]);
    }

    public function store(CandidatRequest $request): RedirectResponse
    {
        $paths = $this->photos->store($request->file('photo'));

        $candidat = Candidat::query()->create([
            ...$request->safe()->except('photo'),
            ...$paths,
        ]);

        AdminLogger::log('candidat.create', $candidat->nom_complet);
        $this->resultats->clearCache();

        return redirect()->route('admin.candidats.index')->with('status', 'Candidat ajouté.');
    }

    public function edit(Candidat $candidat): View
    {
        $talents = Talent::orderBy('nom')->get();
        return view('admin.candidats.form', compact('candidat', 'talents'));
    }

    public function update(CandidatRequest $request, Candidat $candidat): RedirectResponse
    {
        $data = $request->safe()->except('photo');

        if ($request->hasFile('photo')) {
            $paths = $this->photos->store($request->file('photo'), $candidat->photo, $candidat->photo_thumb);
            $data = array_merge($data, $paths);
        }

        $candidat->update($data);
        AdminLogger::log('candidat.update', $candidat->nom_complet);
        $this->resultats->clearCache();

        return redirect()->route('admin.candidats.index')->with('status', 'Candidat mis à jour.');
    }

    public function destroy(Candidat $candidat): RedirectResponse
    {
        AdminLogger::log('candidat.delete', $candidat->nom_complet);
        $this->photos->deletePhotos($candidat->photo, $candidat->photo_thumb);
        $candidat->delete();
        $this->resultats->clearCache();

        return back()->with('status', 'Candidat supprimé.');
    }

    public function toggleActive(Candidat $candidat): RedirectResponse
    {
        $candidat->update(['is_active' => ! $candidat->is_active]);
        AdminLogger::log('candidat.toggle', $candidat->nom_complet.' '.($candidat->is_active ? 'activé' : 'désactivé'));
        $this->resultats->clearCache();

        return back()->with('status', 'Statut du candidat mis à jour.');
    }

    public function preview(CandidatRequest $request): View
    {
        $talent = Talent::find($request->input('talent_id'));
        $candidat = new Candidat($request->only('nom_complet', 'talent_id', 'slogan', 'bio'));

        return view('admin.candidats.preview', compact('candidat', 'talent', 'request'));
    }
}
