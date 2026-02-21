<?php

namespace App\Http\Controllers;

use App\Models\Artista;
use Illuminate\Http\Request;

class ArtistaController extends Controller
{
    // LISTAR TODOS (GET /artistas)
    public function index()
    {
        try {
            // Tenta buscar com a contagem de músicas
            return Artista::withCount('musicas')->get();
        } catch (\Exception $e) {
            // Se der erro (ex: relação não encontrada), retorna o básico
            return Artista::all();
        }
    }

    // CRIAR NOVO (POST /artistas)
    public function store(Request $request)
    {
        $request->validate(['nome' => 'required|string|max:255']);
        return Artista::create($request->all());
    }

    // MOSTRAR UM (GET /artistas/{id})
    public function show(string $id)
    {
        return Artista::findOrFail($id);
    }

    // ATUALIZAR (PUT/PATCH /artistas/{id})
    public function update(Request $request, string $id)
    {
        $artista = Artista::findOrFail($id);
        $artista->update($request->all());
        return $artista;
    }

    // DELETAR (DELETE /artistas/{id})
    public function destroy(string $id)
    {
        return Artista::destroy($id);
    }
}
