<?php

declare(strict_types=1);

namespace App\Http\Requests\Apontamento;

use Illuminate\Foundation\Http\FormRequest;

class FinalizarRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'qtd_produzida' => ['required', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'qtd_produzida.required' => 'A quantidade produzida é obrigatória.',
            'qtd_produzida.integer'  => 'A quantidade produzida deve ser um número inteiro.',
            'qtd_produzida.min'      => 'A quantidade produzida não pode ser negativa.',
        ];
    }
}
