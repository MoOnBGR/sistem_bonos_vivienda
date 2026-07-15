<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Editar Cliente
        </h2>
    </x-slot>

    <div class="w-full">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

            <form method="POST" action="{{ route('funcionario.clientes.actualizar', $cliente) }}">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 lg:grid-cols-[380px_1fr] gap-x-10 gap-y-6 items-start">

                    {{-- Columna izquierda: datos personales --}}
                    <div class="max-w-md">
                        <div>
                            <x-input-label for="identificacion" value="Identificación" />
                            <x-text-input id="identificacion" name="identificacion" type="text" class="mt-1 block w-full" :value="old('identificacion', $cliente->identificacion)" required autofocus />
                            <x-input-error :messages="$errors->get('identificacion')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="nombre" value="Nombre" />
                            <x-text-input id="nombre" name="nombre" type="text" class="mt-1 block w-full" :value="old('nombre', $cliente->nombre)" required />
                            <x-input-error :messages="$errors->get('nombre')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="apellidos" value="Apellidos" />
                            <x-text-input id="apellidos" name="apellidos" type="text" class="mt-1 block w-full" :value="old('apellidos', $cliente->apellidos)" required />
                            <x-input-error :messages="$errors->get('apellidos')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="telefono" value="Teléfono" />
                            <x-text-input id="telefono" name="telefono" type="text" class="mt-1 block w-full" :value="old('telefono', $cliente->telefono)" required />
                            <x-input-error :messages="$errors->get('telefono')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="direccion" value="Dirección" />
                            <textarea id="direccion" name="direccion" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>{{ old('direccion', $cliente->direccion) }}</textarea>
                            <x-input-error :messages="$errors->get('direccion')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="correo" value="Correo de Contacto" />
                            <x-text-input id="correo" name="correo" type="email" class="mt-1 block w-full" :value="old('correo', $cliente->correo)" required />
                            <x-input-error :messages="$errors->get('correo')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="estado" value="Estado" />
                            <select id="estado" name="estado" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="Activo" {{ old('estado', $cliente->estado) === 'Activo' ? 'selected' : '' }}>Activo</option>
                                <option value="Inactivo" {{ old('estado', $cliente->estado) === 'Inactivo' ? 'selected' : '' }}>Inactivo</option>
                            </select>
                            <x-input-error :messages="$errors->get('estado')" class="mt-2" />
                        </div>
                    </div>

                    {{-- Columna derecha: documentos requeridos --}}
                    <div class="lg:border-l lg:border-gray-200 lg:pl-10">
                        <h3 class="text-base font-semibold text-gray-800 mb-1">Documentos requeridos</h3>
                        <p class="text-sm text-gray-500 mb-4">Seleccione los documentos que debe presentar este cliente.</p>

                        @php
                            $seleccionados = old('documentos', $documentosSeleccionados ?? []);
                            $otrosIniciales = old('otros', ($otrosDocumentos ?? []) ?: ['']);
                        @endphp

                        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 gap-x-6 gap-y-2">
                            @foreach($documentosCatalogo as $documento)
                                <label class="flex items-start gap-2 text-sm text-gray-700">
                                    <input type="checkbox"
                                        name="documentos[]"
                                        value="{{ $documento->Id_DocumentoRequerido }}"
                                        class="mt-0.5 rounded border-gray-300 text-[#550000] focus:ring-[#550000]"
                                        {{ in_array($documento->Id_DocumentoRequerido, $seleccionados) ? 'checked' : '' }}>
                                    <span>{{ $documento->nombre }}</span>
                                </label>
                            @endforeach
                        </div>

                        <x-input-error :messages="$errors->get('documentos')" class="mt-2" />

                        {{-- Otros: lista dinámica de documentos personalizados --}}
                        <div class="mt-4" x-data="{ otros: {{ Illuminate\Support\Js::from($otrosIniciales) }} }">
                            <label class="flex items-center gap-2 text-sm font-medium text-gray-700 mb-2">
                                <input type="checkbox"
                                    class="rounded border-gray-300 text-[#550000] focus:ring-[#550000]"
                                    :checked="otros.length > 0"
                                    @change="if ($event.target.checked) { if (otros.length === 0) otros.push('') } else { otros = [] }">
                                Otros
                            </label>

                            <template x-if="otros.length > 0">
                                <div class="space-y-2 pl-6">
                                    <template x-for="(otro, index) in otros" :key="index">
                                        <div class="flex items-center gap-2">
                                            <input type="text"
                                                :name="'otros[' + index + ']'"
                                                x-model="otros[index]"
                                                placeholder="Nombre del documento"
                                                class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">
                                            <button type="button"
                                                @click="otros.splice(index, 1)"
                                                class="text-red-600 text-sm hover:underline shrink-0">
                                                Quitar
                                            </button>
                                        </div>
                                    </template>

                                    <button type="button"
                                        @click="otros.push('')"
                                        class="text-sm text-[#550000] font-medium hover:underline">
                                        + Agregar otro documento
                                    </button>
                                </div>
                            </template>
                        </div>

                        <x-input-error :messages="$errors->get('otros')" class="mt-2" />
                    </div>
                </div>

                <div class="flex items-center justify-between mt-6 pt-6 border-t border-gray-200">
                    <a href="{{ route('funcionario.clientes.index') }}" class="text-sm text-gray-500 hover:underline">
                        &larr; Volver al listado
                    </a>
                    <x-primary-button>Guardar Cambios</x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>