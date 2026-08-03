<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Notificaciones por Correo
        </h2>
    </x-slot>

    <div class="space-y-6">

        @if (session('status'))
            <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded">
                {{ session('status') }}
            </div>
        @endif

        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
                <form method="GET" action="{{ route('funcionario.notificaciones.index') }}" class="flex gap-2 w-full sm:w-auto">
                    <x-text-input type="text" name="termino" class="w-full sm:w-80"
                        placeholder="Buscar por cliente, cédula o asunto"
                        :value="request('termino')" />
                    <x-primary-button>Buscar</x-primary-button>

                    @if(request('termino'))
                        <a href="{{ route('funcionario.notificaciones.index') }}"
                            class="px-4 py-2 rounded-full border border-gray-300 text-gray-600 text-sm font-medium hover:bg-gray-50 transition self-center">
                            Limpiar
                        </a>
                    @endif
                </form>

                <a href="{{ route('funcionario.notificaciones.crear') }}"
                    class="inline-flex items-center px-4 py-2 bg-[#550000] hover:bg-[#3d0000] text-white text-sm font-semibold rounded-lg shadow-sm transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Redactar notificación
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 text-gray-500 uppercase text-xs">
                            <th class="py-3 pr-4">Cliente</th>
                            <th class="py-3 pr-4">Asunto</th>
                            <th class="py-3 pr-4">Enviado por</th>
                            <th class="py-3 pr-4">Fecha de Envío</th>
                            <th class="py-3 pr-4">Estado</th>
                            <th class="py-3 pr-4 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($notificaciones as $notificacion)
                            <tr class="border-b border-gray-100 hover:bg-gray-50">
                                <td class="py-3 pr-4">
                                    <div class="font-medium text-gray-900">
                                        {{ $notificacion->cliente ? $notificacion->cliente->nombre . ' ' . $notificacion->cliente->apellidos : 'Cliente no disponible' }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ $notificacion->cliente ? $notificacion->cliente->identificacion . ' (' . $notificacion->cliente->correo . ')' : '-' }}
                                    </div>
                                </td>
                                <td class="py-3 pr-4">
                                    <span class="font-medium text-gray-800">{{ Str::limit($notificacion->asunto, 40) }}</span>
                                </td>
                                <td class="py-3 pr-4 text-gray-600">
                                    {{ $notificacion->user ? $notificacion->user->name : 'Sistema' }}
                                </td>
                                <td class="py-3 pr-4 text-gray-600">
                                    {{ $notificacion->created_at ? $notificacion->created_at->format('d/m/Y H:i') : '-' }}
                                </td>
                                <td class="py-3 pr-4">
                                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold
                                        {{ $notificacion->estado === 'Enviado' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                        {{ $notificacion->estado }}
                                    </span>
                                </td>
                                <td class="py-3 pr-4 text-right">
                                    <button type="button"
                                        x-data
                                        @click="$dispatch('open-detalle-modal', { 
                                            cliente: '{{ $notificacion->cliente ? e($notificacion->cliente->nombre . ' ' . $notificacion->cliente->apellidos) : '' }}',
                                            correo: '{{ $notificacion->cliente ? e($notificacion->cliente->correo) : '' }}',
                                            asunto: '{{ e($notificacion->asunto) }}',
                                            mensaje: '{{ e($notificacion->mensaje) }}',
                                            fecha: '{{ $notificacion->created_at ? $notificacion->created_at->format('d/m/Y H:i') : '' }}',
                                            estado: '{{ $notificacion->estado }}'
                                        })"
                                        class="text-[#550000] font-medium hover:underline">
                                        Ver detalle
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-6 text-center text-gray-400">
                                    No se encontraron notificaciones registradas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $notificaciones->links() }}
            </div>
        </div>
    </div>

    <!-- Modal para ver detalle de la notificación -->
    <div x-data="{ show: false, cliente: '', correo: '', asunto: '', mensaje: '', fecha: '', estado: '' }"
        @open-detalle-modal.window="show = true; cliente = $event.detail.cliente; correo = $event.detail.correo; asunto = $event.detail.asunto; mensaje = $event.detail.mensaje; fecha = $event.detail.fecha; estado = $event.detail.estado"
        x-show="show" x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display:none;">
        <div class="absolute inset-0 bg-black/50" @click="show = false"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl max-w-lg w-full p-6 text-left">
            <div class="flex justify-between items-start mb-4 border-b pb-3">
                <div>
                    <h3 class="text-lg font-bold text-gray-900" x-text="asunto"></h3>
                    <p class="text-xs text-gray-500 mt-0.5">Para: <span x-text="cliente" class="font-medium"></span> (&lt;<span x-text="correo"></span>&gt;)</p>
                </div>
                <span class="px-2.5 py-1 rounded-full text-xs font-semibold"
                    :class="estado === 'Enviado' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'"
                    x-text="estado"></span>
            </div>

            <div class="mb-4">
                <label class="text-xs text-gray-400 uppercase tracking-wide">Fecha de Envío</label>
                <p class="text-sm text-gray-700 mt-0.5" x-text="fecha"></p>
            </div>

            <div class="mb-6">
                <label class="text-xs text-gray-400 uppercase tracking-wide">Mensaje Redactado</label>
                <div class="mt-1 p-3 bg-gray-50 rounded-lg text-sm text-gray-800 whitespace-pre-wrap border border-gray-200" x-text="mensaje"></div>
            </div>

            <div class="flex justify-end">
                <button type="button" @click="show = false"
                    class="px-5 py-2 rounded-lg bg-gray-200 text-gray-700 font-medium hover:bg-gray-300 transition">
                    Cerrar
                </button>
            </div>
        </div>
    </div>
</x-app-layout>
