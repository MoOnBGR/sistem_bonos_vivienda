<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Historial
        </h2>
    </x-slot>

    <div class="space-y-6">

        <!-- Banda -->
        <div class="bg-[#550000] rounded-2xl p-6 text-white">
            <h3 class="text-2xl font-bold">Historial de Cambios</h3>
            <p class="text-white/70 text-sm mt-1">Registro de todas las acciones realizadas en el sistema.</p>
        </div>

        <!-- Filtros -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <form method="GET" action="{{ route('funcionario.historial.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                
                <!-- Módulo -->
                <div>
                    <label class="text-xs text-gray-500 uppercase tracking-wide">Módulo</label>
                    <select name="modulo" class="block w-full border border-gray-200 rounded-lg px-3 py-2 mt-1 text-sm focus:border-[#550000] focus:ring-[#550000]">
                        <option value="">Todos</option>
                        @foreach($modulos as $modulo)
                            <option value="{{ $modulo }}" {{ request('modulo') == $modulo ? 'selected' : '' }}>
                                {{ $modulo }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Funcionario -->
                <div>
                    <label class="text-xs text-gray-500 uppercase tracking-wide">Funcionario</label>
                    <select name="user_id" class="block w-full border border-gray-200 rounded-lg px-3 py-2 mt-1 text-sm focus:border-[#550000] focus:ring-[#550000]">
                        <option value="">Todos</option>
                        @foreach($funcionarios as $funcionario)
                            <option value="{{ $funcionario->id }}" {{ request('user_id') == $funcionario->id ? 'selected' : '' }}>
                                {{ $funcionario->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Fecha desde -->
                <div>
                    <label class="text-xs text-gray-500 uppercase tracking-wide">Fecha desde</label>
                    <input type="date" name="fecha_desde" value="{{ request('fecha_desde') }}"
                        class="block w-full border border-gray-200 rounded-lg px-3 py-2 mt-1 text-sm focus:border-[#550000] focus:ring-[#550000]">
                </div>

                <!-- Fecha hasta -->
                <div>
                    <label class="text-xs text-gray-500 uppercase tracking-wide">Fecha hasta</label>
                    <input type="date" name="fecha_hasta" value="{{ request('fecha_hasta') }}"
                        class="block w-full border border-gray-200 rounded-lg px-3 py-2 mt-1 text-sm focus:border-[#550000] focus:ring-[#550000]">
                </div>

                <div class="sm:col-span-4 flex gap-3">
                    <button type="submit"
                        class="bg-[#550000] hover:bg-[#3d0000] text-white text-sm font-medium px-6 py-2 rounded-full transition">
                        Filtrar
                    </button>
                    <a href="{{ route('funcionario.historial.index') }}"
                        class="border border-gray-300 text-gray-600 text-sm font-medium px-6 py-2 rounded-full hover:bg-gray-50 transition">
                        Limpiar
                    </a>
                </div>
            </form>
        </div>

        <!-- Tabla -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-gray-200 text-gray-500 uppercase text-xs">
                        <th class="py-3 pr-4">Fecha</th>
                        <th class="py-3 pr-4">Funcionario</th>
                        <th class="py-3 pr-4">Módulo</th>
                        <th class="py-3 pr-4">Acción</th>
                        <th class="py-3 pr-4">Descripción</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($historial as $registro)
                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                            <td class="py-3 pr-4 text-gray-500 text-xs">
                                {{ \Carbon\Carbon::parse($registro->created_at)->format('d/m/Y H:i') }}
                            </td>
                            <td class="py-3 pr-4 font-medium text-gray-800">
                                {{ $registro->user->name ?? 'Sistema' }}
                            </td>
                            <td class="py-3 pr-4">
                                <span class="bg-[#550000]/10 text-[#550000] text-xs font-medium px-2 py-1 rounded-full">
                                    {{ $registro->modulo }}
                                </span>
                            </td>
                            <td class="py-3 pr-4 text-gray-700">{{ $registro->accion }}</td>
                            <td class="py-3 pr-4 text-gray-500">{{ $registro->descripcion }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-10 text-center text-gray-400">
                                No hay registros en el historial aún.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Paginación -->
            <div class="mt-4">
                {{ $historial->links() }}
            </div>
        </div>
    </div>
</x-app-layout>