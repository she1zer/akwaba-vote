<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Candidat;
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
        AdminLogger::log('export.csv', 'Export CSV');

        $rows = Candidat::with('talent')
            ->withCount(['votes as votes_count' => fn ($q) => $q->where('is_valid', true)])
            ->orderBy('talent_id')
            ->get();

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Candidat', 'Talent', 'Votes'], ';');

            foreach ($rows as $row) {
                fputcsv($out, [$row->nom_complet, $row->talent->nom, $row->votes_count], ';');
            }

            fclose($out);
        }, 'akwaba-resultats.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function pdf(): Response
    {
        AdminLogger::log('export.pdf', 'Export PDF');
        $results = $this->resultats->getResults();
        $parametres = \App\Models\Parametre::current();

        $pdf = Pdf::loadView('admin.exports.pdf', compact('results', 'parametres'));

        return $pdf->download('akwaba-resultats.pdf');
    }
}
