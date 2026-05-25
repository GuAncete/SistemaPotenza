<?php

declare(strict_types=1);

namespace App\Http\Requests\Apontamento;

use Illuminate\Foundation\Http\FormRequest;

class BiparRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cod_peca'   => ['required', 'string', 'max:50'],
            'ordem_lote' => ['required', 'string', 'max:50'],
            'qtd_peca'   => ['required', 'integer', 'min:1'],
            'pilha'      => ['required', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'cod_peca.required'   => 'O código da peça é obrigatório.',
            'ordem_lote.required' => 'O número do lote é obrigatório.',
            'qtd_peca.required'   => 'A quantidade de peças é obrigatória.',
            'qtd_peca.min'        => 'A quantidade deve ser maior que zero.',
            'pilha.required'      => 'O número da pilha é obrigatório.',
            'pilha.min'           => 'O número da pilha deve ser maior que zero.',
        ];
    }
}
