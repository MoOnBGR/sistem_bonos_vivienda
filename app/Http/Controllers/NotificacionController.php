<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Notificacion;
use App\Mail\NotificacionCliente;
use App\Helpers\Historial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class NotificacionController extends Controller
{
    private function autorizarAcceso(): void
    {
        $tipo = auth()->user()->tipo_usuario ?? '';
        if (!in_array($tipo, ['Funcionario', 'Administrador'])) {
            abort(403, 'No tiene permisos para acceder a este módulo.');
        }
    }

    public function index(Request $request)
    {
        $this->autorizarAcceso();

        $query = Notificacion::with(['cliente', 'user']);

        if ($request->filled('termino')) {
            $termino = strtolower($request->input('termino'));

            $query->whereHas('cliente', function ($q) use ($termino) {
                $q->whereRaw('LOWER(nombre) LIKE ?', ["%{$termino}%"])
                    ->orWhereRaw('LOWER(apellidos) LIKE ?', ["%{$termino}%"])
                    ->orWhereRaw('LOWER(identificacion) LIKE ?', ["%{$termino}%"]);
            })->orWhereRaw('LOWER(asunto) LIKE ?', ["%{$termino}%"]);
        }

        $notificaciones = $query->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('funcionario.notificaciones.index', compact('notificaciones'));
    }

    public function create(Request $request)
    {
        $this->autorizarAcceso();

        $clientes = Cliente::orderBy('nombre')->orderBy('apellidos')->get();
        $clienteSeleccionadoId = $request->query('cliente_id');

        return view('funcionario.notificaciones.crear', compact('clientes', 'clienteSeleccionadoId'));
    }

    public function store(Request $request)
    {
        $this->autorizarAcceso();

        $validated = $request->validate([
            'cliente_id' => 'required|integer|exists:clientes,Id_Cliente',
            'asunto' => 'required|string|max:255',
            'mensaje' => 'required|string',
        ]);

        $cliente = Cliente::findOrFail($validated['cliente_id']);

        $estado = 'Enviado';
        $statusMessage = 'Notificación enviada correctamente por correo electrónico.';

        try {
            Mail::to($cliente->correo)->send(new NotificacionCliente($validated['asunto'], $validated['mensaje']));
        } catch (\Throwable $e) {
            Log::error('Error al enviar notificación por correo a ' . $cliente->correo . ': ' . $e->getMessage());
            $estado = 'Fallido';
            $statusMessage = 'No se pudo enviar el correo al cliente. La notificación fue registrada con estado "Fallido".';
        }

        $notificacion = Notificacion::create([
            'Id_Cliente' => $cliente->Id_Cliente,
            'user_id' => auth()->id(),
            'asunto' => $validated['asunto'],
            'mensaje' => $validated['mensaje'],
            'estado' => $estado,
        ]);

        Historial::registrar(
            'Notificaciones',
            'Enviar',
            'Se redactó una notificación para el cliente ' . $cliente->nombre . ' ' . $cliente->apellidos . ' (Estado: ' . $estado . ')',
            (string) $notificacion->id
        );

        return redirect()->route('funcionario.notificaciones.index')->with('status', $statusMessage);
    }
}
