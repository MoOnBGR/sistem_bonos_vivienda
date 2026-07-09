<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Clientes
        </h2>
    </x-slot>

    <div class="space-y-6">

        @if (session('status'))
            <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded">
                {{ session('status') }}
            </div>
        @endif

        <div class="bg-white rounded-xl shadow-sm p-6">
            <form method="GET" action="{{ route('funcionario.clientes.index') }}" class="flex gap-2 mb-6">
                <x-text-input type="text" name="termino" class="w-full max-w-md"
                    placeholder="Buscar por nombre, apellidos o cédula"
                    :value="request('termino')" />
                <x-primary-button>Buscar</x-primary-button>

                @if(request('termino'))
                    <a href="{{ route('funcionario.clientes.index') }}"
                        class="px-4 py-2 rounded-full border border-gray-300 text-gray-600 text-sm font-medium hover:bg-gray-50 transition self-center">
                        Limpiar
                    </a>
                @endif
            </form>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 text-gray-500 uppercase text-xs">
                            <th class="py-3 pr-4">Identificación</th>
                            <th class="py-3 pr-4">Nombre</th>
                            <th class="py-3 pr-4">Apellidos</th>
                            <th class="py-3 pr-4">Teléfono</th>
                            <th class="py-3 pr-4">Estado</th>
                            <th class="py-3 pr-4 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($clientes as $cliente)
                            <tr class="border-b border-gray-100 hover:bg-gray-50">
                                <td class="py-3 pr-4">{{ $cliente->identificacion }}</td>
                                <td class="py-3 pr-4">{{ $cliente->nombre }}</td>
                                <td class="py-3 pr-4">{{ $cliente->apellidos }}</td>
                                <td class="py-3 pr-4">{{ $cliente->telefono }}</td>
                                <td class="py-3 pr-4">
                                    <span class="px-2 py-1 rounded-full text-xs font-medium
                                        {{ $cliente->estado === 'Activo' ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-600' }}">
                                        {{ $cliente->estado }}
                                    </span>
                                </td>
                                <td class="py-3 pr-4 text-right space-x-2">
                                    <a href="{{ route('funcionario.clientes.editar', $cliente) }}"
                                        class="text-[#550000] font-medium hover:underline">Editar</a>

                                    <button type="button"
                                        x-data
                                        @click="$dispatch('open-delete-modal', { id: '{{ $cliente->Id_Cliente }}', nombre: '{{ $cliente->nombre }} {{ $cliente->apellidos }}' })"
                                        class="text-red-600 font-medium hover:underline">
                                        Eliminar
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-6 text-center text-gray-400">
                                    No se encontraron clientes.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $clientes->links() }}
            </div>
        </div>
    </div>

    <!-- Modal de confirmación de eliminación -->
    <div x-data="{ show: false, id: null, nombre: '' }"
        @open-delete-modal.window="show = true; id = $event.detail.id; nombre = $event.detail.nombre"
        x-show="show" x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display:none;">
        <div class="absolute inset-0 bg-black/50" @click="show = false"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl max-w-sm w-full p-6 text-center">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">¿Eliminar cliente?</h3>
            <p class="text-sm text-gray-500 mb-6">
                Se eliminará permanentemente el registro de <span x-text="nombre" class="font-medium"></span>.
            </p>
            <form :action="'/funcionario/clientes/' + id" method="POST">
                @csrf
                @method('DELETE')
                <div class="flex justify-center gap-3">
                    <button type="button" @click="show = false"
                        class="px-6 py-2 rounded-full border border-gray-300 text-gray-600 font-medium hover:bg-gray-50 transition">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="px-6 py-2 rounded-full bg-red-600 hover:bg-red-700 text-white font-semibold transition">
                        Sí, eliminar
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>