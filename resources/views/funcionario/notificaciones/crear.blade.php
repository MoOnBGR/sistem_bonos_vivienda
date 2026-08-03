<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Redactar Notificación
        </h2>
    </x-slot>

    <div class="space-y-6 max-w-4xl mx-auto">

        <!-- Banda -->
        <div class="bg-[#550000] rounded-2xl p-6 text-white">
            <h3 class="text-2xl font-bold">Enviar Notificación por Correo</h3>
            <p class="text-white/70 text-sm mt-1">Seleccione un cliente y redacte el mensaje que recibirá directamente en su casilla electrónica.</p>
        </div>

        <!-- Formulario -->
        <div class="bg-white rounded-xl shadow-sm p-8">

            <form method="POST" action="{{ route('funcionario.notificaciones.store') }}" class="space-y-6">
                @csrf

                <!-- Selector de Cliente -->
                <div>
                    <x-input-label for="cliente_id" value="Seleccionar Cliente *" class="text-xs text-gray-500 uppercase tracking-wide" />
                    <select id="cliente_id" name="cliente_id" required
                        class="block w-full border border-gray-200 rounded-lg px-4 py-2.5 mt-1 focus:border-[#550000] focus:ring-[#550000] text-gray-800 text-sm">
                        <option value="">-- Seleccione un cliente --</option>
                        @foreach($clientes as $cliente)
                            <option value="{{ $cliente->Id_Cliente }}"
                                {{ old('cliente_id', $clienteSeleccionadoId) == $cliente->Id_Cliente ? 'selected' : '' }}>
                                {{ $cliente->nombre }} {{ $cliente->apellidos }} - Identificación: {{ $cliente->identificacion }} ({{ $cliente->correo }})
                            </option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('cliente_id')" class="mt-1" />
                </div>

                <!-- Asunto -->
                <div>
                    <x-input-label for="asunto" value="Asunto del correo *" class="text-xs text-gray-500 uppercase tracking-wide" />
                    <x-text-input id="asunto" type="text" name="asunto" value="{{ old('asunto') }}" required
                        class="block w-full mt-1 border-gray-200 focus:border-[#550000] focus:ring-[#550000]"
                        placeholder="Ej. Actualización de estado en su trámite de bono de vivienda" />
                    <x-input-error :messages="$errors->get('asunto')" class="mt-1" />
                </div>

                <!-- Mensaje -->
                <div>
                    <x-input-label for="mensaje" value="Mensaje *" class="text-xs text-gray-500 uppercase tracking-wide" />
                    <textarea id="mensaje" name="mensaje" rows="6" required
                        class="block w-full border border-gray-200 rounded-lg px-4 py-2.5 mt-1 focus:border-[#550000] focus:ring-[#550000] text-gray-800 text-sm"
                        placeholder="Escriba aquí el mensaje detallado para el cliente...">{{ old('mensaje') }}</textarea>
                    <x-input-error :messages="$errors->get('mensaje')" class="mt-1" />
                </div>

                <!-- Acciones -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                    <a href="{{ route('funcionario.notificaciones.index') }}"
                        class="px-6 py-2.5 rounded-lg border border-gray-300 text-gray-600 font-medium text-sm hover:bg-gray-50 transition">
                        Cancelar
                    </a>
                    <button type="submit"
                        class="px-6 py-2.5 bg-[#550000] hover:bg-[#3d0000] text-white font-semibold text-sm rounded-lg transition shadow-sm">
                        Enviar Notificación
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
