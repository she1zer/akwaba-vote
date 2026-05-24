<?php

namespace App\Http\Controllers;

use App\Models\Parametre;
use App\Models\Talent;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $parametres = Parametre::current();
        $talents = Talent::query()
            ->withCount('candidats')
            ->orderBy('ordre')
            ->orderBy('nom')
            ->get();

        return view('public.home', compact('parametres', 'talents'));
    }
}
