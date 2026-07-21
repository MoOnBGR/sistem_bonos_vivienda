<x-app-layout>
    <div class="container mx-auto mt-4 max-w-2xl">
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-2xl font-semibold text-[#1a2a4a] mb-6">
                Subir Documento
            </h2>

            <form action="{{ route('documentos.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">ID del Expediente *</label>
                    <input type="number" name="id_expediente" class="w-full border rounded-lg px-3 py-2" required>
                    <small class="text-gray-500">ID del expediente donde se guardará el documento</small>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre del Documento *</label>
                    <input type="text" name="nombre_doc" class="w-full border rounded-lg px-3 py-2" required>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Documento *</label>
                    <select name="tipo_doc" class="w-full border rounded-lg px-3 py-2" required>
                        <option value="">Seleccione...</option>
                        <option value="Cédula">Cédula</option>
                        <option value="Planos">Planos</option>
                        <option value="Constancia de Ingresos">Constancia de Ingresos</option>
                        <option value="Certificado de Propiedad">Certificado de Propiedad</option>
                        <option value="Otro">Otro</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Archivo *</label>
                    <input type="file" name="archivo" class="w-full border rounded-lg px-3 py-2" accept=".pdf" required>
                    <small class="text-gray-500">PDF, máximo 20MB</small>
                </div>

                <div class="flex justify-between">
                    <a href="{{ route('documentos.index') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400">Cancelar</a>
                    <button type="submit" class="bg-[#1a2a4a] text-white px-4 py-2 rounded-lg hover:bg-[#2a3a5a]">Subir</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>