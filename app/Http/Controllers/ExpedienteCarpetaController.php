<?php

namespace App\Http\Controllers;

use App\Models\Carpeta;
use App\Models\Expediente;
use Illuminate\Http\Request;

class ExpedienteCarpetaController extends Controller
{
    /**
     * Muestra el contenido de un expediente: si $carpeta es null, muestra la raíz
     * (subcarpetas y documentos sin carpeta). Si $carpeta viene, muestra su contenido.
     */
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

    /**
     * Crea una nueva carpeta dentro del expediente.
     */
    public function store(Request $request, Expediente $expediente)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'id_carpeta_padre' => 'nullable|exists:carpetas_expedientes,id_carpeta',
        ]);

        Carpeta::create([
            'id_expediente' => $expediente->id_expediente,
            'id_carpeta_padre' => $request->id_carpeta_padre,
            'nombre' => $request->nombre,
        ]);

        return back()->with('success', 'Carpeta creada correctamente.');
    }

    /**
     * Renombra una carpeta del expediente.
     */
    public function update(Request $request, Carpeta $carpeta)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
        ]);

        $carpeta->update(['nombre' => $request->nombre]);

        return back()->with('success', 'Carpeta renombrada correctamente.');
    }

    /**
     * Elimina una carpeta del expediente.
     */
    public function destroy(Carpeta $carpeta)
    {
        $carpeta->delete();

        return back()->with('success', 'Carpeta eliminada correctamente.');
    }
}