<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExpedienteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'Id_Cliente'     => ['required', 'exists:clientes,Id_Cliente'],
            'id_funcionario' => ['required', 'exists:users,id'],
            'estado'         => ['sometimes', 'in:En proceso,Completado'],
        ];
    }

    public function messages(): array
    {
        return [
            'Id_Cliente.required'     => 'Debe seleccionar un cliente.',
            'Id_Cliente.exists'       => 'El cliente ingresado no existe.',
            'id_funcionario.required' => 'Debe indicar el funcionario responsable.',
            'id_funcionario.exists'   => 'El funcionario indicado no existe.',
            'estado.in'               => 'El estado debe ser "En proceso" o "Completado".',
        ];
    }
}