<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Expediente
        </h2>
    </x-slot>

    <div class="expediente-container" x-data="{ modalNuevaCarpeta: false, modalRenombrar: null }">

        <!-- Pestañas de navegación -->
        <div class="flex gap-2 mb-6 border-b border-gray-200 pb-4">
            <a href="{{ route('expedientes.index') }}"
               class="px-4 py-2 rounded-lg text-sm font-medium bg-gray-100 text-gray-600 hover:bg-gray-200 transition">
                Listado
            </a>
            <span class="px-4 py-2 rounded-lg text-sm font-medium bg-[#550000] text-white">
                Ver
            </span>
            <a href="{{ route('expedientes.editar', $expediente->id_expediente) }}"
               class="px-4 py-2 rounded-lg text-sm font-medium bg-gray-100 text-gray-600 hover:bg-gray-200 transition">
                Actualizar
            </a>
        </div>

        <h3 class="expediente-titulo">
            Expediente: EXP-{{ str_pad($expediente->id_expediente, 4, '0', STR_PAD_LEFT) }}
        </h3>
        <p class="text-sm text-gray-500 -mt-3 mb-4">
            Cliente: {{ $cliente->nombre }} {{ $cliente->apellidos }} (Cédula: {{ $cliente->identificacion }})
        </p>

        @if (session('success'))
            <div class="expediente-mensaje-exito">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="expediente-mensaje-error">{{ session('error') }}</div>
        @endif

        <!-- Información del expediente -->
        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-6">
            <h4 class="font-semibold text-gray-700 mb-2">Información del expediente</h4>
            <p class="text-sm text-gray-600">
                <span class="font-medium">Fecha apertura:</span>
                {{ \Carbon\Carbon::parse($expediente->fecha_creacion)->format('d/m/Y') }}
            </p>
            <p class="text-sm text-gray-600">
                <span class="font-medium">Funcionario:</span> {{ $expediente->funcionario->name }}
            </p>
            <p class="text-sm text-gray-600">
                <span class="font-medium">Estado actual:</span>
                <span class="expediente-estado {{ $expediente->estado === 'En proceso' ? 'en-proceso' : 'completado' }}">
                    {{ $expediente->estado }}
                </span>
            </p>
        </div>

        <!-- Breadcrumb de carpetas del expediente -->
        <div class="flex items-center flex-wrap gap-1 text-sm mb-4">
            <a href="{{ route('expedientes.carpetas.index', $expediente->id_expediente) }}"
               class="text-[#550000] font-medium hover:underline">
                EXP-{{ str_pad($expediente->id_expediente, 4, '0', STR_PAD_LEFT) }}
            </a>
            @foreach ($ruta as $carpetaRuta)
                <span class="text-gray-400">/</span>
                <a href="{{ route('expedientes.carpetas.index', [$expediente->id_expediente, $carpetaRuta->id_carpeta]) }}"
                   class="text-[#550000] font-medium hover:underline">
                    {{ $carpetaRuta->nombre }}
                </a>
            @endforeach
        </div>

        <!-- Carpetas del expediente -->
        <div class="flex items-center justify-between mb-2">
            <h4 class="font-semibold text-gray-700">Carpetas</h4>
            <button type="button" @click="modalNuevaCarpeta = true"
                    class="text-[#550000] font-medium hover:underline bg-transparent">
                + Nueva carpeta
            </button>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
            @forelse ($carpetas as $carpetaItem)
                <div class="border border-gray-200 rounded-lg p-3 hover:bg-gray-50">
                    <a href="{{ route('expedientes.carpetas.index', [$expediente->id_expediente, $carpetaItem->id_carpeta]) }}"
                       class="flex items-center gap-2 text-gray-700 font-medium">
                        📁 <span class="truncate">{{ $carpetaItem->nombre }}</span>
                    </a>
                    <div class="flex gap-3 mt-2 text-xs">
                        <button type="button" @click="modalRenombrar = { id: {{ $carpetaItem->id_carpeta }}, nombre: '{{ $carpetaItem->nombre }}' }"
                                class="text-[#550000] hover:underline bg-transparent">
                            Editar
                        </button>
                        <form method="POST" action="{{ route('expedientes.carpetas.destroy', $carpetaItem->id_carpeta) }}"
                              onsubmit="return confirm('¿Eliminar esta carpeta y todo su contenido?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-[#550000] hover:underline bg-transparent">
                                Eliminar
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <p class="text-gray-400 col-span-full">No hay carpetas en este nivel.</p>
            @endforelse
        </div>

        @if ($carpetaActual)
            <!-- Documentos dentro de esta carpeta -->
            <h4 class="font-semibold text-gray-700 mb-2">
                Documentos en "{{ $carpetaActual->nombre }}"
            </h4>
            <div class="overflow-x-auto mb-6">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 text-gray-500 uppercase text-xs">
                            <th class="py-2 pr-4">Nombre del documento</th>
                            <th class="py-2 pr-4">Fecha subida</th>
                            <th class="py-2 pr-4">Estado</th>
                            <th class="py-2 pr-4 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($documentos as $documento)
                            <tr class="border-b border-gray-100 hover:bg-gray-50">
                                <td class="py-2 pr-4">{{ $documento->nombre_doc }}</td>
                                <td class="py-2 pr-4">{{ \Carbon\Carbon::parse($documento->fecha_subida)->format('d/m/Y') }}</td>
                                <td class="py-2 pr-4">{{ $documento->estado_doc }}</td>
                                <td class="py-2 pr-4 text-right">
                                    <span class="text-gray-400">Ver</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-6 text-center text-gray-400">
                                    Esta carpeta aún no tiene documentos.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif

        @php
            $volverUrl = $carpetaActual
                ? ($carpetaActual->id_carpeta_padre
                    ? route('expedientes.carpetas.index', [$expediente->id_expediente, $carpetaActual->id_carpeta_padre])
                    : route('expedientes.carpetas.index', $expediente->id_expediente))
                : route('expedientes.index');
        @endphp

        <div class="flex gap-3">
            <a href="{{ $volverUrl }}" class="expediente-btn expediente-btn-secundario">
                Volver
            </a>
        </div>

        <!-- Modal: Nueva carpeta -->
        <div x-show="modalNuevaCarpeta" x-cloak
             class="fixed inset-0 bg-black/40 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg p-6 w-full max-w-sm" @click.outside="modalNuevaCarpeta = false">
                <h4 class="font-semibold text-gray-700 mb-4">Nueva carpeta</h4>
                <form method="POST" action="{{ route('expedientes.carpetas.store', $expediente->id_expediente) }}">
                    @csrf
                    @if ($carpetaActual)
                        <input type="hidden" name="id_carpeta_padre" value="{{ $carpetaActual->id_carpeta }}">
                    @endif
                    <input type="text" name="nombre" placeholder="Nombre de la carpeta" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 mb-4">
                    <div class="flex gap-3 justify-end">
                        <button type="button" @click="modalNuevaCarpeta = false"
                                class="text-gray-500 hover:underline bg-transparent">Cancelar</button>
                        <button type="submit"
                                class="text-[#550000] font-medium hover:underline bg-transparent">Crear</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal: Renombrar carpeta -->
        <template x-if="modalRenombrar">
            <div class="fixed inset-0 bg-black/40 flex items-center justify-center z-50">
                <div class="bg-white rounded-lg p-6 w-full max-w-sm" @click.outside="modalRenombrar = null">
                    <h4 class="font-semibold text-gray-700 mb-4">Renombrar carpeta</h4>
                    <form method="POST" :action="'/expedientes/carpetas/' + modalRenombrar.id">
                        @csrf
                        @method('PUT')
                        <input type="text" name="nombre" x-model="modalRenombrar.nombre" required
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 mb-4">
                        <div class="flex gap-3 justify-end">
                            <button type="button" @click="modalRenombrar = null"
                                    class="text-gray-500 hover:underline bg-transparent">Cancelar</button>
                            <button type="submit"
                                    class="text-[#550000] font-medium hover:underline bg-transparent">Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
        </template>
    </div>
</x-app-layout>