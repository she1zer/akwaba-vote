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
            'nom' => ['required', 'string', 'max:120'],
            'icone_svg' => ['nullable', 'string', 'max:5000'],
            'votes_actifs' => ['sometimes', 'boolean'],
            'ordre' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
