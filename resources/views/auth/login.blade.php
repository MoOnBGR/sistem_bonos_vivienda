<x-guest-layout>
    <div class="w-full max-w-4xl bg-white rounded-[2.5rem] shadow-2xl overflow-hidden flex flex-col lg:flex-row">

        <!-- Panel izquierdo geométrico -->
        <div class="hidden lg:flex lg:w-2/5 relative bg-[#2a1620] overflow-hidden items-center">
            <div class="absolute -left-24 -top-10 w-72 h-72 bg-[#46233a] rotate-45"></div>
            <div class="absolute -left-10 top-1/3 w-64 h-64 bg-[#6b3151] rotate-45 opacity-90"></div>
            <div class="absolute left-6 -bottom-16 w-60 h-60 bg-[#1b0f17] rotate-45"></div>

            <div class="relative z-10 bg-white rounded-r-full shadow-lg py-4 pl-6 pr-10 -ml-1">
                <span class="text-[#2a1620] font-bold tracking-widest text-sm">ACCESO</span>
            </div>
        </div>

        <!-- Panel derecho: formulario -->
        <div class="w-full lg:w-3/5 px-8 py-12 sm:px-14 flex flex-col items-center">
            <img src="{{ asset('images/logo.png') }}" alt="Andrea Rojas - Asesoría y Experiencia" class="w-36 mb-3">
            <h2 class="text-2xl font-bold text-[#2a1620] tracking-wide mb-1">INICIAR SESIÓN</h2>
            <p class="text-gray-400 text-sm mb-8">Sistema de gestión de bonos de vivienda</p>

            <x-auth-session-status class="mb-4 w-full max-w-sm" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}" class="w-full max-w-sm space-y-6">
                @csrf

                <!-- Email -->
                <div>
                    <label for="email" class="text-xs text-gray-500 uppercase tracking-wide">Correo electrónico</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                        class="block w-full border-0 border-b-2 border-gray-200 focus:border-[#2a1620] focus:ring-0 px-1 py-2 text-gray-800 placeholder-gray-300"
                        placeholder="correo@ejemplo.com">
                    <x-input-error :messages="$errors->get('email')" class="mt-1" />
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="text-xs text-gray-500 uppercase tracking-wide">Contraseña</label>
                    <input id="password" type="password" name="password" required autocomplete="current-password"
                        class="block w-full border-0 border-b-2 border-gray-200 focus:border-[#2a1620] focus:ring-0 px-1 py-2 text-gray-800 placeholder-gray-300"
                        placeholder="••••••••">
                    <x-input-error :messages="$errors->get('password')" class="mt-1" />
                </div>

                <!-- Olvidaste / Ingresar -->
                <div class="flex items-center justify-between pt-2">
                    @if (Route::has('password.request'))
                        <a class="text-sm text-[#7a1e3c] hover:underline" href="{{ route('password.request') }}">
                            ¿Olvidaste tu contraseña?
                        </a>
                    @endif

                    <button type="submit"
                        class="bg-[#2a1620] hover:bg-[#46233a] text-white font-semibold tracking-wide px-8 py-2.5 rounded-full transition-colors">
                        INGRESAR
                    </button>
                </div>

                <!-- Recordarme -->
                <label for="remember_me" class="inline-flex items-center pt-2">
                    <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-[#2a1620] focus:ring-[#2a1620]" name="remember">
                    <span class="ms-2 text-sm text-gray-500">Recordarme</span>
                </label>
            </form>
        </div>
    </div>
</x-guest-layout>