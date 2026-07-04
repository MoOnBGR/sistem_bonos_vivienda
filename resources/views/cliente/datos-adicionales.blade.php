<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Completar Información Personal
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('cliente.store') }}">
                    @csrf

                    <div>
                        <x-input-label for="identificacion" value="Identificación" />
                        <x-text-input id="identificacion" name="identificacion" type="text" class="mt-1 block w-full" :value="old('identificacion')" required autofocus />
                        <x-input-error :messages="$errors->get('identificacion')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input-label for="nombre" value="Nombre" />
                        <x-text-input id="nombre" name="nombre" type="text" class="mt-1 block w-full" :value="old('nombre')" required />
                        <x-input-error :messages="$errors->get('nombre')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input-label for="apellidos" value="Apellidos" />
                        <x-text-input id="apellidos" name="apellidos" type="text" class="mt-1 block w-full" :value="old('apellidos')" required />
                        <x-input-error :messages="$errors->get('apellidos')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input-label for="telefono" value="Teléfono" />
                        <x-text-input id="telefono" name="telefono" type="text" class="mt-1 block w-full" :value="old('telefono')" required />
                        <x-input-error :messages="$errors->get('telefono')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input-label for="direccion" value="Dirección" />
                        <textarea id="direccion" name="direccion" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>{{ old('direccion') }}</textarea>
                        <x-input-error :messages="$errors->get('direccion')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input-label for="correo" value="Correo de Contacto" />
                        <x-text-input id="correo" name="correo" type="email" class="mt-1 block w-full" :value="old('correo', auth()->user()->email)" required />
                        <x-input-error :messages="$errors->get('correo')" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-end mt-6">
                        <x-primary-button>Guardar y Continuar</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>