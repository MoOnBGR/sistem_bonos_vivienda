<x-app-layout>
    <div class="container mx-auto mt-4 px-4 max-w-3xl">
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="bg-[#1a2a4a] text-white px-6 py-4 flex justify-between items-center">
                <div>
                    <h2 class="text-lg font-semibold">Detalle del Documento</h2>
                    <p class="text-sm text-white/70">{{ $documento->nombre_doc }}</p>
                </div>
                <span class="bg-white/20 text-white px-3 py-1 rounded-full text-xs">ID: {{ $documento->id_documento }}</span>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="mb-2"><span class="font-semibold text-[#1a2a4a]">Nombre:</span> {{ $documento->nombre_doc }}</p>
                        <p class="mb-2"><span class="font-semibold text-[#1a2a4a]">Tipo:</span> {{ $documento->tipo_doc }}</p>
                        <p class="mb-2"><span class="font-semibold text-[#1a2a4a]">Expediente:</span> #{{ $documento->id_expediente }}</p>
                        <p class="mb-2"><span class="font-semibold text-[#1a2a4a]">Fecha:</span> {{ $documento->fecha_subida->format('d/m/Y H:i') }}</p>
                        <p class="mb-2"><span class="font-semibold text-[#1a2a4a]">Estado:</span>
                            @if($documento->estado_doc == 'Validado')
                                <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-medium">Validado</span>
                            @elseif($documento->estado_doc == 'Rechazado')
                                <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs font-medium">Rechazado</span>
                            @else
                                <span class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-xs font-medium">Pendiente</span>
                            @endif
                        </p>
                        @if($documento->id_funcionario)
                            <p class="mb-2"><span class="font-semibold text-[#1a2a4a]">Subido por:</span> {{ $documento->funcionario->name ?? 'Funcionario' }}</p>
                        @endif
                    </div>
                    <div class="flex flex-col items-center justify-center border rounded-lg p-6 bg-gray-50">
                        <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                        <p class="text-sm text-gray-500 mt-2">Vista previa del documento</p>
                        <a href="{{ asset('storage/' . $documento->ruta_almac) }}" target="_blank" 
                           class="mt-3 bg-[#1a2a4a] hover:bg-[#2a3a5a] text-white px-4 py-2 rounded-lg transition text-sm flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            Abrir Documento
                        </a>
                    </div>
                </div>

                <div class="mt-6">
                    <a href="{{ route('documentos.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg transition text-sm inline-flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Volver
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>