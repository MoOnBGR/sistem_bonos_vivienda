<x-app-layout>
    <div class="container mx-auto mt-4 px-4 max-w-2xl">
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="bg-[#1a2a4a] text-white px-6 py-4">
                <h2 class="text-lg font-semibold">Nuevo Documento</h2>
                <p class="text-sm text-white/70">Complete los campos para subir un nuevo documento</p>
            </div>

            <div class="p-6">
                <form action="{{ route('documentos.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    {{-- EXPEDIENTE - SELECTOR MEJORADO --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Expediente <span class="text-red-500">*</span></label>
                        <select name="id_expediente" 
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-[#1a2a4a] focus:outline-none" 
                                required>
                            <option value="">Seleccione un expediente...</option>
                            @foreach($expedientes as $exp)
                                <option value="{{ $exp->id_expediente }}">
                                    @if(isset($exp->codigo_expediente))
                                        {{ $exp->codigo_expediente }}
                                    @else
                                        EXP-{{ str_pad($exp->id_expediente, 4, '0', STR_PAD_LEFT) }}
                                    @endif
                                    - 
                                    {{ $exp->cliente->nombre ?? 'Sin cliente' }}
                                    {{ $exp->cliente->apellidos ?? '' }}
                                    (ID: {{ $exp->id_expediente }})
                                </option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-400 mt-1">Seleccione el expediente al que pertenece el documento</p>
                    </div>

                    {{-- NOMBRE DEL DOCUMENTO --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre del Documento <span class="text-red-500">*</span></label>
                        <input type="text" name="nombre_doc" 
                               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-[#1a2a4a] focus:outline-none" 
                               placeholder="Ej: Cédula de Identidad" required>
                    </div>

                    {{-- TIPO DE DOCUMENTO --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Documento <span class="text-red-500">*</span></label>
                        <select name="tipo_doc" 
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-[#1a2a4a] focus:outline-none" 
                                required>
                            <option value="">Seleccione...</option>
                            <option value="Cédula">Cédula</option>
                            <option value="Planos">Planos</option>
                            <option value="Constancia de Ingresos">Constancia de Ingresos</option>
                            <option value="Certificado de Propiedad">Certificado de Propiedad</option>
                            <option value="Otro">Otro</option>
                        </select>
                    </div>

                    {{-- ARCHIVO --}}
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Archivo <span class="text-red-500">*</span></label>
                        <input type="file" name="archivo" 
                               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-[#1a2a4a] focus:outline-none" 
                               accept=".pdf" required>
                        <p class="text-xs text-gray-400 mt-1">PDF, máximo 20MB</p>
                    </div>

                    {{-- BOTONES --}}
                    <div class="flex justify-between items-center">
                        <a href="{{ route('documentos.index') }}" 
                           class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-2 rounded-lg transition text-sm">
                            Cancelar
                        </a>
                        <button type="submit" 
                                class="bg-[#1a2a4a] hover:bg-[#2a3a5a] text-white px-6 py-2 rounded-lg transition text-sm flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                            </svg>
                            Guardar Documento
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>