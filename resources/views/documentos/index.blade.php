<x-app-layout>
    <div class="container mx-auto mt-4 px-4">

        {{-- ENCABEZADO --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
            <div>
                <h2 class="text-2xl font-bold text-[#1a2a4a]">Documentos</h2>
                <p class="text-sm text-gray-500">Gestion de documentos de los expedientes</p>
            </div>
            <a href="{{ route('documentos.create') }}" 
               class="mt-3 md:mt-0 bg-[#550000] text-white px-6 py-3 rounded-lg hover:bg-[#6d0000] transition shadow-sm flex items-center gap-2 text-sm font-medium">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Nuevo Documento
            </a>
        </div>

        {{-- PESTAÑAS DE NAVEGACIÓN --}}
        <div class="flex flex-wrap gap-2 mb-4 border-b border-gray-200 pb-2">
            <a href="{{ route('documentos.index') }}" 
               class="px-4 py-2 rounded-lg text-sm font-medium transition 
               {{ request()->routeIs('documentos.index') ? 'bg-[#550000] text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                Todos los Documentos
            </a>
            <a href="{{ route('funcionario.documentos.buscar') }}" 
               class="px-4 py-2 rounded-lg text-sm font-medium transition 
               {{ request()->routeIs('funcionario.documentos.buscar*') ? 'bg-[#550000] text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
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

        {{-- TABLA --}}
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-4 py-3 text-left text-[#1a2a4a] font-semibold text-xs uppercase tracking-wider">#</th>
                            <th class="px-4 py-3 text-left text-[#1a2a4a] font-semibold text-xs uppercase tracking-wider">Nombre</th>
                            <th class="px-4 py-3 text-left text-[#1a2a4a] font-semibold text-xs uppercase tracking-wider">Tipo</th>
                            <th class="px-4 py-3 text-left text-[#1a2a4a] font-semibold text-xs uppercase tracking-wider">Expediente</th>
                            <th class="px-4 py-3 text-left text-[#1a2a4a] font-semibold text-xs uppercase tracking-wider">Estado</th>
                            <th class="px-4 py-3 text-left text-[#1a2a4a] font-semibold text-xs uppercase tracking-wider">Fecha</th>
                            <th class="px-4 py-3 text-center text-[#1a2a4a] font-semibold text-xs uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($documentos as $index => $doc)
                        <tr class="border-b hover:bg-gray-50 transition">
                            <td class="px-4 py-3 text-gray-500">{{ $index + 1 }}</td>
                            <td class="px-4 py-3 font-medium text-gray-800">{{ $doc->nombre_doc }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $doc->tipo_doc }}</td>
                            <td class="px-4 py-3 text-gray-600">#{{ $doc->id_expediente }}</td>
                            <td class="px-4 py-3">
                                @if($doc->estado_doc == 'Validado')
                                    <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-medium">Validado</span>
                                @elseif($doc->estado_doc == 'Rechazado')
                                    <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs font-medium">Rechazado</span>
                                @else
                                    <span class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-xs font-medium">Pendiente</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-500">{{ $doc->fecha_subida->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap items-center justify-center gap-2">
                                    <a href="{{ route('documentos.show', $doc->id_documento) }}" 
                                       class="text-blue-600 hover:text-blue-800 text-xs font-medium transition">Ver</a>

                                    @if(auth()->user()->tipo_usuario != 'Cliente')
                                    <button onclick="openModal({{ $doc->id_documento }})"
                                            class="text-yellow-600 hover:text-yellow-800 text-xs font-medium transition">Validar</button>
                                    @endif

                                    <form action="{{ route('documentos.destroy', $doc->id_documento) }}" method="POST" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 text-xs font-medium transition"
                                                onclick="return confirm('¿Eliminar este documento?')">Eliminar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-12 text-gray-500">
                                <p>No hay documentos registrados.</p>
                                <p class="text-sm text-gray-400">Haga clic en "Nuevo Documento" para subir el primero.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-4 py-3 border-t bg-gray-50">
                {{ $documentos->links() }}
            </div>
        </div>
    </div>

    {{-- MODAL VALIDACIÓN --}}
    @foreach($documentos as $doc)
    <div id="modal-{{ $doc->id_documento }}" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 hidden">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
            <h3 class="text-lg font-semibold text-[#1a2a4a] mb-2">Validar Documento</h3>
            <p class="text-sm text-gray-500 mb-4">Cambiar el estado de: <strong>{{ $doc->nombre_doc }}</strong></p>
            <form action="{{ route('documentos.validar', $doc->id_documento) }}" method="POST">
                @csrf @method('PATCH')
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                    <select name="estado_doc" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-[#550000] focus:outline-none" required>
                        <option value="Validado">Aprobar</option>
                        <option value="Rechazado">Rechazar</option>
                    </select>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="closeModal({{ $doc->id_documento }})" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg transition">Cancelar</button>
                    <button type="submit" class="bg-[#550000] hover:bg-[#6d0000] text-white px-4 py-2 rounded-lg transition">Guardar</button>
                </div>
            </form>
        </div>
    </div>
    @endforeach

    <script>
        function openModal(id) { document.getElementById('modal-' + id).classList.remove('hidden'); }
        function closeModal(id) { document.getElementById('modal-' + id).classList.add('hidden'); }
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('bg-black/50')) e.target.classList.add('hidden');
        });
    </script>
</x-app-layout>