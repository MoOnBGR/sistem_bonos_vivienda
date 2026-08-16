<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Gestión de Funcionarios
        </h2>
    </x-slot>

    <div class="space-y-6">

        <!-- Banda -->
        <div class="bg-[#550000] rounded-2xl p-6 text-white flex items-center justify-between">
            <div>
                <h3 class="text-2xl font-bold">Funcionarios del sistema</h3>
                <p class="text-white/70 text-sm mt-1">Administre las cuentas de los funcionarios.</p>
            </div>
            <a href="{{ route('funcionario.crear') }}"
                class="bg-white text-[#550000] font-semibold px-5 py-2.5 rounded-full hover:bg-gray-100 transition text-sm">
                + Nuevo funcionario
            </a>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        <!-- Tabla -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-gray-200 text-gray-500 uppercase text-xs">
                        <th class="py-3 pr-4">Nombre</th>
                        <th class="py-3 pr-4">Correo electrónico</th>
                        <th class="py-3 pr-4">Fecha de registro</th>
                        <th class="py-3 pr-4 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($funcionarios as $funcionario)
                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                            <td class="py-3 pr-4 font-medium text-gray-800">
                                {{ $funcionario->name }}
                                @if($funcionario->id === Auth::id())
                                    <span class="text-xs text-[#550000] ml-1">(tú)</span>
                                @endif
                            </td>
                            <td class="py-3 pr-4 text-gray-500">{{ $funcionario->email }}</td>
                            <td class="py-3 pr-4 text-gray-500">
                                {{ \Carbon\Carbon::parse($funcionario->created_at)->format('d/m/Y') }}
                            </td>
                            <td class="py-3 pr-4 text-right">
                                <div class="flex justify-end gap-3">
                                    <a href="{{ route('funcionario.funcionarios.editar', $funcionario->id) }}"
                                        class="text-[#550000] hover:underline font-medium text-sm">
                                        Editar
                                    </a>
                                    @if($funcionario->id !== Auth::id())
                                        <form method="POST" action="{{ route('funcionario.funcionarios.destroy', $funcionario->id) }}"
                                              onsubmit="return confirm('¿Está seguro de eliminar al funcionario {{ $funcionario->name }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:underline font-medium text-sm">
                                                Eliminar
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-10 text-center text-gray-400">
                                No hay funcionarios registrados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>