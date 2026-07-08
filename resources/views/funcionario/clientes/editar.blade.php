<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Editar Cliente
        </h2>
    </x-slot>

    <div class="max-w-2xl">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

            <form method="POST" action="{{ route('funcionario.clientes.actualizar', $cliente) }}">
                @csrf
                @method('PUT')

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

                <div class="flex items-center justify-between mt-6">
                    <a href="{{ route('funcionario.clientes.index') }}" class="text-sm text-gray-500 hover:underline">
                        &larr; Volver al listado
                    </a>
                    <x-primary-button>Guardar Cambios</x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>