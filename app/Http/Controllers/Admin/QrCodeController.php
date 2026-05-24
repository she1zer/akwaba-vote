<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Parametre;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use chillerlan\QRCode\Output\QROutputInterface;
use chillerlan\QRCode\Output\QRMarkupSVG;
use Illuminate\View\View;

class QrCodeController extends Controller
{
    public function show(): View
    {
        $parametres = Parametre::current();
        $url = url('/');

        $moduleValues = [];
        foreach (QROutputInterface::DEFAULT_MODULE_VALUES as $type => $isDark) {
            $moduleValues[$type] = $isDark ? '#cc0000' : '#0a0a0a';
        }

        $options = new QROptions([
            'outputInterface' => QRMarkupSVG::class,
            'outputBase64' => false,
            'scale' => 8,
            'cssClass' => 'akwa-qr-modules',
            'svgAddXmlHeader' => false,
            'moduleValues' => $moduleValues,
        ]);

        $qrSvg = (new QRCode($options))->render($url);

        return view('admin.qrcode', compact('parametres', 'url', 'qrSvg'));
    }
}
