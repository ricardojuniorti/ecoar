<?php

namespace App\Http\Controllers;

use App\Models\Musica;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MusicaController extends Controller
{
    public function index()
    {
        return Musica::with(['artista', 'estilos', 'momentos'])->get();
    }

    // MÉTODO QUE ESTAVA FALTANDO PARA A TELA DE EDIÇÃO
    public function show($id)
    {
        // Busca a música com todos os relacionamentos necessários
        return Musica::with(['artista', 'estilos', 'momentos'])->findOrFail($id);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'titulo'     => 'required|string|max:150',
            'link'       => 'nullable|url',
            'artista_id' => 'required|exists:artistas,id',
            'estilos'    => 'array',
            'estilos.*'  => 'exists:estilos,id',
            'momentos'   => 'array',
            'momentos.*' => 'exists:momentos,id',
        ]);

        try {
            DB::beginTransaction();
            $musica = Musica::create($request->only(['titulo', 'link', 'artista_id']));

            if ($request->has('estilos')) {
                $musica->estilos()->sync($request->estilos);
            }

            if ($request->has('momentos')) {
                $musica->momentos()->sync($request->momentos);
            }

            DB::commit();
            return response()->json($musica->load(['artista', 'estilos', 'momentos']), 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Erro ao salvar: ' . $e->getMessage()], 500);
        }
    }

    // MÉTODO PARA ATUALIZAR (PUT)
    public function update(Request $request, $id)
    {
        $musica = Musica::findOrFail($id);

        $request->validate([
            'titulo'     => 'required|string|max:150',
            'link'       => 'nullable|url',
            'artista_id' => 'required|exists:artistas,id',
            'estilos'    => 'array',
            'estilos.*'  => 'exists:estilos,id',
            'momentos'   => 'array',
            'momentos.*' => 'exists:momentos,id',
        ]);

        try {
            DB::beginTransaction();

            $musica->update($request->only(['titulo', 'link', 'artista_id']));

            // Sincroniza os relacionamentos (remove os antigos e add os novos)
            $musica->estilos()->sync($request->estilos ?? []);
            $musica->momentos()->sync($request->momentos ?? []);

            DB::commit();
            return response()->json($musica->load(['artista', 'estilos', 'momentos']));
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Erro ao atualizar: ' . $e->getMessage()], 500);
        }
    }

    // MÉTODO PARA EXCLUIR (DELETE)
    public function destroy($id)
    {
        $musica = Musica::findOrFail($id);

        // O Laravel cuidará de remover as relações nas tabelas pivot se
        // você configurou 'onDelete cascade' na migration, senão o sync([]) resolve:
        $musica->estilos()->detach();
        $musica->momentos()->detach();

        $musica->delete();

        return response()->json(['message' => 'Música removida com sucesso']);
    }
}
