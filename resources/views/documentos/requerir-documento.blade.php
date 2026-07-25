<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Requerir Documentos
        </h2>
    </x-slot>

    <div class="expediente-container">

        <!-- Pestañas de navegación -->
        <div class="flex gap-2 mb-6 border-b border-gray-200 pb-4">
            <a href="{{ route('expedientes.index') }}"
               class="px-4 py-2 rounded-lg text-sm font-medium bg-gray-100 text-gray-600 hover:bg-gray-200 transition">
                Listado
            </a>
            <span class="px-4 py-2 rounded-lg text-sm font-medium bg-[#550000] text-white">
                Requerir Documentos
            </span>
        </div>

        <h3 class="expediente-titulo">Requerir Documentos al Cliente</h3>

        {{-- Mensaje de éxito --}}
        @if (session('success'))
            <div class="expediente-mensaje-exito">
                {{ session('success') }}
            </div>
        @endif

        {{-- Mensajes de error --}}
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
            <p class="text-sm text-gray-500 mt-1">
                Seleccione los documentos que desea requerir al cliente.
            </p>
        </div>

        {{-- Formulario para requerir documentos --}}
        <form action="{{ route('documentos.requerir.post') }}" method="POST">
            @csrf
            
            {{-- ID del expediente --}}
            <input type="hidden" name="id_expediente" value="{{ $expediente->id_expediente ?? '' }}">

            {{-- Lista de documentos predefinidos con checkboxes --}}
            <div class="space-y-4">
                <p class="font-semibold text-gray-700">Seleccione los documentos a requerir:</p>

                {{-- Documento 1 --}}
                <div class="border rounded-lg p-4 hover:bg-gray-50 transition">
                    <div class="flex items-start">
                        <input type="checkbox" 
                               name="documentos[]" 
                               value="Cedula de Identidad"
                               id="doc_cedula"
                               class="mt-1 h-5 w-5 text-[#550000] border-gray-300 rounded focus:ring-[#550000]">
                        <label for="doc_cedula" class="ml-3 block">
                            <span class="text-gray-800 font-medium">Cedula de Identidad</span>
                            <span class="text-gray-500 text-sm block">Documento de identificacion oficial del ciudadano.</span>
                        </label>
                    </div>
                </div>

                {{-- Documento 2 --}}
                <div class="border rounded-lg p-4 hover:bg-gray-50 transition">
                    <div class="flex items-start">
                        <input type="checkbox" 
                               name="documentos[]" 
                               value="Comprobante de Domicilio"
                               id="doc_domicilio"
                               class="mt-1 h-5 w-5 text-[#550000] border-gray-300 rounded focus:ring-[#550000]">
                        <label for="doc_domicilio" class="ml-3 block">
                            <span class="text-gray-800 font-medium">Comprobante de Domicilio</span>
                            <span class="text-gray-500 text-sm block">Factura de servicios publicos (agua, luz, telefono) no mayor a 3 meses.</span>
                        </label>
                    </div>
                </div>

                {{-- Documento 3 --}}
                <div class="border rounded-lg p-4 hover:bg-gray-50 transition">
                    <div class="flex items-start">
                        <input type="checkbox" 
                               name="documentos[]" 
                               value="Contrato de Compraventa"
                               id="doc_contrato"
                               class="mt-1 h-5 w-5 text-[#550000] border-gray-300 rounded focus:ring-[#550000]">
                        <label for="doc_contrato" class="ml-3 block">
                            <span class="text-gray-800 font-medium">Contrato de Compraventa</span>
                            <span class="text-gray-500 text-sm block">Documento legal que acredita la compra de la vivienda.</span>
                        </label>
                    </div>
                </div>

                {{-- Documento 4 --}}
                <div class="border rounded-lg p-4 hover:bg-gray-50 transition">
                    <div class="flex items-start">
                        <input type="checkbox" 
                               name="documentos[]" 
                               value="Estado de Cuenta Bancario"
                               id="doc_cuenta"
                               class="mt-1 h-5 w-5 text-[#550000] border-gray-300 rounded focus:ring-[#550000]">
                        <label for="doc_cuenta" class="ml-3 block">
                            <span class="text-gray-800 font-medium">Estado de Cuenta Bancario</span>
                            <span class="text-gray-500 text-sm block">Extracto bancario de los ultimos 3 meses.</span>
                        </label>
                    </div>
                </div>

                {{-- Documento 5 --}}
                <div class="border rounded-lg p-4 hover:bg-gray-50 transition">
                    <div class="flex items-start">
                        <input type="checkbox" 
                               name="documentos[]" 
                               value="Certificado de Ingresos"
                               id="doc_ingresos"
                               class="mt-1 h-5 w-5 text-[#550000] border-gray-300 rounded focus:ring-[#550000]">
                        <label for="doc_ingresos" class="ml-3 block">
                            <span class="text-gray-800 font-medium">Certificado de Ingresos</span>
                            <span class="text-gray-500 text-sm block">Constancia de ingresos emitida por el empleador.</span>
                        </label>
                    </div>
                </div>

                {{-- Documento 6 --}}
                <div class="border rounded-lg p-4 hover:bg-gray-50 transition">
                    <div class="flex items-start">
                        <input type="checkbox" 
                               name="documentos[]" 
                               value="Carta de Recomendacion"
                               id="doc_carta"
                               class="mt-1 h-5 w-5 text-[#550000] border-gray-300 rounded focus:ring-[#550000]">
                        <label for="doc_carta" class="ml-3 block">
                            <span class="text-gray-800 font-medium">Carta de Recomendacion</span>
                            <span class="text-gray-500 text-sm block">Carta de recomendacion de la comunidad o vecinos.</span>
                        </label>
                    </div>
                </div>

                {{-- Documento 7 --}}
                <div class="border rounded-lg p-4 hover:bg-gray-50 transition">
                    <div class="flex items-start">
                        <input type="checkbox" 
                               name="documentos[]" 
                               value="Otro (especificar)"
                               id="doc_otro"
                               class="mt-1 h-5 w-5 text-[#550000] border-gray-300 rounded focus:ring-[#550000]">
                        <label for="doc_otro" class="ml-3 block">
                            <span class="text-gray-800 font-medium">Otro Documento</span>
                            <span class="text-gray-500 text-sm block">Documento adicional requerido por el funcionario.</span>
                        </label>
                    </div>
                </div>
            </div>

            {{-- Campo para descripción adicional (opcional) --}}
            <div class="mt-6">
                <label for="descripcion_adicional" class="block text-sm font-medium text-gray-700 mb-1">
                    Descripción adicional (opcional)
                </label>
                <textarea name="descripcion_adicional" 
                          id="descripcion_adicional" 
                          rows="3"
                          class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#550000] focus:ring focus:ring-[#550000] focus:ring-opacity-50"
                          placeholder="Comentarios adicionales sobre los documentos requeridos...">{{ old('descripcion_adicional') }}</textarea>
            </div>

            {{-- Botones --}}
            <div class="flex items-center justify-between mt-6">
                <button type="submit" class="expediente-btn">
                    Requerir Documentos Seleccionados
                </button>
                <a href="{{ route('expedientes.carpetas.index', $expediente->id_expediente) }}" class="text-gray-600 hover:text-gray-800 transition">
                    Cancelar
                </a>
            </div>
        </form>
    </div>

    {{-- Script para contar documentos seleccionados --}}
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const checkboxes = document.querySelectorAll('input[name="documentos[]"]');
            const submitBtn = document.querySelector('button[type="submit"]');
            
            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const checked = document.querySelectorAll('input[name="documentos[]"]:checked');
                    const count = checked.length;
                    submitBtn.textContent = count === 0 
                        ? 'Requerir Documentos Seleccionados'
                        : `Requerir ${count} Documento${count !== 1 ? 's' : ''} Seleccionado${count !== 1 ? 's' : ''}`;
                });
            });
        });
    </script>
    @endpush
</x-app-layout>