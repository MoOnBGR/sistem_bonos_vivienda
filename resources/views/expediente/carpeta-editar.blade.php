<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Renombrar carpeta
        </h2>
    </x-slot>

    <div class="expediente-container">
        <h3 class="expediente-titulo">Renombrar carpeta</h3>

        @if ($errors->any())
            <div class="expediente-mensaje-error">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('expedientes.carpetas.update', $carpeta->id_carpeta) }}">
            @csrf
            @method('PUT')

            <div class="expediente-form-group">
                <label for="nombre">Nombre de la carpeta</label>
                <input type="text" id="nombre" name="nombre" value="{{ old('nombre', $carpeta->nombre) }}" required>
            </div>

            <div class="flex gap-3 mt-6">
                <button type="submit" class="expediente-btn">
                    Guardar
                </button>
                <a href="{{ route('expedientes.carpetas.index', [$carpeta->id_expediente, $carpeta->id_carpeta_padre]) }}"
                   class="expediente-btn expediente-btn-secundario">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</x-app-layout>