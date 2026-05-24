<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Candidat;
use App\Models\Vote;
use App\Services\AdminLogger;
use App\Services\ResultatService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    public function __construct(private ResultatService $resultats) {}

    public function csv(): StreamedResponse
    {
        AdminLogger::log('export.csv', 'Export CSV résultats');

        $rows = Candidat::with('talent')
            ->withCount(['votes as votes_count' => fn ($q) => $q->where('is_valid', true)->where('is_flagged', false)])
            ->withCount(['votes as votes_flagges' => fn ($q) => $q->where('is_flagged', true)])
            ->orderBy('talent_id')
            ->get();

        $totalParTalent = $rows->groupBy('talent_id')->map(fn ($g) => $g->sum('votes_count'));

        return response()->streamDownload(function () use ($rows, $totalParTalent) {
            $out = fopen('php://output', 'w');
            // BOM UTF-8 pour Excel
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, ['Candidat', 'Talent', 'Votes valides', '% Talent', 'Votes suspects'], ';');

            foreach ($rows as $row) {
                $total = $totalParTalent[$row->talent_id] ?? 1;
                $pct = $total > 0 ? round(($row->votes_count / $total) * 100, 1) : 0;
                fputcsv($out, [
                    $row->nom_complet,
                    $row->talent->nom,
                    $row->votes_count,
                    $pct.'%',
                    $row->votes_flagges,
                ], ';');
            }

            fclose($out);
        }, 'akwaba-resultats-'.now()->format('Ymd-Hi').'.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function csvVotesBruts(): StreamedResponse
    {
        AdminLogger::log('export.csv_bruts', 'Export CSV votes bruts');

        return response()->streamDownload(function () {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, ['ID', 'Candidat', 'Talent', 'IP', 'Session', 'Score confiance', 'Flagué', 'Date'], ';');

            Vote::with(['candidat', 'talent'])->orderBy('id')->chunk(500, function ($votes) use ($out) {
                foreach ($votes as $v) {
                    fputcsv($out, [
                        $v->id,
                        $v->candidat?->nom_complet,
                        $v->talent?->nom,
                        $v->ip_address,
                        substr($v->session_id, 0, 8).'...',
                        $v->score_confiance,
                        $v->is_flagged ? 'OUI' : 'non',
                        $v->created_at?->format('d/m/Y H:i:s'),
                    ], ';');
                }
            });

            fclose($out);
        }, 'akwaba-votes-bruts-'.now()->format('Ymd-Hi').'.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function pdf(): Response
    {
        AdminLogger::log('export.pdf', 'Export PDF');
        $results = $this->resultats->getResults();
        $parametres = \App\Models\Parametre::current();

        $pdf = Pdf::loadView('admin.exports.pdf', compact('results', 'parametres'));

        return $pdf->download('akwaba-resultats-'.now()->format('Ymd').'.pdf');
    }
}
