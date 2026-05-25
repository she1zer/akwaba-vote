@extends('layouts.admin')

@section('page-title', 'Modération des candidatures')

@section('content')

@if(session('status'))
<div class="mb-4 p-3 card-mafia border-green-700/30 text-green-400 text-sm">{{ session('status') }}</div>
@endif

{{-- En attente --}}
<div class="mb-10">
    <div class="flex items-center justify-between mb-4">
        <h2 class="font-display text-xl">
            ⏳ En attente de validation
            @if($enAttente->count())
                <span class="ml-2 text-sm px-2 py-0.5 rounded bg-mafia-red/20 text-mafia-red-bright">{{ $enAttente->count() }}</span>
            @endif
        </h2>
    </div>

    @forelse($enAttente as $c)
    <div class="card-mafia p-5 mb-4 border-l-4 border-mafia-gold">
        <div class="flex flex-col md:flex-row md:items-start gap-4">
            <div class="flex-1">
                <div class="flex items-start gap-3 mb-2">
                    <div class="w-10 h-10 rounded-full bg-mafia-soft border border-mafia-border flex items-center justify-center text-mafia-gold font-bold text-sm flex-shrink-0">
                        {{ $c->initials() }}
                    </div>
                    <div>
                        <p class="font-semibold text-lg">{{ $c->nom_complet }}</p>
                        @if($c->slogan)
                        <p class="text-mafia-muted text-sm italic">« {{ $c->slogan }} »</p>
                        @endif
                        <p class="text-mafia-muted text-xs mt-1">
                            Talent : <span class="text-mafia-text">{{ $c->talent?->nom }}</span>
                            · Proposé {{ $c->propose_le?->diffForHumans() }}
                            · IP : {{ $c->propose_par_ip }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="flex flex-col gap-2 min-w-[200px]">
                {{-- Valider --}}
                <form method="POST" action="{{ route('admin.moderation.valider', $c) }}">
                    @csrf
                    <button type="submit" class="w-full btn-mafia text-sm bg-green-900/30 border-green-700 text-green-400 hover:bg-green-800/40 min-h-[40px]">
                        ✅ Valider
                    </button>
                </form>

                {{-- Modifier et valider --}}
                <details class="group">
                    <summary class="cursor-pointer text-sm text-mafia-muted hover:text-mafia-gold px-3 py-2 rounded border border-mafia-border hover:border-mafia-gold transition">
                        ✏️ Modifier puis valider
                    </summary>
                    <form method="POST" action="{{ route('admin.moderation.modifier', $c) }}" class="mt-2 space-y-2 p-3 bg-mafia-soft rounded">
                        @csrf
                        <input type="text" name="nom_complet" value="{{ $c->nom_complet }}"
                               class="w-full bg-mafia-card border border-mafia-border rounded px-2 py-1.5 text-sm"
                               placeholder="Nom complet" required>
                        <input type="text" name="slogan" value="{{ $c->slogan }}"
                               class="w-full bg-mafia-card border border-mafia-border rounded px-2 py-1.5 text-sm"
                               placeholder="Slogan (optionnel)">
                        <button type="submit" class="w-full btn-mafia text-sm min-h-[36px]">Sauvegarder et valider</button>
                    </form>
                </details>

                {{-- Rejeter --}}
                <details class="group">
                    <summary class="cursor-pointer text-sm text-red-400 hover:text-red-300 px-3 py-2 rounded border border-red-900/50 hover:border-red-700 transition">
                        ❌ Rejeter
                    </summary>
                    <form method="POST" action="{{ route('admin.moderation.rejeter', $c) }}" class="mt-2 space-y-2 p-3 bg-mafia-soft rounded">
                        @csrf
                        <textarea name="note_admin" rows="2" placeholder="Raison du rejet (optionnel)…"
                                  class="w-full bg-mafia-card border border-mafia-border rounded px-2 py-1.5 text-sm"></textarea>
                        <button type="submit" class="w-full text-sm px-3 py-2 rounded border border-red-700 text-red-400 hover:bg-red-900/20 transition min-h-[36px]">
                            Confirmer le rejet
                        </button>
                    </form>
                </details>

                {{-- Supprimer --}}
                <form method="POST" action="{{ route('admin.moderation.supprimer', $c) }}"
                      onsubmit="return confirm('Supprimer définitivement {{ addslashes($c->nom_complet) }} ?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="w-full text-xs text-mafia-muted hover:text-red-500 px-3 py-1.5 rounded border border-transparent hover:border-red-900/30 transition">
                        🗑 Supprimer
                    </button>
                </form>
            </div>
        </div>
    </div>
    @empty
    <div class="card-mafia p-8 text-center text-mafia-muted">
        <p class="text-2xl mb-2">✨</p>
        <p>Aucune candidature en attente de modération.</p>
    </div>
    @endforelse
</div>

{{-- Rejetés récents --}}
@if($rejetes->count())
<div>
    <h2 class="font-display text-lg mb-4 text-mafia-muted">Candidatures rejetées récentes</h2>
    <div class="card-mafia overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-mafia-soft text-mafia-muted">
                <tr>
                    <th class="p-3 text-left">Nom</th>
                    <th class="p-3 text-left">Talent</th>
                    <th class="p-3 text-left">Note</th>
                    <th class="p-3 text-left">Proposé le</th>
                    <th class="p-3"></th>
                </tr>
            </thead>
            <tbody>
                @foreach($rejetes as $c)
                <tr class="border-t border-mafia-border opacity-60">
                    <td class="p-3">{{ $c->nom_complet }}</td>
                    <td class="p-3 text-mafia-muted">{{ $c->talent?->nom }}</td>
                    <td class="p-3 text-mafia-muted italic text-xs">{{ $c->note_admin ?: '—' }}</td>
                    <td class="p-3 text-mafia-muted">{{ $c->propose_le?->format('d/m H:i') }}</td>
                    <td class="p-3">
                        <form method="POST" action="{{ route('admin.moderation.supprimer', $c) }}"
                              onsubmit="return confirm('Supprimer définitivement ?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-xs text-mafia-muted hover:text-red-500">Supprimer</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif
@endsection
