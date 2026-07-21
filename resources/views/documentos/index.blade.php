<x-app-layout>
    <div class="container mx-auto mt-4">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-semibold text-[#1a2a4a]">
                Gestión de Documentos
            </h2>
            <a href="{{ route('documentos.create') }}" 
               class="bg-[#1a2a4a] text-white px-4 py-2 rounded-full hover:bg-[#2a3a5a] transition">
                + Subir Documento
            </a>
        </div>

        @if(session('success'))
            <div class="bg-green-100 text-green-700 p-3 rounded-lg mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if(session('warning'))
            <div class="bg-yellow-100 text-yellow-700 p-3 rounded-lg mb-4">
                {{ session('warning') }}
            </div>
        @endif

        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-4 py-3 text-left text-[#1a2a4a]">ID</th>
                            <th class="px-4 py-3 text-left text-[#1a2a4a]">Nombre</th>
                            <th class="px-4 py-3 text-left text-[#1a2a4a]">Tipo</th>
                            <th class="px-4 py-3 text-left text-[#1a2a4a]">Expediente</th>
                            <th class="px-4 py-3 text-left text-[#1a2a4a]">Estado</th>
                            <th class="px-4 py-3 text-left text-[#1a2a4a]">Fecha</th>
                            <th class="px-4 py-3 text-left text-[#1a2a4a]">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($documentos as $doc)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-3">{{ $doc->id_documento }}</td>
                            <td class="px-4 py-3 font-medium">{{ $doc->nombre_doc }}</td>
                            <td class="px-4 py-3">{{ $doc->tipo_doc }}</td>
                            <td class="px-4 py-3">#{{ $doc->id_expediente }}</td>
                            <td class="px-4 py-3">
                                @if($doc->estado_doc == 'Validado')
                                    <span class="bg-green-500 text-white px-3 py-1 rounded-full text-xs">Validado</span>
                                @elseif($doc->estado_doc == 'Rechazado')
                                    <span class="bg-red-500 text-white px-3 py-1 rounded-full text-xs">Rechazado</span>
                                @else
                                    <span class="bg-yellow-500 text-white px-3 py-1 rounded-full text-xs">Pendiente</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">{{ $doc->fecha_subida->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-3 space-x-1">
                                <a href="{{ route('documentos.show', $doc->id_documento) }}" 
                                   class="bg-blue-500 text-white px-3 py-1 rounded-full text-xs hover:bg-blue-600">Ver</a>
                                
                                @auth
                                    @if(auth()->user()->tipo_usuario != 'Cliente')
                                    <button onclick="openModal({{ $doc->id_documento }})"
                                            class="bg-yellow-500 text-white px-3 py-1 rounded-full text-xs hover:bg-yellow-600">
                                        Validar
                                    </button>
                                    @endif
                                @endauth
                                
                                <form action="{{ route('documentos.destroy', $doc->id_documento) }}" 
                                      method="POST" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded-full text-xs hover:bg-red-600"
                                            onclick="return confirm('¿Eliminar este documento?')">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-8 text-gray-500">
                                <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <p>No hay documentos registrados</p>
                                <p class="text-sm">Sube el primer documento</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-4 py-3 border-t">
                {{ $documentos->links() }}
            </div>
        </div>
    </div>

    {{-- Modales de validación --}}
    @foreach($documentos as $doc)
    <div id="modal-{{ $doc->id_documento }}" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 hidden">
        <div class="bg-white rounded-lg max-w-md w-full p-6">
            <h3 class="text-lg font-semibold text-[#1a2a4a] mb-4">Validar: {{ $doc->nombre_doc }}</h3>
            <form action="{{ route('documentos.validar', $doc->id_documento) }}" method="POST">
                @csrf @method('PATCH')
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                    <select name="estado_doc" class="w-full border rounded-lg px-3 py-2" required>
                        <option value="Validado">✅ Aprobar</option>
                        <option value="Rechazado">❌ Rechazar</option>
                    </select>
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="closeModal({{ $doc->id_documento }})" 
                            class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400">Cancelar</button>
                    <button type="submit" class="bg-[#1a2a4a] text-white px-4 py-2 rounded-lg hover:bg-[#2a3a5a]">Guardar</button>
                </div>
            </form>
        </div>
    </div>
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