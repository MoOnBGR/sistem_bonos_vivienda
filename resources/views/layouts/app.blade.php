<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-gray-100">
    <div class="min-h-screen flex">

        <!-- SIDEBAR -->
        <aside class="w-56 min-h-screen bg-[#2a1020] flex flex-col fixed top-0 left-0 z-30">

            <!-- Logo / Nombre del sistema -->
            <div class="px-6 py-6 border-b border-white/10">
                <img src="{{ asset('images/logo-white.png') }}" alt="Andrea Rojas" class="w-32 mb-2">
                <p class="text-white/50 text-xs">Panel {{ Auth::user()->tipo_usuario ?? '' }}</p>
            </div>

            <!-- Navegación según rol -->
            <nav class="flex-1 px-3 py-4 space-y-1">

                @if(Auth::user()->tipo_usuario === 'Funcionario')
                    <!-- Inicio -->
                    <a href="{{ route('funcionario.dashboard') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition
                                                {{ request()->routeIs('funcionario.dashboard') ? 'bg-[#550000] text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        Inicio
                    </a>

                    <!-- Clientes -->
                    <a href="#"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition text-white/70 hover:bg-white/10 hover:text-white">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Clientes
                    </a>


                    <!-- Expedientes -->
                    <a href="#"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition text-white/70 hover:bg-white/10 hover:text-white">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Expedientes
                    </a>

                    <!-- Documentos -->
                    <a href="#"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition text-white/70 hover:bg-white/10 hover:text-white">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                        Documentos
                    </a>

                    <!-- Historial -->
                    <a href="#"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition text-white/70 hover:bg-white/10 hover:text-white">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Historial
                    </a>

                    <!-- Notificaciones -->
                    <a href="#"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition text-white/70 hover:bg-white/10 hover:text-white">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        Notificaciones
                    </a>

                @else
                            <!-- Menú Cliente -->
                            <a href="{{ route('cliente.dashboard') }}"
                                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition
                                                {{ request()->routeIs('cliente.dashboard') ? 'bg-[#550000] text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                </svg>
                                Inicio
                            </a>

                            <a href="#"
                                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition text-white/70 hover:bg-white/10 hover:text-white">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Mi Trámite
                            </a>

                            <a href="#"
                                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition text-white/70 hover:bg-white/10 hover:text-white">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                                Mis Documentos
                            </a>
                            <a href="{{ route('cliente.editar') }}"
                                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition
                    {{ request()->routeIs('cliente.editar') ? 'bg-[#550000] text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                Editar Datos
                            </a>


                @endif
            </nav>

            <!-- Cerrar sesión abajo -->
            <div x-data="{ confirmLogout: false }" class="px-3 py-4 border-t border-white/10">
                <button @click="confirmLogout = true"
                    class="flex items-center gap-3 w-full px-3 py-2.5 rounded-lg text-sm font-medium text-white/70 hover:bg-white/10 hover:text-white transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    Cerrar sesión
                </button>

                <form id="logout-form-sidebar" method="POST" action="{{ route('logout') }}" class="hidden">
                    @csrf
                </form>

                <!-- Modal confirmación -->
                <div x-show="confirmLogout" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4"
                    style="display:none;">
                    <div class="absolute inset-0 bg-black/50" @click="confirmLogout = false"></div>
                    <div class="relative bg-white rounded-2xl shadow-2xl max-w-sm w-full p-6 text-center">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">¿Desea cerrar sesión?</h3>
                        <p class="text-sm text-gray-500 mb-6">Tendrá que volver a iniciar sesión para acceder al
                            sistema.</p>
                        <div class="flex justify-center gap-3">
                            <button type="button" @click="confirmLogout = false"
                                class="px-6 py-2 rounded-full border border-gray-300 text-gray-600 font-medium hover:bg-gray-50 transition">
                                Cancelar
                            </button>
                            <button type="button" @click="document.getElementById('logout-form-sidebar').submit()"
                                class="px-6 py-2 rounded-full bg-[#550000] hover:bg-[#3d0000] text-white font-semibold transition">
                                Sí, cerrar sesión
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </aside>

        <!-- CONTENIDO PRINCIPAL -->
        <div class="ml-56 flex-1 flex flex-col min-h-screen">

            <!-- Header -->
            @isset($header)
                <header class="bg-white shadow-sm">
                    <div class="py-4 px-6 flex items-center justify-between">
                        {{ $header }}
                        <div class="text-sm text-gray-500">{{ Auth::user()->name }}</div>
                    </div>
                </header>
            @endisset

            <!-- Contenido -->
            <main class="flex-1 p-6">
                {{ $slot }}
            </main>
        </div>
    </div>
</body>

</html>