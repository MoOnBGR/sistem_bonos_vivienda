<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Expediente creado
        </h2>
    </x-slot>

    <div class="expediente-container">
        <div class="expediente-mensaje-exito mb-6">
            Expediente creado exitosamente.
        </div>

        <h3 class="expediente-titulo">Resumen del expediente</h3>

        <div class="expediente-form-group">
            <label>Cliente</label>
            <p class="text-gray-800 font-medium bg-gray-50 border border-gray-200 rounded-lg px-3 py-2">
                {{ $expediente->cliente->nombre }} {{ $expediente->cliente->apellidos }} — Cédula: {{ $expediente->cliente->identificacion }}
            </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="expediente-form-group">
                <label>Fecha de apertura</label>
                <p class="text-gray-800 font-medium bg-gray-50 border border-gray-200 rounded-lg px-3 py-2">
                    {{ \Carbon\Carbon::parse($expediente->fecha_creacion)->format('d/m/Y') }}
                </p>
            </div>
            <div class="expediente-form-group">
                <label>Estado</label>
                <p class="text-gray-800 font-medium bg-gray-50 border border-gray-200 rounded-lg px-3 py-2">
                    {{ $expediente->estado }}
                </p>
            </div>
        </div>

 <div class="flex gap-3 mt-6">
    <a href="{{ route('expedientes.crear.buscar') }}" class="text-[#550000] font-medium hover:underline">
        Crear otro expediente
    </a>
    <a href="{{ route('expedientes.index') }}" class="text-[#550000] font-medium hover:underline">
        Volver al inicio
    </a>
</div>
    </div>
</x-app-layout>