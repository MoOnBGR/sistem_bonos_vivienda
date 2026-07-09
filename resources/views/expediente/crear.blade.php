<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Crear Expediente
        </h2>
    </x-slot>

    <div class="expediente-container">

        <!-- Pestañas de navegación (estilo del prototipo) -->
        <div class="flex gap-2 mb-6 border-b border-gray-200 pb-4">
            <a href="{{ route('expedientes.consultar', $cliente->Id_Cliente) }}"
               class="px-4 py-2 rounded-lg text-sm font-medium bg-gray-100 text-gray-600 hover:bg-gray-200 transition">
                Listado
            </a>
            <span class="px-4 py-2 rounded-lg text-sm font-medium bg-[#550000] text-white">
                Crear
            </span>
            <span class="px-4 py-2 rounded-lg text-sm font-medium bg-gray-100 text-gray-400">
                Actualizar
            </span>
            <span class="px-4 py-2 rounded-lg text-sm font-medium bg-gray-100 text-gray-400">
                Cerrar
            </span>
        </div>

        <h3 class="expediente-titulo">Nuevo expediente</h3>

        @if ($errors->any())
            <div class="expediente-mensaje-error">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('expedientes.store') }}">
            @csrf

            <input type="hidden" name="Id_Cliente" value="{{ $cliente->Id_Cliente }}">

            <!-- Cliente (solo lectura) -->
            <div class="expediente-form-group">
                <label>Cliente</label>
                <p class="text-gray-800 font-medium bg-gray-50 border border-gray-200 rounded-lg px-3 py-2">
                    {{ $cliente->nombre }} {{ $cliente->apellidos }} — Cédula: {{ $cliente->identificacion }}
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Fecha de apertura -->
                <div class="expediente-form-group">
                    <label for="fecha_creacion">Fecha apertura *</label>
                    <input type="date" id="fecha_creacion" name="fecha_creacion"
                           value="{{ old('fecha_creacion', date('Y-m-d')) }}" required>
                </div>

                <!-- Funcionario -->
                <div class="expediente-form-group">
                    <label for="id_funcionario">Funcionario *</label>
                    <select id="id_funcionario" name="id_funcionario" required>
                        <option value="">Seleccionar funcionario</option>
                        @foreach ($funcionarios as $funcionario)
                            <option value="{{ $funcionario->id }}"
                                {{ old('id_funcionario', Auth::id()) == $funcionario->id ? 'selected' : '' }}>
                                {{ $funcionario->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Estado inicial -->
            <div class="expediente-form-group">
                <label for="estado">Estado inicial</label>
                <select id="estado" name="estado">
                    <option value="En proceso" selected>En proceso</option>
                    <option value="Completado">Completado</option>
                </select>
            </div>

            <div class="flex gap-3 mt-6">
                <button type="submit" class="expediente-btn">
                    Crear expediente
                </button>
                <a href="{{ url()->previous() }}" class="expediente-btn expediente-btn-secundario">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</x-app-layout>