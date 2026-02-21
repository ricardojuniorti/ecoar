<?php

namespace App\Http\Controllers;

use App\Models\Momento;
use Illuminate\Http\Request;

class MomentoController extends Controller
{
    /**
     * Lista todos os momentos.
     * Rota: GET /api/v1/momentos
     */
    public function index()
    {
        return Momento::all();
    }

    /**
     * Cria um novo momento.
     * Rota: POST /api/v1/momentos
     */
    public function store(Request $request)
    {
        // Validação: Obrigatório, texto e único na tabela 'momentos'
        $request->validate([
            'descricao' => 'required|string|max:100|unique:momentos,descricao'
        ]);

        $momento = Momento::create($request->all());

        return response()->json($momento, 201);
    }

    /**
     * Exibe um momento específico.
     * Rota: GET /api/v1/momentos/{id}
     */
    public function show(string $id)
    {
        return Momento::findOrFail($id);
    }

    /**
     * Atualiza um momento existente.
     * Rota: PUT/PATCH /api/v1/momentos/{id}
     */
    public function update(Request $request, string $id)
    {
        $momento = Momento::findOrFail($id);

        // Validação ignora o ID atual na verificação de unicidade
        $request->validate([
            'descricao' => 'required|string|max:100|unique:momentos,descricao,' . $id
        ]);

        $momento->update($request->all());

        return response()->json($momento);
    }

    /**
     * Remove um momento.
     * Rota: DELETE /api/v1/momentos/{id}
     */
    public function destroy(string $id)
    {
        Momento::destroy($id);
        return response()->noContent();
    }
}
