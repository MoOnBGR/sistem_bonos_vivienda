<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Documentos por Cliente
        </h2>
    </x-slot>

    <div class="container mx-auto mt-4 px-4">

        {{-- ENCABEZADO --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
            <div>
                <h2 class="text-2xl font-bold text-[#1a2a4a]">Documentos por Cliente</h2>
                <p class="text-sm text-gray-500">Busque un cliente por su cédula para ver sus documentos</p>
            </div>
            <a href="{{ route('documentos.index') }}" 
               class="mt-3 md:mt-0 bg-gray-200 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-300 transition shadow-sm flex items-center gap-2 text-sm font-medium">
                Volver al Listado
            </a>
        </div>

        {{-- PESTAÑAS DE NAVEGACIÓN --}}
        <div class="flex flex-wrap gap-2 mb-4 border-b border-gray-200 pb-2">
            <a href="{{ route('documentos.index') }}" 
               class="px-4 py-2 rounded-lg text-sm font-medium transition bg-gray-200 text-gray-700 hover:bg-gray-300">
                Todos los Documentos
            </a>
            <a href="{{ route('funcionario.documentos.buscar') }}" 
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

        @if(session('error'))
            <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-lg mb-4">
                {{ session('error') }}
            </div>
        @endif

        {{-- FORMULARIO DE BÚSQUEDA --}}
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <form method="GET" action="{{ route('funcionario.documentos.buscar') }}" class="flex gap-2">
                <input type="text" 
                       name="cedula" 
                       placeholder="Ingrese la cédula del cliente..." 
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
                {{-- Información del cliente --}}
                <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div>
                            <p class="text-xs text-gray-500 uppercase font-medium">Cliente</p>
                            <p class="text-gray-800 font-semibold">{{ $cliente->nombre ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase font-medium">Cédula</p>
                            <p class="text-gray-800 font-semibold">{{ $cliente->identificacion ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase font-medium">Teléfono</p>
                            <p class="text-gray-800 font-semibold">{{ $cliente->telefono ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase font-medium">Correo</p>
                            <p class="text-gray-800 font-semibold">{{ $cliente->correo ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                @if($expediente)
                    {{-- Botones de acción --}}
                    <div class="flex flex-wrap gap-3 mb-6">
                        <a href="{{ route('documentos.requerir', $expediente->id_expediente) }}" 
                           class="bg-[#550000] hover:bg-[#6d0000] text-white px-6 py-3 rounded-lg transition font-medium flex items-center gap-2">
                            Solicitar Documentos
                        </a>
                        <a href="{{ route('funcionario.documentos.subir', $expediente->id_expediente) }}" 
                           class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-3 rounded-lg transition font-medium flex items-center gap-2">
                            Subir Documento (Empresa)
                        </a>
                    </div>

                    {{-- LISTA 1: PENDIENTES DE ACEPTAR O RECHAZAR --}}
<div class="bg-white rounded-lg shadow-sm overflow-hidden mb-6">
    <div class="px-6 py-4 bg-yellow-50 border-b border-yellow-200">
        <h4 class="font-semibold text-yellow-800">
             Pendientes de Aceptar o Rechazar
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
                                {{-- 👁️ VER DOCUMENTO --}}
                                @if($doc->ruta_almac)
                                    <a href="{{ Storage::url($doc->ruta_almac) }}" 
                                       target="_blank" 
                                       class="text-blue-600 hover:text-blue-800 text-xs font-medium transition bg-blue-50 hover:bg-blue-100 px-3 py-1 rounded-lg">
                                         Ver
                                    </a>
                                @endif

                                {{-- ✅ ACEPTAR --}}
                                <form action="{{ route('documentos.validar', $doc->id_documento) }}" method="POST" class="inline">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="estado_doc" value="Validado">
                                    <button type="submit" class="text-green-600 hover:text-green-800 text-xs font-medium bg-green-50 hover:bg-green-100 px-3 py-1 rounded-lg"
                                            onclick="return confirm('¿Aceptar este documento?')">
                                         Aceptar
                                    </button>
                                </form>

                                {{-- ❌ RECHAZAR --}}
                                <form action="{{ route('documentos.validar', $doc->id_documento) }}" method="POST" class="inline">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="estado_doc" value="Rechazado">
                                    <button type="submit" class="text-red-600 hover:text-red-800 text-xs font-medium bg-red-50 hover:bg-red-100 px-3 py-1 rounded-lg"
                                            onclick="return confirm('¿Rechazar este documento?')">
                                         Rechazar
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

                    {{-- LISTA 2: PENDIENTES DE SUBIR --}}
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
                                            <th class="px-4 py-2 text-left text-gray-600 text-xs uppercase">Requerido</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($pendientesSubir as $req)
                                        <tr class="border-b hover:bg-gray-50">
                                            <td class="px-4 py-2 font-medium">{{ $req->nombre }}</td>
                                            <td class="px-4 py-2 text-gray-500">{{ $req->created_at->format('d/m/Y H:i') }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>

                    {{-- LISTA 3: SUBIDOS POR EMPRESA --}}
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
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($subidosEmpresa as $doc)
                                        <tr class="border-b hover:bg-gray-50">
                                            <td class="px-4 py-2 font-medium">{{ $doc->nombre_doc }}</td>
                                            <td class="px-4 py-2 text-gray-600">{{ $doc->tipo_doc }}</td>
                                            <td class="px-4 py-2 text-gray-500">{{ $doc->created_at->format('d/m/Y H:i') }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>

                    {{-- LISTA 4: ACEPTADOS --}}
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
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($aceptados as $doc)
                                        <tr class="border-b hover:bg-gray-50">
                                            <td class="px-4 py-2 font-medium">{{ $doc->nombre_doc }}</td>
                                            <td class="px-4 py-2 text-gray-600">{{ $doc->tipo_doc }}</td>
                                            <td class="px-4 py-2 text-gray-500">{{ $doc->created_at->format('d/m/Y H:i') }}</td>
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
                    No se encontró un cliente con la cédula: <strong>{{ request('cedula') }}</strong>
                </div>
            @endif
        @endif
    </div>
</x-app-layout>