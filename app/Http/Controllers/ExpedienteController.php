<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExpedienteRequest;
use App\Models\Cliente;
use App\Models\Expediente;
use App\Models\User;
use Illuminate\Http\Request;

class ExpedienteController extends Controller
{
    /**
     * Caso de uso 3 (búsqueda previa): pide la cédula para ubicar al cliente
     * antes de crear/consultar/actualizar/cerrar un expediente.
     */
    public function buscarPorCedula(Request $request)
    {
        $identificacion = $request->input('identificacion');

        $cliente = Cliente::where('identificacion', $identificacion)->first();

        if (!$cliente) {
            return back()->withErrors(['identificacion' => 'Cliente no encontrado']);
        }

        return redirect()
            ->route('expedientes.consultar', $cliente->Id_Cliente);
    }

    /**
     * Caso de uso 4: Crear Expediente
     */
    public function create(Cliente $cliente)
    {
        $funcionarios = User::where('tipo_usuario', 'Funcionario')->get();

        return view('expediente.crear', compact('cliente', 'funcionarios'));
    }

    public function store(ExpedienteRequest $request)
    {
        $datos = $request->validated();
        $datos['fecha_creacion'] = $request->input('fecha_creacion', now());
        $datos['estado'] = $request->input('estado', 'En proceso');

        Expediente::create($datos);

        return redirect()
            ->route('expedientes.consultar', $datos['Id_Cliente'])
            ->with('success', 'Expediente creado exitosamente.');
    }

    /**
     * Caso de uso 6: Consultar expediente
     */
    public function consultarPorCliente(Cliente $cliente)
    {
        $expediente = Expediente::where('Id_Cliente', $cliente->Id_Cliente)
            ->with('documentos')
            ->first();

        if (!$expediente) {
            return back()->with('error', 'No existe expediente para este cliente');
        }

        return view('expediente.consultar', compact('cliente', 'expediente'));
    }

    /**
     * Caso de uso 5: Actualizar expediente
     */
    public function edit(Expediente $expediente)
    {
        $funcionarios = User::where('tipo_usuario', 'Funcionario')->get();

        return view('expediente.actualizar', compact('expediente', 'funcionarios'));
    }

    public function update(ExpedienteRequest $request, Expediente $expediente)
    {
        $expediente->update($request->validated());

        return redirect()
            ->route('expedientes.consultar', $expediente->Id_Cliente)
            ->with('success', 'Expediente actualizado correctamente.');
    }

    /**
     * Caso de uso 7: Cerrar expediente
     */
    public function cerrar(Expediente $expediente)
    {
        if ($expediente->estado === 'Completado') {
            return back()->with('error', 'El expediente ya se encuentra cerrado');
        }

        $expediente->update(['estado' => 'Completado']);

        return redirect()
            ->route('expedientes.consultar', $expediente->Id_Cliente)
            ->with('success', 'Expediente cerrado correctamente.');
    }
}