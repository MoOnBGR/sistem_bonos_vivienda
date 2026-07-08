<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    public function create()
    {
        if (auth()->user()->cliente) {
            return redirect()->route('dashboard');
        }

        return view('cliente.datos-adicionales');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'identificacion' => 'required|string|max:20|unique:clientes,identificacion',
            'nombre' => 'required|string|max:100',
            'apellidos' => 'required|string|max:100',
            'telefono' => 'required|string|max:20',
            'direccion' => 'required|string',
            'correo' => 'required|email|max:150',
        ]);

        $validated['Id_user'] = auth()->id();
        $validated['estado'] = 'Activo';

        Cliente::create($validated);

        return redirect()->route('dashboard');
    }

    public function buscar(Request $request)
    {
        $clientes = null;

        if ($request->filled('termino')) {
            $termino = strtolower($request->input('termino'));

            $clientes = Cliente::where(function ($q) use ($termino) {
                $q->whereRaw('LOWER(nombre) LIKE ?', ["%{$termino}%"])
                    ->orWhereRaw('LOWER(apellidos) LIKE ?', ["%{$termino}%"])
                    ->orWhereRaw('LOWER(identificacion) LIKE ?', ["%{$termino}%"]);
            })->get();
        }

        return view('cliente.dashboard-busqueda', compact('clientes'));
    }

    public function edit()
    {
        $cliente = auth()->user()->cliente;

        if (!$cliente) {
            return redirect()->route('cliente.datos');
        }

        return view('cliente.editar-datos', compact('cliente'));
    }

    public function update(Request $request)
    {
        $cliente = auth()->user()->cliente;

        $validated = $request->validate([
            'identificacion' => 'required|string|max:20|unique:clientes,identificacion,' . $cliente->Id_Cliente . ',Id_Cliente',
            'nombre' => 'required|string|max:100',
            'apellidos' => 'required|string|max:100',
            'telefono' => 'required|string|max:20',
            'direccion' => 'required|string',
            'correo' => 'required|email|max:150',
        ]);

        $cliente->update($validated);

        return redirect()->route('cliente.dashboard')->with('status', 'Datos actualizados correctamente.');
    }
    public function index(Request $request)
    {
        if (auth()->user()->tipo_usuario !== 'Funcionario') {
            abort(403);
        }

        $query = Cliente::query();

        if ($request->filled('termino')) {
            $termino = strtolower($request->input('termino'));

            $query->where(function ($q) use ($termino) {
                $q->whereRaw('LOWER(nombre) LIKE ?', ["%{$termino}%"])
                    ->orWhereRaw('LOWER(apellidos) LIKE ?', ["%{$termino}%"])
                    ->orWhereRaw('LOWER(identificacion) LIKE ?', ["%{$termino}%"]);
            });
        }

        $clientes = $query->orderBy('nombre')->paginate(10)->withQueryString();

        return view('funcionario.clientes.index', compact('clientes'));
    }

    public function editFuncionario(Cliente $cliente)
    {
        if (auth()->user()->tipo_usuario !== 'Funcionario') {
            abort(403);
        }

        return view('funcionario.clientes.editar', compact('cliente'));
    }

    public function updateFuncionario(Request $request, Cliente $cliente)
    {
        if (auth()->user()->tipo_usuario !== 'Funcionario') {
            abort(403);
        }

        $validated = $request->validate([
            'identificacion' => 'required|string|max:20|unique:clientes,identificacion,' . $cliente->Id_Cliente . ',Id_Cliente',
            'nombre' => 'required|string|max:100',
            'apellidos' => 'required|string|max:100',
            'telefono' => 'required|string|max:20',
            'direccion' => 'required|string',
            'correo' => 'required|email|max:150',
            'estado' => 'required|in:Activo,Inactivo',
        ]);

        $cliente->update($validated);

        return redirect()->route('funcionario.clientes.index')->with('status', 'Cliente actualizado correctamente.');
    }

    public function destroy(Cliente $cliente)
    {
        if (auth()->user()->tipo_usuario !== 'Funcionario') {
            abort(403);
        }

        $cliente->delete();

        return redirect()->route('funcionario.clientes.index')->with('status', 'Cliente eliminado correctamente.');
    }

}