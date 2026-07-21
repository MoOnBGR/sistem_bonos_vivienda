<x-app-layout>
    <div class="container mx-auto mt-4">
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-2xl font-semibold text-[#1a2a4a] mb-6">
                Documentos de: {{ $cliente->nombre ?? 'Cliente' }}
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

            {{-- Información del cliente --}}
            <div class="bg-gray-50 rounded-lg p-4 mb-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p><strong>Cliente:</strong> {{ $cliente->nombre ?? 'N/A' }} {{ $cliente->apellidos ?? '' }}</p>
                        <p><strong>Cédula:</strong> {{ $cliente->identificacion ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p><strong>Expediente:</strong> #{{ $expediente->id_expediente ?? 'N/A' }}</p>
                        <p><strong>Estado:</strong> 
                            <span class="px-3 py-1 rounded-full text-xs {{ $expediente->estado == 'Cerrado' ? 'bg-red-500 text-white' : 'bg-green-500 text-white' }}">
                                {{ $expediente->estado ?? 'Sin expediente' }}
                            </span>
                        </p>
                    </div>
                </div>
            </div>

            {{-- Formulario para requerir --}}
            @if($expediente && $expediente->estado != 'Cerrado')
            <div class="bg-gray-50 rounded-lg p-4 mb-4">
                <h3 class="font-semibold text-[#1a2a4a] mb-3">Requerir nuevo documento</h3>
                <form action="{{ route('funcionario.documentos.requerir') }}" method="POST" class="flex gap-3">
                    @csrf
                    <input type="hidden" name="id_expediente" value="{{ $expediente->id_expediente ?? '' }}">
                    <input type="text" name="tipo_documento" placeholder="Tipo de documento" class="flex-1 border rounded-lg px-3 py-2" required>
                    <input type="text" name="descripcion" placeholder="Descripción (opcional)" class="flex-1 border rounded-lg px-3 py-2">
                    <button type="submit" class="bg-[#1a2a4a] text-white px-4 py-2 rounded-lg hover:bg-[#2a3a5a]">Requerir</button>
                </form>
            </div>
            @endif

            {{-- Tabla --}}
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-4 py-3 text-left text-[#1a2a4a]">#</th>
                            <th class="px-4 py-3 text-left text-[#1a2a4a]">Documento</th>
                            <th class="px-4 py-3 text-left text-[#1a2a4a]">Tipo</th>
                            <th class="px-4 py-3 text-left text-[#1a2a4a]">Estado</th>
                            <th class="px-4 py-3 text-left text-[#1a2a4a]">Fecha</th>
                            <th class="px-4 py-3 text-left text-[#1a2a4a]">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($documentos as $doc)
                        <tr class="border-b">
                            <td class="px-4 py-3">{{ $doc->id_documento }}</td>
                            <td class="px-4 py-3 font-medium">{{ $doc->nombre_doc }}</td>
                            <td class="px-4 py-3">{{ $doc->tipo_doc }}</td>
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
                                <a href="{{ asset('storage/' . $doc->ruta_almac) }}" 
                                   target="_blank" 
                                   class="bg-green-500 text-white px-3 py-1 rounded-full text-xs hover:bg-green-600">Descargar</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-8 text-gray-500">
                                <p>No hay documentos para este cliente</p>
                                <p class="text-sm">Requerir documentos usando el formulario</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                <a href="{{ route('funcionario.clientes.show', $cliente->Id_cliente ?? $cliente->id) }}" 
                   class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400">← Volver al Cliente</a>
            </div>
        </div>
    </div>
</x-app-layout>