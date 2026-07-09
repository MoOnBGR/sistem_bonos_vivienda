<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Expedientes
        </h2>
    </x-slot>

    <div class="expediente-container">

        <!-- Pestañas de navegación -->
        <div class="flex gap-2 mb-6 border-b border-gray-200 pb-4">
            <span class="px-4 py-2 rounded-lg text-sm font-medium bg-[#550000] text-white">
                Listado
            </span>
            <a href="{{ route('expedientes.crear.buscar') }}"
               class="px-4 py-2 rounded-lg text-sm font-medium bg-gray-100 text-gray-600 hover:bg-gray-200 transition">
                Crear
            </a>
            <span class="px-4 py-2 rounded-lg text-sm font-medium bg-gray-100 text-gray-400">
                Actualizar
            </span>
            <span class="px-4 py-2 rounded-lg text-sm font-medium bg-gray-100 text-gray-400">
                Cerrar
            </span>
        </div>

        <h3 class="expediente-titulo">Expedientes</h3>

        @if (session('success'))
            <div class="expediente-mensaje-exito">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="expediente-mensaje-error">{{ session('error') }}</div>
        @endif

        <!-- Búsqueda rápida: filtra el listado por cédula de un cliente -->
        <form method="GET" action="{{ route('expedientes.index') }}" class="flex gap-2 mb-2 items-end">
            <div class="expediente-form-group mb-0">
                <label>Buscar expediente por cédula</label>
                <input type="text" name="identificacion" value="{{ request('identificacion') }}" placeholder="Cédula del cliente..." class="w-64">
            </div>
            <button type="submit" class="text-[#550000] font-medium hover:underline bg-transparent mb-2">Buscar</button>
        </form>

        @if ($busquedaSinResultados)
            <div class="expediente-mensaje-error mb-2">Cliente no encontrado</div>
            <p class="text-gray-500 mb-6">No hay registros de expedientes con esa cédula. Dale clic en "Filtrar" para ver todos nuevamente.</p>
        @elseif ($clienteBuscado)
            <div class="expediente-mensaje-exito mb-6">
                El cliente {{ $clienteBuscado->nombre }} {{ $clienteBuscado->apellidos }} tiene {{ $expedientes->count() }} expediente(s) asociado(s). Dale clic en "Filtrar" para ver todos los expedientes nuevamente.
            </div>
        @endif

        <!-- Filtros -->
        <form method="GET" action="{{ route('expedientes.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-6 items-end">
            <div class="expediente-form-group mb-0">
                <label>Estado</label>
                <select name="estado">
                    <option value="">Todos</option>
                    <option value="En proceso" {{ request('estado') === 'En proceso' ? 'selected' : '' }}>En proceso</option>
                    <option value="Completado" {{ request('estado') === 'Completado' ? 'selected' : '' }}>Completado</option>
                </select>
            </div>

            <div class="expediente-form-group mb-0">
                <label>Fecha desde</label>
                <input type="date" name="fecha_desde" value="{{ request('fecha_desde') }}">
            </div>

            <div class="expediente-form-group mb-0">
                <label>Fecha hasta</label>
                <input type="date" name="fecha_hasta" value="{{ request('fecha_hasta') }}">
            </div>

            <div class="sm:col-span-3 flex gap-3">
                <button type="submit" class="text-[#550000] font-medium hover:underline bg-transparent">Filtrar</button>
                <a href="{{ route('expedientes.index') }}" class="text-[#550000] font-medium hover:underline">Limpiar</a>
            </div>
        </form>

        @unless ($busquedaSinResultados)
        <!-- Tabla de expedientes -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-gray-200 text-gray-500 uppercase text-xs">
                        <th class="py-3 pr-4">ID Expediente</th>
                        <th class="py-3 pr-4">Cliente</th>
                        <th class="py-3 pr-4">Fecha apertura</th>
                        <th class="py-3 pr-4">Funcionario</th>
                        <th class="py-3 pr-4">Estado</th>
                        <th class="py-3 pr-4 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($expedientes as $expediente)
                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                            <td class="py-3 pr-4 font-medium">EXP-{{ str_pad($expediente->id_expediente, 4, '0', STR_PAD_LEFT) }}</td>
                            <td class="py-3 pr-4">{{ $expediente->cliente->nombre }} {{ $expediente->cliente->apellidos }}</td>
                            <td class="py-3 pr-4">{{ \Carbon\Carbon::parse($expediente->fecha_creacion)->format('d/m/Y') }}</td>
                            <td class="py-3 pr-4">{{ $expediente->funcionario->name }}</td>
                            <td class="py-3 pr-4">
                                <span class="expediente-estado {{ $expediente->estado === 'Completado' ? 'completado' : 'en-proceso' }}">
                                    {{ $expediente->estado }}
                                </span>
                            </td>
                            <td class="py-3 pr-4 text-right space-x-2">
                                <a href="{{ route('expedientes.consultar', $expediente->Id_Cliente) }}"
                                   class="text-[#550000] font-medium hover:underline">Ver</a>
                                <a href="{{ route('expedientes.editar', $expediente->id_expediente) }}"
                                   class="text-[#550000] font-medium hover:underline">Editar</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-6 text-center text-gray-400">
                                No hay expedientes registrados todavía.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @endunless
    </div>
</x-app-layout>