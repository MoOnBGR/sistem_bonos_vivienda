<x-app-layout>
    <div class="container mx-auto mt-4">
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-2xl font-semibold text-[#1a2a4a] mb-6">
                Mis Documentos Requeridos
            </h2>

            @if(session('success'))
                <div class="bg-green-100 text-green-700 p-3 rounded-lg mb-4">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 text-red-700 p-3 rounded-lg mb-4">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Barra de progreso --}}
            <div class="mb-6">
                <div class="flex justify-between mb-1">
                    <span>Progreso</span>
                    <span>{{ $porcentaje ?? 0 }}%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-4">
                    <div class="bg-[#1a2a4a] h-4 rounded-full" style="width: {{ $porcentaje ?? 0 }}%"></div>
                </div>
            </div>

            {{-- Resumen --}}
            <div class="grid grid-cols-3 gap-4 mb-6">
                <div class="bg-gray-100 rounded-lg p-4 text-center">
                    <p class="text-2xl font-bold text-[#1a2a4a]">{{ count($documentosRequeridos ?? []) }}</p>
                    <p class="text-sm text-gray-600">Total</p>
                </div>
                <div class="bg-green-100 rounded-lg p-4 text-center">
                    <p class="text-2xl font-bold text-green-600">{{ $subidos ?? 0 }}</p>
                    <p class="text-sm text-gray-600">Subidos</p>
                </div>
                <div class="bg-red-100 rounded-lg p-4 text-center">
                    <p class="text-2xl font-bold text-red-600">{{ $faltantes ?? 0 }}</p>
                    <p class="text-sm text-gray-600">Pendientes</p>
                </div>
            </div>

            {{-- Tabla --}}
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-4 py-3 text-left text-[#1a2a4a]">#</th>
                            <th class="px-4 py-3 text-left text-[#1a2a4a]">Documento</th>
                            <th class="px-4 py-3 text-left text-[#1a2a4a]">Estado</th>
                            <th class="px-4 py-3 text-left text-[#1a2a4a]">Fecha</th>
                            <th class="px-4 py-3 text-left text-[#1a2a4a]">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($documentosRequeridos ?? [] as $index => $doc)
                        <tr class="border-b">
                            <td class="px-4 py-3">{{ $index + 1 }}</td>
                            <td class="px-4 py-3 font-medium">{{ $doc['nombre'] }}</td>
                            <td class="px-4 py-3">
                                @if($doc['subido'])
                                    <span class="bg-green-500 text-white px-3 py-1 rounded-full text-xs">✅ Subido</span>
                                @else
                                    <span class="bg-red-500 text-white px-3 py-1 rounded-full text-xs">❌ Pendiente</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($doc['fecha_subida'])
                                    {{ $doc['fecha_subida']->format('d/m/Y H:i') }}
                                @else
                                    <span class="text-gray-400">No subido</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($doc['subido'])
                                    <a href="#" class="bg-blue-500 text-white px-3 py-1 rounded-full text-xs hover:bg-blue-600">Descargar</a>
                                @else
                                    <button class="bg-[#1a2a4a] text-white px-3 py-1 rounded-full text-xs hover:bg-[#2a3a5a]"
                                            onclick="openModal({{ $index }})">Subir</button>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-8 text-gray-500">
                                <p>No hay documentos requeridos</p>
                                <p class="text-sm">Contacta al funcionario encargado</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(isset($todosSubidos) && $todosSubidos)
                <div class="mt-4 bg-green-100 text-green-700 p-3 rounded-lg">
                    ✅ ¡Felicidades! Todos tus documentos están subidos.
                </div>
            @elseif(isset($faltantes) && $faltantes > 0)
                <div class="mt-4 bg-yellow-100 text-yellow-700 p-3 rounded-lg">
                    ⚠️ Te faltan {{ $faltantes }} documento(s) por subir.
                </div>
            @endif

            <div class="mt-4">
                <a href="{{ route('cliente.dashboard') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400">← Volver</a>
            </div>
        </div>
    </div>

    {{-- Modales de subida --}}
    @foreach($documentosRequeridos ?? [] as $index => $doc)
        @if(!$doc['subido'])
        <div id="modal-{{ $index }}" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 hidden">
            <div class="bg-white rounded-lg max-w-md w-full p-6">
                <h3 class="text-lg font-semibold text-[#1a2a4a] mb-4">Subir: {{ $doc['nombre'] }}</h3>
                <form action="#" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="tipo_documento" value="{{ $doc['nombre'] }}">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Seleccionar archivo PDF</label>
                        <input type="file" name="archivo" class="w-full border rounded-lg px-3 py-2" accept=".pdf" required>
                        <small class="text-gray-500">PDF, máximo 20MB</small>
                    </div>
                    <div class="flex justify-end space-x-2">
                        <button type="button" onclick="closeModal({{ $index }})" 
                                class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400">Cancelar</button>
                        <button type="submit" class="bg-[#1a2a4a] text-white px-4 py-2 rounded-lg hover:bg-[#2a3a5a]">Subir</button>
                    </div>
                </form>
            </div>
        </div>
        @endif
    @endforeach

    <script>
    function openModal(id) {
        document.getElementById('modal-' + id).classList.remove('hidden');
    }
    function closeModal(id) {
        document.getElementById('modal-' + id).classList.add('hidden');
    }
    </script>
</x-app-layout>