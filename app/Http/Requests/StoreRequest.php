<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'min:10', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'mobile_phone' => ['required', 'string', 'max:20'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'min' => 'Campo deve ter no mínimo :min caracteres',
            'required' => 'O campo :attribute é obrigatório',
            'image' => 'O arquivo não é uma imagem válida',
            'mimes' => 'Envie uma imagem JPG, PNG ou WEBP',
            'logo.max' => 'A imagem deve ter no máximo 2 MB',
        ];
    }
}
