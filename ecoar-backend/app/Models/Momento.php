<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model; // <--- Importante
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Momento extends Model
{
    use HasFactory;

    protected $fillable = ['descricao'];
}
