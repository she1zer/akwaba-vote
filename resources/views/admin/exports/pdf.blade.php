<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #111; }
        h1 { color: #cc0000; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background: #f5f5f5; }
    </style>
</head>
<body>
    <h1>{{ $parametres->nom_evenement }} — Résultats</h1>
    <p>Généré le {{ now()->format('d/m/Y H:i') }}</p>
    @foreach($results as $block)
        <h2>{{ $block['nom'] }}</h2>
        <table>
            <thead><tr><th>Candidat</th><th>Votes</th><th>%</th></tr></thead>
            <tbody>
                @foreach($block['candidats'] as $c)
                <tr><td>{{ $c['nom'] }}</td><td>{{ $c['votes'] }}</td><td>{{ $c['percent'] }}%</td></tr>
                @endforeach
            </tbody>
        </table>
    @endforeach
</body>
</html>
