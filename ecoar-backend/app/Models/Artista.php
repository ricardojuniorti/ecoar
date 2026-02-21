<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Artista extends Model
{
    protected $fillable = ['nome'];

    // Sem essa função, o withCount dá erro 500
    public function musicas()
    {
        return $this->hasMany(Musica::class, 'artista_id');
    }
}
