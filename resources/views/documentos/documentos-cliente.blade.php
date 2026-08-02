<x-app-layout>
    <div class="container mx-auto mt-4 px-4">

        {{-- ENCABEZADO --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
            <div>
                <h2 class="text-2xl font-bold text-[#1a2a4a]">Documentos por Cliente</h2>
                <p class="text-sm text-gray-500">Busque un cliente por su cedula para ver sus documentos</p>
            </div>
            <a href="{{ route('documentos.index') }}" 
               class="mt-3 md:mt-0 bg-gray-200 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-300 transition shadow-sm flex items-center gap-2 text-sm font-medium">
                Volver al Listado
            </a>
        </div>

        {{-- PESTAÑAS --}}
        <div class="flex flex-wrap gap-2 mb-4 border-b border-gray-200 pb-2">
            <a href="{{ route('documentos.index') }}" 
               class="px-4 py-2 rounded-lg text-sm font-medium transition bg-gray-200 text-gray-700 hover:bg-gray-300">
                Todos los Documentos
            </a>
            <a href="{{ route('funcionario.documentos.buscar', ['cedula' => request('cedula')]) }}" 
               class="px-4 py-2 rounded-lg text-sm font-medium transition bg-[#550000] text-white">
                Documentos por Cliente
            </a>
        </div>

        {{-- MENSAJES --}}
        @if(session('success'))
            <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded-lg mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if(session('warning'))
            <div class="bg-yellow-50 border-l-4 border-yellow-500 text-yellow-700 p-4 rounded-lg mb-4">
                {{ session('warning') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-lg mb-4">
                {{ session('error') }}
            </div>
        @endif

        {{-- BUSCADOR --}}
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <form method="GET" action="{{ route('funcionario.documentos.buscar') }}" class="flex gap-2">
                <input type="text" 
                       name="cedula" 
                       placeholder="Ingrese la cedula del cliente..." 
                       required
                       value="{{ request('cedula') }}" 
                       class="flex-1 rounded-lg border-gray-300 shadow-sm focus:border-[#550000] focus:ring focus:ring-[#550000] focus:ring-opacity-50 px-4 py-2">
                <button type="submit" class="bg-[#550000] hover:bg-[#6d0000] text-white px-6 py-2 rounded-lg transition font-medium">
                    Buscar
                </button>
            </form>
        </div>

        {{-- RESULTADOS --}}
        @if(request('cedula'))
            @if($cliente)
                {{-- Info del cliente --}}
                <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div>
                            <p class="text-xs text-gray-500 uppercase font-medium">Cliente</p>
                            <p class="text-gray-800 font-semibold">{{ $cliente->nombre ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase font-medium">Cedula</p>
                            <p class="text-gray-800 font-semibold">{{ $cliente->identificacion ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase font-medium">Telefono</p>
                            <p class="text-gray-800 font-semibold">{{ $cliente->telefono ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase font-medium">Correo</p>
                            <p class="text-gray-800 font-semibold">{{ $cliente->correo ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                @if($expediente)
                    {{-- Botones de accion --}}
                    <div class="flex flex-wrap gap-3 mb-6">
                        <a href="{{ route('documentos.requerir', $expediente->id_expediente) }}" 
                           class="bg-[#550000] hover:bg-[#6d0000] text-white px-6 py-3 rounded-lg transition font-medium flex items-center gap-2">
                            Solicitar Documentos
                        </a>
                        <a href="{{ route('documentos.create', ['expediente' => $expediente->id_expediente, 'cedula' => $cliente->identificacion ?? '']) }}" 
                           class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-3 rounded-lg transition font-medium flex items-center gap-2">
                            Subir Documento (Empresa)
                        </a>
                    </div>

                    {{-- Pendientes de Aceptar --}}
                    <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-6">
                        <div class="px-6 py-4 bg-yellow-50 border-b border-yellow-200">
                            <h4 class="font-semibold text-yellow-800">
                                Pendientes de Aceptar
                                <span class="text-sm font-normal text-yellow-600 ml-2">
                                    ({{ $pendientesAceptar->count() }} documentos)
                                </span>
                            </h4>
                        </div>
                        @if($pendientesAceptar->isEmpty())
                            <div class="p-4 text-gray-500 text-sm">No hay documentos pendientes de validar.</div>
                        @else
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-2 text-left text-gray-600 text-xs uppercase">Nombre</th>
                                            <th class="px-4 py-2 text-left text-gray-600 text-xs uppercase">Tipo</th>
                                            <th class="px-4 py-2 text-left text-gray-600 text-xs uppercase">Fecha</th>
                                            <th class="px-4 py-2 text-center text-gray-600 text-xs uppercase">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($pendientesAceptar as $doc)
                                        <tr class="border-b hover:bg-gray-50">
                                            <td class="px-4 py-2 font-medium">{{ $doc->nombre_doc }}</td>
                                            <td class="px-4 py-2 text-gray-600">{{ $doc->tipo_doc }}</td>
                                            <td class="px-4 py-2 text-gray-500">{{ $doc->created_at->format('d/m/Y H:i') }}</td>
                                            <td class="px-4 py-2 text-center">
                                                <div class="flex justify-center items-center gap-2">
                                                    <a href="{{ Storage::url($doc->ruta_almac) }}" 
                                                       target="_blank" 
                                                       class="text-blue-600 hover:text-blue-800 text-xs font-medium transition">
                                                        Ver
                                                    </a>
                                                    <form action="{{ route('documentos.validar', $doc->id_documento) }}" method="POST" class="inline">
                                                        @csrf @method('PATCH')
                                                        <input type="hidden" name="estado_doc" value="Validado">
                                                        <button type="submit" class="text-green-600 hover:text-green-800 text-xs font-medium transition"
                                                                onclick="return confirm('¿Aceptar este documento?')">
                                                            Aceptar
                                                        </button>
                                                    </form>
                                                    <button onclick="openModal({{ $doc->id_documento }})"
                                                            class="text-red-600 hover:text-red-800 text-xs font-medium transition">
                                                        Rechazar
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>

                    {{-- Pendientes de Subir --}}
                    <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-6">
                        <div class="px-6 py-4 bg-red-50 border-b border-red-200">
                            <h4 class="font-semibold text-red-800">
                                Pendientes de Subir
                                <span class="text-sm font-normal text-red-600 ml-2">
                                    ({{ $pendientesSubir->count() }} documentos)
                                </span>
                            </h4>
                        </div>
                        @if($pendientesSubir->isEmpty())
                            <div class="p-4 text-gray-500 text-sm">No hay documentos pendientes de subir.</div>
                        @else
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-2 text-left text-gray-600 text-xs uppercase">Nombre</th>
                                            <th class="px-4 py-2 text-center text-gray-600 text-xs uppercase">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($pendientesSubir as $req)
                                        <tr class="border-b hover:bg-gray-50">
                                            <td class="px-4 py-2 font-medium">{{ $req->nombre ?? 'Documento' }}</td>
                                            <td class="px-4 py-2 text-center">
                                                <div class="flex justify-center items-center gap-2">
                                                    <form action="{{ route('documentos.solicitud.destroy', $req->Id_DocumentoRequerido) }}" method="POST" class="inline">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="text-red-600 hover:text-red-800 text-xs font-medium transition"
                                                                onclick="return confirm('¿Eliminar esta solicitud de documento?')">
                                                            Eliminar
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                    {{-- Subidos por Empresa --}}
                    <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-6">
                        <div class="px-6 py-4 bg-blue-50 border-b border-blue-200">
                            <h4 class="font-semibold text-blue-800">
                                Subidos por Empresa
                                <span class="text-sm font-normal text-blue-600 ml-2">
                                    ({{ $subidosEmpresa->count() }} documentos)
                                </span>
                            </h4>
                        </div>
                        @if($subidosEmpresa->isEmpty())
                            <div class="p-4 text-gray-500 text-sm">No hay documentos subidos por la empresa.</div>
                        @else
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-2 text-left text-gray-600 text-xs uppercase">Nombre</th>
                                            <th class="px-4 py-2 text-left text-gray-600 text-xs uppercase">Tipo</th>
                                            <th class="px-4 py-2 text-left text-gray-600 text-xs uppercase">Fecha</th>
                                            <th class="px-4 py-2 text-center text-gray-600 text-xs uppercase">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($subidosEmpresa as $doc)
                                        <tr class="border-b hover:bg-gray-50">
                                            <td class="px-4 py-2 font-medium">{{ $doc->nombre_doc }}</td>
                                            <td class="px-4 py-2 text-gray-600">{{ $doc->tipo_doc }}</td>
                                            <td class="px-4 py-2 text-gray-500">{{ $doc->created_at->format('d/m/Y H:i') }}</td>
                                            <td class="px-4 py-2 text-center">
                                                <div class="flex justify-center items-center gap-2">
                                                    <a href="{{ Storage::url($doc->ruta_almac) }}" 
                                                    target="_blank" 
                                                    class="text-blue-600 hover:text-blue-800 text-xs font-medium transition">
                                                        Ver
                                                    </a>
                                                    <form action="{{ route('documentos.destroy', $doc->id_documento) }}" method="POST" class="inline">
                                                        @csrf @method('DELETE')
                                                        <input type="hidden" name="cedula" value="{{ request('cedula') }}">
                                                        <button type="submit" class="text-red-600 hover:text-red-800 text-xs font-medium transition"
                                                                onclick="return confirm('¿Eliminar este documento?')">
                                                            Eliminar
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                    {{-- Rechazados --}}
                    <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-6">
                        <div class="px-6 py-4 bg-red-100 border-b border-red-300">
                            <h4 class="font-semibold text-red-800">
                                Rechazados
                                <span class="text-sm font-normal text-red-600 ml-2">
                                    ({{ $rechazados->count() }} documentos)
                                </span>
                            </h4>
                        </div>
                        @if($rechazados->isEmpty())
                            <div class="p-4 text-gray-500 text-sm">No hay documentos rechazados.</div>
                        @else
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-2 text-left text-gray-600 text-xs uppercase">Nombre</th>
                                            <th class="px-4 py-2 text-left text-gray-600 text-xs uppercase">Motivo del Rechazo</th>
                                            <th class="px-4 py-2 text-left text-gray-600 text-xs uppercase">Fecha</th>
                                            <th class="px-4 py-2 text-center text-gray-600 text-xs uppercase">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($rechazados as $doc)
                                        <tr class="border-b hover:bg-gray-50">
                                            <td class="px-4 py-2 font-medium">{{ $doc->nombre_doc }}</td>
                                            <td class="px-4 py-2 text-red-600 text-sm">{{ $doc->motivo_rechazo ?? 'Sin motivo' }}</td>
                                            <td class="px-4 py-2 text-gray-500">{{ $doc->created_at->format('d/m/Y H:i') }}</td>
                                            <td class="px-4 py-2 text-center">
                                                <div class="flex justify-center items-center gap-2">
                                                    <a href="{{ Storage::url($doc->ruta_almac) }}" 
                                                       target="_blank" 
                                                       class="text-blue-600 hover:text-blue-800 text-xs font-medium transition">
                                                        Ver
                                                    </a>
                                                    <form action="{{ route('documentos.destroy', $doc->id_documento) }}" method="POST" class="inline">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="text-red-600 hover:text-red-800 text-xs font-medium transition"
                                                                onclick="return confirm('¿Eliminar este documento?')">
                                                            Eliminar
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>

                    {{-- Aceptados --}}
                    <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-6">
                        <div class="px-6 py-4 bg-green-50 border-b border-green-200">
                            <h4 class="font-semibold text-green-800">
                                Aceptados
                                <span class="text-sm font-normal text-green-600 ml-2">
                                    ({{ $aceptados->count() }} documentos)
                                </span>
                            </h4>
                        </div>
                        @if($aceptados->isEmpty())
                            <div class="p-4 text-gray-500 text-sm">No hay documentos aceptados.</div>
                        @else
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-2 text-left text-gray-600 text-xs uppercase">Nombre</th>
                                            <th class="px-4 py-2 text-left text-gray-600 text-xs uppercase">Tipo</th>
                                            <th class="px-4 py-2 text-left text-gray-600 text-xs uppercase">Fecha</th>
                                            <th class="px-4 py-2 text-center text-gray-600 text-xs uppercase">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($aceptados as $doc)
                                        <tr class="border-b hover:bg-gray-50">
                                            <td class="px-4 py-2 font-medium">{{ $doc->nombre_doc }}</td>
                                            <td class="px-4 py-2 text-gray-600">{{ $doc->tipo_doc }}</td>
                                            <td class="px-4 py-2 text-gray-500">{{ $doc->created_at->format('d/m/Y H:i') }}</td>
                                            <td class="px-4 py-2 text-center">
                                                <div class="flex justify-center items-center gap-2">
                                                    <a href="{{ Storage::url($doc->ruta_almac) }}" 
                                                       target="_blank" 
                                                       class="text-blue-600 hover:text-blue-800 text-xs font-medium transition">
                                                        Ver
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>

                @else
                    <div class="bg-yellow-50 border-l-4 border-yellow-500 text-yellow-700 p-4 rounded-lg">
                        Este cliente no tiene un expediente activo.
                    </div>
                @endif
            @else
                <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-lg">
                    No se encontro un cliente con la cedula: <strong>{{ request('cedula') }}</strong>
                </div>
            @endif
        @endif
    </div>

    {{-- MODALES DE VALIDACION CON MOTIVO DE RECHAZO --}}
    @if(isset($pendientesAceptar))
        @foreach($pendientesAceptar as $doc)
        <div id="modal-{{ $doc->id_documento }}" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 hidden">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
                <h3 class="text-lg font-semibold text-[#1a2a4a] mb-2">Validar Documento</h3>
                <p class="text-sm text-gray-500 mb-4">Cambiar el estado de: <strong>{{ $doc->nombre_doc }}</strong></p>
                <form action="{{ route('documentos.validar', $doc->id_documento) }}" method="POST">
                    @csrf @method('PATCH')
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                        <select name="estado_doc" id="estado_select_{{ $doc->id_documento }}" 
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-[#1a2a4a] focus:outline-none" 
                                onchange="toggleMotivo({{ $doc->id_documento }})" required>
                            <option value="Validado">Aprobar</option>
                            <option value="Rechazado">Rechazar</option>
                        </select>
                    </div>
                    <div class="mb-4 hidden" id="motivo_container_{{ $doc->id_documento }}">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Motivo del Rechazo <span class="text-red-500">*</span></label>
                        <textarea name="motivo_rechazo" id="motivo_rechazo_{{ $doc->id_documento }}" 
                                  class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-[#1a2a4a] focus:outline-none" 
                                  rows="3" placeholder="Escribe el motivo por el cual rechazas este documento..."></textarea>
                    </div>
                    <div class="flex justify-end gap-3">
                        <button type="button" onclick="closeModal({{ $doc->id_documento }})" 
                                class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg transition">Cancelar</button>
                        <button type="submit" class="bg-[#1a2a4a] hover:bg-[#2a3a5a] text-white px-4 py-2 rounded-lg transition">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
        @endforeach
    @endif

    {{-- MODAL PARA EDITAR (REEMPLAZAR DOCUMENTO RECHAZADO) --}}
    <div id="modal-editar" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 hidden">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
            <h3 class="text-lg font-semibold text-[#1a2a4a] mb-2">Reemplazar Documento</h3>
            <p class="text-sm text-gray-500 mb-4">Seleccione el archivo corregido para reemplazar el documento rechazado.</p>
            <form id="form-editar-documento" action="" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nuevo Archivo PDF <span class="text-red-500">*</span></label>
                    <input type="file" name="archivo" class="w-full border border-gray-300 rounded-lg px-4 py-2" accept=".pdf" required>
                    <p class="text-xs text-gray-400 mt-1">PDF, maximo 20MB</p>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="closeEditarModal()" 
                            class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg transition">Cancelar</button>
                    <button type="submit" class="bg-[#550000] hover:bg-[#6d0000] text-white px-4 py-2 rounded-lg transition">Reemplazar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function toggleMotivo(id) {
            const select = document.getElementById('estado_select_' + id);
            const container = document.getElementById('motivo_container_' + id);
            if (select.value === 'Rechazado') {
                container.classList.remove('hidden');
            } else {
                container.classList.add('hidden');
            }
        }

        function openModal(id) { 
            document.getElementById('modal-' + id).classList.remove('hidden');
            const container = document.getElementById('motivo_container_' + id);
            if (container) container.classList.add('hidden');
        }

        function closeModal(id) { 
            document.getElementById('modal-' + id).classList.add('hidden');
        }

        function openEditarModal(documentoId) {
            const modal = document.getElementById('modal-editar');
            const form = document.getElementById('form-editar-documento');
            form.action = '/documentos/' + documentoId;
            modal.classList.remove('hidden');
        }

        function closeEditarModal() {
            document.getElementById('modal-editar').classList.add('hidden');
        }

        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('bg-black/50')) {
                e.target.classList.add('hidden');
            }
        });
    </script>
</x-app-layout>