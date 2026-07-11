<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Actualizar Expediente
        </h2>
    </x-slot>

    <div class="expediente-container">

        <!-- Pestañas de navegación -->
        <div class="flex gap-2 mb-6 border-b border-gray-200 pb-4">
            <a href="{{ route('expedientes.index') }}"
               class="px-4 py-2 rounded-lg text-sm font-medium bg-gray-100 text-gray-600 hover:bg-gray-200 transition">
                Listado
            </a>
            <a href="{{ route('expedientes.consultar', $expediente->Id_Cliente) }}"
               class="px-4 py-2 rounded-lg text-sm font-medium bg-gray-100 text-gray-600 hover:bg-gray-200 transition">
                Ver
            </a>
            <span class="px-4 py-2 rounded-lg text-sm font-medium bg-[#550000] text-white">
                Actualizar
            </span>
        </div>

        <h3 class="expediente-titulo">
            EXP-{{ str_pad($expediente->id_expediente, 4, '0', STR_PAD_LEFT) }} ·
            {{ $expediente->cliente->nombre }} {{ $expediente->cliente->apellidos }}
        </h3>
        <p class="text-sm text-gray-500 -mt-3 mb-4">
            Editando información del expediente
        </p>

        @if (session('success'))
            <div class="expediente-mensaje-exito">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="expediente-mensaje-error">{{ session('error') }}</div>
        @endif

        @if ($errors->any())
            <div class="expediente-mensaje-error">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Formulario: Guardar cambios -->
        <form method="POST" action="{{ route('expedientes.update', $expediente->id_expediente) }}">
            @csrf
            @method('PUT')

            <!-- Cliente (solo lectura) -->
            <div class="expediente-form-group">
                <label>Cliente</label>
                <p class="text-gray-800 font-medium bg-gray-50 border border-gray-200 rounded-lg px-3 py-2">
                    {{ $expediente->cliente->nombre }} {{ $expediente->cliente->apellidos }} (no editable)
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Fecha de apertura (solo lectura) -->
                <div class="expediente-form-group">
                    <label>Fecha apertura</label>
                    <p class="text-gray-800 font-medium bg-gray-50 border border-gray-200 rounded-lg px-3 py-2">
                        {{ \Carbon\Carbon::parse($expediente->fecha_creacion)->format('d/m/Y') }} (no editable)
                    </p>
                </div>

                <!-- Funcionario -->
                <div class="expediente-form-group">
                    <label for="id_funcionario">Funcionario</label>
                    <select id="id_funcionario" name="id_funcionario" required>
                        @foreach ($funcionarios as $funcionario)
                            <option value="{{ $funcionario->id }}"
                                {{ old('id_funcionario', $expediente->id_funcionario) == $funcionario->id ? 'selected' : '' }}>
                                {{ $funcionario->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Estado -->
            <div class="expediente-form-group">
                <label for="estado">Estado</label>
                <select id="estado" name="estado">
                    <option value="En proceso" {{ old('estado', $expediente->estado) === 'En proceso' ? 'selected' : '' }}>En proceso</option>
                    <option value="Completado" {{ old('estado', $expediente->estado) === 'Completado' ? 'selected' : '' }}>Completado</option>
                </select>
            </div>

            <!-- Necesario porque Id_Cliente es requerido por el Request -->
            <input type="hidden" name="Id_Cliente" value="{{ $expediente->Id_Cliente }}">

            <div class="flex gap-3 mt-6">
                <button type="submit" class="expediente-btn">
                    Guardar cambios
                </button>
                <a href="{{ route('expedientes.consultar', $expediente->Id_Cliente) }}" class="expediente-btn expediente-btn-secundario">
                    Cancelar
                </a>
            </div>
        </form>

        <!-- Separador -->
        <hr class="my-6 border-gray-200">

        @if ($expediente->estado !== 'Inactivo')
            <!-- Cerrar expediente (acción independiente) -->
            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                <h4 class="font-semibold text-red-800 mb-1">Cerrar expediente</h4>
                <p class="text-sm text-red-700 mb-3">
                    Esta acción marcará el expediente como "Inactivo". Podrás seguir consultándolo, pero no se podrán
                    hacer más cambios.
                </p>
                <form method="POST" action="{{ route('expedientes.cerrar', $expediente->id_expediente) }}"
                      onsubmit="return confirm('¿Estás seguro de cerrar este expediente?');">
                    @csrf
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-semibold px-6 py-2 rounded-lg">
                        Cerrar expediente
                    </button>
                </form>
            </div>
        @else
            <!-- Reabrir expediente -->
            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                <h4 class="font-semibold text-green-800 mb-1">Expediente cerrado</h4>
                <p class="text-sm text-green-700 mb-3">
                    Este expediente se encuentra cerrado. Si fue un error, puedes reabrirlo aquí (volverá al estado "En proceso").
                </p>
                <form method="POST" action="{{ route('expedientes.reabrir', $expediente->id_expediente) }}"
                      onsubmit="return confirm('¿Reabrir este expediente?');">
                    @csrf
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-2 rounded-lg">
                        Reabrir expediente
                    </button>
                </form>
            </div>
        @endif
    </div>
</x-app-layout>