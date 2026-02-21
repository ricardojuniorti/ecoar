<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabelas de Apoio (Simples)
        Schema::create('artistas', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->timestamps();
        });

        Schema::create('estilos', function (Blueprint $table) {
            $table->id();
            $table->string('descricao');
            $table->timestamps();
        });

        Schema::create('momentos', function (Blueprint $table) {
            $table->id();
            $table->string('descricao');
            $table->timestamps();
        });

        // 2. Tabela Principal
        Schema::create('musicas', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->string('link')->nullable();

            // Padrão Laravel: Detecta automaticamente que 'artista_id' aponta para 'artistas.id'
            $table->foreignId('artista_id')->constrained()->onDelete('cascade');

            $table->timestamps();
        });

        // 3. Tabelas Pivô (Nomes em Ordem Alfabética dos Models)

        // Estilo + Musica = estilo_musica (E vem antes de M)
        Schema::create('estilo_musica', function (Blueprint $table) {
            $table->foreignId('musica_id')->constrained()->onDelete('cascade');
            $table->foreignId('estilo_id')->constrained()->onDelete('cascade');
            // Chave primária composta opcional, mas recomendada
            $table->primary(['musica_id', 'estilo_id']);
        });

        // Momento + Musica = momento_musica (Mo vem antes de Mu)
        Schema::create('momento_musica', function (Blueprint $table) {
            $table->foreignId('musica_id')->constrained()->onDelete('cascade');
            $table->foreignId('momento_id')->constrained()->onDelete('cascade');
            $table->primary(['musica_id', 'momento_id']);
        });
    }

    public function down(): void
    {
        // A ordem de drop importa por causa das chaves estrangeiras
        Schema::dropIfExists('momento_musica');
        Schema::dropIfExists('estilo_musica');
        Schema::dropIfExists('musicas');
        Schema::dropIfExists('momentos');
        Schema::dropIfExists('estilos');
        Schema::dropIfExists('artistas');
    }
};
