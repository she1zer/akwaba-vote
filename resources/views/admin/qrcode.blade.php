@extends('layouts.admin')

@section('page-title', 'QR Code événement')

@section('content')
<div class="max-w-lg mx-auto text-center">
    <div class="glow-pulse rounded-2xl p-8 border-2 border-mafia-red bg-mafia-card inline-block print-only:block">
        <svg viewBox="0 0 400 420" class="w-full max-w-sm mx-auto" aria-label="Cadre QR Code événement" role="img">
            <rect x="10" y="10" width="380" height="400" fill="#0a0a0a" stroke="#cc0000" stroke-width="3" rx="8"/>
            <path d="M10 10 L60 10 M340 10 L390 10 M10 410 L60 410 M340 410 L390 410" stroke="#b8960c" stroke-width="4"/>
            <foreignObject x="50" y="40" width="300" height="300">
                <div xmlns="http://www.w3.org/1999/xhtml" class="bg-black p-4 flex items-center justify-center" style="background:#0a0a0a">
                    <div id="qr-svg-wrapper">{!! $qrSvg !!}</div>
                </div>
            </foreignObject>
            <text x="200" y="385" text-anchor="middle" fill="#f0f0f0" font-family="Playfair Display" font-size="18">{{ $parametres->nom_evenement }}</text>
        </svg>
    </div>

    <p class="text-mafia-muted text-sm mt-4 break-all">{{ $url }}</p>

    <div class="flex flex-wrap gap-3 justify-center mt-6 no-print">
        <button type="button" id="download-qr" class="btn-mafia text-sm">Télécharger PNG</button>
        <button type="button" onclick="window.print()" class="btn-outline-mafia text-sm">Imprimer</button>
    </div>
</div>
@endsection
