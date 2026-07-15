<?php

namespace Database\Seeders;

use App\Models\DocumentoRequerido;
use Illuminate\Database\Seeder;

class DocumentoRequeridoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $documentos = [
            'Plano certificado',
            'Certificacion catastral',
            'Constancia de deuda',
            'Personeria juridica',
            'Declaracion jurada (estado civil)',
            'Alineamiento del mop',
            'Disponibilidad de agua',
            'Disponibilidad de luz',
            'Uso de suelo',
            'Fotocopia de la cedula',
            'Fotocopia Cedula del vendedor',
            'Recibo con dirección exacta',
            'Constancia salarial',
            'Respaldo de salario',
            'Carta de descargo',
            'Reporte de ingresos de la CCSS',
            'Justificacion de traspaso',
            'Microfil',
            'Certificación civil',
            'Constancia de pensión',
            'Carta calificadora',
            'Convenio de la caja',
            'Informacion posesoria',
            'Compra venta',
            'No segregable',
            'Carta de trabajo anterior',
            'Capitulacion matrimonial',
            'Historico de bienes',
        ];

        foreach ($documentos as $orden => $nombre) {
            DocumentoRequerido::firstOrCreate(
                ['nombre' => $nombre],
                ['orden' => $orden]
            );
        }
    }
}