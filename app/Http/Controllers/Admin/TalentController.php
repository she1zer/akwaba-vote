<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\TalentRequest;
use App\Models\Talent;
use App\Services\AdminLogger;
use App\Services\ResultatService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TalentController extends Controller
{
    public function __construct(private ResultatService $resultats) {}

    public function index(): View
    {
        $talents = Talent::withCount('candidats')
            ->withCount(['votes as votes_count' => fn ($q) => $q->where('is_valid', true)->where('is_flagged', false)])
            ->orderBy('ordre')
            ->get();

        return view('admin.talents.index', compact('talents'));
    }

    public function create(): View
    {
        return view('admin.talents.form', ['talent' => new Talent]);
    }

    public function store(TalentRequest $request): RedirectResponse
    {
        $talent = Talent::query()->create($request->validated());
        AdminLogger::log('talent.create', $talent->nom);
        $this->resultats->clearCache();

        return redirect()->route('admin.talents.index')->with('status', 'Talent créé.');
    }

    public function edit(Talent $talent): View
    {
        return view('admin.talents.form', compact('talent'));
    }

    public function update(TalentRequest $request, Talent $talent): RedirectResponse
    {
        $talent->update($request->validated());
        AdminLogger::log('talent.update', $talent->nom);
        $this->resultats->clearCache();

        return redirect()->route('admin.talents.index')->with('status', 'Talent mis à jour.');
    }

    public function destroy(Talent $talent): RedirectResponse
    {
        AdminLogger::log('talent.delete', $talent->nom);
        $talent->delete();
        $this->resultats->clearCache();

        return back()->with('status', 'Talent supprimé.');
    }

    public function reorder(Talent $talent, string $direction): RedirectResponse
    {
        $swap = Talent::where('ordre', $direction === 'up' ? '<' : '>', $talent->ordre)
            ->orderBy('ordre', $direction === 'up' ? 'desc' : 'asc')
            ->first();

        if ($swap) {
            [$talent->ordre, $swap->ordre] = [$swap->ordre, $talent->ordre];
            $talent->save();
            $swap->save();
        }

        return back()->with('status', 'Ordre mis à jour.');
    }
}
