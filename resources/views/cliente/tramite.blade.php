<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Mi Trámite
        </h2>
    </x-slot>

    <div class="space-y-6">

        <!-- Banda -->
        <div class="bg-[#550000] rounded-2xl p-6 text-white">
            <h3 class="text-2xl font-bold">Estado de mi Trámite</h3>
            <p class="text-white/70 text-sm mt-1">Consulte el estado actual de su solicitud de bono de vivienda.</p>
        </div>

        @if(!$expediente)
            <!-- Sin expediente -->
            <div class="bg-white rounded-xl shadow-sm p-8 text-center">
                <div class="text-gray-400 text-5xl mb-4">📋</div>
                <h4 class="text-lg font-semibold text-gray-700 mb-2">No tiene un expediente activo</h4>
                <p class="text-gray-500 text-sm">Un funcionario debe crear su expediente para iniciar el proceso de solicitud de bono.</p>
            </div>
        @else
            <!-- Estado del expediente -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h4 class="font-semibold text-gray-700 mb-4 text-lg">Información del expediente</h4>
                
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-xs text-gray-500 uppercase tracking-wide">Número de expediente</p>
                        <p class="text-lg font-bold text-[#550000] mt-1">
                            EXP-{{ str_pad($expediente->id_expediente, 4, '0', STR_PAD_LEFT) }}
                        </p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-xs text-gray-500 uppercase tracking-wide">Fecha de apertura</p>
                        <p class="text-lg font-bold text-gray-700 mt-1">
                            {{ \Carbon\Carbon::parse($expediente->fecha_creacion)->format('d/m/Y') }}
                        </p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-xs text-gray-500 uppercase tracking-wide">Funcionario asignado</p>
                        <p class="text-lg font-bold text-gray-700 mt-1">
                            {{ $expediente->funcionario->name ?? 'No asignado' }}
                        </p>
                    </div>
                </div>

                <!-- Estado actual con barra de progreso -->
                <div class="mb-6">
                    <p class="text-xs text-gray-500 uppercase tracking-wide mb-3">Estado actual</p>
                    @php
                        $totalDocs = $documentos->count();
                        $validados = $documentos->where('estado_doc', 'Validado')->count();
                        $rechazados = $documentos->where('estado_doc', 'Rechazado')->count();
                        $pendientes = $documentos->where('estado_doc', 'Pendiente')->count();
                        $esInactivo = $expediente->estado === 'Inactivo';
                        $progreso = $totalDocs > 0 ? round(($validados / $totalDocs) * 100) : 0;
                    @endphp

                    @if($esInactivo)
                        <div class="flex items-center gap-3">
                            <span class="bg-red-100 text-red-700 text-sm font-semibold px-4 py-2 rounded-full">
                                Expediente cerrado
                            </span>
                            <p class="text-gray-500 text-sm">Su expediente ha sido cerrado. Contacte al funcionario para más información.</p>
                        </div>
                    @else
                        <div class="flex items-center gap-3 mb-4">
                            <span class="bg-yellow-100 text-yellow-700 text-sm font-semibold px-4 py-2 rounded-full">
                                {{ $expediente->estado }}
                            </span>
                        </div>

                        <!-- Resumen de documentos -->
                        <div class="grid grid-cols-3 gap-3 mb-4">
                            <div class="bg-green-50 rounded-lg p-3 text-center">
                                <p class="text-2xl font-bold text-green-700">{{ $validados }}</p>
                                <p class="text-xs text-green-600 mt-1">Validados</p>
                            </div>
                            <div class="bg-yellow-50 rounded-lg p-3 text-center">
                                <p class="text-2xl font-bold text-yellow-700">{{ $pendientes }}</p>
                                <p class="text-xs text-yellow-600 mt-1">Pendientes</p>
                            </div>
                            <div class="bg-red-50 rounded-lg p-3 text-center">
                                <p class="text-2xl font-bold text-red-700">{{ $rechazados }}</p>
                                <p class="text-xs text-red-600 mt-1">Rechazados</p>
                            </div>
                        </div>

                        <!-- Barra de progreso -->
                        <div>
                            <div class="flex justify-between text-xs text-gray-400 mb-1">
                                <span>Progreso de documentos</span>
                                <span>{{ $progreso }}% validados</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-3">
                                <div class="bg-[#550000] h-3 rounded-full transition-all duration-500"
                                    style="width: {{ $progreso }}%"></div>
                            </div>
                            @if($progreso === 100)
                                <p class="text-green-600 text-xs mt-2 font-medium">Todos los documentos han sido validados.</p>
                            @elseif($rechazados > 0)
                                <p class="text-red-600 text-xs mt-2">Tiene {{ $rechazados }} documento(s) rechazado(s). Por favor corrija y vuelva a subir.</p>
                            @else
                                <p class="text-gray-500 text-xs mt-2">Siga subiendo sus documentos para avanzar en el proceso.</p>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <!-- Documentos -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h4 class="font-semibold text-gray-700 mb-4 text-lg">Mis documentos</h4>

                @if($documentos->isEmpty())
                    <p class="text-gray-400 text-sm text-center py-4">No ha subido documentos aún.</p>
                @else
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="border-b border-gray-200 text-gray-500 uppercase text-xs">
                                <th class="py-3 pr-4">Documento</th>
                                <th class="py-3 pr-4">Fecha subida</th>
                                <th class="py-3 pr-4">Estado</th>
                                <th class="py-3 pr-4">Motivo de rechazo</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($documentos as $doc)
                                <tr class="border-b border-gray-100 hover:bg-gray-50">
                                    <td class="py-3 pr-4 font-medium text-gray-700">{{ $doc->nombre_doc }}</td>
                                    <td class="py-3 pr-4 text-gray-500">
                                        {{ \Carbon\Carbon::parse($doc->fecha_subida)->format('d/m/Y') }}
                                    </td>
                                    <td class="py-3 pr-4">
                                        <span class="px-2 py-1 rounded-full text-xs font-medium
                                            @if($doc->estado_doc === 'Validado') bg-green-100 text-green-700
                                            @elseif($doc->estado_doc === 'Rechazado') bg-red-100 text-red-700
                                            @else bg-yellow-100 text-yellow-700
                                            @endif">
                                            {{ $doc->estado_doc }}
                                        </span>
                                    </td>
                                    <td class="py-3 pr-4 text-gray-500 text-xs">
                                        {{ $doc->motivo_rechazo ?? '—' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        @endif
    </div>
</x-app-layout>