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
     * (búsqueda por cédula/nombre/apellidos, estado, rango de fechas).
     */
    public function index(Request $request)
    {
        $expedientes = collect();
        $busquedaSinResultados = false;
        $mensajeSinResultados = null;

        if ($request->filled('busqueda')) {
            $termino = $request->busqueda;

            $idsClientes = Cliente::where('identificacion', 'like', "%{$termino}%")
                ->orWhere('nombre', 'like', "%{$termino}%")
                ->orWhere('apellidos', 'like', "%{$termino}%")
                ->pluck('Id_Cliente');

            if ($idsClientes->isEmpty()) {
                $busquedaSinResultados = true;
                $mensajeSinResultados = 'No hay registros de expedientes con esa cédula, nombre o apellido.';
            } else {
                $expedientes = Expediente::with(['cliente', 'funcionario'])
                    ->whereIn('Id_Cliente', $idsClientes)
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

            if ($expedientes->isEmpty()) {
                $tieneEstado = $request->filled('estado');
                $tieneFecha = $request->filled('fecha_desde') || $request->filled('fecha_hasta');

                if ($tieneEstado && $tieneFecha) {
                    $busquedaSinResultados = true;
                    $mensajeSinResultados = 'No hay registros de expedientes con ese estado y esa fecha.';
                } elseif ($tieneEstado) {
                    $busquedaSinResultados = true;
                    $mensajeSinResultados = 'No hay registros de expedientes con ese estado.';
                } elseif ($tieneFecha) {
                    $busquedaSinResultados = true;
                    $mensajeSinResultados = 'No hay registros de expedientes con esa fecha.';
                }
            }
        }

        return view('expediente.index', compact('expedientes', 'busquedaSinResultados', 'mensajeSinResultados'));
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

        return redirect()->route('expedientes.consultar', $cliente->Id_Cliente);
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

        $cliente = Cliente::find($datos['Id_Cliente']);
        \App\Helpers\Historial::registrar(
            'Expedientes',
            'Crear',
            'Se creó el expediente EXP-' . str_pad($expediente->id_expediente, 4, '0', STR_PAD_LEFT) . ' para el cliente: ' . ($cliente->nombre ?? '') . ' ' . ($cliente->apellidos ?? ''),
            $expediente->id_expediente
        );

        return redirect()
            ->route('expedientes.confirmacion', $expediente->id_expediente)
            ->with('success', 'Expediente creado exitosamente.');
    }

    public function confirmacion(Expediente $expediente)
    {
        $expediente->load('cliente');

        return view('expediente.confirmacionCreacionExpediente', compact('expediente'));
    }

    public function consultarPorCliente(Cliente $cliente)
    {
        $expediente = Expediente::where('Id_Cliente', $cliente->Id_Cliente)->first();

        if (!$expediente) {
            return back()->with('error', 'No existe expediente para este cliente');
        }

        return redirect()->route('expedientes.carpetas.index', $expediente->id_expediente);
    }

    public function edit(Expediente $expediente)
    {
        $funcionarios = User::where('tipo_usuario', 'Funcionario')->get();

        return view('expediente.actualizar', compact('expediente', 'funcionarios'));
    }

    public function update(ExpedienteRequest $request, Expediente $expediente)
    {
        $expediente->update($request->validated());

        \App\Helpers\Historial::registrar(
            'Expedientes',
            'Actualizar',
            'Se actualizó el expediente EXP-' . str_pad($expediente->id_expediente, 4, '0', STR_PAD_LEFT) . ' del cliente: ' . ($expediente->cliente->nombre ?? '') . ' ' . ($expediente->cliente->apellidos ?? ''),
            $expediente->id_expediente
        );

        return redirect()
            ->route('expedientes.consultar', $expediente->Id_Cliente)
            ->with('success', 'Expediente actualizado correctamente.');
    }

    public function cerrar(Expediente $expediente)
    {
        if ($expediente->estado === 'Inactivo') {
            return back()->with('error', 'El expediente ya se encuentra cerrado');
        }

        $expediente->update(['estado' => 'Inactivo']);

        \App\Helpers\Historial::registrar(
            'Expedientes',
            'Cerrar',
            'Se cerró el expediente EXP-' . str_pad($expediente->id_expediente, 4, '0', STR_PAD_LEFT) . ' del cliente: ' . ($expediente->cliente->nombre ?? '') . ' ' . ($expediente->cliente->apellidos ?? ''),
            $expediente->id_expediente
        );

        return redirect()
            ->route('expedientes.consultar', $expediente->Id_Cliente)
            ->with('success', 'Expediente cerrado correctamente.');
    }

    public function reabrir(Expediente $expediente)
    {
        if ($expediente->estado !== 'Inactivo') {
            return back()->with('error', 'El expediente no está cerrado.');
        }

        $expediente->update(['estado' => 'En proceso']);

        \App\Helpers\Historial::registrar(
            'Expedientes',
            'Reabrir',
            'Se reabrio el expediente EXP-' . str_pad($expediente->id_expediente, 4, '0', STR_PAD_LEFT) . ' del cliente: ' . ($expediente->cliente->nombre ?? '') . ' ' . ($expediente->cliente->apellidos ?? ''),
            $expediente->id_expediente
        );

        return redirect()
            ->route('expedientes.consultar', $expediente->Id_Cliente)
            ->with('success', 'Expediente reabierto correctamente.');
    }
}