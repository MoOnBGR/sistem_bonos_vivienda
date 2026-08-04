<?php

namespace App\Http\Controllers;

use App\Models\Documento;
use App\Models\Expediente;
use App\Models\DocumentoRequerido;
use App\Models\Carpeta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentoController extends Controller
{
    public function index()
    {
        $documentos = Documento::with(['expediente.cliente'])
                               ->orderBy('created_at', 'desc')
                               ->paginate(10);
        return view('documentos.index', compact('documentos'));
    }

    public function create(Request $request)
    {
        $expedientes = Expediente::with('cliente')
                                ->orderBy('created_at', 'desc')
                                ->get();

        $expedienteId = $request->query('expediente');
        $cedula = $request->query('cedula');

        return view('documentos.create', compact('expedientes', 'expedienteId', 'cedula'));
    }

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
            return back()->with('warning', 'Ya existe un documento con ese nombre en este expediente.');
        }

        $path = $request->file('archivo')->store('documentos', 'public');
        $estado = auth()->user()->tipo_usuario == 'Funcionario' ? 'Validado' : 'Pendiente';

        Documento::create([
            'id_expediente' => $request->id_expediente,
            'id_funcionario' => auth()->id(),
            'Id_Cliente' => null,
            'nombre_doc' => $request->nombre_doc,
            'tipo_doc' => $request->tipo_doc,
            'ruta_almac' => $path,
            'estado_doc' => $estado,
            'es_duplicado' => $existe,
        ]);

        \App\Helpers\Historial::registrar(
            'Documentos',
            'Subir',
            'Se subió el documento: ' . $request->nombre_doc . ' al expediente #' . $request->id_expediente,
            $request->id_expediente
        );

        $cedula = $request->input('cedula') ?? $request->query('cedula') ?? '';
        return redirect()->route('funcionario.documentos.buscar', ['cedula' => $cedula])
                ->with('success', 'Documento subido exitosamente.');
    }

    public function show($id)
    {
        $documento = Documento::with(['expediente.cliente'])->findOrFail($id);
        return view('documentos.show', compact('documento'));
    }

    public function update(Request $request, $id)
    {
        $documento = Documento::findOrFail($id);
        
        $request->validate([
            'estado_doc' => 'required|in:Validado,Rechazado',
            'motivo_rechazo' => 'required_if:estado_doc,Rechazado|nullable|string|max:500',
        ]);

        $cedula = $request->input('cedula') ?? '';

        if ($request->estado_doc == 'Rechazado') {
            $documento->estado_doc = 'Rechazado';
            $documento->motivo_rechazo = $request->motivo_rechazo;
            $documento->save();

            \App\Helpers\Historial::registrar(
                'Documentos',
                'Rechazar',
                'Se rechazó el documento: ' . $documento->nombre_doc . '. Motivo: ' . $request->motivo_rechazo,
                $documento->id_expediente
            );

            return redirect()->route('funcionario.documentos.buscar', ['cedula' => $cedula])
                            ->with('warning', 'Documento rechazado. El cliente debera corregirlo y subirlo de nuevo.');
        }

        $documento->estado_doc = 'Validado';
        $documento->motivo_rechazo = null;
        $documento->save();

        \App\Helpers\Historial::registrar(
            'Documentos',
            'Validar',
            'Se validó el documento: ' . $documento->nombre_doc,
            $documento->id_expediente
        );

        $this->moverACarpetaAprobados($documento);

        return redirect()->route('funcionario.documentos.buscar', ['cedula' => $cedula])
                        ->with('success', 'Documento validado correctamente.');
    }

    public function destroy($id)
    {
        $documento = Documento::findOrFail($id);
        
        if (Storage::disk('public')->exists($documento->ruta_almac)) {
            Storage::disk('public')->delete($documento->ruta_almac);
        }
        
        $nombre = $documento->nombre_doc;
        $expedienteId = $documento->id_expediente;
        $documento->delete();

        \App\Helpers\Historial::registrar(
            'Documentos',
            'Eliminar',
            'Se eliminó el documento: ' . $nombre . ' del expediente #' . $expedienteId,
            $expedienteId
        );

        return redirect()->back()->with('success', "Documento '{$nombre}' eliminado.");
    }

    public function misDocumentos()
    {
        $cliente = auth()->user()->cliente;
        
        if (!$cliente) {
            return redirect()->route('cliente.datos')
                ->with('error', 'Debes completar tus datos primero.');
        }

        $expediente = Expediente::where('Id_Cliente', $cliente->Id_Cliente)
                                ->where('estado', '!=', 'Cerrado')
                                ->first();

        if (!$expediente) {
            return view('cliente.mis-documentos', [
                'pendientesSubir' => collect(),
                'subidos' => collect(),
                'aceptados' => collect(),
                'rechazados' => collect(),
                'expedienteId' => null
            ]);
        }

        $clienteDocumentosRequeridos = \App\Models\ClienteDocumentoRequerido::where('Id_Cliente', $cliente->Id_Cliente)
                                                                            ->pluck('Id_DocumentoRequerido')
                                                                            ->toArray();
        
        $requeridos = DocumentoRequerido::whereIn('Id_DocumentoRequerido', $clienteDocumentosRequeridos)->get();
        
        $documentosSubidos = Documento::where('id_expediente', $expediente->id_expediente)
                                      ->where('Id_Cliente', $cliente->Id_Cliente)
                                      ->get();
        
        $pendientesSubir = collect();
        $subidos = collect();
        $aceptados = collect();
        $rechazados = $documentosSubidos->where('estado_doc', 'Rechazado');

        $documentosNoRechazados = $documentosSubidos->where('estado_doc', '!=', 'Rechazado');
        
        foreach ($requeridos as $req) {
            $subido = $documentosNoRechazados->where('nombre_doc', $req->nombre)->first();
            
            if ($subido) {
                if ($subido->estado_doc == 'Validado') {
                    $aceptados->push($subido);
                } else {
                    $subidos->push($subido);
                }
            } else {
                $pendientesSubir->push($req);
            }
        }

        return view('cliente.mis-documentos', [
            'pendientesSubir' => $pendientesSubir,
            'subidos' => $subidos,
            'aceptados' => $aceptados,
            'rechazados' => $rechazados,
            'expedienteId' => $expediente->id_expediente
        ]);
    }

    public function subirDocumentoCliente(Request $request)
    {
        $request->validate([
            'id_expediente' => 'required|exists:expedientes,id_expediente',
            'nombre_doc' => 'required|string|max:200',
            'archivo' => 'required|file|max:20480|mimes:pdf',
        ]);

        $rechazadoAnterior = Documento::where('id_expediente', $request->id_expediente)
                                      ->where('nombre_doc', $request->nombre_doc)
                                      ->where('estado_doc', 'Rechazado')
                                      ->first();

        if ($rechazadoAnterior) {
            if (Storage::disk('public')->exists($rechazadoAnterior->ruta_almac)) {
                Storage::disk('public')->delete($rechazadoAnterior->ruta_almac);
            }
            $rechazadoAnterior->delete();
        }

        $existe = Documento::where('id_expediente', $request->id_expediente)
                          ->where('nombre_doc', $request->nombre_doc)
                          ->where('estado_doc', '!=', 'Rechazado')
                          ->exists();

        if ($existe) {
            return back()->with('error', 'Este documento ya fue subido.');
        }

        $cliente = auth()->user()->cliente;
        
        if (!$cliente) {
            return back()->with('error', 'No se encontro el cliente.');
        }

        $path = $request->file('archivo')->store('documentos', 'public');

        Documento::create([
            'id_expediente' => $request->id_expediente,
            'id_funcionario' => null,
            'Id_Cliente' => $cliente->Id_Cliente,
            'nombre_doc' => $request->nombre_doc,
            'tipo_doc' => 'PDF',
            'ruta_almac' => $path,
            'estado_doc' => 'Pendiente',
            'es_duplicado' => false,
        ]);

        \App\Helpers\Historial::registrar(
            'Documentos',
            'Subir',
            'Cliente subió el documento: ' . $request->nombre_doc . ' al expediente #' . $request->id_expediente,
            $request->id_expediente
        );

        return redirect()->route('cliente.documentos')
                        ->with('success', 'Documento subido exitosamente. Queda pendiente de validacion.');
    }

    public function documentosFuncionario($idCliente)
    {
        $cliente = \App\Models\Cliente::findOrFail($idCliente);
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

    public function requerirDocumento(Request $request)
    {
        try {
            $request->validate([
                'id_expediente' => 'required|exists:expedientes,id_expediente',
                'documentos' => 'required|array|min:1',
                'documentos.*' => 'string|max:100'
            ]);

            $expediente = Expediente::find($request->id_expediente);
            
            if (!$expediente) {
                return redirect()->back()->with('error', 'Expediente no encontrado.');
            }

            $clienteId = $expediente->Id_Cliente;

            foreach ($request->documentos as $nombre_documento) {
                $docRequerido = DocumentoRequerido::firstOrCreate([
                    'nombre' => $nombre_documento
                ]);

                \App\Models\ClienteDocumentoRequerido::firstOrCreate([
                    'Id_Cliente' => $clienteId,
                    'Id_DocumentoRequerido' => $docRequerido->Id_DocumentoRequerido,
                ]);
            }

            $cantidad = count($request->documentos);

            \App\Helpers\Historial::registrar(
                'Documentos',
                'Requerir',
                'Se requirieron ' . $cantidad . ' documento(s) al cliente del expediente #' . $request->id_expediente,
                $request->id_expediente
            );

            $cedula = $expediente->cliente->identificacion ?? '';
            return redirect()->route('funcionario.documentos.buscar', ['cedula' => $cedula])
                            ->with('success', "Se requirieron $cantidad documento(s) al cliente.");

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al requerir los documentos: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function buscarCliente(Request $request)
    {
        $cedula = $request->input('cedula');
        $cliente = null;
        $expediente = null;
        
        $pendientesAceptar = collect();
        $pendientesSubir = collect();
        $subidosEmpresa = collect();
        $aceptados = collect();
        $rechazados = collect();

        if ($cedula) {
            $cliente = \App\Models\Cliente::where('identificacion', $cedula)->first();
            
            if ($cliente) {
                $expediente = Expediente::where('Id_Cliente', $cliente->Id_Cliente)
                                        ->where('estado', '!=', 'Cerrado')
                                        ->first();
                
                if ($expediente) {
                    $todosDocumentos = Documento::where('id_expediente', $expediente->id_expediente)
                                                ->orderBy('created_at', 'desc')
                                                ->get();
                    
                    $pendientesAceptar = $todosDocumentos->where('estado_doc', 'Pendiente')->whereNotNull('Id_Cliente');
                    $subidosEmpresa = $todosDocumentos->where('estado_doc', 'Validado')->whereNull('Id_Cliente');
                    $aceptados = $todosDocumentos->where('estado_doc', 'Validado')->whereNotNull('Id_Cliente');
                    $rechazados = $todosDocumentos->where('estado_doc', 'Rechazado');
                    
                    $clienteDocumentosRequeridos = \App\Models\ClienteDocumentoRequerido::where('Id_Cliente', $cliente->Id_Cliente)
                                                                                        ->pluck('Id_DocumentoRequerido')
                                                                                        ->toArray();
                    
                    $requeridos = DocumentoRequerido::whereIn('Id_DocumentoRequerido', $clienteDocumentosRequeridos)->get();
                    $pendientesSubir = collect();

                    foreach ($requeridos as $req) {
                        $yaSubido = $todosDocumentos->where('nombre_doc', $req->nombre)
                                                    ->where('estado_doc', '!=', 'Rechazado')
                                                    ->first();
                        
                        if (!$yaSubido) {
                            $rechazado = $todosDocumentos->where('nombre_doc', $req->nombre)
                                                        ->where('estado_doc', 'Rechazado')
                                                        ->first();
                            
                            if ($rechazado) {
                                $req->documento_rechazado_id = $rechazado->id_documento;
                                $req->ruta_almac = $rechazado->ruta_almac;
                                $req->motivo_rechazo = $rechazado->motivo_rechazo;
                                $req->tipo_doc = $rechazado->tipo_doc ?? 'Pendiente';
                            } else {
                                $req->documento_rechazado_id = null;
                                $req->ruta_almac = null;
                                $req->motivo_rechazo = null;
                                $req->tipo_doc = 'Pendiente';
                            }
                            $pendientesSubir->push($req);
                        }
                    }
                }
            }
        }

        return view('documentos.documentos-cliente', compact(
            'cliente', 
            'expediente',
            'pendientesAceptar',
            'pendientesSubir',
            'subidosEmpresa',
            'aceptados',
            'rechazados'
        ));
    }

    public function subirDocumentoEmpresa($id_expediente, $carpetaId = null)
    {
        $expediente = Expediente::findOrFail($id_expediente);
        return view('documentos.subir-empresa', compact('expediente', 'carpetaId'));
    }

    public function mostrarRequerir($id_expediente)
    {
        $expediente = Expediente::with('cliente')->findOrFail($id_expediente);
        $cedula = $expediente->cliente->identificacion ?? '';
        return view('documentos.requerir-documento', compact('expediente', 'cedula'));
    }

    public function storeDocumentoEmpresa(Request $request)
    {
        try {
            $validated = $request->validate([
                'id_expediente' => 'required|integer|exists:expedientes,id_expediente',
                'nombre_doc' => 'required|string|max:200',
                'tipo_doc' => 'required|string|max:80',
                'archivo' => 'required|file|max:20480|mimes:pdf',
                'id_carpeta' => 'nullable|exists:carpetas_expedientes,id_carpeta',
            ]);

            $existe = Documento::where('id_expediente', $request->id_expediente)
                              ->where('nombre_doc', $request->nombre_doc)
                              ->exists();

            if ($existe) {
                return redirect()->back()->withInput()
                    ->with('warning', 'Ya existe un documento con ese nombre en este expediente.');
            }

            $path = $request->file('archivo')->store('documentos', 'public');

            $documento = Documento::create([
                'id_expediente' => $request->id_expediente,
                'id_funcionario' => auth()->id(),
                'Id_Cliente' => null,
                'nombre_doc' => $request->nombre_doc,
                'tipo_doc' => $request->tipo_doc,
                'ruta_almac' => $path,
                'estado_doc' => 'Validado',
                'es_duplicado' => false,
                'id_carpeta' => $request->id_carpeta ?? null,
            ]);

            \App\Helpers\Historial::registrar(
                'Documentos',
                'Subir Empresa',
                'Funcionario subió documento de empresa: ' . $request->nombre_doc . ' al expediente #' . $request->id_expediente,
                $request->id_expediente
            );

            $cedula = $request->query('cedula') ?? '';
            return redirect()->route('funcionario.documentos.buscar', ['cedula' => $cedula])
                            ->with('success', 'Documento de empresa subido exitosamente.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al subir el documento: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function eliminarDocumentoCliente($id)
    {
        $documento = Documento::findOrFail($id);
        
        $cliente = auth()->user()->cliente;
        if ($documento->Id_Cliente != $cliente->Id_Cliente) {
            abort(403, 'No tienes permiso para eliminar este documento.');
        }
        
        if ($documento->estado_doc == 'Validado') {
            return back()->with('error', 'No puedes eliminar un documento que ya fue validado.');
        }
        
        if (Storage::disk('public')->exists($documento->ruta_almac)) {
            Storage::disk('public')->delete($documento->ruta_almac);
        }
        
        $nombre = $documento->nombre_doc;
        $expedienteId = $documento->id_expediente;
        $documento->delete();

        \App\Helpers\Historial::registrar(
            'Documentos',
            'Eliminar',
            'Cliente eliminó el documento: ' . $nombre . ' del expediente #' . $expedienteId,
            $expedienteId
        );

        return redirect()->route('cliente.documentos')
                        ->with('success', "Documento '{$nombre}' eliminado. Vuelve a subirlo cuando estes listo.");
    }

    public function eliminarSolicitud($id)
    {
        $solicitud = DocumentoRequerido::findOrFail($id);
        $nombre = $solicitud->nombre;

        \App\Models\ClienteDocumentoRequerido::where('Id_DocumentoRequerido', $id)->delete();
        $solicitud->delete();

        \App\Helpers\Historial::registrar(
            'Documentos',
            'Eliminar Solicitud',
            'Se eliminó la solicitud de documento: ' . $nombre
        );

        return redirect()->back()->with('success', 'Solicitud de documento eliminada correctamente.');
    }

    // ==========================================
    // SUBIR DOCUMENTO A CARPETA ESPECÍFICA
    // ==========================================
    public function subirDocumentoCarpeta(Request $request, $expedienteId, $carpetaId)
    {
        $request->validate([
            'archivo' => 'required|file|max:20480|mimes:pdf',
            'nombre_doc' => 'required|string|max:200',
            'tipo_doc' => 'required|string|max:80'
        ]);

        $expediente = Expediente::findOrFail($expedienteId);
        
        $existe = Documento::where('id_expediente', $expedienteId)
                          ->where('nombre_doc', $request->nombre_doc)
                          ->where('id_carpeta', $carpetaId)
                          ->exists();

        if ($existe) {
            return back()->with('warning', 'Ya existe un documento con ese nombre en esta carpeta.');
        }

        $path = $request->file('archivo')->store('documentos', 'public');

        Documento::create([
            'id_expediente' => $expedienteId,
            'id_funcionario' => auth()->id(),
            'Id_Cliente' => null,
            'nombre_doc' => $request->nombre_doc,
            'tipo_doc' => $request->tipo_doc,
            'ruta_almac' => $path,
            'estado_doc' => 'Validado',
            'es_duplicado' => false,
            'id_carpeta' => $carpetaId,
        ]);

        \App\Helpers\Historial::registrar(
            'Documentos',
            'Subir Carpeta',
            'Se subió el documento: ' . $request->nombre_doc . ' a la carpeta del expediente #' . $expedienteId,
            $expedienteId
        );

        return redirect()->back()->with('success', 'Documento subido correctamente a la carpeta.');
    }

    // ==========================================
    // MOVER DOCUMENTO A OTRA CARPETA (Método original)
    // ==========================================
    public function moverDocumento(Request $request, $id)
    {
        $request->validate([
            'id_carpeta_destino' => 'nullable|exists:carpetas_expedientes,id_carpeta',
        ]);

        $documento = Documento::findOrFail($id);
        $carpetaOrigen = $documento->id_carpeta;
        $documento->id_carpeta = $request->id_carpeta_destino;
        $documento->save();

        $nombreCarpetaOrigen = $carpetaOrigen ? Carpeta::find($carpetaOrigen)?->nombre : 'Raíz';
        $nombreCarpetaDestino = $request->id_carpeta_destino ? Carpeta::find($request->id_carpeta_destino)?->nombre : 'Raíz';

        \App\Helpers\Historial::registrar(
            'Documentos',
            'Mover',
            'Se movió el documento: ' . $documento->nombre_doc . ' de "' . $nombreCarpetaOrigen . '" a "' . $nombreCarpetaDestino . '"',
            $documento->id_expediente
        );

        return redirect()->back()->with('success', 'Documento movido correctamente.');
    }

    // ==========================================
    // COPIAR DOCUMENTO (guardar en sesión)
    // ==========================================
    public function copiarDocumento(Request $request, $id)
    {
        $documento = Documento::findOrFail($id);
        
        // Guardar en sesión el documento a mover
        session(['documento_a_mover' => $id]);
        
        return response()->json([
            'success' => true,
            'message' => 'Documento copiado para mover',
            'documento' => $documento->nombre_doc
        ]);
    }

    // ==========================================
    // PEGAR DOCUMENTO (mover a carpeta actual)
    // ==========================================
    public function pegarDocumento(Request $request, $expedienteId)
    {
        $documentoId = session('documento_a_mover');
        
        if (!$documentoId) {
            return response()->json([
                'error' => 'No hay documento para pegar'
            ], 400);
        }
        
        $documento = Documento::findOrFail($documentoId);
        
        // Validar que el documento pertenezca al expediente
        if ($documento->id_expediente != $expedienteId) {
            return response()->json([
                'error' => 'El documento no pertenece a este expediente'
            ], 400);
        }
        
        // Obtener la carpeta actual desde la vista
        $carpetaId = $request->input('id_carpeta_destino');
        
        // Si no se especifica carpeta, se mueve a la raíz (null)
        $carpetaDestino = $carpetaId ?: null;
        
        // Guardar datos para historial
        $carpetaOrigen = $documento->id_carpeta;
        $nombreCarpetaOrigen = $carpetaOrigen ? 
            \App\Models\Carpeta::find($carpetaOrigen)?->nombre : 'Raíz';
        $nombreCarpetaDestino = $carpetaDestino ? 
            \App\Models\Carpeta::find($carpetaDestino)?->nombre : 'Raíz';
        
        // Mover el documento
        $documento->id_carpeta = $carpetaDestino;
        $documento->save();
        
        // Limpiar sesión
        session()->forget('documento_a_mover');
        
        \App\Helpers\Historial::registrar(
            'Documentos',
            'Mover (Copiar/Pegar)',
            'Se movió el documento: ' . $documento->nombre_doc . 
            ' de "' . $nombreCarpetaOrigen . '" a "' . $nombreCarpetaDestino . '"',
            $documento->id_expediente
        );
        
        return response()->json([
            'success' => true,
            'message' => 'Documento movido correctamente',
            'documento' => $documento->nombre_doc
        ]);
    }

    // ==========================================
    // CANCELAR MOVIMIENTO
    // ==========================================
    public function cancelarMovimiento()
    {
        session()->forget('documento_a_mover');
        return response()->json([
            'success' => true,
            'message' => 'Movimiento cancelado'
        ]);
    }

    private function moverACarpetaAprobados($documento)
    {
        if (class_exists(\App\Models\Carpeta::class)) {
            $carpeta = \App\Models\Carpeta::firstOrCreate([
                'id_expediente' => $documento->id_expediente,
                'nombre' => 'Documentos Aprobados',
            ]);

            $documento->id_carpeta = $carpeta->id_carpeta ?? null;
            $documento->save();
        }
    }
}