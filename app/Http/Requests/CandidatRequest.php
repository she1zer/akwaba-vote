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
        $photoRule = $this->isMethod('post')
            ? ['required', 'image', 'mimes:jpeg,png,webp', 'max:2048']
            : ['nullable', 'image', 'mimes:jpeg,png,webp', 'max:2048'];

        return [
            'talent_id' => ['required', 'integer', 'exists:talents,id'],
            'nom_complet' => ['required', 'string', 'max:120', 'regex:/^[\pL\s\'\-\.]+$/u'],
            'photo' => $photoRule,
        ];
    }
}
