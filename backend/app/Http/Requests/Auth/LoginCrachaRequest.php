<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginCrachaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'matricula' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'matricula.required' => 'Bipe o crachá para continuar.',
        ];
    }
}
