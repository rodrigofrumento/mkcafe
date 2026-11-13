<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** Converte "1.234,56" (formato brasileiro) para "1234.56" antes de validar. */
    protected function prepareForValidation(): void
    {
        $price = (string) $this->input('price', '');

        if (str_contains($price, ',')) {
            $this->merge(['price' => str_replace(['.', ','], ['', '.'], $price)]);
        }
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'min:30', 'max:255'],
            'body' => ['required', 'string'],
            'price' => ['required', 'numeric', 'min:0', 'max:99999999'],
            'categories' => ['nullable', 'array'],
            'categories.*' => ['integer', 'exists:categories,id'],
            'photos' => ['nullable', 'array', 'max:10'],
            'photos.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'Este campo é obrigatório',
            'min' => 'O campo deve conter no mínimo :min caracteres',
            'numeric' => 'Informe um preço válido',
            'image' => 'O arquivo não é uma imagem válida',
            'mimes' => 'Envie imagens JPG, PNG ou WEBP',
            'photos.*.max' => 'Cada imagem deve ter no máximo 4 MB',
        ];
    }
}
