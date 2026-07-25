<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Subir Documento - Empresa
        </h2>
    </x-slot>

    <div class="expediente-container">

        <!-- Pestañas de navegación -->
        <div class="flex gap-2 mb-6 border-b border-gray-200 pb-4">
            <a href="{{ route('documentos.index') }}"
               class="px-4 py-2 rounded-lg text-sm font-medium bg-gray-100 text-gray-600 hover:bg-gray-200 transition">
                Listado
            </a>
            <span class="px-4 py-2 rounded-lg text-sm font-medium bg-[#550000] text-white">
                Subir Documento
            </span>
        </div>

        <h3 class="expediente-titulo">Subir Documento de Empresa</h3>

        {{-- Mensaje de éxito --}}
        @if (session('success'))
            <div class="expediente-mensaje-exito">
                {{ session('success') }}
            </div>
        @endif

        {{-- Mensajes de error generales --}}
        @if ($errors->any())
            <div class="expediente-mensaje-error">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Información del expediente --}}
        <div class="bg-gray-50 p-4 rounded-lg mb-6">
            <p class="text-sm text-gray-600">
                <strong>Expediente #:</strong> {{ $expediente->id_expediente ?? 'N/A' }}
                @if(isset($expediente->cliente))
                    | <strong>Cliente:</strong> {{ $expediente->cliente->nombre ?? 'Sin cliente' }}
                @endif
                | <strong>Estado:</strong> {{ $expediente->estado ?? 'Sin estado' }}
            </p>
        </div>

        <p class="text-sm text-gray-500 mb-4">
            Complete los campos para subir un documento al expediente seleccionado.
        </p>

        {{-- Formulario --}}
        <form action="{{ route('funcionario.documentos.subir-empresa') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            
            {{-- ID del expediente (oculto) --}}
            <input type="hidden" name="id_expediente" value="{{ $expediente->id_expediente ?? '' }}">

            {{-- Nombre del documento --}}
            <div>
                <label for="nombre_doc" class="block text-sm font-medium text-gray-700 mb-1">
                    Nombre del Documento <span class="text-red-600">*</span>
                </label>
                <input type="text" 
                       name="nombre_doc" 
                       id="nombre_doc" 
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#550000] focus:ring focus:ring-[#550000] focus:ring-opacity-50"
                       placeholder="Ej: Contrato de compraventa"
                       value="{{ old('nombre_doc') }}"
                       required>
                @error('nombre_doc')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Tipo de documento --}}
            <div>
                <label for="tipo_doc" class="block text-sm font-medium text-gray-700 mb-1">
                    Tipo de Documento <span class="text-red-600">*</span>
                </label>
                <select name="tipo_doc" 
                        id="tipo_doc" 
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#550000] focus:ring focus:ring-[#550000] focus:ring-opacity-50"
                        required>
                    <option value="">Seleccione un tipo...</option>
                    <option value="PDF">PDF</option>
                    <option value="Word">Word</option>
                    <option value="Excel">Excel</option>
                    <option value="Imagen">Imagen</option>
                    <option value="Otro">Otro</option>
                </select>
                @error('tipo_doc')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Archivo (SOLO PDF) --}}
            <div>
                <label for="archivo" class="block text-sm font-medium text-gray-700 mb-1">
                    Archivo PDF <span class="text-red-600">*</span>
                </label>
                <input type="file" 
                       name="archivo" 
                       id="archivo" 
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#550000] focus:ring focus:ring-[#550000] focus:ring-opacity-50"
                       accept=".pdf"
                       required>
                <p class="text-sm text-red-600 mt-1">
                    Solo se permiten archivos PDF (max. 20 MB)
                </p>
                @error('archivo')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Descripción --}}
            <div>
                <label for="descripcion" class="block text-sm font-medium text-gray-700 mb-1">
                    Descripción
                </label>
                <textarea name="descripcion" 
                          id="descripcion" 
                          rows="4"
                          class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#550000] focus:ring focus:ring-[#550000] focus:ring-opacity-50"
                          placeholder="Breve descripcion del documento...">{{ old('descripcion') }}</textarea>
                @error('descripcion')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Botones --}}
            <div class="flex items-center justify-between mt-6">
                <button type="submit" class="expediente-btn">
                    Subir Documento
                </button>
                <a href="{{ route('funcionario.clientes.index') }}" class="text-gray-600 hover:text-gray-800 transition">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</x-app-layout>