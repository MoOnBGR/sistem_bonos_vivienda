<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Subir Documento
        </h2>
    </x-slot>

    <div class="expediente-container">

        <div class="flex gap-2 mb-6 border-b border-gray-200 pb-4">
            <a href="{{ route('documentos.index') }}"
               class="px-4 py-2 rounded-lg text-sm font-medium bg-gray-100 text-gray-600 hover:bg-gray-200 transition">
                Listado
            </a>
            <span class="px-4 py-2 rounded-lg text-sm font-medium bg-[#550000] text-white">
                Subir Documento
            </span>
        </div>

        <h3 class="expediente-titulo">Subir Documento al Expediente</h3>

        @if (session('success'))
            <div class="expediente-mensaje-exito">
                {{ session('success') }}
            </div>
        @endif

        @if(session('warning'))
            <div class="bg-yellow-50 border-l-4 border-yellow-500 text-yellow-700 p-4 rounded-lg mb-4">
                {{ session('warning') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="expediente-mensaje-error">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(isset($expedienteId) && $expedienteId)
            @php
                $expSeleccionado = $expedientes->firstWhere('id_expediente', $expedienteId);
            @endphp
            @if($expSeleccionado)
                <div class="bg-gray-50 p-4 rounded-lg mb-6">
                    <p class="text-sm text-gray-600">
                        <strong>Expediente #:</strong> {{ $expSeleccionado->id_expediente ?? 'N/A' }}
                        @if(isset($expSeleccionado->cliente))
                            | <strong>Cliente:</strong> {{ $expSeleccionado->cliente->nombre ?? 'Sin cliente' }}
                        @endif
                        | <strong>Estado:</strong> {{ $expSeleccionado->estado ?? 'Sin estado' }}
                    </p>
                </div>
            @endif
        @endif

        <p class="text-sm text-gray-500 mb-4">
            Complete los campos para subir un documento al expediente seleccionado.
        </p>

        <form action="{{ route('documentos.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf

            {{-- ✅ CAMPO OCULTO PARA LA CÉDULA --}}
            <input type="hidden" name="cedula" value="{{ $cedula ?? '' }}">

            <div>
                <label for="id_expediente" class="block text-sm font-medium text-gray-700 mb-1">
                    Expediente <span class="text-red-600">*</span>
                </label>
                <select name="id_expediente" 
                        id="id_expediente"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#550000] focus:ring focus:ring-[#550000] focus:ring-opacity-50"
                        required>
                    <option value="">Seleccione un expediente...</option>
                    @foreach($expedientes as $exp)
                        <option value="{{ $exp->id_expediente }}" {{ isset($expedienteId) && $expedienteId == $exp->id_expediente ? 'selected' : '' }}>
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
                @error('id_expediente')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="nombre_doc" class="block text-sm font-medium text-gray-700 mb-1">
                    Nombre del Documento <span class="text-red-600">*</span>
                </label>
                <input type="text" 
                       name="nombre_doc" 
                       id="nombre_doc" 
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#550000] focus:ring focus:ring-[#550000] focus:ring-opacity-50"
                       placeholder="Ej: Cedula de Identidad"
                       value="{{ old('nombre_doc') }}"
                       required>
                @error('nombre_doc')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="tipo_doc" class="block text-sm font-medium text-gray-700 mb-1">
                    Tipo de Documento <span class="text-red-600">*</span>
                </label>
                <select name="tipo_doc" 
                        id="tipo_doc" 
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#550000] focus:ring focus:ring-[#550000] focus:ring-opacity-50"
                        required>
                    <option value="">Seleccione un tipo...</option>
                    <option value="Cedula">Cedula</option>
                    <option value="Planos">Planos</option>
                    <option value="Constancia de Ingresos">Constancia de Ingresos</option>
                    <option value="Certificado de Propiedad">Certificado de Propiedad</option>
                    <option value="Otro">Otro</option>
                </select>
                @error('tipo_doc')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

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

            <div class="flex items-center justify-between mt-6">
                <button type="submit" class="bg-[#550000] hover:bg-[#6d0000] text-white px-6 py-2 rounded-lg transition font-medium">
                    Subir Documento
                </button>
                <a href="{{ route('funcionario.documentos.buscar', ['cedula' => $cedula ?? '']) }}" 
                   class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-2 rounded-lg transition text-sm">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</x-app-layout>