<x-app-layout>
    <div class="container mx-auto mt-4 px-4">
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="bg-[#1a2a4a] text-white px-6 py-4">
                <h2 class="text-lg font-semibold">Mis Documentos Requeridos</h2>
                <p class="text-sm text-white/70">Documentos que debe subir para su trámite</p>
            </div>

            <div class="p-6">
                @if(session('success'))
                    <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded-lg mb-4">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-lg mb-4">{{ session('error') }}</div>
                @endif

                {{-- PROGRESO --}}
                @php
                    $total = $pendientesSubir->count() + $subidos->count() + $aceptados->count();
                    $subidosTotal = $subidos->count() + $aceptados->count();
                    $porcentaje = $total > 0 ? round(($subidosTotal / $total) * 100) : 0;
                @endphp

                <div class="mb-6">
                    <div class="flex justify-between text-sm font-medium text-gray-700 mb-1">
                        <span>Progreso</span>
                        <span>{{ $porcentaje }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                        <div class="bg-[#1a2a4a] h-2.5 rounded-full transition-all" style="width: {{ $porcentaje }}%"></div>
                    </div>
                </div>

                {{-- TARJETAS RESUMEN --}}
                <div class="grid grid-cols-4 gap-4 mb-6">
                    <div class="bg-gray-50 rounded-lg p-4 text-center">
                        <p class="text-2xl font-bold text-[#1a2a4a]">{{ $total }}</p>
                        <p class="text-xs text-gray-500">Total</p>
                    </div>
                    <div class="bg-red-50 rounded-lg p-4 text-center">
                        <p class="text-2xl font-bold text-red-600">{{ $pendientesSubir->count() }}</p>
                        <p class="text-xs text-gray-500">Pendientes</p>
                    </div>
                    <div class="bg-yellow-50 rounded-lg p-4 text-center">
                        <p class="text-2xl font-bold text-yellow-600">{{ $subidos->count() }}</p>
                        <p class="text-xs text-gray-500">Subidos</p>
                    </div>
                    <div class="bg-green-50 rounded-lg p-4 text-center">
                        <p class="text-2xl font-bold text-green-600">{{ $aceptados->count() }}</p>
                        <p class="text-xs text-gray-500">Aceptados</p>
                    </div>
                </div>

                {{-- ========================================== --}}
                {{-- LISTA 1: PENDIENTES DE SUBIR --}}
                {{-- ========================================== --}}
                <div class="mb-6">
                    <h3 class="font-semibold text-red-700 bg-red-50 px-4 py-2 rounded-lg border border-red-200 mb-3">
                         Pendientes de Subir ({{ $pendientesSubir->count() }})
                    </h3>
                    @if($pendientesSubir->isEmpty())
                        <p class="text-gray-500 text-sm px-2">✅ No hay documentos pendientes de subir.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-50 border-b">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-[#1a2a4a] font-semibold text-xs uppercase">Documento</th>
                                        <th class="px-4 py-3 text-left text-[#1a2a4a] font-semibold text-xs uppercase">Requerido</th>
                                        <th class="px-4 py-3 text-center text-[#1a2a4a] font-semibold text-xs uppercase">Acción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pendientesSubir as $index => $doc)
                                    <tr class="border-b hover:bg-gray-50">
                                        <td class="px-4 py-3 font-medium">{{ $doc->nombre }}</td>
                                        <td class="px-4 py-3 text-gray-500">
                                            {{ $doc->created_at ? $doc->created_at->format('d/m/Y H:i') : 'Fecha no disponible' }}
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <button onclick="openModal('pendiente', {{ $index }})"
                                                    class="bg-[#1a2a4a] hover:bg-[#2a3a5a] text-white px-4 py-1.5 rounded-lg text-xs font-medium transition">
                                                Subir
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

                {{-- ========================================== --}}
                {{-- LISTA 2: SUBIDOS (PENDIENTES DE VALIDACIÓN) --}}
                {{-- ========================================== --}}
                <div class="mb-6">
                    <h3 class="font-semibold text-yellow-700 bg-yellow-50 px-4 py-2 rounded-lg border border-yellow-200 mb-3">
                         Subidos (Pendientes de Validación) ({{ $subidos->count() }})
                    </h3>
                    @if($subidos->isEmpty())
                        <p class="text-gray-500 text-sm px-2">No hay documentos esperando validación.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-50 border-b">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-[#1a2a4a] font-semibold text-xs uppercase">Documento</th>
                                        <th class="px-4 py-3 text-left text-[#1a2a4a] font-semibold text-xs uppercase">Fecha Subido</th>
                                        <th class="px-4 py-3 text-center text-[#1a2a4a] font-semibold text-xs uppercase">Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($subidos as $doc)
                                    <tr class="border-b hover:bg-gray-50">
                                        <td class="px-4 py-3 font-medium">{{ $doc->nombre_doc }}</td>
                                        <td class="px-4 py-3 text-gray-500">
                                            {{ $doc->created_at ? $doc->created_at->format('d/m/Y H:i') : 'Fecha no disponible' }}
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <span class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-xs font-medium">
                                                Pendiente
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

                {{-- ========================================== --}}
                {{-- LISTA 3: ACEPTADOS --}}
                {{-- ========================================== --}}
                <div class="mb-6">
                    <h3 class="font-semibold text-green-700 bg-green-50 px-4 py-2 rounded-lg border border-green-200 mb-3">
                         Aceptados ({{ $aceptados->count() }})
                    </h3>
                    @if($aceptados->isEmpty())
                        <p class="text-gray-500 text-sm px-2">No hay documentos aceptados.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-50 border-b">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-[#1a2a4a] font-semibold text-xs uppercase">Documento</th>
                                        <th class="px-4 py-3 text-left text-[#1a2a4a] font-semibold text-xs uppercase">Fecha Aceptación</th>
                                        <th class="px-4 py-3 text-center text-[#1a2a4a] font-semibold text-xs uppercase">Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($aceptados as $doc)
                                    <tr class="border-b hover:bg-gray-50">
                                        <td class="px-4 py-3 font-medium">{{ $doc->nombre_doc }}</td>
                                        <td class="px-4 py-3 text-gray-500">
                                            {{ $doc->updated_at ? $doc->updated_at->format('d/m/Y H:i') : 'Fecha no disponible' }}
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-medium">
                                                 Aceptado
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

                <div class="mt-4">
                    <a href="{{ route('cliente.dashboard') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg transition text-sm inline-flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Volver
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- MODALES DE SUBIDA --}}
    @foreach($pendientesSubir as $index => $doc)
    <div id="modal-pendiente-{{ $index }}" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 hidden">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
            <h3 class="text-lg font-semibold text-[#1a2a4a] mb-2">Subir Documento</h3>
            <p class="text-sm text-gray-500 mb-4">Subiendo: <strong>{{ $doc->nombre }}</strong></p>
            <form action="{{ route('cliente.documentos.subir') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="nombre_doc" value="{{ $doc->nombre }}">
                <input type="hidden" name="id_expediente" value="{{ $expedienteId ?? '' }}">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Archivo PDF</label>
                    <input type="file" name="archivo" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-[#1a2a4a] focus:outline-none" accept=".pdf" required>
                    <p class="text-xs text-gray-400 mt-1">PDF, máximo 20MB</p>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="closeModal('pendiente', {{ $index }})" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg transition">Cancelar</button>
                    <button type="submit" class="bg-[#1a2a4a] hover:bg-[#2a3a5a] text-white px-4 py-2 rounded-lg transition">Subir</button>
                </div>
            </form>
        </div>
    </div>
    @endforeach

    <script>
        function openModal(tipo, index) {
            document.getElementById('modal-' + tipo + '-' + index).classList.remove('hidden');
        }
        function closeModal(tipo, index) {
            document.getElementById('modal-' + tipo + '-' + index).classList.add('hidden');
        }
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('bg-black/50')) {
                document.querySelectorAll('.fixed.inset-0.z-50').forEach(el => {
                    if (!el.classList.contains('hidden')) el.classList.add('hidden');
                });
            }
        });
    </script>
</x-app-layout>