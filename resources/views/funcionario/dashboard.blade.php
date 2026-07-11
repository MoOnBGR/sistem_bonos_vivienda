<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Funcionario
        </h2>
    </x-slot>

    <div class="space-y-6">

        <!--bienvenida -->
        <div class="bg-[#550000] rounded-2xl p-6 text-white">
            <h3 class="text-2xl font-bold">Bienvenido, {{ Auth::user()->name }} </h3>
            <p class="text-white/70 text-sm mt-1">Sistema de Bonos de Vivienda Andrea Rojas</p>
        </div>

        <!-- Módulos -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

            <a href="{{ route('funcionario.clientes.index') }}"
                class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-[#550000] hover:shadow-md transition">
                <h4 class="text-[#550000] font-semibold text-lg mb-1">Clientes</h4>
                <p class="text-gray-500 text-sm">Registrar, consultar y actualizar clientes.</p>
            </a>

            <a href="{{ route('expedientes.index') }}"
                class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-[#550000] hover:shadow-md transition">
                <h4 class="text-[#550000] font-semibold text-lg mb-1">Expedientes</h4>
                <p class="text-gray-500 text-sm">Crear, actualizar y gestionar expedientes.</p>
            </a>

            <a href="#"
                class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-[#550000] hover:shadow-md transition">
                <h4 class="text-[#550000] font-semibold text-lg mb-1">Documentos</h4>
                <p class="text-gray-500 text-sm">Subir, organizar y validar documentos.</p>
            </a>

            <a href="#"
                class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-[#550000] hover:shadow-md transition">
                <h4 class="text-[#550000] font-semibold text-lg mb-1">Historial</h4>
                <p class="text-gray-500 text-sm">Ver historial de cambios y trámites.</p>
            </a>

            <a href="#"
                class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-[#550000] hover:shadow-md transition">
                <h4 class="text-[#550000] font-semibold text-lg mb-1">Notificaciones</h4>
                <p class="text-gray-500 text-sm">Revisar notificaciones del sistema.</p>
            </a>

        </div>
    </div>
</x-app-layout>