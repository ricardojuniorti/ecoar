<?php

namespace App\Http\Controllers;

use App\Models\Estilo;
use Illuminate\Http\Request;

class EstiloController extends Controller
{
    /**
     * Lista todos os estilos.
     * Rota: GET /api/v1/estilos
     */
    public function index() {
        // Retorna estilos com a contagem de músicas relacionadas
        return Estilo::withCount('musicas')->get();
    }

    /**
     * Cria um novo estilo.
     * Rota: POST /api/v1/estilos
     */
    public function store(Request $request)
    {
        // Validação: Obrigatório, texto, máx 50 chars e ÚNICO na tabela estilos
        $request->validate([
            'descricao' => 'required|string|max:50|unique:estilos,descricao'
        ]);

        $estilo = Estilo::create($request->all());

        return response()->json($estilo, 201); // Retorna 201 (Created)
    }

    /**
     * Exibe um estilo específico.
     * Rota: GET /api/v1/estilos/{id}
     */
    public function show(string $id)
    {
        return Estilo::findOrFail($id);
    }

    /**
     * Atualiza um estilo existente.
     * Rota: PUT/PATCH /api/v1/estilos/{id}
     */
    public function update(Request $request, string $id)
    {
        $estilo = Estilo::findOrFail($id);

        // A validação unique aqui ignora o ID atual (para permitir salvar o mesmo nome se for o próprio registro)
        $request->validate([
            'descricao' => 'required|string|max:50|unique:estilos,descricao,' . $id
        ]);

        $estilo->update($request->all());

        return response()->json($estilo);
    }

    /**
     * Remove um estilo.
     * Rota: DELETE /api/v1/estilos/{id}
     */
    public function destroy(string $id)
    {
        Estilo::destroy($id);
        return response()->noContent(); // Retorna 204 (Sem conteúdo, sucesso)
    }
}
