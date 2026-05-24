<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CandidatRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'talent_id'     => ['required', 'exists:talents,id'],
            'nom_complet'   => ['required', 'string', 'max:100'],
            'slogan'        => ['nullable', 'string', 'max:200'],
            'bio'           => ['nullable', 'string', 'max:1000'],
            'genre'         => ['nullable', 'in:M,F,autre'],
            'contact_email' => ['nullable', 'email', 'max:150'],
            'ordre'         => ['nullable', 'integer', 'min:0'],
            'is_active'     => ['sometimes', 'boolean'],
            'photo'         => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ];
    }
}
