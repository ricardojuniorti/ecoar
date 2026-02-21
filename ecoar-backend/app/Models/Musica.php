<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Musica extends Model
{
    use HasFactory;

    // O Laravel assume table 'musicas'
    protected $fillable = ['titulo', 'link', 'artista_id'];

    public function artista()
    {
        return $this->belongsTo(Artista::class);
        // Assume 'artista_id' automaticamente
    }

    public function estilos()
    {
        return $this->belongsToMany(Estilo::class);
        // Assume tabela 'estilo_musica' e chaves 'musica_id'/'estilo_id'
    }

    public function momentos()
    {
        return $this->belongsToMany(Momento::class);
        // Assume tabela 'momento_musica'
    }
}
