<?php

namespace App\Http\Controllers;

use App\Models\Documento;
use App\Models\Expediente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentoController extends Controller
{
    // ==========================================
    // 1. LISTAR DOCUMENTOS (INDEX)
    // ==========================================
    public function index()
    {
        $documentos = Documento::with(['expediente.cliente'])
                               ->orderBy('created_at', 'desc')
                               ->paginate(10);
        return view('documentos.index', compact('documentos'));
    }

    // ==========================================
    // 2. MOSTRAR FORMULARIO PARA SUBIR (CREATE)
    // ==========================================
    public function create()
    {
        return view('documentos.create');
    }

    // ==========================================
    // 3. GUARDAR DOCUMENTO (STORE)
    // ==========================================
    public function store(Request $request)
    {
        $request->validate([
            'id_expediente' => 'required|exists:expedientes,id_expediente',
            'archivo' => 'required|file|max:20480|mimes:pdf',
            'nombre_doc' => 'required|string|max:200',
            'tipo_doc' => 'required|string|max:80'
        ]);

        $existe = Documento::where('id_expediente', $request->id_expediente)
                          ->where('nombre_doc', $request->nombre_doc)
                          ->exists();

        if ($existe) {
            return back()->with('warning', 'Ya existe un documento con ese nombre.');
        }

        $path = $request->file('archivo')->store('documentos', 'public');

        Documento::create([
            'id_expediente' => $request->id_expediente,
            'id_funcionario' => auth()->id(),
            'Id_Cliente' => null,
            'nombre_doc' => $request->nombre_doc,
            'tipo_doc' => $request->tipo_doc,
            'ruta_almac' => $path,
            'estado_doc' => 'Pendiente',
            'es_duplicado' => $existe,
        ]);

        return redirect()->route('documentos.index')
                        ->with('success', 'Documento subido exitosamente.');
    }

    // ==========================================
    // 4. VER UN DOCUMENTO (SHOW)
    // ==========================================
    public function show($id)
    {
        $documento = Documento::with(['expediente.cliente'])->findOrFail($id);
        return view('documentos.show', compact('documento'));
    }

    // ==========================================
    // 5. VALIDAR DOCUMENTO (UPDATE)
    // ==========================================
    public function update(Request $request, $id)
    {
        $documento = Documento::findOrFail($id);
        
        $request->validate([
            'estado_doc' => 'required|in:Validado,Rechazado',
        ]);

        $documento->estado_doc = $request->estado_doc;
        $documento->save();

        return redirect()->route('documentos.index')
                        ->with('success', "Documento {$request->estado_doc} correctamente.");
    }

    // ==========================================
    // 6. ELIMINAR DOCUMENTO (DESTROY)
    // ==========================================
    public function destroy($id)
    {
        $documento = Documento::findOrFail($id);
        
        if (Storage::disk('public')->exists($documento->ruta_almac)) {
            Storage::disk('public')->delete($documento->ruta_almac);
        }
        
        $nombre = $documento->nombre_doc;
        $documento->delete();

        return redirect()->route('documentos.index')
                        ->with('success', "Documento '{$nombre}' eliminado.");
    }

    // ==========================================
    // 7. CLIENTE - VER SUS DOCUMENTOS
    // ==========================================
    public function misDocumentos()
    {
        $cliente = auth()->user()->cliente;
        
        if (!$cliente) {
            return redirect()->route('cliente.datos')
                ->with('error', 'Debes completar tus datos primero.');
        }

        // CORREGIDO: Id_Cliente con C mayúscula
        $expediente = Expediente::where('Id_Cliente', $cliente->Id_Cliente)
                                ->where('estado', '!=', 'Cerrado')
                                ->first();

        if (!$expediente) {
            return view('cliente.mis-documentos', [
                'documentosRequeridos' => [],
                'porcentaje' => 0,
                'subidos' => 0,
                'faltantes' => 0,
                'todosSubidos' => false,
                'expedienteId' => null
            ]);
        }

        $requeridos = \App\Models\DocumentoRequerido::where('id_expediente', $expediente->id_expediente)->get();
        
        $documentos = [];
        $subidos = 0;
        $total = $requeridos->count();

        foreach ($requeridos as $req) {
            $subido = Documento::where('id_expediente', $expediente->id_expediente)
                               ->where('tipo_doc', $req->tipo_documento)
                               ->first();
            
            $documentos[] = [
                'id' => $subido->id_documento ?? null,
                'nombre' => $req->tipo_documento,
                'subido' => !is_null($subido),
                'fecha_subida' => $subido->fecha_subida ?? null,
            ];
            
            if ($subido) {
                $subidos++;
            }
        }

        $faltantes = $total - $subidos;
        $porcentaje = $total > 0 ? round(($subidos / $total) * 100) : 0;
        $todosSubidos = $faltantes == 0 && $total > 0;

        return view('cliente.mis-documentos', [
            'documentosRequeridos' => $documentos,
            'porcentaje' => $porcentaje,
            'subidos' => $subidos,
            'faltantes' => $faltantes,
            'todosSubidos' => $todosSubidos,
            'expedienteId' => $expediente->id_expediente
        ]);
    }

    // ==========================================
    // 8. CLIENTE - SUBIR DOCUMENTO
    // ==========================================
    public function subirDocumentoCliente(Request $request)
    {
        $request->validate([
            'id_expediente' => 'required|exists:expedientes,id_expediente',
            'tipo_documento' => 'required|string',
            'archivo' => 'required|file|max:20480|mimes:pdf',
        ]);

        $existe = Documento::where('id_expediente', $request->id_expediente)
                          ->where('tipo_doc', $request->tipo_documento)
                          ->exists();

        if ($existe) {
            return back()->with('error', 'Este documento ya fue subido.');
        }

        $path = $request->file('archivo')->store('documentos', 'public');

        // CORREGIDO: Id_Cliente con C mayúscula
        Documento::create([
            'id_expediente' => $request->id_expediente,
            'id_funcionario' => null,
            'Id_Cliente' => auth()->user()->cliente->Id_Cliente,
            'nombre_doc' => $request->tipo_documento,
            'tipo_doc' => $request->tipo_documento,
            'ruta_almac' => $path,
            'estado_doc' => 'Pendiente',
            'es_duplicado' => false,
        ]);

        return redirect()->route('cliente.documentos')
                        ->with('success', 'Documento subido exitosamente.');
    }

    // ==========================================
    // 9. FUNCIONARIO - VER DOCUMENTOS DE CLIENTE
    // ==========================================
    public function documentosFuncionario($idCliente)
    {
        $cliente = \App\Models\Cliente::findOrFail($idCliente);
        
        // CORREGIDO: Id_Cliente con C mayúscula
        $expediente = Expediente::where('Id_Cliente', $idCliente)->first();

        if (!$expediente) {
            return redirect()->back()->with('error', 'Este cliente no tiene expediente.');
        }

        $documentos = Documento::where('id_expediente', $expediente->id_expediente)
                              ->orderBy('created_at', 'desc')
                              ->get();

        return view('funcionario.documentos-cliente', [
            'cliente' => $cliente,
            'expediente' => $expediente,
            'documentos' => $documentos
        ]);
    }

    // ==========================================
    // 10. FUNCIONARIO - REQUERIR DOCUMENTO
    // ==========================================
    public function requerirDocumento(Request $request)
    {
        $request->validate([
            'id_expediente' => 'required|exists:expedientes,id_expediente',
            'tipo_documento' => 'required|string|max:100',
            'descripcion' => 'nullable|string'
        ]);

        \App\Models\DocumentoRequerido::create([
            'id_expediente' => $request->id_expediente,
            'tipo_documento' => $request->tipo_documento,
            'descripcion' => $request->descripcion,
            'fecha_requerido' => now()
        ]);

        return redirect()->back()->with('success', 'Documento requerido al cliente.');
    }
}