<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Crear Funcionario
        </h2>
    </x-slot>

    <div class="space-y-6">

        <!-- Banda -->
        <div class="bg-[#550000] rounded-2xl p-6 text-white">
            <h3 class="text-2xl font-bold">Crear nuevo Funcionario</h3>
            <p class="text-white/70 text-sm mt-1">Complete los datos para registrar un nuevo funcionario en el sistema.</p>
        </div>

        <!-- Formulario -->
        <div class="bg-white rounded-xl shadow-sm p-8 max-w-lg">

            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('funcionario.funcionarios.store') }}" class="space-y-5">
                @csrf

                <!-- Nombre -->
                <div>
                    <label class="text-xs text-gray-500 uppercase tracking-wide">Nombre completo</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                        class="block w-full border border-gray-200 rounded-lg px-4 py-2.5 mt-1 focus:border-[#550000] focus:ring-[#550000] text-gray-800"
                        placeholder="Nombre del funcionario">
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Correo -->
                <div>
                    <label class="text-xs text-gray-500 uppercase tracking-wide">Correo electrónico</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                        class="block w-full border border-gray-200 rounded-lg px-4 py-2.5 mt-1 focus:border-[#550000] focus:ring-[#550000] text-gray-800"
                        placeholder="correo@empresa.com">
                    @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Contraseña -->
                <div>
                    <label class="text-xs text-gray-500 uppercase tracking-wide">Contraseña</label>
                    <input type="password" name="password" required
                        class="block w-full border border-gray-200 rounded-lg px-4 py-2.5 mt-1 focus:border-[#550000] focus:ring-[#550000] text-gray-800"
                        placeholder="••••••••">
                    @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Confirmar Contraseña -->
                <div>
                    <label class="text-xs text-gray-500 uppercase tracking-wide">Confirmar contraseña</label>
                    <input type="password" name="password_confirmation" required
                        class="block w-full border border-gray-200 rounded-lg px-4 py-2.5 mt-1 focus:border-[#550000] focus:ring-[#550000] text-gray-800"
                        placeholder="••••••••">
                </div>

                <button type="submit"
                    class="w-full bg-[#550000] hover:bg-[#3d0000] text-white font-semibold py-2.5 rounded-lg transition">
                    Crear Funcionario
                </button>
            </form>
        </div>
    </div>
</x-app-layout>