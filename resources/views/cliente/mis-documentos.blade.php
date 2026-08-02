<x-app-layout>
    <div class="container mx-auto mt-4 px-4">
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="bg-[#550000] text-white px-6 py-4">
                <h2 class="text-lg font-semibold">Mis Documentos</h2>
                <p class="text-sm text-white/70">Documentos requeridos para su tramite</p>
            </div>

            <div class="p-6">

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

                @if(session('warning'))
                    <div class="bg-yellow-50 border-l-4 border-yellow-500 text-yellow-700 p-4 rounded-lg mb-4">
                        {{ session('warning') }}
                    </div>
                @endif

                {{-- BARRA DE PROGRESO --}}
                @php
                    $totalDocumentos = $pendientesSubir->count() + $subidos->count() + $aceptados->count();
                    $completados = $subidos->count() + $aceptados->count();
                    $porcentaje = $totalDocumentos > 0 ? round(($completados / $totalDocumentos) * 100) : 0;
                @endphp

                @if($totalDocumentos > 0)
                    <div class="mb-6">
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-sm font-medium text-gray-700">Progreso de documentos</span>
                            <span class="text-sm font-medium text-[#6d0000]">{{ $porcentaje }}% completado</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-4 overflow-hidden">
                            <div class="h-4 rounded-full transition-all duration-500" style="width: {{ $porcentaje }}%; background-color: #6d0000;"></div>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">
                            Documentos completados: {{ $completados }} de {{ $totalDocumentos }}
                        </p>
                    </div>
                @endif

                {{-- Subidos (Pendientes de Validacion) --}}
                <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-6">
                    <div class="px-6 py-4 bg-yellow-50 border-b border-yellow-200">
                        <h4 class="font-semibold text-yellow-800">
                            Subidos (Pendientes de Validacion)
                            <span class="text-sm font-normal text-yellow-600 ml-2">
                                ({{ $subidos->count() }} documentos)
                            </span>
                        </h4>
                    </div>
                    @if($subidos->isEmpty())
                        <div class="p-4 text-gray-500 text-sm">No hay documentos pendientes de validacion.</div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-gray-600 text-xs uppercase">Nombre</th>
                                        <th class="px-4 py-2 text-left text-gray-600 text-xs uppercase">Fecha</th>
                                        <th class="px-4 py-2 text-center text-gray-600 text-xs uppercase">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($subidos as $doc)
                                    <tr class="border-b hover:bg-gray-50">
                                        <td class="px-4 py-2 font-medium">{{ $doc->nombre_doc }}</td>
                                        <td class="px-4 py-2 text-gray-500">{{ $doc->created_at->format('d/m/Y H:i') }}</td>
                                        <td class="px-4 py-2 text-center">
                                            <div class="flex justify-center items-center gap-3">
                                                <a href="{{ Storage::url($doc->ruta_almac) }}" 
                                                   target="_blank" 
                                                   class="bg-green-800 hover:bg-[#6d0000] text-white px-5 py-2.5 rounded-lg text-sm font-medium transition">
                                                    Ver
                                                </a>
                                                <form action="{{ route('cliente.documentos.eliminar', $doc->id_documento) }}" method="POST" class="inline">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="bg-[#550000] hover:bg-[#6d0000] text-white px-5 py-2.5 rounded-lg text-sm font-medium transition"
                                                            onclick="return confirm('¿Eliminar este documento? Volvera a aparecer en pendientes de subir.')">
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
                                        <th class="px-4 py-2 text-left text-gray-600 text-xs uppercase">Motivo (si fue rechazado)</th>
                                        <th class="px-4 py-2 text-center text-gray-600 text-xs uppercase">Accion</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pendientesSubir as $req)
                                    <tr class="border-b hover:bg-gray-50">
                                        <td class="px-4 py-2 font-medium">{{ $req->nombre ?? 'Documento' }}</td>
                                        <td class="px-4 py-2 text-gray-500">
                                            @if(isset($req->motivo_rechazo))
                                                <span class="text-red-600 text-xs">Rechazado: {{ $req->motivo_rechazo }}</span>
                                            @else
                                                <span class="text-gray-400 text-xs">Pendiente de subir</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-2 text-center">
                                            <form action="{{ route('cliente.documentos.subir') }}" method="POST" enctype="multipart/form-data" class="inline-flex items-center gap-2">
                                                @csrf
                                                <input type="hidden" name="id_expediente" value="{{ $expedienteId }}">
                                                <input type="hidden" name="nombre_doc" value="{{ $req->nombre ?? 'Documento' }}">
                                                <input type="file" name="archivo" accept=".pdf" class="text-sm border border-gray-300 rounded-lg px-3 py-1.5" required>
                                                <button type="submit" class="bg-[#550000] hover:bg-[#6d0000] text-white px-5 py-2.5 rounded-lg text-sm font-medium transition">
                                                    Subir
                                                </button>
                                            </form>
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
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($rechazados as $doc)
                                    <tr class="border-b hover:bg-gray-50">
                                        <td class="px-4 py-2 font-medium">{{ $doc->nombre_doc }}</td>
                                        <td class="px-4 py-2 text-red-600 text-sm">{{ $doc->motivo_rechazo ?? 'Sin motivo' }}</td>
                                        <td class="px-4 py-2 text-gray-500">{{ $doc->created_at->format('d/m/Y H:i') }}</td>
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
                                        <th class="px-4 py-2 text-left text-gray-600 text-xs uppercase">Fecha</th>
                                        <th class="px-4 py-2 text-center text-gray-600 text-xs uppercase">Accion</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($aceptados as $doc)
                                    <tr class="border-b hover:bg-gray-50">
                                        <td class="px-4 py-2 font-medium">{{ $doc->nombre_doc }}</td>
                                        <td class="px-4 py-2 text-gray-500">{{ $doc->created_at->format('d/m/Y H:i') }}</td>
                                        <td class="px-4 py-2 text-center">
                                            <a href="{{ Storage::url($doc->ruta_almac) }}" 
                                               target="_blank" 
                                               class="bg-[#550000] hover:bg-[#6d0000] text-white px-5 py-2.5 rounded-lg text-sm font-medium transition inline-block">
                                                Ver
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

                <div class="mt-4">
                    <a href="{{ route('cliente.dashboard') }}" 
                       class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-5 py-2.5 rounded-lg transition text-sm inline-block">
                        Volver
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>