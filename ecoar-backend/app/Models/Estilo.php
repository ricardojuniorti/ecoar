<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model; // <--- Importante
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Estilo extends Model
{
    use HasFactory;

    protected $fillable = ['descricao'];

    public function musicas() {
    return $this->belongsToMany(Musica::class, 'estilo_musica');
}
}
