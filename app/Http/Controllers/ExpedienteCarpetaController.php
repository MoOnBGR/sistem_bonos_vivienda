<?php

namespace App\Http\Controllers;

use App\Models\Carpeta;
use App\Models\Expediente;
use Illuminate\Http\Request;

class ExpedienteCarpetaController extends Controller
{
    public function index(Expediente $expediente, ?Carpeta $carpeta = null)
    {
        $expediente->load('cliente', 'funcionario');

        $carpetas = Carpeta::where('id_expediente', $expediente->id_expediente)
            ->where('id_carpeta_padre', $carpeta?->id_carpeta)
            ->orderBy('nombre')
            ->get();

        $documentos = $expediente->documentos()
            ->where('id_carpeta', $carpeta?->id_carpeta)
            ->get();

        $ruta = collect();
        $actual = $carpeta;
        while ($actual) {
            $ruta->prepend($actual);
            $actual = $actual->carpetaPadre;
        }

        return view('expediente.consultar', [
            'expediente' => $expediente,
            'cliente' => $expediente->cliente,
            'carpetaActual' => $carpeta,
            'carpetas' => $carpetas,
            'documentos' => $documentos,
            'ruta' => $ruta,
        ]);
    }

    public function store(Request $request, Expediente $expediente)
    {
        if ($expediente->estado === 'Inactivo') {
            return back()->with('error', 'Este expediente está cerrado. No se pueden crear carpetas.');
        }

        $request->validate([
            'nombre' => 'required|string|max:100',
            'id_carpeta_padre' => 'nullable|exists:carpetas_expedientes,id_carpeta',
        ]);

        $carpeta = Carpeta::create([
            'id_expediente' => $expediente->id_expediente,
            'id_carpeta_padre' => $request->id_carpeta_padre,
            'nombre' => $request->nombre,
        ]);

        \App\Helpers\Historial::registrar(
            'Carpetas',
            'Crear',
            'Se creó la carpeta "' . $carpeta->nombre . '" en el expediente EXP-' . str_pad($expediente->id_expediente, 4, '0', STR_PAD_LEFT),
            $expediente->id_expediente
        );

        return back()->with('success', 'Carpeta creada correctamente.');
    }

    public function edit(Carpeta $carpeta)
    {
        return view('expediente.carpeta-editar', compact('carpeta'));
    }

    public function update(Request $request, Carpeta $carpeta)
    {
        $expediente = Expediente::findOrFail($carpeta->id_expediente);

        if ($expediente->estado === 'Inactivo') {
            return back()->with('error', 'Este expediente está cerrado. No se pueden renombrar carpetas.');
        }

        $request->validate([
            'nombre' => 'required|string|max:100',
        ]);

        $nombreAnterior = $carpeta->nombre;
        $carpeta->update(['nombre' => $request->nombre]);

        \App\Helpers\Historial::registrar(
            'Carpetas',
            'Renombrar',
            'Se renombró la carpeta "' . $nombreAnterior . '" a "' . $request->nombre . '" en el expediente EXP-' . str_pad($carpeta->id_expediente, 4, '0', STR_PAD_LEFT),
            $carpeta->id_expediente
        );

        return redirect()
            ->route('expedientes.carpetas.index', [
                $carpeta->id_expediente,
                $carpeta->id_carpeta_padre,
            ])
            ->with('success', 'Carpeta renombrada correctamente.');
    }

    public function destroy(Carpeta $carpeta)
    {
        $expediente = Expediente::findOrFail($carpeta->id_expediente);

        if ($expediente->estado === 'Inactivo') {
            return back()->with('error', 'Este expediente está cerrado. No se pueden eliminar carpetas.');
        }

        $nombre = $carpeta->nombre;
        $expedienteId = $carpeta->id_expediente;

        $carpeta->delete();

        \App\Helpers\Historial::registrar(
            'Carpetas',
            'Eliminar',
            'Se eliminó la carpeta "' . $nombre . '" del expediente EXP-' . str_pad($expedienteId, 4, '0', STR_PAD_LEFT),
            $expedienteId
        );

        return back()->with('success', 'Carpeta eliminada correctamente.');
    }
}