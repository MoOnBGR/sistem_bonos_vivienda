<x-guest-layout>
    <div class="w-full max-w-4xl bg-white rounded-[2.5rem] shadow-2xl overflow-hidden flex flex-col lg:flex-row">

        <!-- Panel izquierdo-->
        <div class="hidden lg:flex lg:w-2/5 relative bg-[#550000] items-center justify-center overflow-hidden">
            <div class="absolute -top-10 -left-16 w-72 h-72 bg-[#a64545] opacity-40 rounded-full blur-3xl"></div>
            <div class="absolute bottom-0 -right-10 w-80 h-80 bg-[#7a1e1e] opacity-50 rounded-full blur-3xl"></div>
            <div class="absolute top-1/3 left-10 w-56 h-56 bg-[#c97b7b] opacity-30 rounded-full blur-2xl"></div>
            <div class="absolute -bottom-16 left-1/3 w-64 h-64 bg-[#380000] opacity-60 rounded-full blur-3xl"></div>

            <img src="{{ asset('images/logo-white.png') }}" alt="Andrea Rojas - Asesoría y Experiencia" class="relative z-10 w-56">
        </div>

        <!-- Panel derecho: formulario -->
        <div class="w-full lg:w-3/5 px-8 py-12 sm:px-14 flex flex-col items-center">
            <img src="{{ asset('images/logo.png') }}" alt="Andrea Rojas - Asesoría y Experiencia" class="w-36 mb-3 lg:hidden">
            <h2 class="text-2xl font-bold text-[#550000] tracking-wide mb-1">INICIAR SESIÓN</h2>
            <p class="text-gray-400 text-sm mb-8">Sistema de gestión de bonos de vivienda</p>

            <x-auth-session-status class="mb-4 w-full max-w-sm" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}" class="w-full max-w-sm space-y-6">
                @csrf

                <!-- Email -->
                <div>
                    <label for="email" class="text-xs text-gray-500 uppercase tracking-wide">Correo electrónico</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                        class="block w-full border-0 border-b-2 border-gray-200 focus:border-[#550000] focus:ring-0 px-1 py-2 text-gray-800 placeholder-gray-300"
                        placeholder="correo@ejemplo.com">
                    <x-input-error :messages="$errors->get('email')" class="mt-1" />
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="text-xs text-gray-500 uppercase tracking-wide">Contraseña</label>
                    <input id="password" type="password" name="password" required autocomplete="current-password"
                        class="block w-full border-0 border-b-2 border-gray-200 focus:border-[#550000] focus:ring-0 px-1 py-2 text-gray-800 placeholder-gray-300"
                        placeholder="••••••••">
                    <x-input-error :messages="$errors->get('password')" class="mt-1" />
                </div>

                <!-- Recordarme -->
                <label for="remember_me" class="inline-flex items-center pt-2">
                    <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-[#550000] focus:ring-[#550000]" name="remember">
                    <span class="ms-2 text-sm text-gray-500">Recordarme</span>
                </label>
                
                 <div class="flex items-center justify-between pt-2">
                     <button type="submit"
                        class="bg-[#550000] hover:bg-[#3d0000] text-white font-semibold tracking-wide px-8 py-2.5 rounded-full transition-colors">
                        INGRESAR
                    </button>
                 </div>
                
                <div class="flex items-center justify-between pt-2">

                     <button type="submit"
                        class="text-sm text-[#550000] hover:underline">
                        Registrarse
                    </button>
                    @if (Route::has('password.request'))
                        <a class="text-sm text-[#550000] hover:underline" href="{{ route('password.request') }}">
                            ¿Olvidaste tu contraseña?
                        </a>
                    @endif
                </div>
                
            </form>
        </div>
    </div>
</x-guest-layout>