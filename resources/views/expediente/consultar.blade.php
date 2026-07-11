<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Expediente
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
                <span class="expediente-estado {{ $expediente->estado === 'Completado' ? 'completado' : 'en-proceso' }}">
                    {{ $expediente->estado }}
                </span>
            </p>
        </div>

        <!-- Documentos adjuntos -->
        <h4 class="font-semibold text-gray-700 mb-2">Documentos adjuntos</h4>
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
                    @forelse ($expediente->documentos as $documento)
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
                                Este expediente aún no tiene documentos adjuntos.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="flex gap-3">
            <a href="{{ route('expedientes.editar', $expediente->id_expediente) }}" class="expediente-btn">
                Editar
            </a>
            <a href="{{ route('expedientes.index') }}" class="expediente-btn expediente-btn-secundario">
                Volver
            </a>
        </div>
    </div>
</x-app-layout>