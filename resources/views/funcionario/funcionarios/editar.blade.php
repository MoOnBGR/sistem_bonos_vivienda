<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Editar Funcionario
        </h2>
    </x-slot>

    <div class="space-y-6">

        <!-- Banda -->
        <div class="bg-[#550000] rounded-2xl p-6 text-white">
            <h3 class="text-2xl font-bold">Editar Funcionario</h3>
            <p class="text-white/70 text-sm mt-1">Modifique los datos del funcionario.</p>
        </div>

        <!-- Formulario -->
        <div class="bg-white rounded-xl shadow-sm p-8 max-w-lg">

            @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
                    <ul class="list-disc list-inside text-sm">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('funcionario.funcionarios.update', $funcionario->id) }}" class="space-y-5">
                @csrf
                @method('PUT')

                <!-- Nombre -->
                <div>
                    <label class="text-xs text-gray-500 uppercase tracking-wide">Nombre completo</label>
                    <input type="text" name="name" value="{{ old('name', $funcionario->name) }}" required
                        class="block w-full border border-gray-200 rounded-lg px-4 py-2.5 mt-1 focus:border-[#550000] focus:ring-[#550000] text-gray-800">
                </div>

                <!-- Correo -->
                <div>
                    <label class="text-xs text-gray-500 uppercase tracking-wide">Correo electrónico</label>
                    <input type="email" name="email" value="{{ old('email', $funcionario->email) }}" required
                        class="block w-full border border-gray-200 rounded-lg px-4 py-2.5 mt-1 focus:border-[#550000] focus:ring-[#550000] text-gray-800">
                </div>

                <!-- Nueva Contraseña (opcional) -->
                <div>
                    <label class="text-xs text-gray-500 uppercase tracking-wide">
                        Nueva contraseña <span class="text-gray-400 normal-case">(dejar vacío para no cambiar)</span>
                    </label>
                    <input type="password" name="password"
                        class="block w-full border border-gray-200 rounded-lg px-4 py-2.5 mt-1 focus:border-[#550000] focus:ring-[#550000] text-gray-800"
                        placeholder="••••••••">
                </div>

                <!-- Confirmar Contraseña -->
                <div>
                    <label class="text-xs text-gray-500 uppercase tracking-wide">Confirmar nueva contraseña</label>
                    <input type="password" name="password_confirmation"
                        class="block w-full border border-gray-200 rounded-lg px-4 py-2.5 mt-1 focus:border-[#550000] focus:ring-[#550000] text-gray-800"
                        placeholder="••••••••">
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="submit"
                        class="bg-[#550000] hover:bg-[#3d0000] text-white font-semibold py-2.5 px-6 rounded-lg transition">
                        Guardar cambios
                    </button>
                    <a href="{{ route('funcionario.funcionarios.index') }}"
                        class="border border-gray-300 text-gray-600 font-medium py-2.5 px-6 rounded-lg hover:bg-gray-50 transition">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>