<?php

namespace App\Http\Controllers;

use App\Models\prueba;
use Illuminate\Http\Request;

class PruebaController extends Controller
{
  public function index()
    {
        return prueba::with('aula')->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required',
            'apellido' => 'required',
            'email' => 'required|email|unique:estudiantes',
            'aula_id' => 'required|exists:aulas,id',
        ]);

        return prueba::create($validated);
    }

    public function show($id)
    {
        return prueba::with('aula')->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $prueba = prueba::findOrFail($id);
        $prueba->update($request->all());
        return $prueba;
    }

    public function destroy($id)
    {
        prueba::destroy($id);
        return response()->noContent();
    }
}
