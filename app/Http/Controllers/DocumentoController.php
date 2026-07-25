<?php

namespace App\Http\Controllers;

use App\Models\Documento;
use App\Models\Expediente;
use App\Models\DocumentoRequerido;
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
        $expedientes = Expediente::with('cliente')
                                 ->orderBy('created_at', 'desc')
                                 ->get();

        return view('documentos.create', compact('expedientes'));
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
        ], [
            'id_expediente.required' => 'El campo ID del expediente es obligatorio.',
            'id_expediente.exists' => 'El expediente seleccionado no existe.',
            'archivo.required' => 'Debes seleccionar un archivo PDF.',
            'archivo.max' => 'El archivo no debe superar los 20 MB.',
            'archivo.mimes' => 'Solo se permiten archivos en formato PDF.',
            'nombre_doc.required' => 'El campo Nombre del Documento es obligatorio.',
            'nombre_doc.max' => 'El nombre del documento no debe exceder los 200 caracteres.',
            'tipo_doc.required' => 'El campo Tipo de Documento es obligatorio.',
            'tipo_doc.max' => 'El tipo de documento no debe exceder los 80 caracteres.'
        ]);

        $existe = Documento::where('id_expediente', $request->id_expediente)
                          ->where('nombre_doc', $request->nombre_doc)
                          ->exists();

        if ($existe) {
            return back()->with('warning', '⚠️ Ya existe un documento con ese nombre en este expediente.');
        }

        $path = $request->file('archivo')->store('documentos', 'public');

        // Si es funcionario, el documento queda Validado automáticamente
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

        return redirect()->route('documentos.index')
                        ->with('success', '✅ Documento subido exitosamente.');
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
        ], [
            'estado_doc.required' => 'El estado del documento es obligatorio.',
            'estado_doc.in' => 'El estado seleccionado no es válido.'
        ]);

        if ($request->estado_doc == 'Rechazado') {
            $documento->estado_doc = 'Pendiente';
            $documento->save();
            return redirect()->back()->with('warning', '⚠️ Documento rechazado. El cliente deberá corregirlo y subirlo de nuevo.');
        }

        $documento->estado_doc = 'Validado';
        $documento->save();

        $this->moverACarpetaAprobados($documento);

        return redirect()->route('documentos.index')
                        ->with('success', '✅ Documento validado correctamente.');
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
                        ->with('success', "✅ Documento '{$nombre}' eliminado.");
    }

    // ==========================================
// 7. CLIENTE - VER SUS DOCUMENTOS
// ==========================================
public function misDocumentos()
{
    $cliente = auth()->user()->cliente;
    
    if (!$cliente) {
        return redirect()->route('cliente.datos')
            ->with('error', '❌ Debes completar tus datos primero.');
    }

    $expediente = Expediente::where('Id_Cliente', $cliente->Id_Cliente)
                            ->where('estado', '!=', 'Cerrado')
                            ->first();

    if (!$expediente) {
        return view('cliente.mis-documentos', [
            'pendientesSubir' => collect(),
            'subidos' => collect(),
            'aceptados' => collect(),
            'expedienteId' => null
        ]);
    }

    // 1. Obtener documentos requeridos para este cliente
    $clienteDocumentosRequeridos = \App\Models\ClienteDocumentoRequerido::where('Id_Cliente', $cliente->Id_Cliente)
                                                                        ->pluck('Id_DocumentoRequerido')
                                                                        ->toArray();
    
    $requeridos = DocumentoRequerido::whereIn('Id_DocumentoRequerido', $clienteDocumentosRequeridos)->get();
    
    // 2. Obtener todos los documentos subidos por el cliente
    $documentosSubidos = Documento::where('id_expediente', $expediente->id_expediente)
                                  ->where('Id_Cliente', $cliente->Id_Cliente)
                                  ->get();
    
    // 3. Clasificar documentos
    $pendientesSubir = collect();
    $subidos = collect();
    $aceptados = collect();
    
    // Obtener nombres de documentos subidos
    $nombresSubidos = $documentosSubidos->pluck('nombre_doc')->toArray();
    
    foreach ($requeridos as $req) {
        $subido = $documentosSubidos->where('nombre_doc', $req->nombre)->first();
        
        if ($subido) {
            // El documento fue subido
            if ($subido->estado_doc == 'Validado') {
                $aceptados->push($subido);
            } else {
                $subidos->push($subido);
            }
        } else {
            // El documento NO ha sido subido
            $pendientesSubir->push($req);
        }
    }

    return view('cliente.mis-documentos', [
        'pendientesSubir' => $pendientesSubir,
        'subidos' => $subidos,
        'aceptados' => $aceptados,
        'expedienteId' => $expediente->id_expediente
    ]);
}

    // ==========================================
// 8. CLIENTE - SUBIR DOCUMENTO
// ==========================================
public function subirDocumentoCliente(Request $request)
{
    // Validar que llegue nombre_doc, no tipo_documento
    $request->validate([
        'id_expediente' => 'required|exists:expedientes,id_expediente',
        'nombre_doc' => 'required|string|max:200',
        'archivo' => 'required|file|max:20480|mimes:pdf',
    ], [
        'id_expediente.required' => 'El expediente es obligatorio.',
        'id_expediente.exists' => 'El expediente seleccionado no existe.',
        'nombre_doc.required' => 'El nombre del documento es obligatorio.',
        'archivo.required' => 'Debes seleccionar un archivo PDF.',
        'archivo.max' => 'El archivo no debe superar los 20 MB.',
        'archivo.mimes' => 'Solo se permiten archivos en formato PDF.'
    ]);

    // Verificar si ya existe este documento en el expediente
    $existe = Documento::where('id_expediente', $request->id_expediente)
                      ->where('nombre_doc', $request->nombre_doc)
                      ->exists();

    if ($existe) {
        return back()->with('error', '❌ Este documento ya fue subido.');
    }

    // Obtener el cliente autenticado
    $cliente = auth()->user()->cliente;
    
    if (!$cliente) {
        return back()->with('error', '❌ No se encontró el cliente.');
    }

    // Guardar el archivo
    $path = $request->file('archivo')->store('documentos', 'public');

    // Crear el documento en la tabla documentos
    Documento::create([
        'id_expediente' => $request->id_expediente,
        'id_funcionario' => null,
        'Id_Cliente' => $cliente->Id_Cliente,
        'nombre_doc' => $request->nombre_doc,
        'tipo_doc' => 'PDF',
        'ruta_almac' => $path,
        'estado_doc' => 'Pendiente', // Esperando validación del funcionario
        'es_duplicado' => false,
    ]);

    return redirect()->route('cliente.documentos')
                    ->with('success', '✅ Documento subido exitosamente. Queda pendiente de validación.');
}
    // ==========================================
    // 9. FUNCIONARIO - VER DOCUMENTOS DE CLIENTE
    // ==========================================
    public function documentosFuncionario($idCliente)
    {
        $cliente = \App\Models\Cliente::findOrFail($idCliente);
        
        $expediente = Expediente::where('Id_Cliente', $idCliente)->first();

        if (!$expediente) {
            return redirect()->back()->with('error', '❌ Este cliente no tiene expediente.');
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
// 10. FUNCIONARIO - REQUERIR DOCUMENTOS (MÚLTIPLES)
// ==========================================
public function requerirDocumento(Request $request)
{
    try {
        $request->validate([
            'id_expediente' => 'required|exists:expedientes,id_expediente',
            'documentos' => 'required|array|min:1',
            'documentos.*' => 'string|max:100'
        ], [
            'id_expediente.required' => 'El expediente es obligatorio.',
            'id_expediente.exists' => 'El expediente seleccionado no existe.',
            'documentos.required' => 'Debes seleccionar al menos un documento.',
            'documentos.min' => 'Debes seleccionar al menos un documento.'
        ]);

        // Obtener el expediente para saber a qué cliente pertenece
        $expediente = Expediente::find($request->id_expediente);
        
        if (!$expediente) {
            return redirect()->back()->with('error', '❌ Expediente no encontrado.');
        }

        $clienteId = $expediente->Id_Cliente;

        foreach ($request->documentos as $nombre_documento) {
            // 1. Buscar o crear el documento en el catálogo
            $docRequerido = DocumentoRequerido::firstOrCreate([
                'nombre' => $nombre_documento
            ]);

            // 2. Crear la relación Cliente-DocumentoRequerido (si no existe)
            \App\Models\ClienteDocumentoRequerido::firstOrCreate([
                'Id_Cliente' => $clienteId,
                'Id_DocumentoRequerido' => $docRequerido->Id_DocumentoRequerido,
            ]);
        }

        $cantidad = count($request->documentos);

        return redirect()->route('expedientes.carpetas.index', $request->id_expediente)
                        ->with('success', "✅ Se requirieron $cantidad documento(s) al cliente.");

    } catch (\Exception $e) {
        return redirect()->back()
            ->with('error', '❌ Error al requerir los documentos: ' . $e->getMessage())
            ->withInput();
    }
}
   // ==========================================
// 11. FUNCIONARIO - BUSCAR CLIENTE POR CÉDULA
// ==========================================
public function buscarCliente(Request $request)
{
    $cedula = $request->input('cedula');
    $cliente = null;
    $expediente = null;
    
    // Listas de documentos
    $pendientesAceptar = collect();
    $pendientesSubir = collect();
    $subidosEmpresa = collect();
    $aceptados = collect();

    if ($cedula) {
        $cliente = \App\Models\Cliente::where('identificacion', $cedula)->first();
        
        if ($cliente) {
            $expediente = Expediente::where('Id_Cliente', $cliente->Id_Cliente)
                                    ->where('estado', '!=', 'Cerrado')
                                    ->first();
            
            if ($expediente) {
                // 1. Obtener todos los documentos del expediente
                $todosDocumentos = Documento::where('id_expediente', $expediente->id_expediente)
                                            ->orderBy('created_at', 'desc')
                                            ->get();
                
                // 2. Clasificar documentos
                $pendientesAceptar = $todosDocumentos->where('estado_doc', 'Pendiente')
                                                     ->whereNotNull('Id_Cliente');
                
                $subidosEmpresa = $todosDocumentos->where('estado_doc', 'Validado')
                                                  ->whereNull('Id_Cliente');
                
                $aceptados = $todosDocumentos->where('estado_doc', 'Validado')
                                             ->whereNotNull('Id_Cliente');
                
                // 3. Documentos requeridos para este cliente (desde ClienteDocumentoRequerido)
                $clienteDocumentosRequeridos = \App\Models\ClienteDocumentoRequerido::where('Id_Cliente', $cliente->Id_Cliente)
                                                                                    ->pluck('Id_DocumentoRequerido')
                                                                                    ->toArray();
                
                // 4. Obtener los nombres de esos documentos requeridos
                $requeridos = DocumentoRequerido::whereIn('Id_DocumentoRequerido', $clienteDocumentosRequeridos)->get();
                
                // 5. Nombres de documentos ya subidos
                $nombresSubidos = $todosDocumentos->pluck('nombre_doc')->toArray();
                
                // 6. Filtrar los que faltan por subir
                $pendientesSubir = $requeridos->filter(function($req) use ($nombresSubidos) {
                    return !in_array($req->nombre, $nombresSubidos);
                });
            }
        }
    }

    return view('documentos.documentos-cliente', compact(
        'cliente', 
        'expediente',
        'pendientesAceptar',
        'pendientesSubir',
        'subidosEmpresa',
        'aceptados'
    ));
}
    // ==========================================
    // 12. FUNCIONARIO - SUBIR DOCUMENTO DE EMPRESA
    // ==========================================
    public function subirDocumentoEmpresa($id_expediente)
    {
        $expediente = Expediente::findOrFail($id_expediente);
        return view('documentos.subir-empresa', compact('expediente'));
    }

    // ==========================================
    // 13. FUNCIONARIO - MOSTRAR FORMULARIO PARA REQUERIR
    // ==========================================
    public function mostrarRequerir($id_expediente)
    {
        $expediente = Expediente::findOrFail($id_expediente);
        return view('documentos.requerir-documento', compact('expediente'));
    }

    // ==========================================
// 14. FUNCIONARIO - GUARDAR DOCUMENTO DE EMPRESA (CORREGIDO)
// ==========================================
public function storeDocumentoEmpresa(Request $request)
{
    try {
        // DEPURACIÓN: Ver qué datos llegan (eliminar después de probar)
        // dd($request->all());

        // Validación con mensajes en español
        $validated = $request->validate([
            'id_expediente' => 'required|integer|exists:expedientes,id_expediente',
            'nombre_doc' => 'required|string|max:200',
            'tipo_doc' => 'required|string|max:80',
            'archivo' => 'required|file|max:20480|mimes:pdf'
        ], [
            'id_expediente.required' => 'El expediente es obligatorio.',
            'id_expediente.integer' => 'El expediente debe ser un número válido.',
            'id_expediente.exists' => 'El expediente seleccionado no existe en la base de datos.',
            'nombre_doc.required' => 'El nombre del documento es obligatorio.',
            'nombre_doc.max' => 'El nombre no debe exceder los 200 caracteres.',
            'tipo_doc.required' => 'El tipo de documento es obligatorio.',
            'tipo_doc.max' => 'El tipo no debe exceder los 80 caracteres.',
            'archivo.required' => 'Debes seleccionar un archivo PDF.',
            'archivo.max' => 'El archivo no debe superar los 20 MB.',
            'archivo.mimes' => 'Solo se permiten archivos en formato PDF.'
        ]);

        // Verificar si ya existe un documento con ese nombre
        $existe = Documento::where('id_expediente', $request->id_expediente)
                          ->where('nombre_doc', $request->nombre_doc)
                          ->exists();

        if ($existe) {
            return redirect()->back()
                ->withInput()
                ->with('warning', '⚠️ Ya existe un documento con ese nombre en este expediente.');
        }

        // Guardar el archivo
        $path = $request->file('archivo')->store('documentos', 'public');

        // Crear el documento
        $documento = Documento::create([
            'id_expediente' => $request->id_expediente,
            'id_funcionario' => auth()->id(),
            'Id_Cliente' => null,
            'nombre_doc' => $request->nombre_doc,
            'tipo_doc' => $request->tipo_doc,
            'ruta_almac' => $path,
            'estado_doc' => 'Validado',
            'es_duplicado' => false,
        ]);

        if (!$documento) {
            return redirect()->back()
                ->with('error', '❌ Error al guardar el documento en la base de datos.')
                ->withInput();
        }

        return redirect()->route('expedientes.carpetas.index', $request->id_expediente)
                        ->with('success', '✅ Documento de empresa subido exitosamente.');

    } catch (\Illuminate\Validation\ValidationException $e) {
        return redirect()->back()
            ->withErrors($e->validator)
            ->withInput();
    } catch (\Illuminate\Database\QueryException $e) {
        return redirect()->back()
            ->with('error', '❌ Error de base de datos: ' . $e->getMessage())
            ->withInput();
    } catch (\Exception $e) {
        return redirect()->back()
            ->with('error', '❌ Error al subir el documento: ' . $e->getMessage())
            ->withInput();
    }
}

    // ==========================================
    // FUNCIÓN AUXILIAR - MOVER A CARPETA APROBADOS
    // ==========================================
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