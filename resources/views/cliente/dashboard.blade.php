<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Cliente
        </h2>
    </x-slot>

    <div class="space-y-6">

        <!-- bienvenida -->
        <div class="bg-[#550000] rounded-2xl p-6 text-white">
            <h3 class="text-2xl font-bold">Bienvenido, {{ Auth::user()->name }}</h3>
            <p class="text-white/70 text-sm mt-1">Sistema de Bonos de Vivienda Andrea Rojas</p>
        </div>

        <!-- Módulos -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">

            <a href="#" class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-[#550000] hover:shadow-md transition">
                <h4 class="text-[#550000] font-semibold text-lg mb-1">Mi Trámite</h4>
                <p class="text-gray-500 text-sm">Consulte el estado actual de su solicitud de bono.</p>
            </a>

            <a href="#" class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-[#550000] hover:shadow-md transition">
                <h4 class="text-[#550000] font-semibold text-lg mb-1">Mis Documentos</h4>
                <p class="text-gray-500 text-sm">Consulte los documentos necesarios de su expediente.</p>
            </a>

        </div>
    </div>
</x-app-layout>