<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DocumentoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_expediente' => ['required', 'exists:expedientes,id_expediente'],
            'nombre_doc'    => ['required', 'string', 'max:200'],
            'tipo_doc'      => ['required', 'string', 'max:80'],
            'archivo'       => ['required', 'file', 'mimes:pdf,jpg,jpeg,png,docx', 'max:10240'],
        ];
    }

    public function messages(): array
    {
        return [
            'id_expediente.required' => 'Debe indicar a qué expediente pertenece el documento.',
            'id_expediente.exists'   => 'El expediente indicado no existe.',
            'nombre_doc.required'    => 'Debe ingresar un nombre para el documento.',
            'tipo_doc.required'      => 'Debe indicar el tipo de documento.',
            'archivo.required'       => 'Debe seleccionar un archivo para subir.',
            'archivo.mimes'          => 'Documento no permitido, seleccione otro (formatos válidos: PDF, JPG, PNG, DOCX).',
            'archivo.max'            => 'El archivo no debe superar los 10 MB.',
        ];
    }
}