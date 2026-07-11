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
     * Pantalla de Listado: muestra todos los expedientes con filtros
     * (cliente, estado, rango de fechas), tal como el prototipo de Figma.
     */
    public function index(Request $request)
    {
        $clienteBuscado = null;
        $busquedaSinResultados = false;

        if ($request->filled('identificacion')) {
            $clienteBuscado = Cliente::where('identificacion', $request->identificacion)->first();

            if (!$clienteBuscado) {
                $busquedaSinResultados = true;
                $expedientes = collect();
            } else {
                $expedientes = Expediente::with(['cliente', 'funcionario'])
                    ->where('Id_Cliente', $clienteBuscado->Id_Cliente)
                    ->orderByDesc('fecha_creacion')
                    ->get();
            }
        } else {
            $query = Expediente::with(['cliente', 'funcionario']);

            if ($request->filled('estado')) {
                $query->where('estado', $request->estado);
            } else {
                $query->whereNotIn('estado', ['Completado', 'Inactivo']);
            }

            if ($request->filled('fecha_desde')) {
                $query->whereDate('fecha_creacion', '>=', $request->fecha_desde);
            }

            if ($request->filled('fecha_hasta')) {
                $query->whereDate('fecha_creacion', '<=', $request->fecha_hasta);
            }

            $expedientes = $query->orderByDesc('fecha_creacion')->get();
        }

        return view('expediente.index', compact('expedientes', 'clienteBuscado', 'busquedaSinResultados'));
    }

    /**
     * Busca un cliente por cédula para iniciar el flujo de "Nuevo expediente"
     * desde el botón de la pantalla de Listado.
     */
    public function buscarParaCrear(Request $request)
    {
        $identificacion = $request->input('identificacion');

        $cliente = Cliente::where('identificacion', $identificacion)->first();

        if (!$cliente) {
            return back()->withErrors(['identificacion' => 'Cliente no encontrado'])->withInput();
        }

        $yaTieneExpediente = Expediente::where('Id_Cliente', $cliente->Id_Cliente)->exists();

        if ($yaTieneExpediente) {
            return back()->withErrors(['identificacion' => 'Este cliente ya tiene un expediente registrado.'])->withInput();
        }

        return redirect()->route('expedientes.crear', $cliente->Id_Cliente);
    }

    /**
     * Caso de uso 3 (búsqueda previa): pide la cédula para ubicar al cliente
     * antes de consultar/actualizar/cerrar un expediente.
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

        $yaTieneExpediente = Expediente::where('Id_Cliente', $datos['Id_Cliente'])->exists();

        if ($yaTieneExpediente) {
            return back()->with('error', 'Este cliente ya tiene un expediente registrado.');
        }

        $datos['fecha_creacion'] = $request->input('fecha_creacion', now());
        $datos['estado'] = $request->input('estado', 'En proceso');

        $expediente = Expediente::create($datos);

        return redirect()
            ->route('expedientes.confirmacion', $expediente->id_expediente)
            ->with('success', 'Expediente creado exitosamente.');
    }

    /**
     * Pantalla de confirmación tras crear un expediente.
     */
    public function confirmacion(Expediente $expediente)
    {
        $expediente->load('cliente');

        return view('expediente.confirmacionCreacionExpediente', compact('expediente'));
    }

    /**
     * Caso de uso 6: Consultar expediente (ahora redirige a la vista de carpetas)
     */
    public function consultarPorCliente(Cliente $cliente)
    {
        $expediente = Expediente::where('Id_Cliente', $cliente->Id_Cliente)->first();

        if (!$expediente) {
            return back()->with('error', 'No existe expediente para este cliente');
        }

        return redirect()->route('expedientes.carpetas.index', $expediente->id_expediente);
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
        if ($expediente->estado === 'Inactivo') {
            return back()->with('error', 'El expediente ya se encuentra cerrado');
        }

        $expediente->update(['estado' => 'Inactivo']);

        return redirect()
            ->route('expedientes.consultar', $expediente->Id_Cliente)
            ->with('success', 'Expediente cerrado correctamente.');
    }

    /**
     * Reabrir un expediente que estaba cerrado (Inactivo).
     */
    public function reabrir(Expediente $expediente)
    {
        if ($expediente->estado !== 'Inactivo') {
            return back()->with('error', 'El expediente no está cerrado.');
        }

        $expediente->update(['estado' => 'En proceso']);

        return redirect()
            ->route('expedientes.consultar', $expediente->Id_Cliente)
            ->with('success', 'Expediente reabierto correctamente.');
    }
}