<?php

declare(strict_types=1);

namespace App\Http\Requests\Sessao;

use Illuminate\Foundation\Http\FormRequest;

class IniciarSessaoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'maquina_id' => ['required', 'integer', 'exists:maquinas,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'maquina_id.required' => 'O campo máquina é obrigatório.',
            'maquina_id.exists'   => 'Máquina não encontrada.',
        ];
    }
}
