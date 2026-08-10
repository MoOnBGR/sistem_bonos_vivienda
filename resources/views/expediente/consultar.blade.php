<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Expediente
        </h2>
    </x-slot>

    <div class="expediente-container" x-data="{ 
        modalNuevaCarpeta: false, 
        modalRenombrar: null, 
        modalSubirDocumento: false, 
        modalMoverDocumento: null 
    }">

        <!-- Pestañas de navegación -->
        <div class="flex gap-2 mb-6 border-b border-gray-200 pb-4">
            <a href="{{ route('expedientes.index') }}"
               class="px-4 py-2 rounded-lg text-sm font-medium bg-gray-100 text-gray-600 hover:bg-gray-200 transition">
                Listado
            </a>
            <span class="px-4 py-2 rounded-lg text-sm font-medium bg-[#550000] text-white">
                Ver
            </span>
            <a href="{{ route('expedientes.editar', $expediente->id_expediente) }}"
               class="px-4 py-2 rounded-lg text-sm font-medium bg-gray-100 text-gray-600 hover:bg-gray-200 transition">
                Actualizar
            </a>
        </div>

        <h3 class="expediente-titulo">
            Expediente: EXP-{{ str_pad($expediente->id_expediente, 4, '0', STR_PAD_LEFT) }}
        </h3>
        <p class="text-sm text-gray-500 -mt-3 mb-4">
            Cliente: {{ $cliente->nombre }} {{ $cliente->apellidos }} (Cédula: {{ $cliente->identificacion }})
        </p>

        @if (session('success'))
            <div class="expediente-mensaje-exito">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="expediente-mensaje-error">{{ session('error') }}</div>
        @endif

        <!-- Información del expediente -->
        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-6">
            <h4 class="font-semibold text-gray-700 mb-2">Información del expediente</h4>
            <p class="text-sm text-gray-600">
                <span class="font-medium">Fecha apertura:</span>
                {{ \Carbon\Carbon::parse($expediente->fecha_creacion)->format('d/m/Y') }}
            </p>
            <p class="text-sm text-gray-600">
                <span class="font-medium">Funcionario:</span> {{ $expediente->funcionario->name }}
            </p>
            <p class="text-sm text-gray-600">
                <span class="font-medium">Estado actual:</span>
                <span class="expediente-estado {{ $expediente->estado === 'En proceso' ? 'en-proceso' : 'completado' }}">
                    {{ $expediente->estado }}
                </span>
            </p>
        </div>

        <!-- Botón Validar Documentos -->
        <div class="mb-4">
            <a href="{{ route('funcionario.documentos.buscar', ['cedula' => $cliente->identificacion]) }}" 
               class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg transition font-medium flex items-center gap-2 w-fit">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Validar Documentos
            </a>
        </div>

        <!-- Breadcrumb -->
        <div class="flex items-center flex-wrap gap-1 text-sm mb-4">
            <a href="{{ route('expedientes.carpetas.index', $expediente->id_expediente) }}"
               class="text-[#550000] font-medium hover:underline">
                EXP-{{ str_pad($expediente->id_expediente, 4, '0', STR_PAD_LEFT) }}
            </a>
            @foreach ($ruta as $carpetaRuta)
                <span class="text-gray-400">/</span>
                <a href="{{ route('expedientes.carpetas.index', [$expediente->id_expediente, $carpetaRuta->id_carpeta]) }}"
                   class="text-[#550000] font-medium hover:underline">
                    {{ $carpetaRuta->nombre }}
                </a>
            @endforeach
        </div>

        <!-- Carpetas -->
        <div class="flex items-center justify-between mb-2">
            <h4 class="font-semibold text-gray-700">Carpetas</h4>
            <button type="button" @click="modalNuevaCarpeta = true"
                    class="text-[#550000] font-medium hover:underline bg-transparent">
                + Nueva carpeta
            </button>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
            @forelse ($carpetas as $carpetaItem)
                <div class="border border-gray-200 rounded-lg p-3 hover:bg-gray-50">
                    <a href="{{ route('expedientes.carpetas.index', [$expediente->id_expediente, $carpetaItem->id_carpeta]) }}"
                       class="flex items-center gap-2 text-gray-700 font-medium">
                        📁 <span class="truncate">{{ $carpetaItem->nombre }}</span>
                    </a>
                    <div class="flex gap-3 mt-2 text-xs">
                        <button type="button"
                                onclick="renombrarCarpeta({{ $carpetaItem->id_carpeta }}, '{{ addslashes($carpetaItem->nombre) }}')"
                                class="text-[#550000] hover:underline bg-transparent">
                            Editar
                        </button>
                        <form method="POST" action="{{ route('expedientes.carpetas.destroy', $carpetaItem->id_carpeta) }}"
                              onsubmit="return confirm('¿Eliminar esta carpeta y todo su contenido?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-[#550000] hover:underline bg-transparent">
                                Eliminar
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <p class="text-gray-400 col-span-full">No hay carpetas en este nivel.</p>
            @endforelse
        </div>

        @php
            $volverUrl = $carpetaActual
                ? ($carpetaActual->id_carpeta_padre
                    ? route('expedientes.carpetas.index', [$expediente->id_expediente, $carpetaActual->id_carpeta_padre])
                    : route('expedientes.carpetas.index', $expediente->id_expediente))
                : route('expedientes.index');
        @endphp

        <div class="flex gap-3 mb-6">
            <a href="{{ $volverUrl }}" class="expediente-btn expediente-btn-secundario">
                Volver
            </a>
        </div>

        <!-- Modal: Nueva carpeta -->
        <div x-show="modalNuevaCarpeta" x-cloak
             class="fixed inset-0 bg-black/40 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg p-6 w-full max-w-sm" @click.outside="modalNuevaCarpeta = false">
                <h4 class="font-semibold text-gray-700 mb-4">Nueva carpeta</h4>
                <form method="POST" action="{{ route('expedientes.carpetas.store', $expediente->id_expediente) }}">
                    @csrf
                    @if ($carpetaActual)
                        <input type="hidden" name="id_carpeta_padre" value="{{ $carpetaActual->id_carpeta }}">
                    @endif
                    <input type="text" name="nombre" placeholder="Nombre de la carpeta" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 mb-4">
                    <div class="flex gap-3 justify-end">
                        <button type="button" @click="modalNuevaCarpeta = false"
                                class="text-gray-500 hover:underline bg-transparent">Cancelar</button>
                        <button type="submit"
                                class="text-[#550000] font-medium hover:underline bg-transparent">Crear</button>
                    </div>
                </form>
            </div>
        </div>

        @if ($carpetaActual)
            <!-- DOCUMENTOS DENTRO DE LA CARPETA -->
            <div class="flex items-center justify-between mb-2">
                <h4 class="font-semibold text-gray-700">
                    Documentos en "{{ $carpetaActual->nombre }}"
                </h4>
                <div class="flex gap-2">
                    @if(session('documento_a_mover'))
                        <button type="button" 
                                id="btnPegar"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                            📋 Pegar documento
                        </button>
                        <button type="button" 
                                id="btnCancelarPegar"
                                class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                            Cancelar
                        </button>
                    @endif
                    <button type="button" 
                            @click="modalSubirDocumento = true"
                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                        Subir documento
                    </button>
                </div>
            </div>

            @if(session('documento_a_mover'))
                @php
                    $docId = session('documento_a_mover');
                    $docPendiente = \App\Models\Documento::find($docId);
                    $tipoOperacion = session('tipo_operacion', 'cortar');
                @endphp
                @if($docPendiente)
                    <div class="bg-yellow-50 border-l-4 border-yellow-500 text-yellow-700 p-3 mb-4 rounded-lg text-sm">
                        <strong>{{ $tipoOperacion === 'copiar' ? '📋 Copiando' : '✂️ Moviendo' }}:</strong> 
                        "{{ $docPendiente->nombre_doc }}" 
                        — Navega a la carpeta destino y haz clic en <strong>"Pegar documento"</strong>
                    </div>
                @endif
            @endif

            <div class="overflow-x-auto mb-6">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 text-gray-500 uppercase text-xs">
                            <th class="py-2 pr-4">Nombre del documento</th>
                            <th class="py-2 pr-4">Fecha subida</th>
                            <th class="py-2 pr-4">Estado</th>
                            <th class="py-2 pr-4 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($documentos as $documento)
                        <tr class="border-b border-gray-100 hover:bg-gray-50 documento-item" 
                            data-documento-id="{{ $documento->id_documento }}"
                            data-documento-nombre="{{ $documento->nombre_doc }}">
                            <td class="py-2 pr-4">{{ $documento->nombre_doc }}</td>
                            <td class="py-2 pr-4">{{ \Carbon\Carbon::parse($documento->fecha_subida)->format('d/m/Y') }}</td>
                            <td class="py-2 pr-4">
                                <span class="px-2 py-1 rounded-full text-xs font-medium
                                    {{ $documento->estado_doc === 'Validado' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                    {{ $documento->estado_doc }}
                                </span>
                            </td>
                            <td class="py-2 pr-4 text-right">
                                <div class="flex justify-end items-center gap-2">
                                    <button type="button" 
                                            class="btn-accion text-blue-600 hover:text-blue-800 font-medium transition text-sm"
                                            data-id="{{ $documento->id_documento }}"
                                            data-nombre="{{ $documento->nombre_doc }}"
                                            data-tipo="copiar">
                                        Copiar
                                    </button>
                                    <button type="button" 
                                            class="btn-accion text-yellow-600 hover:text-yellow-800 font-medium transition text-sm"
                                            data-id="{{ $documento->id_documento }}"
                                            data-nombre="{{ $documento->nombre_doc }}"
                                            data-tipo="cortar">
                                        Cortar
                                    </button>
                                    <a href="{{ Storage::url($documento->ruta_almac) }}" 
                                       target="_blank" 
                                       class="text-gray-600 hover:text-gray-800 font-medium transition text-sm">
                                        Ver
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="py-6 text-center text-gray-400">
                                Esta carpeta aún no tiene documentos.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- MODAL: SUBIR DOCUMENTO A CARPETA -->
            <div x-show="modalSubirDocumento" x-cloak
                 class="fixed inset-0 bg-black/40 flex items-center justify-center z-50">
                <div class="bg-white rounded-lg p-6 w-full max-w-md" @click.outside="modalSubirDocumento = false">
                    <h4 class="font-semibold text-gray-700 mb-4">Subir Documento a Carpeta</h4>
                    <form method="POST" action="{{ route('expedientes.carpetas.documentos.subir', [$expediente->id_expediente, $carpetaActual->id_carpeta]) }}" 
                          enctype="multipart/form-data">
                        @csrf
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nombre del Documento *</label>
                            <input type="text" name="nombre_doc" required
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2">
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Documento *</label>
                            <select name="tipo_doc" required
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2">
                                <option value="">Seleccione...</option>
                                <option value="Cédula">Cédula</option>
                                <option value="Planos">Planos</option>
                                <option value="Constancia de Ingresos">Constancia de Ingresos</option>
                                <option value="Certificado de Propiedad">Certificado de Propiedad</option>
                                <option value="Otro">Otro</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Archivo PDF *</label>
                            <input type="file" name="archivo" accept=".pdf" required
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2">
                            <p class="text-xs text-gray-400 mt-1">PDF, máximo 20MB</p>
                        </div>
                        <div class="flex gap-3 justify-end">
                            <button type="button" @click="modalSubirDocumento = false"
                                    class="text-gray-500 hover:underline bg-transparent">Cancelar</button>
                            <button type="submit"
                                    class="bg-[#550000] hover:bg-[#6d0000] text-white px-4 py-2 rounded-lg transition">Subir</button>
                        </div>
                    </form>
                </div>
            </div>
        @endif

        <script>
        // ==========================================
        // RENOMBRAR CARPETA
        // ==========================================
        function renombrarCarpeta(id, nombreActual) {
            const nuevoNombre = prompt('Nuevo nombre de la carpeta:', nombreActual);
            if (!nuevoNombre || nuevoNombre.trim() === '' || nuevoNombre === nombreActual) return;

            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '/expedientes/carpetas/' + id;
            form.innerHTML = `
                <input name="_token" value="${token}">
                <input name="_method" value="PUT">
                <input name="nombre" value="${nuevoNombre.trim()}">
            `;
            document.body.appendChild(form);
            form.submit();
        }

        document.addEventListener('DOMContentLoaded', function() {

            // ==========================================
            // BOTONES COPIAR Y CORTAR
            // ==========================================
            document.querySelectorAll('.btn-accion').forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    const id = this.getAttribute('data-id');
                    const nombre = this.getAttribute('data-nombre');
                    const tipo = this.getAttribute('data-tipo');
                    
                    const token = document.querySelector('meta[name="csrf-token"]');
                    if (!token) {
                        alert('Error: No se encontró el token de seguridad');
                        return;
                    }
                    
                    fetch('/documentos/' + id + '/copiar', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token.getAttribute('content')
                        },
                        body: JSON.stringify({ tipo: tipo })
                    })
                    .then(function(response) {
                        return response.json();
                    })
                    .then(function(data) {
                        if (data.success) {
                            if (tipo === 'copiar') {
                                alert('Documento "' + nombre + '" copiado. Navega a la carpeta destino y haz clic en "Pegar documento".');
                            } else {
                                alert('Documento "' + nombre + '" listo para mover. Navega a la carpeta destino y haz clic en "Pegar documento".');
                            }
                            location.reload();
                        } else {
                            alert('Error: ' + (data.message || 'No se pudo procesar'));
                        }
                    })
                    .catch(function(error) {
                        console.error('Error:', error);
                        alert('Error al procesar el documento.');
                    });
                });
            });

            // ==========================================
            // BOTÓN PEGAR
            // ==========================================
            const btnPegar = document.getElementById('btnPegar');
            if (btnPegar) {
                btnPegar.addEventListener('click', function() {
                    const carpetaId = {{ $carpetaActual->id_carpeta ?? 'null' }};
                    const expedienteId = {{ $expediente->id_expediente }};
                    const token = document.querySelector('meta[name="csrf-token"]');
                    
                    fetch('/expedientes/' + expedienteId + '/pegar', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token.getAttribute('content')
                        },
                        body: JSON.stringify({ id_carpeta_destino: carpetaId })
                    })
                    .then(function(response) {
                        return response.json();
                    })
                    .then(function(data) {
                        if (data.success) {
                            alert(data.message);
                            location.reload();
                        } else {
                            alert('Error: ' + (data.error || 'No se pudo pegar el documento'));
                        }
                    })
                    .catch(function(error) {
                        console.error('Error:', error);
                        alert('Error al pegar el documento');
                    });
                });
            }

            // ==========================================
            // BOTÓN CANCELAR
            // ==========================================
            const btnCancelarPegar = document.getElementById('btnCancelarPegar');
            if (btnCancelarPegar) {
                btnCancelarPegar.addEventListener('click', function() {
                    const token = document.querySelector('meta[name="csrf-token"]');
                    fetch('/documentos/cancelar-movimiento', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token.getAttribute('content')
                        }
                    })
                    .then(function(response) {
                        return response.json();
                    })
                    .then(function(data) {
                        if (data.success) {
                            location.reload();
                        }
                    })
                    .catch(function(error) {
                        console.error('Error:', error);
                    });
                });
            }
        });
        </script>
    </div>
</x-app-layout>