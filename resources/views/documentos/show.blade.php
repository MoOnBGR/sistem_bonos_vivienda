<x-app-layout>
    <div class="container mx-auto mt-4 max-w-3xl">
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-2xl font-semibold text-[#1a2a4a] mb-4">
                {{ $documento->nombre_doc }}
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p><strong class="text-[#1a2a4a]">ID:</strong> {{ $documento->id_documento }}</p>
                    <p><strong class="text-[#1a2a4a]">Tipo:</strong> {{ $documento->tipo_doc }}</p>
                    <p><strong class="text-[#1a2a4a]">Estado:</strong>
                        @if($documento->estado_doc == 'Validado')
                            <span class="bg-green-500 text-white px-3 py-1 rounded-full text-xs">Validado</span>
                        @elseif($documento->estado_doc == 'Rechazado')
                            <span class="bg-red-500 text-white px-3 py-1 rounded-full text-xs">Rechazado</span>
                        @else
                            <span class="bg-yellow-500 text-white px-3 py-1 rounded-full text-xs">Pendiente</span>
                        @endif
                    </p>
                    <p><strong class="text-[#1a2a4a]">Expediente:</strong> #{{ $documento->id_expediente }}</p>
                    <p><strong class="text-[#1a2a4a]">Fecha:</strong> {{ $documento->fecha_subida->format('d/m/Y H:i') }}</p>
                </div>
                <div class="text-center border rounded-lg p-4">
                    <svg class="w-16 h-16 mx-auto text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                    <p class="mt-2"><strong>Vista previa</strong></p>
                    <a href="{{ asset('storage/' . $documento->ruta_almac) }}" 
                       target="_blank" 
                       class="bg-[#1a2a4a] text-white px-4 py-2 rounded-lg inline-block mt-2 hover:bg-[#2a3a5a]">
                        Abrir Documento
                    </a>
                </div>
            </div>

            <div class="mt-4">
                <a href="{{ route('documentos.index') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400">← Volver</a>
            </div>
        </div>
    </div>
</x-app-layout>