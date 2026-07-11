<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Crear Expediente
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
        Crear
    </span>
</div>

        <h3 class="expediente-titulo">Buscar cliente</h3>

        @if ($errors->any())
            <div class="expediente-mensaje-error">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <p class="text-sm text-gray-500 mb-4">
            Escribe la cédula del cliente para el que quieres crear un nuevo expediente.
        </p>

        <form method="POST" action="{{ route('expedientes.buscarCrear') }}" class="flex gap-2">
            @csrf
            <input type="text" name="identificacion" placeholder="Cédula del cliente..." required
                   value="{{ old('identificacion') }}" class="flex-1">
            <button type="submit" class="expediente-btn">Buscar</button>
        </form>
    </div>
</x-app-layout>