<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TalentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nom'                         => ['required', 'string', 'max:100'],
            'description'                 => ['nullable', 'string', 'max:500'],
            'icone_svg'                   => ['nullable', 'string'],
            'votes_actifs'                => ['sometimes', 'boolean'],
            'ordre'                       => ['nullable', 'integer', 'min:0'],
            'couleur_hex'                 => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'max_votes_par_ip'            => ['nullable', 'integer', 'min:1', 'max:10'],
            'allow_candidature_spontanee' => ['sometimes', 'boolean'],
        ];
    }
}
